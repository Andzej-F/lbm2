<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

/**
 * Login controller
 * 
 * PHP version 8.1.1
 */
class Login extends \Core\Controller
{
    /**
     * Show the login page
     * 
     * @return void
     */
    public function newAction()
    {
        View::renderTemplate('Login/new.html');
    }

    /**
     * Log in a user
     * 
     * @return void 
     */
    public function createAction()
    {

        $user = User::authenticate($_POST['email'], $_POST['password']);

        if ($user) {

            Auth::login($user);

            Flash::addMessage('Login successful');

            $this->redirect(Auth::getReturnToPage());
        } else {

            Flash::addMessage('Login unsuccessful, please try again', Flash::WARNING);

            View::renderTemplate('Login/new.html', [
                'email' => $_POST['email']
            ]);
        }
    }

    /**
     * Log out a user
     * 
     * @return void
     */
    public function destroyAction()
    {
        $this->requireLogin();

        Auth::logout();

        $this->redirect('/login/show-logout-message');
        $this->redirect('/');
    }

    /**
     * Show a "logged out" flash message and redirect to the homepage.
     * Necessary to use the flash messages as they use the session and 
     * at the end of the logout method (destroyAction) the session is 
     * destroyed so a new action needs to be called in order to use the
     * session.
     * 
     * @return void
     */
    public function showLogoutMessageAction()
    {

        Flash::addMessage('Logout successful');

        $this->redirect('/');
    }
}
