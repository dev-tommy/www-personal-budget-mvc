<?php

namespace App;

/**
 * App authentication
 *
 * PHP version 7.3
 *
 * Created with course PHP MVC framework by author Dave Hollingworth
 * e-mail: tomasz.frydrychowicz.programista@gmail.com
 */

class Auth
{
    public static function login($user)
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->id;
    }

    public static function logout()
    {
        // Unset all of the session variables.
        $_SESSION = [];

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Finally, destroy the session.
        session_destroy();
    }

    public static function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
}