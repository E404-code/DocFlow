<?php
// Mail config for SMTP (Brevo-ready)
// Fill these values from Brevo dashboard:
// Transactional > SMTP & API  (host, port, login, SMTP key)
// Senders, Domains & Dedicated IPs > Senders (verified from_email)
return [
    'host' => '',
    'port' => 587,
    'username' => '',
    'password' => '',
    'secure' => 'tls', // 'tls', 'ssl', or ''
    'from_email' => 'useroole714@gmail.com',
    'from_name' => 'Docuflow'
];
