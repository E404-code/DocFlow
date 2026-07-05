<?php
define('ASSETS_PATH', '/doc/public/assets');
define('PUBLIC_PATH', $_SERVER['DOCUMENT_ROOT'] . '/doc/public');
session_start();
$page = "chat";

if (empty($_SESSION['uid'])) {
  header("Location: /doc/public/index.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="ar">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>DocuFlow - AI Chat</title>
  <link rel="icon" type="image/png" href="<?= ASSETS_PATH . '/img/favicon/favicon.png' ?>">
  <link rel="stylesheet" href="<?= ASSETS_PATH . '/css/global.css' ?>">
  <link rel="stylesheet" href="<?= ASSETS_PATH . '/css/dashboard.css' ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <link rel="stylesheet" href="<?= ASSETS_PATH . '/css/chat.css' ?>">
</head>

<body data-theme="light">
  <div class="layout">
    <?php require_once PUBLIC_PATH . '/components/aside.php' ?>

    <main>
      <div class="topbar">
        <div class="top-actions">
          <button class="btn" id="newChatBtn">محادثة جديدة</button>
          <button class="btn" onclick="toggleLang()">AR / EN</button>
          <button class="btn" onclick="toggleTheme()">☀ / 🌙</button>
        </div>
      </div>

      <section class="chat-wrap">
        <header class="chat-header">
          <div class="chat-title">
            <h2>مساعد DocuFlow</h2>
            <p>اسأل عن البحث، الجلب، الحذف، التعديل، أو الإنشاء</p>
          </div>
          <div class="chat-status" id="chatStatus">متصل</div>
        </header>

        <div class="chat-body" id="chatBody">
          <div class="message bot">
            <div class="bubble">مرحباً! أرسل التوكن الخاص بك للبدء.</div>
          </div>
        </div>

        <form class="chat-input" id="chatForm">
          <textarea id="chatMessage" rows="1" placeholder="اكتب رسالتك هنا..." autocomplete="off"></textarea>
          <div class="chat-action">
            <button type="submit" class="btn primary">
              <i class="fa-solid fa-arrow-up"></i>
            </button>

          </div>
        </form>
      </section>
    </main>
  </div>

  <script src="<?= ASSETS_PATH ?>/js/translations.js"></script>
  <script src="<?= ASSETS_PATH ?>/js/theme.js"></script>
  <script src="<?= ASSETS_PATH ?>/js/global.js"></script>
  <script src="<?= ASSETS_PATH ?>/js/chat.js"></script>
</body>

</html>