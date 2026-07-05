<?php
define('ASSETS_PATH', '/doc/public/assets');
define('PUBLIC_PATH', $_SERVER['DOCUMENT_ROOT'] . '/doc/public');
session_start();
$page = "dashboard";
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
  <title>DocuFlow - Enterprise Dashboard</title>
  <link rel="icon" type="image/png" href="<?= ASSETS_PATH . '/img/favicon/favicon.png' ?>">
  <link rel="stylesheet" href="<?= ASSETS_PATH . '/css/global.css' ?>">
  <link rel="stylesheet" href="<?= ASSETS_PATH . '/css/dashboard.css' ?>">
</head>

<body data-theme="light">
  <div class="layout">
    <?php require_once PUBLIC_PATH . '/components/aside.php' ?>

    <main >
      <div class="topbar">
        <!-- <div class="search"><input data-i18n-placeholder="search" placeholder="Search documents..."></div> -->
        <div class="top-actions">
          <a href="/doc/public/pages/documents/upload.php"><button class="btn primary" data-i18n="newUpload">New
              Upload</button></a>
          <button class="btn" onclick="toggleLang()">AR / EN</button>
          <button class="btn" onclick="toggleTheme()">☀ / 🌙</button>
        </div>
      </div>

      <div class="cards">
        <div class="card">
          <h4 data-i18n="totalDocs">Total Documents</h4>
          <p data-stat="total_docs">--</p>
        </div>
        <div class="card">
          <h4 data-i18n="department">Department</h4>
          <p data-stat="department_docs">--</p>
        </div>
        <div class="card">
          <h4 data-i18n="myDoc">My Documents</h4>
          <p data-stat="my_docs">--</p>
        </div>
        <div class="card">
          <h4 data-i18n="users">Active Users</h4>
          <p data-stat="active_users">--</p>
        </div>
      </div>

      <div class="grid">
        <div class="card">
          <h4 data-i18n="recentDocs">Recent Documents</h4>
          <table>
            <thead>
              <tr>
                <th data-i18n="title">Title</th>
                <th data-i18n="visibility">Visibility</th>
                <th data-i18n="date">Date</th>
              </tr>
            </thead>
            <tbody id="recentDocsBody">
              <tr>
                <td colspan="3" class="muted" data-i18n="noRecentDocs">No recent documents</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="card">
          <h4 data-i18n="activityLog">Activity Log</h4>
          <ul class="log">
            <li data-i18n="logUpload">📄 Uploaded document</li>
            <li data-i18n="logDelete">🗑 Deleted document</li>
            <li data-i18n="logLogin">🔐 Logged in</li>
          </ul>
        </div>
      </div>

      <div class="grid grid-2">
        <div class="card">
          <h4 data-i18n="statusOverview">Status Overview</h4>
          <div class="status-list">
            <div class="status-row">
              <div class="status-label" data-i18n="statusNew">New</div>
              <div class="status-bar"><div class="status-fill" data-status="new"></div></div>
              <div class="status-count" data-status-count="new">0</div>
            </div>
            <div class="status-row">
              <div class="status-label" data-i18n="statusWaiting">Waiting Reservation</div>
              <div class="status-bar"><div class="status-fill" data-status="w-resv"></div></div>
              <div class="status-count" data-status-count="w-resv">0</div>
            </div>
            <div class="status-row">
              <div class="status-label" data-i18n="statusOn">On Reservation</div>
              <div class="status-bar"><div class="status-fill" data-status="on-resv"></div></div>
              <div class="status-count" data-status-count="on-resv">0</div>
            </div>
            <div class="status-row">
              <div class="status-label" data-i18n="statusEnough">Enough</div>
              <div class="status-bar"><div class="status-fill" data-status="enough"></div></div>
              <div class="status-count" data-status-count="enough">0</div>
            </div>
            <div class="status-row">
              <div class="status-label" data-i18n="statusPendingDelivery">Pending Delivery</div>
              <div class="status-bar"><div class="status-fill" data-status="pending-delivery"></div></div>
              <div class="status-count" data-status-count="pending-delivery">0</div>
            </div>
            <div class="status-row">
              <div class="status-label" data-i18n="statusDelivered">Delivered</div>
              <div class="status-bar"><div class="status-fill" data-status="delivered"></div></div>
              <div class="status-count" data-status-count="delivered">0</div>
            </div>
          </div>
        </div>
        <div class="card">
          <h4 data-i18n="lastUpdated">Last updated</h4>
          <div class="last-updated">
            <span id="lastUpdated" class="muted">--</span>
            <button class="btn" id="refreshDashboard" type="button" data-i18n="refresh">Refresh</button>
          </div>
          <div class="mini-help" data-i18n="dashboardHint">
            Stats are refreshed from the database and reflect your group access.
          </div>
        </div>
      </div>
    </main>
  </div>
  <script src="<?= ASSETS_PATH ?>/js/translations.js"></script>
  <script src="<?= ASSETS_PATH ?>/js/dashboard.js"></script>
  <script src="<?= ASSETS_PATH ?>/js/theme.js"></script>
</body>

</html>
