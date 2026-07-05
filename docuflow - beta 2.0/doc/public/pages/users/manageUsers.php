<?php
define('ASSETS_PATH', '/doc/public/assets');
define('PUBLIC_PATH', $_SERVER['DOCUMENT_ROOT'] . '/doc/public');
session_start();
$page = "manageUsers";

if (empty($_SESSION['uid'])) {
  header("Location: /doc/public/index.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>DocuFlow - Manage Users</title>
  <link rel="icon" type="image/png" href="<?= ASSETS_PATH . '/img/favicon/favicon.png' ?>">
  <link rel="stylesheet" href="<?= ASSETS_PATH . '/css/global.css' ?>">
  <link rel="stylesheet" href="<?= ASSETS_PATH . '/css/dashboard.css' ?>">
  <link rel="stylesheet" href="<?= ASSETS_PATH . '/css/users-management.css' ?>">
  <link rel="stylesheet" href="<?= ASSETS_PATH . '/css/notifications.css' ?>">
</head>

<body data-theme="light">
  <div class="layout">
    <?php require_once PUBLIC_PATH . '/components/aside.php' ?>

    <main>
      <div class="topbar">
        <div class="top-actions">
          <button class="btn" onclick="toggleLang()">AR / EN</button>
          <button class="btn" onclick="toggleTheme()">☀ / 🌙</button>
        </div>
      </div>

      <div class="card users-card">
        <div class="users-header">
          <div>
            <h3 data-i18n="manageUsers">Manage Users</h3>
            <p class="muted" data-i18n="manageUsersHint">Manage users created under your account</p>
          </div>
          <div class="users-actions">
            <input id="userSearch" class="form-control" type="text" data-i18n-placeholder="searchUser"
              placeholder="Search user...">
            <select id="statusFilter" class="form-select">
              <option value="all" data-i18n="allStatuses">All statuses</option>
              <option value="1" data-i18n="statusActive">Active</option>
              <option value="0" data-i18n="statusInactive">Inactive</option>
              <option value="3" data-i18n="statusDisabled">Disabled</option>
            </select>
          </div>
        </div>

        <div class="table-wrap">
          <table class="users-table">
            <thead>
              <tr>
                <th data-i18n="userName">Name</th>
                <th data-i18n="userEmail">Email</th>
                <th data-i18n="userRole">Role</th>
                <th data-i18n="userStatus">Status</th>
                <th data-i18n="actions">Actions</th>
              </tr>
            </thead>
            <tbody id="usersBody">
              <tr>
                <td colspan="5" class="muted" data-i18n="noUsers">No users found</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <script src="<?= ASSETS_PATH ?>/js/translations.js"></script>
  <script src="<?= ASSETS_PATH ?>/js/theme.js"></script>
  <script src="<?= ASSETS_PATH ?>/js/notifications.js"></script>
  <script src="<?= ASSETS_PATH ?>/js/users-management.js"></script>
</body>

</html>
