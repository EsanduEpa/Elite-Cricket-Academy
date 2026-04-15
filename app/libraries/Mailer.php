<?php

/**
 * Raw SMTP mailer for demo email delivery without external libraries.
 */
class Mailer
{
    public static function send(string $toEmail, string $subject, string $htmlBody, string $toName = ''): bool
    {
        self::loadConfig();

        if (defined('SMTP_ENABLED') && SMTP_ENABLED === false) {
            error_log('Mailer: SMTP is disabled.');
            return false;
        }

        $host = defined('SMTP_HOST') ? SMTP_HOST : '';
        $port = defined('SMTP_PORT') ? (int) SMTP_PORT : 587;
        $user = defined('SMTP_USER') ? SMTP_USER : '';
        $pass = defined('SMTP_PASS') ? str_replace(' ', '', SMTP_PASS) : '';
        $fromEmail = defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : $user;
        $fromName = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'Elite Cricket Academy';

        if ($host === '' || $user === '' || $pass === '' || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            error_log('Mailer: missing SMTP settings or invalid recipient.');
            return false;
        }

        $sock = fsockopen($host, $port, $errno, $errstr, 15);
        if (!$sock) {
            error_log("Mailer: connect failed - $errstr ($errno)");
            return false;
        }

        stream_set_timeout($sock, 15);

        try {
            self::expect($sock, 220);

            self::cmd($sock, 'EHLO localhost');
            self::expect($sock, 250);

            self::cmd($sock, 'STARTTLS');
            self::expect($sock, 220);

            if (!stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new RuntimeException('Could not enable SMTP TLS.');
            }

            self::cmd($sock, 'EHLO localhost');
            self::expect($sock, 250);

            self::cmd($sock, 'AUTH LOGIN');
            self::expect($sock, 334);
            self::cmd($sock, base64_encode($user));
            self::expect($sock, 334);
            self::cmd($sock, base64_encode($pass));
            self::expect($sock, 235);

            self::cmd($sock, 'MAIL FROM:<' . $fromEmail . '>');
            self::expect($sock, 250);
            self::cmd($sock, 'RCPT TO:<' . $toEmail . '>');
            self::expect($sock, 250);

            self::cmd($sock, 'DATA');
            self::expect($sock, 354);
            fwrite($sock, self::buildMessage($fromEmail, $fromName, $toEmail, $toName, $subject, $htmlBody));
            self::expect($sock, 250);

            self::cmd($sock, 'QUIT');
            return true;
        } catch (RuntimeException $e) {
            error_log('Mailer error: ' . $e->getMessage());
            return false;
        } finally {
            fclose($sock);
        }
    }

    private static function loadConfig(): void
    {
        $configPath = dirname(__DIR__) . '/config/mail_config.php';
        if (file_exists($configPath)) {
            require_once $configPath;
        }
    }

    private static function buildMessage(
        string $fromEmail,
        string $fromName,
        string $toEmail,
        string $toName,
        string $subject,
        string $htmlBody
    ): string {
        $boundary = bin2hex(random_bytes(12));
        $plainText = html_entity_decode(strip_tags($htmlBody), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $headers = implode("\r\n", [
            'From: ' . self::formatAddress($fromEmail, $fromName),
            'To: ' . self::formatAddress($toEmail, $toName ?: $toEmail),
            'Subject: =?UTF-8?B?' . base64_encode(self::cleanHeader($subject)) . '?=',
            'MIME-Version: 1.0',
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
            'Date: ' . date(DATE_RFC2822),
        ]);

        $body = '--' . $boundary . "\r\n"
            . "Content-Type: text/plain; charset=UTF-8\r\n"
            . "Content-Transfer-Encoding: 8bit\r\n\r\n"
            . self::dotStuff($plainText) . "\r\n"
            . '--' . $boundary . "\r\n"
            . "Content-Type: text/html; charset=UTF-8\r\n"
            . "Content-Transfer-Encoding: 8bit\r\n\r\n"
            . self::dotStuff($htmlBody) . "\r\n"
            . '--' . $boundary . "--\r\n";

        return $headers . "\r\n\r\n" . $body . ".\r\n";
    }

    private static function formatAddress(string $email, string $name): string
    {
        return '=?UTF-8?B?' . base64_encode(self::cleanHeader($name)) . '?= <' . $email . '>';
    }

    private static function cleanHeader(string $value): string
    {
        return trim(str_replace(["\r", "\n"], '', $value));
    }

    private static function dotStuff(string $body): string
    {
        $normalized = str_replace(["\r\n", "\r"], "\n", $body);
        $stuffed = preg_replace('/^\./m', '..', $normalized);
        return str_replace("\n", "\r\n", $stuffed);
    }

    private static function cmd($sock, string $command): void
    {
        fwrite($sock, $command . "\r\n");
    }

    private static function expect($sock, int $expectedCode): string
    {
        $response = '';
        while (($line = fgets($sock, 512)) !== false) {
            $response .= $line;
            if (strlen($line) >= 4 && substr($line, 3, 1) === ' ') {
                break;
            }
        }

        $actualCode = (int) substr($response, 0, 3);
        if ($actualCode !== $expectedCode) {
            throw new RuntimeException("SMTP expected $expectedCode, got $actualCode: $response");
        }

        return $response;
    }
}
