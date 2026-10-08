<?php

function smtpWrite($socket, string $data): void
{
    $length = strlen($data);
    $written = 0;

    while($written < $length) {
        $result = fwrite($socket, substr($data, $written));
        if($result === false || $result === 0) {
            throw new RuntimeException('Could not write to the Gmail SMTP connection.');
        }
        $written += $result;
    }
}

function smtpRead($socket, array $expected_codes): string
{
    $response = '';

    do {
        $line = fgets($socket, 515);
        if($line === false) {
            throw new RuntimeException('Gmail SMTP closed the connection unexpectedly.');
        }

        $response .= $line;
        if(!preg_match('/\A(\d{3})([ -])/', $line, $matches)) {
            throw new RuntimeException('Gmail SMTP returned an invalid response.');
        }
        $code = (int)$matches[1];
        $more_lines = $matches[2] === '-';
    } while($more_lines);

    if(!in_array($code, $expected_codes, true)) {
        throw new RuntimeException('Gmail SMTP rejected a command: ' . trim($response));
    }

    return $response;
}

function smtpCommand($socket, string $command, array $expected_codes): string
{
    smtpWrite($socket, $command . "\r\n");
    return smtpRead($socket, $expected_codes);
}

function smtpEncodeHeader(string $value): string
{
    $value = trim(preg_replace('/[\r\n]+/', ' ', $value));
    return '=?UTF-8?B?' . base64_encode($value) . '?=';
}

function sendStoreEmail(string $recipient, string $subject, string $htmlBody, string $textBody): void
{
    $host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
    $port = (int)(getenv('SMTP_PORT') ?: 587);
    $username = getenv('SMTP_USERNAME') ?: ORDER_NOTIFICATION_EMAIL;
    $password = preg_replace('/\s+/', '', getenv('SMTP_APP_PASSWORD') ?: '');
    $from_email = getenv('SMTP_FROM_EMAIL') ?: $username;
    $from_name = getenv('SMTP_FROM_NAME') ?: SITE_NAME;

    if($host !== 'smtp.gmail.com' || $port !== 587) {
        throw new RuntimeException('Direct Gmail SMTP requires SMTP_HOST=smtp.gmail.com and SMTP_PORT=587.');
    }
    if($username === '' || $password === '' || !filter_var($username, FILTER_VALIDATE_EMAIL)
        || !filter_var($from_email, FILTER_VALIDATE_EMAIL)) {
        throw new RuntimeException('SMTP settings are missing or invalid. Configure SMTP_USERNAME and SMTP_APP_PASSWORD.');
    }
    if(!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException('The email recipient is invalid.');
    }
    if(!extension_loaded('openssl') || !function_exists('stream_socket_enable_crypto')) {
        throw new RuntimeException('PHP OpenSSL is required for secure Gmail SMTP. Enable the openssl extension.');
    }

    $timeout = 20;
    $errno = 0;
    $error = '';
    $ssl_context = stream_context_create([
        'ssl' => [
            'peer_name' => $host,
            'verify_peer' => true,
            'verify_peer_name' => true,
            'SNI_enabled' => true,
        ],
    ]);
    $socket = stream_socket_client(
        'tcp://' . $host . ':' . $port,
        $errno,
        $error,
        $timeout,
        STREAM_CLIENT_CONNECT,
        $ssl_context
    );

    if($socket === false) {
        throw new RuntimeException('Could not connect to Gmail SMTP: ' . $error . ' (' . $errno . ').');
    }

    stream_set_timeout($socket, $timeout);

    try {
        smtpRead($socket, [220]);
        smtpCommand($socket, 'EHLO localhost', [250]);
        smtpCommand($socket, 'STARTTLS', [220]);

        $crypto_enabled = stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        if($crypto_enabled !== true) {
            throw new RuntimeException('Could not establish a verified TLS connection to Gmail SMTP.');
        }

        smtpCommand($socket, 'EHLO localhost', [250]);
        smtpCommand($socket, 'AUTH LOGIN', [334]);
        smtpCommand($socket, base64_encode($username), [334]);
        smtpCommand($socket, base64_encode($password), [235]);
        smtpCommand($socket, 'MAIL FROM:<' . $from_email . '>', [250]);
        smtpCommand($socket, 'RCPT TO:<' . $recipient . '>', [250, 251]);
        smtpCommand($socket, 'DATA', [354]);

        $boundary = '=_oats_' . bin2hex(random_bytes(18));
        $from_header = smtpEncodeHeader($from_name) . ' <' . $from_email . '>';
        $subject_header = smtpEncodeHeader($subject);
        $message_id_domain = substr(strrchr($from_email, '@'), 1);
        $message_id = '<' . bin2hex(random_bytes(16)) . '@' . $message_id_domain . '>';

        $headers = [
            'Date: ' . date(DATE_RFC2822),
            'From: ' . $from_header,
            'To: <' . $recipient . '>',
            'Subject: ' . $subject_header,
            'Message-ID: ' . $message_id,
            'MIME-Version: 1.0',
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        ];

        $textBody = str_replace(["\r\n", "\r"], "\n", $textBody);
        $htmlBody = str_replace(["\r\n", "\r"], "\n", $htmlBody);
        $message = implode("\r\n", $headers) . "\r\n\r\n";
        $message .= '--' . $boundary . "\r\n";
        $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
        $message .= quoted_printable_encode($textBody) . "\r\n";
        $message .= '--' . $boundary . "\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
        $message .= quoted_printable_encode($htmlBody) . "\r\n";
        $message .= '--' . $boundary . '--';
        $message = str_replace(["\r\n", "\r", "\n"], "\r\n", $message);
        $message = preg_replace('/(?m)^\./', '..', $message);

        smtpWrite($socket, $message . "\r\n.\r\n");
        smtpRead($socket, [250]);
        smtpWrite($socket, "QUIT\r\n");
    } finally {
        fclose($socket);
    }
}
