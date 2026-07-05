<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/doc/public/');
session_start();

// Check if user is logged in session contains user
if (isset($_SESSION['uid']) && !empty($_SESSION['uid'])) {
    header('Location: ./pages/dashboard/dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>DocuFlow – Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="./assets/img/favicon/favicon.png">

    <!-- Global Styles -->
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/auth.css">
    <link rel="stylesheet" href="assets/css/success-modal.css">

    <!-- Bootstrap (layout only) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <!-- Header -->
    <header class="NavBar">
        <a href="#" class="brand">D</a>
        <a href="#" class="sub-brand">ocFlow</a>
    </header>

    <hr class="horizontalLine">

    <!-- Page Content -->
    <main class="container-xxl">
        <section class="container" id="container">

            <!-- Login Card -->
            <form id="loginForm" class="loginForm">

                <legend data-i18n="LoginBoxHead">Login</legend>

                <!-- Email -->
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label" data-i18n="LoginEmailLabel">
                        Email address
                    </label>

                    <input type="email" class="form-control" id="exampleInputEmail1" placeholder="example@gmail.com"
                        data-i18n-placeholder="EmailPlaceholder">

                    <div class="p-2 form-text" id="emailHelp" data-i18n="LoginEmailHint">
                        We'll never share your email with anyone else.
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label" data-i18n="LoginPasswordLabel">
                        Password
                    </label>

                    <input type="password" class="form-control" id="exampleInputPassword1"
                        placeholder="Password (min 6 characters)" data-i18n-placeholder="PasswordPlaceholder">

                    <div id="passwordHelp" class="form-text"></div>
                </div>

                <!-- Links Row -->
                <div class="links">

                    <div class="check-div">
                        <label class="form-check-label" for="exampleCheck">
                            <a href="javascript:void(0)" id="policy" class="check-label" data-i18n="IReadAndAccept">
                                I read and accept
                            </a>
                        </label>

                        <input type="checkbox" class="form-check-input" id="exampleCheck" checked>
                    </div>

                    <a href="./status-error/403.html" class="reset" data-i18n="ResetPassword">
                        reset password
                    </a>

                </div>

                <!-- Submit -->
                <button type="submit" class="btn btn-primary" data-i18n="SubmitButton">
                    Submit
                </button>

            </form>

            <!-- Copyright -->
            <footer class="auth-footer">
                <span>&copy;</span>
                <span class="copy"></span>
            </footer>

        </section>
    </main>

    <!-- Overlay -->
    <div class="overlay model-show"></div>

    <!-- Privacy Modal -->
    <div class="model-box model-show">
        <div class="model-head">
            <h3 data-i18n="PolicyTitle">Policy</h3>
            <span onclick="hideModel()">&times;</span>
        </div>

        <div>
            <p class="model-content" data-i18n="PolicyText"><!-- Privacy policy content --></p>
        </div>

        <div class="model-foot">
            <button type="button" onclick="hideModel()" data-i18n="CloseButton">
                Close
            </button>
            <button type="button" onclick="hideModelAndAcceprt()" data-i18n="AcceptButton">
                Accept
            </button>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="success-modal">
        <div class="modalbox">
            <svg veiwbox="0 0 100 100" width="100%" height="100%">
                <circle cx="50" cy="50" r="40" class="cricle" />
                <circle cx="50" cy="50" r="40" class="progress" />
                <path class="check" d="M27 50 L45 65 L66 35" fill="none" />
            </svg>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/js/global.js"></script>
    <script src="assets/js/auth.js"></script>
    <script src="assets/js/translations.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>