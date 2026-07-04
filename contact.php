<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;

function redirect_with_status(string $status): void
{
    header('Location: contact.html?status=' . rawurlencode($status));
    exit;
}

function log_contact_error(string $message): void
{
    error_log('[Straya contact form] ' . $message);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_with_status('direct');
}

if (!empty($_POST['website'] ?? '')) {
    redirect_with_status('success');
}

$required = ['name', 'email', 'phone', 'service', 'location', 'message'];
$data = [];

foreach ($required as $field) {
    $value = trim((string)($_POST[$field] ?? ''));
    if ($value === '') {
        redirect_with_status('error');
    }
    $data[$field] = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$visitorEmail = filter_var(trim((string)($_POST['email'] ?? '')), FILTER_VALIDATE_EMAIL);
if ($visitorEmail === false) {
    redirect_with_status('error');
}

$configPath = __DIR__ . '/config.php';
if (!is_file($configPath)) {
    log_contact_error('Missing config.php');
    redirect_with_status('config');
}

try {
    $config = require $configPath;
} catch (Throwable $exception) {
    log_contact_error('Unable to load config.php: ' . $exception->getMessage());
    redirect_with_status('config');
}
if (!is_array($config)) {
    log_contact_error('config.php must return an array');
    redirect_with_status('config');
}

$requiredConfig = [
    'smtp_host',
    'smtp_port',
    'smtp_username',
    'smtp_password',
    'from_email',
    'from_name',
    'to_email',
    'to_name',
];

foreach ($requiredConfig as $key) {
    if (!array_key_exists($key, $config) || trim((string)$config[$key]) === '') {
        log_contact_error('Missing config key: ' . $key);
        redirect_with_status('config');
    }
}

$autoload = __DIR__ . '/vendor/autoload.php';
if (is_file($autoload)) {
    require_once $autoload;
} else {
    $phpmailerFiles = [
        __DIR__ . '/PHPMailer/src/Exception.php',
        __DIR__ . '/PHPMailer/src/PHPMailer.php',
        __DIR__ . '/PHPMailer/src/SMTP.php',
    ];

    foreach ($phpmailerFiles as $file) {
        if (!is_file($file)) {
            log_contact_error('Missing PHPMailer dependency. Expected Composer vendor/autoload.php or PHPMailer/src files.');
            redirect_with_status('dependency');
        }
    }

    foreach ($phpmailerFiles as $file) {
        require_once $file;
    }
}

if (!class_exists(PHPMailer::class)) {
    log_contact_error('PHPMailer class not available after dependency load');
    redirect_with_status('dependency');
}

$body = '<h2>New Straya Mobile Welding enquiry</h2>'
    . '<p><strong>Name:</strong> ' . $data['name'] . '</p>'
    . '<p><strong>Email:</strong> ' . $data['email'] . '</p>'
    . '<p><strong>Phone:</strong> ' . $data['phone'] . '</p>'
    . '<p><strong>Service:</strong> ' . $data['service'] . '</p>'
    . '<p><strong>Location/Suburb:</strong> ' . $data['location'] . '</p>'
    . '<p><strong>Message:</strong><br>' . nl2br($data['message']) . '</p>';

try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = (string)$config['smtp_host'];
    $mail->SMTPAuth = true;
    $mail->Username = (string)$config['smtp_username'];
    $mail->Password = (string)$config['smtp_password'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = (int)$config['smtp_port'];

    $mail->setFrom((string)$config['from_email'], (string)$config['from_name']);
    $mail->addAddress((string)$config['to_email'], (string)$config['to_name']);
    $mail->addReplyTo($visitorEmail, html_entity_decode($data['name'], ENT_QUOTES, 'UTF-8'));

    $mail->isHTML(true);
    $mail->Subject = 'Website enquiry: ' . html_entity_decode($data['service'], ENT_QUOTES, 'UTF-8');
    $mail->Body = $body;
    $mail->AltBody = strip_tags(str_replace(['<br>', '<br />'], "\n", $body));

    $mail->send();
    redirect_with_status('success');
} catch (Throwable $exception) {
    log_contact_error('Mail send failed: ' . $exception->getMessage());
    redirect_with_status('error');
}
