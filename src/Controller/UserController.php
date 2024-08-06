<?php

namespace Controller;

use Model\Event;
use Model\Ticket;
use Model\User;
use View\LoginView;
use View\UserView;

class UserController
{
    public function indexAction(): void
    {
        $user = new User();
        if ($user->isLoggedIn()) {
            (new UserView($user))->render();
        } else {
            header('Location: /');
        }
    }

    public function loginAction(): void
    {
        $user = new User();
        if ($user->isLoggedIn()) {
            header('Location: /user');
        }
        if (empty($_POST['user']) || empty($_POST['password'])) {
            $_SESSION['error'] = 'You must enter a username and password';
            header('Location: /');
        }
        $user->loginUser($_POST['user'], $_POST['password']);
        header('Location: /');
    }

    public function logoutAction(): void
    {
        $user = new User();
        if ($user->isLoggedIn()) {
            if (isset($_COOKIE['token'])) {
                unset($_COOKIE['token']);
                setcookie('token', '', -1, '/');
            }
            session_destroy();
            header('Location: /');
        }
    }

    public function registerAction(): void
    {
        $user = new User();
        if ($user->isLoggedIn()) {
            header('Location: /user');
        }
        if (isset($_POST['submit'])) {
            echo 'here';
            $result = $user->createUser($_POST['username'], $_POST['password'], $_POST['name'], $_POST['email']);
            if ($result) {
                header('Location: /');
            }
        }
        (new LoginView())->renderRegister();
    }

    public function forgotAction(): void
    {
        $user = new User();
        if ($user->isLoggedIn()) {
            header('Location: /user');
        }
        if (isset($_POST['submit'])) {
            $result = $user->forgotPassword($_POST['email']);
            if ($result) {
                $_SESSION['success_forgot'] = 'Un mail de resetare a parolei a fost trimis la adresa introdusa.';
                $_SESSION['cheat_link'] = $result;
            } else {
                $_SESSION['error'] = 'Utilizatorul cu acest email nu exista sau are deja un proces de resetare activ.';
            }
        }
        (new LoginView())->renderForgot();
    }

    public function resetPasswordAction(): void
    {
        $user = new User();
        if ($user->isLoggedIn()) {
            header('Location: /user');
        }
        if (!empty($_GET['email']) && !empty($_GET['token'])) {
            if ($user->canResetPassword($_GET['email'], $_GET['token'])) {
                if (!empty($_POST['submit'])) {
                    if ($_POST['pass1'] !== $_POST['pass2']) {
                        $_SESSION['error'] = 'Parolele noi nu sunt identice.';
                    } elseif ($user->resetPassword($_GET['email'], $_POST['pass1'])) {
                        $_SESSION['success_reset'] = 'Parola a fost schimbata.';
                    } else {
                        $_SESSION['error'] = 'Parola nu a fost schimbata.';
                    }
                }
            } else {
                $_SESSION['error'] = 'Solicitarea de resetare a parolei a expirat.';
            }
            (new LoginView())->renderReset();
        } else {
            header('Location: /');
        }
    }

    public function buyTickets(): void
    {
        $user = new User();
        if (!$user->isLoggedIn()) {
            http_response_code(404);
        }
        if (!empty($_GET['user']) && !empty($_GET['event'] && !empty($_GET['count']))) {
            if (!is_numeric($_GET['event'])) {
                http_response_code(400);
                exit();
            }
            $event = Event::getById(intval($_GET['event']));
            if (!is_null($event)) {
                $ticket = new Ticket(intval($_GET['user']), $event, intval($_GET['count']));

                if ($ticket->save()) {
                    http_response_code(200);
                } else {
                    http_response_code(400);
                }
            } else {
                http_response_code(400);
            }
            exit();
        }
    }
}