<?php
session_start();
if (!isset($_SESSION['uid']) || empty($_SESSION['uid']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'group') {
  header('Location: /doc/public/pages/dashboard/dashboard.php');
  exit();
}

define('ASSETS_PATH', '/doc/public/assets');
define('PUBLIC_PATH', $_SERVER['DOCUMENT_ROOT'] . '/doc/public');
$page = "addUser"; // Fix for aside active state
?>
<!doctype html>
<html lang="en" data-theme="light">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>DocuFlow - Add User</title>
  <link rel="icon" type="image/png" href="<?= ASSETS_PATH ?>/img/favicon/favicon.png" />
  <link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/global.css" />
  <link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/dashboard.css" />
  <link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/documents-upload.css" />
  <link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/register.css" />
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
        <form id="registerForm" class="register-form" novalidate>
          <fieldset>
            <legend data-i18n="createAccount">Create Account</legend>

            <div class="field">
              <label for="name" class="form-label" data-i18n="fullName">Full Name</label>
              <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name"
                data-i18n-placeholder="fullNamePlaceholder" required />
            </div>

            <div class="field">
              <label for="email" class="form-label" data-i18n="emailAddress">Email address</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Example@gmail.com"
                data-i18n-placeholder="emailPlaceholder" required />
              <div id="emailHelp" class="form-text" data-i18n="emailHelp">
                We'll never share your email with anyone else.
              </div>
            </div>

            <div class="field">
              <label for="password" class="form-label" data-i18n="password">Password</label>
              <input type="password" class="form-control" id="password" name="password"
                placeholder="Password (min 6 characters)" data-i18n-placeholder="passwordPlaceholder" required />
            </div>

            <div class="field">
              <label for="confirmPassword" class="form-label" data-i18n="confirmPassword">Confirm Password</label>
              <input type="password" class="form-control" id="confirmPassword" name="confirmPassword"
                placeholder="Confirm your password" data-i18n-placeholder="confirmPasswordPlaceholder" required />
            </div>

            <div class="field">
              <label for="role-option" class="form-label" data-i18n="roleOption">Role Option</label>
              <select class="form-select" id="role-option" name="role-option" required>
                <option value="group" data-i18n="groupManager">Group Manager</option>
                <?= $_SESSION['role'] !== 'admin' ? '' : '<option value="merchant" data-i18n="merchant">Merchant</option>' ?>
                <option value="employee" data-i18n="employee" selected>Employee</option>
              </select>
            </div>
          </fieldset>

          <div class="form-actions">
            <button type="submit" class="btn submit-btn" data-i18n="createAccount">
              Create Account
            </button>
          </div>
        </form>

        <!-- Copyright -->
        <footer class="auth-footer">
          <span>&copy;</span>
          <span class="copy"></span>
        </footer>
      </div>
    </main>
  </div>

  <script src="<?= ASSETS_PATH ?>/js/translations.js"></script>
  <script src="<?= ASSETS_PATH ?>/js/theme.js"></script>
  <script src="<?= ASSETS_PATH ?>/js/global.js"></script>
  <script src="<?= ASSETS_PATH ?>/js/register.js"></script>
  <script src="<?= ASSETS_PATH ?>/js/notifications.js"></script>
</body>

</html>