<?php
define('ASSETS_PATH', '/doc/public/assets');
define('PUBLIC_PATH', $_SERVER['DOCUMENT_ROOT'] . '/doc/public');
session_start();
$page = "profile";

if (empty($_SESSION['uid'])) {
  header("Location: /doc/public/index.php");
  exit();
}

// Get user data from session
$userEmail = $_SESSION['email'] ?? 'user@example.com';
$isEditMode = isset($_GET['edit']) && $_GET['edit'] === 'true';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>DocuFlow - User Profile</title>
  <link rel="icon" type="image/png" href="<?= ASSETS_PATH . '/img/favicon/favicon.png' ?>">
  <link rel="stylesheet" href="<?= ASSETS_PATH . '/css/global.css' ?>">
  <link rel="stylesheet" href="<?= ASSETS_PATH . '/css/dashboard.css' ?>">
  <link rel="stylesheet" href="<?= ASSETS_PATH . '/css/profile.css' ?>">
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

      <div class="profile-container">
        <div class="profile-header">
          <div class="profile-avatar">

          </div>
          <div class="profile-info">
            <h2></h2>
            <span class="profile-role"></span>
            <div class="profile-actions">
              <?php if (!$isEditMode): ?>
                <a href="?edit=true" class="btn btn-primary" data-i18n="editProfile">Edit Profile</a>
              <?php else: ?>
                <a href="?" class="btn btn-secondary" data-i18n="viewMode">View Mode</a>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="profile-form">
          <div class="success-message" id="successMessage">
            Profile updated successfully!
          </div>

          <form id="profileForm">
            <div class="form-grid">
              <div class="form-group">
                <label for="name" data-i18n="fullName">Full Name</label>
                <input type="text" id="name" name="name" <?= !$isEditMode ? 'disabled' : '' ?>>
              </div>

              <div class="form-group">
                <label for="email" data-i18n="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($userEmail) ?>" <?= !$isEditMode ? 'disabled' : '' ?>>
              </div>

              <div class="form-group">
                <label for="role" data-i18n="role">Role</label>
                <input type="text" id="role" name="role" disabled>
              </div>

              <div class="form-group">
                <label for="joined" data-i18n="joinedDate">Joined Date</label>
                <input type="text" id="joined" name="joined" disabled>
              </div>

              <div class="form-group">
                <label for="apiToken">API Token</label>
                <div style="display:flex; gap:8px;">
                  <input type="text" id="apiToken" name="apiToken" value="Hidden for security" disabled>
                  <button type="button" class="btn btn-secondary" id="regenerateTokenBtn">Regenerate</button>
                </div>
                <small>This will generate a new token and send it to your email.</small>
              </div>
            </div>

            <div class="password-section" <?= !$isEditMode ? 'style="display: none;"' : '' ?>>
              <div class="password-header">
                <h3 data-i18n="changePassword">Change Password</h3>
                <button type="button" class="btn btn-secondary" onclick="togglePasswordFields()"
                  data-i18n="editPassword">Edit Password</button>
              </div>

              <div id="passwordFields" style="display: none;">
                <div class="form-grid">
                  <div class="form-group">
                    <label for="currentPassword" data-i18n="currentPassword">Current Password</label>
                    <input type="password" id="currentPassword" name="currentPassword"
                      placeholder="Enter current password">
                  </div>

                  <div class="form-group">
                    <label for="newPassword" data-i18n="newPassword">New Password</label>
                    <input type="password" id="newPassword" name="newPassword" placeholder="Enter new password">
                  </div>

                  <div class="form-group">
                    <label for="confirmPassword" data-i18n="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword"
                      placeholder="Confirm new password">
                  </div>
                </div>
              </div>
            </div>

            <div class="btn-group" <?= !$isEditMode ? 'style="display: none;"' : '' ?>>
              <button type="submit" class="btn btn-primary" data-i18n="saveChanges">Save Changes</button>
              <button type="button" class="btn btn-secondary" onclick="resetForm()" data-i18n="cancel">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>

  <script src="<?= ASSETS_PATH ?>/js/translations.js"></script>
  <script src="<?= ASSETS_PATH ?>/js/theme.js"></script>
  <script src="<?= ASSETS_PATH ?>/js/profile.js"></script>
  <script src="<?= ASSETS_PATH ?>/js/global.js"></script>
</body>

</html>