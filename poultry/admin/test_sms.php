<?php
require __DIR__ . '/../vendor/autoload.php';  // Composer autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

// Load .env from parent folder
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Now you can use $_ENV['GMAIL_USER'] and $_ENV['GMAIL_APP_PASSWORD']


$mail = new PHPMailer(true);

try {
    // Enable debug output
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = 'html';

    // SMTP settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['GMAIL_USER'];
    $mail->Password   = $_ENV['GMAIL_APP_PASSWORD'];
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom($_ENV['GMAIL_USER'], 'Your Store');

    // Replace with your test number and carrier gateway
    $phone = '09635354999'; // the number to test
    $carrier = 'globe'; // globe, tnt, smart, tm
    $gateways = [
        'globe' => '@sms.globe.com.ph',
        'tnt'   => '@sms.tnt.ph',
        'smart' => '@sms.smart.com.ph',
        'tm'    => '@sms.tm.com.ph'
    ];
    $to = $phone . $gateways[$carrier];

    $mail->addAddress($to);
    $mail->isHTML(false);
    $mail->Subject = '';
    $mail->Body    = "Test message from your system.";

    $mail->send();
    echo "Message sent! Check your phone.";
} catch (Exception $e) {
    echo "Mailer Error: {$mail->ErrorInfo}";
}
