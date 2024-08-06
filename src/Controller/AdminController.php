<?php

namespace Controller;

use DateTime;
use JetBrains\PhpStorm\NoReturn;
use Model\Event;
use Model\User;
use View\AdminView;

class AdminController
{
    public function indexAction(): void
    {
        $user = new User();
        if ($user->isLoggedIn()) {
            (new AdminView($user))->render();
        }
    }

    #[NoReturn] public function editEventAction(): void
    {
        $user = new User();
        if ($user->isLoggedIn() && $user->getLevel() === 3) {
            if (!isset($_GET['id']) || !isset($_POST['event_name']) || !isset($_POST['event_date']) || !isset($_POST['event_logo'])) {
                http_response_code(400);
                exit;
            }
            $event = Event::getById($_GET['id']);
            if (!is_null($event)) {
                $event->setTitle($_POST['event_name']);
                $event->setEventDate(new DateTime($_POST['event_date']));
                $event->setLogo($_POST['event_logo']);
            } else {
                $event = new Event();
                $event->setTitle($_POST['event_name']);
                $event->setEventDate(new DateTime($_POST['event_date']));
                $event->setLogo($_POST['event_logo']);
            }
            if ($event->save()) {
                http_response_code(200);
            } else {
                http_response_code(400);
            }
        } else {
            http_response_code(401);
        }
        exit;
    }

    #[NoReturn] public function deleteEventAction(): void
    {
        $user = new User();
        if ($user->isLoggedIn() && $user->getLevel() === 3) {
            if (!isset($_GET['id'])) {
                http_response_code(400);
                exit;
            }
            $event = Event::getById($_GET['id']);
            if (!is_null($event)) {
                if ($event->delete()) {
                    http_response_code(200);
                } else {
                    http_response_code(400);
                }
            } else {
                http_response_code(404);
            }
        } else {
            http_response_code(401);
        }
        exit;
    }
}