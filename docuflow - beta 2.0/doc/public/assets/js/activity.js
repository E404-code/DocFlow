document.addEventListener("DOMContentLoaded", function () {
  initializeActivityPage();
});

function initializeActivityPage() {
  // Initialize filters
  const actionFilter = document.getElementById('filter-action');
  const userFilter = document.getElementById('filter-user');
  const dateFilter = document.getElementById('filter-date');
  const searchInput = document.getElementById('search-activity');

  // Add event listeners
  if (actionFilter) actionFilter.addEventListener('change', applyFilters);
  if (userFilter) userFilter.addEventListener('input', debounce(applyFilters, 300));
  if (dateFilter) dateFilter.addEventListener('change', applyFilters);
  if (searchInput) searchInput.addEventListener('input', debounce(searchActivities, 300));

  // Load initial data
  loadActivities();
}

// Debounce function for search
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Apply filters to activities
function applyFilters() {
  const actionFilter = document.getElementById('filter-action').value;
  const userFilter = document.getElementById('filter-user').value.toLowerCase();
  const dateFilter = document.getElementById('filter-date').value;

  const activities = document.querySelectorAll('.activity-item');
  let visibleCount = 0;

  activities.forEach(activity => {
    const action = activity.dataset.action || '';
    const user = (activity.dataset.user || '').toLowerCase();
    const date = activity.dataset.date || '';

    let matches = true;

    // Filter by action (support grouped actions)
    if (actionFilter) {
      const actionValue = (action || '').toLowerCase();
      const filterValue = actionFilter.toLowerCase();
      const isGrouped =
        actionValue === filterValue ||
        actionValue.includes(filterValue) ||
        (filterValue === 'edit' && actionValue.includes('update')) ||
        (filterValue === 'upload' && actionValue.includes('create_document'));
      if (!isGrouped) {
        matches = false;
      }
    }

    // Filter by user
    if (userFilter && !user.includes(userFilter)) {
      matches = false;
    }

    // Filter by date
    if (dateFilter && date !== dateFilter) {
      matches = false;
    }

    if (matches) {
      activity.classList.remove('hidden');
      visibleCount++;
    } else {
      activity.classList.add('hidden');
    }
  });

  updateStats(visibleCount);
}

// Search activities
function searchActivities() {
  const searchTerm = document.getElementById('search-activity').value.toLowerCase();
  const activities = document.querySelectorAll('.activity-item');
  let visibleCount = 0;

  activities.forEach(activity => {
    const text = (activity.textContent || '').toLowerCase();
    
    if (text.includes(searchTerm)) {
      activity.classList.remove('hidden');
      visibleCount++;
    } else {
      activity.classList.add('hidden');
    }
  });

  updateStats(visibleCount);
}

// Reset all filters
function resetFilters() {
  document.getElementById('filter-action').value = '';
  document.getElementById('filter-user').value = '';
  document.getElementById('filter-date').value = '';
  document.getElementById('search-activity').value = '';

  const activities = document.querySelectorAll('.activity-item');
  activities.forEach(activity => {
    activity.classList.remove('hidden');
  });

  updateStats(activities.length);
}

// Update statistics
function updateStats(visibleCount) {
  const totalActivities = document.querySelectorAll('.activity-item').length;
  const totalEl = document.getElementById('totalActivitiesValue');
  if (totalEl) totalEl.textContent = totalActivities;

  // Update page info
  const pageInfo = document.querySelector('.page-info');
  if (pageInfo) {
    pageInfo.textContent = `Showing ${visibleCount} of ${totalActivities}`;
  }
}

// Load activities (mock function - replace with actual API call)
function loadActivities() {
  fetch('/doc/api/get_activities.php', { method: 'POST' })
    .then(res => res.json())
    .then(data => {
      if (data.status !== 'success') return;
      renderActivities(data.data.activities || []);
      updateStatsFromApi(data.data.stats || {});
    })
    .catch(err => console.error('Activity load error:', err));
}

function updateStatsFromApi(stats) {
  const totalEl = document.getElementById('totalActivitiesValue');
  const todayEl = document.getElementById('todayActivitiesValue');
  const activeEl = document.getElementById('activeUsersValue');
  if (totalEl) totalEl.textContent = stats.total_activities ?? 0;
  if (todayEl) todayEl.textContent = stats.today_activities ?? 0;
  if (activeEl) activeEl.textContent = stats.active_users ?? 0;
}

