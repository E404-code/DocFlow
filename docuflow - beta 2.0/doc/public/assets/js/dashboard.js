// Dashboard functionality
document.addEventListener('DOMContentLoaded', function () {
  loadDashboardData();
  setupEventListeners();
});

async function loadDashboardData() {
  try {
    const response = await fetch('/doc/api/dashboard_stats.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({})
    });

    const result = await response.json();

    if (result.status === 'success') {
      const stats = result.data.statistics || {};
      updateStatistics(stats);
      updateRecentDocuments(result.data.recent_documents || []);
      updateActivityLog(result.data.activity || []);
      updateStatusOverview(result.data.status_counts || {}, stats.total_docs || 0);
      updateLastUpdated(result.data.updated_at);
    }
  } catch (error) {
    console.error('Error loading dashboard data:', error);
  }
}

function updateStatistics(stats) {
  document.querySelectorAll('[data-stat]').forEach((el) => {
    const key = el.dataset.stat;
    if (Object.prototype.hasOwnProperty.call(stats, key)) {
      el.textContent = stats[key] ?? 0;
    }
  });
}

function updateRecentDocuments(documents) {
  const tableBody = document.getElementById('recentDocsBody');
  if (!tableBody) return;

  tableBody.innerHTML = '';

  if (!documents.length) {
    const row = document.createElement('tr');
    row.innerHTML = `<td colspan="3" class="muted">${t('noRecentDocs', 'No recent documents')}</td>`;
    tableBody.appendChild(row);
    return;
  }

  documents.forEach((doc) => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${escapeHtml(doc.title || doc.customer_name || '—')}</td>
      <td>${translateVisibility(doc.visibility)}</td>
      <td>${formatDate(doc.created_at)}</td>
    `;
    tableBody.appendChild(row);
  });
}

function updateActivityLog(activities) {
  const list = document.querySelector('ul.log');
  if (!list) return;

  list.innerHTML = '';

  if (!activities.length) {
    const li = document.createElement('li');
    li.className = 'muted';
    li.textContent = t('noActivity', 'No recent activity');
    list.appendChild(li);
    return;
  }

  activities.forEach((item) => {
    const li = document.createElement('li');
    const description = item.description || item.action || 'Activity';
    const name = item.name ? ` • ${item.name}` : '';
    li.innerHTML = `
      <span class="log-title">${escapeHtml(description)}</span>
      <span class="log-time">${formatDateTime(item.created_at)}${escapeHtml(name)}</span>
    `;
    list.appendChild(li);
  });
}

function updateStatusOverview(statusCounts, totalDocs) {
  const total = totalDocs || Object.values(statusCounts).reduce((a, b) => a + b, 0);

  document.querySelectorAll('[data-status]').forEach((bar) => {
    const key = bar.dataset.status;
    const count = statusCounts[key] || 0;
    const percent = total ? Math.round((count / total) * 100) : 0;
    bar.style.width = `${percent}%`;
  });

  document.querySelectorAll('[data-status-count]').forEach((el) => {
    const key = el.dataset.statusCount;
    el.textContent = statusCounts[key] || 0;
  });
}

function updateLastUpdated(isoTime) {
  const el = document.getElementById('lastUpdated');
  if (!el) return;
  if (!isoTime) {
    el.textContent = '--';
    return;
  }
  el.textContent = formatDateTime(isoTime);
}

function setupEventListeners() {
  const logoutLink = document.querySelector('a[data-i18n="logout"]');
  if (logoutLink) {
    logoutLink.addEventListener('click', function (e) {
      e.preventDefault();
      logout();
    });
  }

  const refreshBtn = document.getElementById('refreshDashboard');
  if (refreshBtn) {
    refreshBtn.addEventListener('click', function () {
      loadDashboardData();
    });
  }
}

async function logout() {
  try {
    await fetch('./../../../api/logout.php');
    window.location.href = './../../index.php';
  } catch (error) {
    console.error('Logout error:', error);
    window.location.href = './../../index.php';
  }
}

// Utility functions
function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text ?? '';
  return div.innerHTML;
}

function t(key, fallback) {
  try {
    if (typeof translations !== 'undefined' && translations[currentLang] && translations[currentLang][key]) {
      return translations[currentLang][key];
    }
  } catch (e) {
    // ignore translation lookup errors
  }
  return fallback;
}

function translateVisibility(visibility) {
  const translations = {
    private: 'Private',
    department: 'Department',
    public: 'Public'
  };
  return translations[visibility] || visibility || '—';
}

function formatDate(dateString) {
  const date = new Date(dateString);
  if (Number.isNaN(date.getTime())) return '--';
  return date.toLocaleDateString();
}

function formatDateTime(dateString) {
  const date = new Date(dateString);
  if (Number.isNaN(date.getTime())) return '--';
  return date.toLocaleString();
}
