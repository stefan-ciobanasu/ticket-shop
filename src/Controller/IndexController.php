<?php

namespace Controller;

use View\LoginView;
use Model\User;

class IndexController
{
    private User $user;
    public function __construct()
    {
        $this->user = new User();
    }

    public function indexAction(): void
    {
        if (!$this->user->isLoggedIn()) {
            (new LoginView())->renderLogin();
        } else {
            $redirectRoute = match($this->user->getLevel()) {
                2 => '/user',
                3 => '/admin',
            };
            header('Location: ' . $redirectRoute);
        }
    }
}