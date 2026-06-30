<?php

class SessionControl
{
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function loginUser($username): void
    {
        session_regenerate_id(true);
        $_SESSION["LoggedInUsername"] = $username;
    }

    public function logoutUser(): void
    {
        $_SESSION = [];
        setcookie(session_name(), '', time() - 3600, session_get_cookie_params()['path'], session_get_cookie_params()['domain'], session_get_cookie_params()['secure'], session_get_cookie_params()['httponly']);
        session_destroy();
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION["LoggedInUsername"]);
    }

    public function getLoggedInUsername(): ?string
    {
        return $_SESSION["LoggedInUsername"] ?? null;
    }






}