let managedUsers = [];

document.addEventListener('DOMContentLoaded', function () {
  loadUsers();
  bindFilters();
});

async function loadUsers() {
  try {
    const response = await fetch('/doc/api/users_manage.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({})
    });
    const result = await response.json();
    if (result.status === 'success') {
      managedUsers = result.users || [];
      renderUsers(managedUsers);
    } else {
      renderUsers([]);
    }
  } catch (error) {
    console.error('Failed to load users:', error);
    renderUsers([]);
  }
}

function bindFilters() {
  const search = document.getElementById('userSearch');
  const status = document.getElementById('statusFilter');

  if (search) {
    search.addEventListener('input', applyFilters);
  }
  if (status) {
    status.addEventListener('change', applyFilters);
  }
}

function applyFilters() {
  const searchValue = (document.getElementById('userSearch')?.value || '').toLowerCase();
  const statusValue = document.getElementById('statusFilter')?.value || 'all';

  const filtered = managedUsers.filter((u) => {
    const name = (u.name || '').toLowerCase();
    const email = (u.email || '').toLowerCase();
    const matchesSearch = !searchValue || name.includes(searchValue) || email.includes(searchValue);
    const matchesStatus = statusValue === 'all' || String(u.active) === statusValue;
    return matchesSearch && matchesStatus;
  });

  renderUsers(filtered);
}

function renderUsers(users) {
  const body = document.getElementById('usersBody');
  if (!body) return;

  body.innerHTML = '';

  if (!users.length) {
    const row = document.createElement('tr');
    row.innerHTML = `<td colspan="5" class="muted">${t('noUsers', 'No users found')}</td>`;
    body.appendChild(row);
    return;
  }

  users.forEach((u) => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${escapeHtml(u.name)}</td>
      <td>${escapeHtml(u.email)}</td>
      <td>${escapeHtml(u.role)}</td>
      <td>${statusBadge(u.active)}</td>
      <td class="actions-cell">
        ${actionButtons(u)}
      </td>
    `;
    body.appendChild(row);
  });
}

function statusBadge(active) {
  const value = String(active);
  if (value === '1') {
    return `<span class="status-pill status-active">${t('statusActive', 'Active')}</span>`;
  }
  if (value === '3') {
    return `<span class="status-pill status-disabled">${t('statusDisabled', 'Disabled')}</span>`;
  }
  return `<span class="status-pill status-inactive">${t('statusInactive', 'Inactive')}</span>`;
}

function actionButtons(user) {
  const id = user.id;
  const active = String(user.active);
  const enableLabel = t('enable', 'Enable');
  const disableLabel = t('disable', 'Disable');
  const deleteLabel = t('deleteUser', 'Delete');

  const toggleBtn = active === '3'
    ? `<button class="action-btn secondary" onclick="userAction(${id}, 'enable')">${enableLabel}</button>`
    : `<button class="action-btn" onclick="userAction(${id}, 'disable')">${disableLabel}</button>`;

  const deleteBtn = `<button class="action-btn danger" onclick="confirmDelete(${id})">${deleteLabel}</button>`;

  return `${toggleBtn} ${deleteBtn}`;
}

async function userAction(id, action) {
  try {
    const response = await fetch('/doc/api/user_action.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id, action })
    });
    const result = await response.json();
    if (result.status === 'success') {
      if (window.notifications) {
        notifications.success(result.message || t('actionSuccess', 'Action completed successfully'));
      }
      // Optimistic local update to keep UI responsive
      updateLocalUserState(id, action);
      applyFilters();
    } else {
      if (window.notifications) {
        notifications.error(result.message || t('actionFailed', 'Action failed. Please try again.'));
      }
    }
  } catch (error) {
    console.error('User action failed:', error);
    if (window.notifications) {
      notifications.error(t('actionFailed', 'Action failed. Please try again.'));
    }
  }
}

function updateLocalUserState(id, action) {
  const idx = managedUsers.findIndex(u => Number(u.id) === Number(id));
  if (idx === -1) return;

  if (action === 'delete') {
    managedUsers.splice(idx, 1);
    return;
  }

  if (action === 'disable') {
    managedUsers[idx].active = '3';
  } else if (action === 'enable') {
    managedUsers[idx].active = '0';
  }
}

function confirmDelete(id) {
  const message = t('confirmDelete', 'Are you sure you want to delete this user?');
  if (confirm(message)) {
    userAction(id, 'delete');
  }
}

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