function renderActivities(activities) {
  const list = document.querySelector('.activities-list');
  if (!list) return;
  list.innerHTML = '';

  if (!activities.length) {
    const empty = document.createElement('div');
    empty.className = 'activity-item';
    empty.innerHTML = `<div class="activity-content"><div class="activity-details">${t('noActivity','No recent activity')}</div></div>`;
    list.appendChild(empty);
    updateStats(0);
    return;
  }

  activities.forEach(activity => {
    const el = createActivityElement({
      action: activity.action,
      user_name: activity.name,
      details: activity.description,
      date: activity.created_at,
      ip: '-'
    });
    list.appendChild(el);
  });
  updateStats(activities.length);
}

// Show activity details (expandable)
function showActivityDetails(activityElement) {
  // Toggle expanded state
  const isExpanded = activityElement.classList.contains('expanded');
  
  // Close all other expanded activities
  document.querySelectorAll('.activity-item.expanded').forEach(item => {
    if (item !== activityElement) {
      item.classList.remove('expanded');
    }
  });

  activityElement.classList.toggle('expanded');
}

// Pagination functions
function previousPage() {
  // Implement pagination logic here
  console.log('Previous page');
}

function nextPage() {
  // Implement pagination logic here
  console.log('Next page');
}

// Export activities (optional feature)
function exportActivities(format = 'csv') {
  const activities = document.querySelectorAll('.activity-item:not(.hidden)');
  const data = [];

  activities.forEach(activity => {
    const user = activity.querySelector('.activity-user').textContent;
    const action = activity.querySelector('.activity-action').textContent;
    const details = activity.querySelector('.activity-details').textContent;
    const date = activity.querySelector('.activity-date').textContent;
    const ip = activity.querySelector('.activity-ip').textContent;

    data.push({ user, action, details, date, ip });
  });

  if (format === 'csv') {
    downloadCSV(data, 'activities.csv');
  } else if (format === 'json') {
    downloadJSON(data, 'activities.json');
  }
}

// Download CSV
function downloadCSV(data, filename) {
  const csv = [
    Object.keys(data[0]).join(','),
    ...data.map(row => Object.values(row).join(','))
  ].join('\n');

  const blob = new Blob([csv], { type: 'text/csv' });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = filename;
  a.click();
  window.URL.revokeObjectURL(url);
}

// Download JSON
function downloadJSON(data, filename) {
  const json = JSON.stringify(data, null, 2);
  const blob = new Blob([json], { type: 'application/json' });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = filename;
  a.click();
  window.URL.revokeObjectURL(url);
}

// Real-time updates (WebSocket or polling - optional)
function startRealTimeUpdates() {
  // This would connect to WebSocket or use polling
  // For demonstration, we'll use polling every 30 seconds
  
  setInterval(() => {
    // Check for new activities
    // fetch('/api/activities/latest')
    //   .then(response => response.json())
    //   .then(data => {
    //     if (data.length > 0) {
    //       addNewActivities(data);
    //     }
    //   });
  }, 30000);
}

// Add new activities to the list
function addNewActivities(newActivities) {
  const activitiesList = document.querySelector('.activities-list');
  
  newActivities.forEach(activity => {
    const activityElement = createActivityElement(activity);
    activitiesList.insertBefore(activityElement, activitiesList.firstChild);
  });

  // Update stats
  const totalActivities = document.querySelectorAll('.activity-item').length;
  updateStats(totalActivities);

  // Show notification for new activities
  if (newActivities.length > 0) {
    notifications.info(`${newActivities.length} new activities detected`);
  }
}

// Create activity element
function createActivityElement(activity) {
  const div = document.createElement('div');
  div.className = 'activity-item';
  div.dataset.action = activity.action;
  div.dataset.user = activity.user_name;
  div.dataset.date = new Date(activity.date).toISOString().split('T')[0];

  // Icon mapping
  const icons = {
    login: '🔐',
    logout: 'L',
    upload: '📄',
    edit: '✏️',
    delete: '🗑️',
    create: '➕'
  };

  const actionLabel = t(activity.action, activity.action);

  div.innerHTML = `
    <div class="activity-icon">
      <span>${icons[activity.action] || '📋'}</span>
    </div>
    <div class="activity-content">
      <div class="activity-header-info">
        <span class="activity-user">${activity.user_name}</span>
        <span class="activity-action">${actionLabel}</span>
        <span class="activity-date">${new Date(activity.date).toLocaleString()}</span>
      </div>
      <div class="activity-details">${activity.details}</div>
      <div class="activity-meta">
        <span class="activity-ip">IP: ${activity.ip}</span>
      </div>
    </div>
  `;

  // Add click handler
  div.addEventListener('click', function() {
    showActivityDetails(this);
  });

  return div;
}

function t(key, fallback) {
  try {
    if (typeof translations !== 'undefined' && translations[currentLang] && translations[currentLang][key]) {
      return translations[currentLang][key];
    }
  } catch (e) {
    // ignore
  }
  return fallback;
}

