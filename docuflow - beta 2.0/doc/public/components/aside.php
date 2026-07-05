<?php
define('PAGES_PATH', '/doc');
if (!$_SESSION) {
  session_start();
}

// Set default page if not defined
if (!isset($page)) {
  $page = '';
}
?>
<aside >
  <div class="justifiy">
    <div class="logo" data-i18n="app">DocuFlow</div>
    <button aria-label="Open Nide Bar">
      <span></span>
      <span></span>
      <span></span>
    </button>
  </div>
  <nav class="nav">
    <a class="<?= $page === "dashboard" ? 'active' : '' ?>" data-i18n="dashboard"
      href="<?= PAGES_PATH ?>/public/pages/dashboard/dashboard.php">Dashboard</a>

    <a class="<?= $page === "documents" ? 'active' : '' ?>" data-i18n="documents"
      href="<?= PAGES_PATH ?>/public/pages/documents/list.php">Documents</a>
    <a class="<?= $page === "crud" ? 'active' : '' ?>" data-i18n="upload"
      href="<?= PAGES_PATH ?>/public/pages/documents/upload.php">Upload</a>
    <?php
    if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'group') {
      echo '<a class="' . ($page === "addUser" ? 'active' : '') . '" data-i18n="addUser" href="' . PAGES_PATH . '/public/pages/users/addUser.php">Add User</a>';
      echo '<a class="' . ($page === "manageUsers" ? 'active' : '') . '" data-i18n="manageUsers" href="' . PAGES_PATH . '/public/pages/users/manageUsers.php">Manage Users</a>';
    } ?>
    <a class="<?= $page === "activity" ? 'active' : '' ?>" data-i18n="activity" href="<?= PAGES_PATH . '/public/pages/users/activity.php' ?>">Activity</a>
    <a class="<?= $page === "chat" ? 'active' : '' ?>" data-i18n="chat" href="<?= PAGES_PATH . '/public/pages/chat/chat.php' ?>">AI Chat</a>

    <a class="<?= $page === "profile" ? 'active' : '' ?>" data-i18n="profile"href="<?= PAGES_PATH . '/public/pages/users/profile/profile.php' ?>">My Profile</a>

    <a data-i18n="logout" class="logout">Logout</a>
  </nav>
</aside>
