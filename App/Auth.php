<?php

namespace App;

use \App\Models\User;

/**
 * Authentication
 * 
 * PHP versiom 8.1.1
 */
class Auth
{
    /**
     * Login the user
     * 
     * @param User $user The user model
     * 
     * @return void
     */
    public static function login($user)
    {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user->user_id;
    }

    /**
     * Logout the user
     * 
     * @return void
     */
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

    /**
     * Remember the originally-requested page in the session
     * 
     * @return void
     */
    public static function rememberRequestedPage()
    {
        $_SESSION['return_to'] = '/' . $_SERVER['QUERY_STRING'];
    }

    /**
     * Get the originally requested page to return to after requiring login, 
     * or default to the homepage
     * 
     * @return void
     */
    public static function getReturnToPage()
    {
        return $_SESSION['return_to'] ?? '/';
    }

    /**
     * Get the current logged-in user, from the session or the remember-me cookie
     * 
     * @return mixed The user model(object) or null if not logged in
     */
    public static function getUser()
    {
        if (isset($_SESSION['user_id'])) {
            // Look up the user based on "id" stored in the session
            return User::findByID($_SESSION['user_id']);
        }
    }
}
