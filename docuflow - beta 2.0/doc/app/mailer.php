<?php

function smtp_read_response($fp)
{
    $data = '';
    while ($str = fgets($fp, 515)) {
        $data .= $str;
        if (isset($str[3]) && $str[3] === ' ') {
            break;
        }
    }
    return $data;
}

function smtp_expect_ok($response)
{
    if (!$response || strlen($response) < 3) {
        return false;
    }
    $code = intval(substr($response, 0, 3));
    return $code >= 200 && $code < 400;
}

function smtp_write_command($fp, $command)
{
    fwrite($fp, $command . "\r\n");
    return smtp_read_response($fp);
}

function send_mail_smtp($to, $subject, $body)
{
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/doc/config/mail.php';
    if (!file_exists($configPath)) {
        error_log('Mail config not found');
        return false;
    }
    $config = require $configPath;

    $host = $config['host'] ?? '';
    $port = $config['port'] ?? 587;
    $username = $config['username'] ?? '';
    $password = $config['password'] ?? '';
    $secure = $config['secure'] ?? 'tls';
    $fromEmail = $config['from_email'] ?? '';
    $fromName = $config['from_name'] ?? '';

    if (!$host || !$username || !$password || !$fromEmail) {
        error_log('Mail config is missing required fields');
        return false;
    }

    $target = $secure === 'ssl' ? "ssl://{$host}:{$port}" : "{$host}:{$port}";
    $fp = stream_socket_client($target, $errno, $errstr, 30);
    if (!$fp) {
        error_log("SMTP connect failed: {$errstr} ({$errno})");
        return false;
    }

    $resp = smtp_read_response($fp);
    if (!smtp_expect_ok($resp)) {
        fclose($fp);
        return false;
    }

    $resp = smtp_write_command($fp, 'EHLO localhost');
    if (!smtp_expect_ok($resp)) {
        fclose($fp);
        return false;
    }

    if ($secure === 'tls') {
        $resp = smtp_write_command($fp, 'STARTTLS');
        if (!smtp_expect_ok($resp)) {
            fclose($fp);
            return false;
        }
        stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        $resp = smtp_write_command($fp, 'EHLO localhost');
        if (!smtp_expect_ok($resp)) {
            fclose($fp);
            return false;
        }
    }

    $resp = smtp_write_command($fp, 'AUTH LOGIN');
    if (!smtp_expect_ok($resp)) {
        fclose($fp);
        return false;
    }

    $resp = smtp_write_command($fp, base64_encode($username));
    if (!smtp_expect_ok($resp)) {
        fclose($fp);
        return false;
    }

    $resp = smtp_write_command($fp, base64_encode($password));
    if (!smtp_expect_ok($resp)) {
        fclose($fp);
        return false;
    }

    $resp = smtp_write_command($fp, 'MAIL FROM:<' . $fromEmail . '>');
    if (!smtp_expect_ok($resp)) {
        fclose($fp);
        return false;
    }

    $resp = smtp_write_command($fp, 'RCPT TO:<' . $to . '>');
    if (!smtp_expect_ok($resp)) {
        fclose($fp);
        return false;
    }

    $resp = smtp_write_command($fp, 'DATA');
    if (!smtp_expect_ok($resp)) {
        fclose($fp);
        return false;
    }

    $headers = [];
    $headers[] = 'From: ' . ($fromName ? "{$fromName} <{$fromEmail}>" : $fromEmail);
    $headers[] = 'To: <' . $to . '>';
    $headers[] = 'Subject: ' . $subject;
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: text/plain; charset=UTF-8';

    $message = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.";
    $resp = smtp_write_command($fp, $message);
    if (!smtp_expect_ok($resp)) {
        fclose($fp);
        return false;
    }

    smtp_write_command($fp, 'QUIT');
    fclose($fp);
    return true;
}
