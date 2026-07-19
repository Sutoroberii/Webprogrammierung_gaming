<?php

class RegistrationMailService
{
    private string $pendingDirectory;
    private string $mailDirectory;

    public function __construct()
    {
        $projectRoot = dirname(__DIR__, 2);
        $this->pendingDirectory = $projectRoot . "/data/registration-pending";
        $this->mailDirectory = $projectRoot . "/data/registration-mails";

        $this->ensureDirectory($this->pendingDirectory);
        $this->ensureDirectory($this->mailDirectory);

        // Die temporären Registrierungsdaten dürfen nicht direkt über den Browser abrufbar sein.
        $htaccess = $this->pendingDirectory . "/.htaccess";
        if (!is_file($htaccess)) {
            file_put_contents($htaccess, "Require all denied\n", LOCK_EX);
        }
    }

    public function createRegistrationMail(
        string $username,
        string $email,
        string $passwordHash
    ): string {
        $token = $this->saveRequest([
            'type' => 'registration',
            'username' => $username,
            'email' => strtolower(trim($email)),
            'password_hash' => $passwordHash,
        ]);

        $confirmUrl = '../../registrierung-bestaetigen.php?token=' . rawurlencode($token);

        $html = $this->createHtmlDocument(
            'Registrierung bestätigen',
            '<p>Bitte ignoriere diese Nachricht, wenn du nicht versucht hast, dich zu registrieren.</p>'
            . '<p>Andernfalls klicke auf den folgenden Link, um die Registrierung abzuschließen:</p>'
            . '<p><a href="' . htmlspecialchars($confirmUrl, ENT_QUOTES, 'UTF-8') . '">Registrierung bestätigen</a></p>'
            . '<p>Der Link ist eine Stunde gültig.</p>'
        );

        return $this->writeMailFile($html);
    }

    public function createPasswordResetMail(string $username): string
    {
        $token = $this->saveRequest([
            'type' => 'password_reset',
            'username' => $username,
        ]);

        $resetUrl = '../../passwort-aendern.php?token=' . rawurlencode($token);

        $html = $this->createHtmlDocument(
            'Hinweis zur Registrierung',
            '<p>Bitte ignoriere diese Nachricht, wenn du nicht versucht hast, dich zu registrieren.</p>'
            . '<p>Die angegebene E-Mail-Adresse ist bereits registriert.</p>'
            . '<p>Solltest du dein Passwort vergessen haben, kannst du es über den folgenden Link ändern:</p>'
            . '<p><a href="' . htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8') . '">Passwort ändern</a></p>'
            . '<p>Der Link ist eine Stunde gültig.</p>'
        );

        return $this->writeMailFile($html);
    }

    public function getRequest(string $token, string $expectedType): ?array
    {
        if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
            return null;
        }

        $path = $this->getRequestPath($token);
        if (!is_file($path)) {
            return null;
        }

        $json = file_get_contents($path);
        if ($json === false) {
            return null;
        }

        $request = json_decode($json, true);
        if (!is_array($request)) {
            return null;
        }

        if (($request['type'] ?? null) !== $expectedType) {
            return null;
        }

        if (!isset($request['expires_at']) || (int) $request['expires_at'] < time()) {
            @unlink($path);
            return null;
        }

        return $request;
    }

    public function deleteRequest(string $token): void
    {
        if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
            return;
        }

        $path = $this->getRequestPath($token);
        if (is_file($path)) {
            @unlink($path);
        }
    }

    private function saveRequest(array $request): string
    {
        $token = bin2hex(random_bytes(32));
        $request['created_at'] = time();
        $request['expires_at'] = time() + 3600;

        $json = json_encode($request, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            throw new RuntimeException('Die Registrierungsdaten konnten nicht gespeichert werden.');
        }

        $written = file_put_contents($this->getRequestPath($token), $json, LOCK_EX);
        if ($written === false) {
            throw new RuntimeException('Die Registrierungsdaten konnten nicht gespeichert werden.');
        }

        return $token;
    }

    private function getRequestPath(string $token): string
    {
        return $this->pendingDirectory . '/' . hash('sha256', $token) . '.json';
    }

    private function writeMailFile(string $html): string
    {
        $filename = 'mail_' . bin2hex(random_bytes(12)) . '.html';
        $path = $this->mailDirectory . '/' . $filename;

        if (file_put_contents($path, $html, LOCK_EX) === false) {
            throw new RuntimeException('Die simulierte E-Mail konnte nicht gespeichert werden.');
        }

        return 'data/registration-mails/' . $filename;
    }

    private function createHtmlDocument(string $title, string $body): string
    {
        $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

        return '<!DOCTYPE html>'
            . '<html lang="de"><head><meta charset="UTF-8">'
            . '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
            . '<title>' . $safeTitle . '</title>'
            . '<style>'
            . 'body{font-family:Arial,sans-serif;max-width:720px;margin:40px auto;padding:0 20px;line-height:1.6;}'
            . 'a{display:inline-block;padding:10px 16px;background:#333;color:#fff;text-decoration:none;border-radius:6px;}'
            . '</style></head><body>'
            . '<h1>' . $safeTitle . '</h1>'
            . $body
            . '</body></html>';
    }

    private function ensureDirectory(string $directory): void
    {
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new RuntimeException('Ein benötigter Ordner konnte nicht erstellt werden.');
        }
    }
}
