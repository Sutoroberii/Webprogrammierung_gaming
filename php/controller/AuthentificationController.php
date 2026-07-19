<?php

require_once __DIR__ . "/../model/Authentication.php";
require_once __DIR__ . "/../model/AuthenticationDao.php";
require_once __DIR__ . "/../model/User.php";
require_once __DIR__ . "/../model/SessionControl.php";
require_once __DIR__ . "/../service/RegistrationMailService.php";

class AuthenticationController
{
    private AuthenticationDao $authDao;
    private UserDao $userDao;
    private SessionControl $sessionControl;
    private RegistrationMailService $registrationMailService;

    public function __construct(SessionControl $sessionControl)
    {
        $this->sessionControl = $sessionControl;
        $this->authDao = Authentication::getInstance();
        $this->userDao = User::getInstance();
        $this->registrationMailService = new RegistrationMailService();
    }

    public function validateRegister(
        string $username,
        string $email,
        string $password,
        string $confirmPassword
    ): array {
        $username = trim($username);
        $email = trim($email);
        $errors = [];

        $this->validateUsernameSyntax($username, $errors);
        $this->validateEmailSyntax($email, $errors);
        $this->validatePassword($password, $confirmPassword, $errors);

        return $errors;
    }

    public function login(string $username, string $password): array
    {
        $username = trim($username);

        if ($username === '') {
            return ['success' => false, 'error' => 'Name ist nötig'];
        }

        if ($password === '') {
            return ['success' => false, 'error' => 'Passwort ist nötig'];
        }

        $user = $this->authDao->getUserByUsername($username);
        if ($user === null || !password_verify($password, $user->getPasswordHash())) {
            return ['success' => false, 'error' => 'Name oder Passwort ist falsch'];
        }

        $this->sessionControl->loginUser($user->getUsername());
        $_SESSION['username'] = $user->getUsername();

        return ['success' => true];
    }

    public function logout(): void
    {
        $this->sessionControl->logoutUser();
    }

    /**
     * Startet nur den Registrierungsprozess.
     * Der Benutzer wird hier ausdrücklich noch nicht angelegt.
     */
    public function register(
        string $username,
        string $email,
        string $password,
        string $confirmPassword
    ): array {
        $username = trim($username);
        $email = strtolower(trim($email));

        $errors = $this->validateRegister($username, $email, $password, $confirmPassword);
        if (!empty($errors)) {
            return ['success' => false, 'error' => implode("\n", $errors)];
        }

        $existingUser = $this->authDao->getUserByEmail($email);

        if ($existingUser !== null) {
            $mailFile = $this->registrationMailService->createPasswordResetMail(
                $existingUser->getUsername()
            );

            return ['success' => true, 'mail_file' => $mailFile];
        }

        if ($this->authDao->usernameAlreadyTaken($username)) {
            return ['success' => false, 'error' => 'Name ist bereits vergeben'];
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        if ($passwordHash === false) {
            throw new RuntimeException('Das Passwort konnte nicht verarbeitet werden.');
        }

        $mailFile = $this->registrationMailService->createRegistrationMail(
            $username,
            $email,
            $passwordHash
        );

        return ['success' => true, 'mail_file' => $mailFile];
    }

    public function confirmRegistration(string $token): array
    {
        $request = $this->registrationMailService->getRequest($token, 'registration');

        if ($request === null) {
            return [
                'success' => false,
                'error' => 'Der Bestätigungslink ist ungültig oder abgelaufen.'
            ];
        }

        $username = (string) ($request['username'] ?? '');
        $email = (string) ($request['email'] ?? '');
        $passwordHash = (string) ($request['password_hash'] ?? '');

        if ($username === '' || $email === '' || $passwordHash === '') {
            $this->registrationMailService->deleteRequest($token);
            return ['success' => false, 'error' => 'Die Registrierungsdaten sind unvollständig.'];
        }

        if ($this->authDao->usernameAlreadyTaken($username)
            || $this->authDao->emailAlreadyTaken($email)) {
            $this->registrationMailService->deleteRequest($token);
            return [
                'success' => false,
                'error' => 'Die Registrierung kann nicht mehr abgeschlossen werden.'
            ];
        }

        $this->authDao->createUser($username, $email, $passwordHash);
        $this->userDao->createProfile($username, $email);
        $this->registrationMailService->deleteRequest($token);

        return ['success' => true];
    }

    public function changePassword(
        string $token,
        string $password,
        string $confirmPassword
    ): array {
        $request = $this->registrationMailService->getRequest($token, 'password_reset');

        if ($request === null) {
            return [
                'success' => false,
                'error' => 'Der Link zum Ändern des Passworts ist ungültig oder abgelaufen.'
            ];
        }

        $errors = [];
        $this->validatePassword($password, $confirmPassword, $errors);
        if (!empty($errors)) {
            return ['success' => false, 'error' => implode("\n", $errors)];
        }

        $username = (string) ($request['username'] ?? '');
        if ($username === '' || $this->authDao->getUserByUsername($username) === null) {
            $this->registrationMailService->deleteRequest($token);
            return ['success' => false, 'error' => 'Der Benutzer wurde nicht gefunden.'];
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        if ($passwordHash === false) {
            throw new RuntimeException('Das Passwort konnte nicht verarbeitet werden.');
        }

        $this->authDao->updatePassword($username, $passwordHash);
        $this->registrationMailService->deleteRequest($token);

        return ['success' => true];
    }

    private function validateUsernameSyntax(string $username, array &$errors): void
    {
        if ($username === '') {
            $errors[] = 'Name ist nötig';
            return;
        }

        if (!preg_match('/^[a-zA-Z0-9-]{1,10}$/', $username)) {
            $errors[] = 'Name darf nur Zahlen, Buchstaben und Bindestriche enthalten und höchstens 10 Zeichen lang sein';
        }
    }

    private function validateEmailSyntax(string $email, array &$errors): void
    {
        if ($email === '') {
            $errors[] = 'E-Mail ist nötig';
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'E-Mail ist ungültig';
        }

        // Absichtlich keine Meldung "E-Mail ist bereits vergeben".
    }

    private function validatePassword(
        string $password,
        string $confirmPassword,
        array &$errors
    ): void {
        if ($password === '') {
            $errors[] = 'Passwort ist nötig';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Passwörter stimmen nicht überein';
        }
    }
}
