<?php
session_start();
if (!isset($_SESSION['uid']) || empty($_SESSION['uid'])) {
  header('Location: /doc/public/pages/dashboard/dashboard.php');
  exit();
}

define('ASSETS_PATH', '/doc/public/assets');
define('PUBLIC_PATH', $_SERVER['DOCUMENT_ROOT'] . '/doc/public');
$page = "activity";
?>
<!doctype html>
<html lang="en" data-theme="light">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>DocuFlow - Activity</title>
  <link rel="icon" type="image/png" href="<?= ASSETS_PATH ?>/img/favicon/favicon.png" />
  <link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/global.css" />
  <link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/dashboard.css" />
  <link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/documents-upload.css" />
</head>
<body>
  <div class="layout">
    <?php require_once PUBLIC_PATH . '/components/aside.php' ?>
    
    <main>
      <div class="topbar">
        <div class="top-actions">
          <button class="btn" onclick="toggleLang()">AR / EN</button>
          <button class="btn" onclick="toggleTheme()">☀ / 🌙</button>
        </div>
      </div>

      <div class="form-box">
        <div class="activity-header">
          <h1 data-i18n="activityLog">Activity Log</h1>
          
          <!-- Filters -->
          <div class="activity-filters">
            <div class="filter-group">
              <label for="filter-action" data-i18n="filterByAction">Filter by Action</label>
              <select id="filter-action" class="form-select">
                <option value="" data-i18n="allActions">All Actions</option>
                <option value="login" data-i18n="login">Login</option>
                <option value="logout" data-i18n="logout">Logout</option>
                <option value="upload" data-i18n="upload">Upload</option>
                <option value="edit" data-i18n="edit">Edit</option>
                <option value="delete" data-i18n="delete">Delete</option>
                <option value="create" data-i18n="create">Create</option>
              </select>
            </div>
            
            <div class="filter-group">
              <label for="filter-user" data-i18n="filterByUser">Filter by User</label>
              <input type="text" id="filter-user" class="form-control" 
                     placeholder="Search user..." data-i18n-placeholder="searchUser">
            </div>
            
            <div class="filter-group">
              <label for="filter-date" data-i18n="filterByDate">Filter by Date</label>
              <input type="date" id="filter-date" class="form-control">
            </div>
            
            <div class="filter-actions">
              <button class="btn" onclick="applyFilters()" data-i18n="applyFilters">Apply</button>
              <button class="btn reset-btn" onclick="resetFilters()" data-i18n="reset">Reset</button>
            </div>
          </div>
          
          <!-- Search -->
          <div class="activity-search">
            <input type="text" id="search-activity" class="form-control" 
                   placeholder="Search activities..." data-i18n-placeholder="searchActivities">
            <button class="btn" onclick="searchActivities()" data-i18n="search">Search</button>
          </div>
        </div>

        <!-- Activities List -->
        <div class="activities-container">
          <div class="activities-stats">
            <div class="stat-card">
              <div class="stat-number" data-i18n="totalActivities">Total Activities</div>
              <div class="stat-value" id="totalActivitiesValue">0</div>
            </div>
            <div class="stat-card">
              <div class="stat-number" data-i18n="todayActivities">Today's Activities</div>
              <div class="stat-value" id="todayActivitiesValue">0</div>
            </div>
            <div class="stat-card">
              <div class="stat-number" data-i18n="activeUsers">Active Users</div>
              <div class="stat-value" id="activeUsersValue">0</div>
            </div>
          </div>

          <div class="activities-list">
            <div class="activity-item">
              <div class="activity-content">
                <div class="activity-details" data-i18n="noActivity">No recent activity</div>
              </div>
            </div>
          </div>
          
          <!-- Pagination -->
          <div class="pagination">
            <button class="btn" onclick="previousPage()" data-i18n="previous">Previous</button>
            <span class="page-info">Page 1 of 1</span>
            <button class="btn" onclick="nextPage()" data-i18n="next">Next</button>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="<?= ASSETS_PATH ?>/js/translations.js"></script>
  <script src="<?= ASSETS_PATH ?>/js/theme.js"></script>
  <script src="<?= ASSETS_PATH ?>/js/global.js"></script>
  <script src="<?= ASSETS_PATH ?>/js/activity.js"></script>
</body>
</html>

