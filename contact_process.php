<?php

declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

function clean_field(string $key, int $maxLength = 500): string
{
    $value = filter_input(INPUT_POST, $key, FILTER_UNSAFE_RAW);
    if (!is_string($value)) {
        return '';
    }

    $value = trim($value);
    $value = str_replace(["\r", "\n"], ' ', $value);

    return substr($value, 0, $maxLength);
}

$name = clean_field('name', 120);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$subject = clean_field('subject', 150);
$message = clean_field('message', 2000);

if (!$email || $name === '' || $message === '') {
    http_response_code(400);
    exit('Please provide a valid name, email, and message.');
}

$to = 'ronnywoods77@gmail.com';
$mailSubject = $subject !== '' ? $subject : 'New Mos Studio website message';
$body = implode("\n", [
    'New message from Mos Studio website',
    '',
    'Name: ' . $name,
    'Email: ' . $email,
    '',
    'Message:',
    $message,
]);

$headers = [
    'From: Mos Studio Website <no-reply@mos.wstudio3d.com>',
    'Reply-To: ' . $email,
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'X-Content-Type-Options: nosniff',
];

$sent = mail($to, $mailSubject, $body, implode("\r\n", $headers));

http_response_code($sent ? 200 : 500);
echo $sent ? 'Message sent.' : 'Unable to send message.';
