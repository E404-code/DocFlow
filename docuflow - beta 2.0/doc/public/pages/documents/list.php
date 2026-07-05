<?php
define('ASSETS_PATH', '/doc/public/assets');
define('PUBLIC_PATH', $_SERVER['DOCUMENT_ROOT'] . '/doc/public');
session_start();
$page="documents";
if (!isset($_SESSION['uid']) && !$_SESSION['uid'] == true) {
  header('Location: /doc/public/index.php');
  exit();
}

// ط¹ط±ط¶ ط±ط³ط§ط¦ظ„ ط§ظ„ط®ط·ط£
$error_message = '';
if (isset($_GET['error'])) {
  switch ($_GET['error']) {
    case 'document_not_found':
      $error_message = 'ط§ظ„ظ…ط³طھظ†ط¯ ط§ظ„ظ…ط·ظ„ظˆط¨ ط؛ظٹط± ظ…ظˆط¬ظˆط¯ ظپظٹ ظ‚ط§ط¹ط¯ط© ط§ظ„ط¨ظٹط§ظ†ط§طھ';
      break;
    case 'access_denied':
      $error_message = 'ظ„ط§ طھظ…ظ„ظƒ طµظ„ط§ط­ظٹط© ط§ظ„ظˆطµظˆظ„ ط¥ظ„ظ‰ ظ‡ط°ط§ ط§ظ„ظ…ط³طھظ†ط¯';
      break;
    default:
      $error_message = 'ط­ط¯ط« ط®ط·ط£ ط؛ظٹط± ظ…طھظˆظ‚ط¹';
      break;
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>DocuFlow - Document List</title>
  <link rel="icon" type="image/png" href="<?= ASSETS_PATH . '/img/favicon/favicon.png' ?>">
  <link rel="stylesheet" href="<?= ASSETS_PATH . '/css/global.css' ?>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
  <link rel="stylesheet" href="<?= ASSETS_PATH . '/css/dashboard.css' ?>">
  <link rel="stylesheet" href="<?= ASSETS_PATH . '/css/documents-list.css' ?>">
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
      <?php if (!empty($error_message)): ?>
        <div class="error-message"
          style="background: #f8d7da; color: #721c24; padding: 15px; margin: 20px 0; border-radius: 5px; border: 1px solid #f5c6cb; text-align: center; font-weight: bold;">
          <?= $error_message ?>
        </div>
      <?php endif; ?>

      <?php require_once './table.php' ?>

      <!-- Scripts -->
      <script src="<?= ASSETS_PATH ?>/js/translations.js"></script>
      <script src="<?= ASSETS_PATH ?>/js/theme.js"></script>
      <script src="<?= ASSETS_PATH ?>/js/documents-list.js"></script>
</body>

</html>

