<?php

namespace View;

use Model\Repository;
use Model\Ticket;
use Model\User;

readonly class UserView
{
    public function __construct(private User $user)
    {
    }
    public function render(): void
    {
        $name = $this->user->getName();
        $ticketsTable = $this->renderTicketList();
        $addTicketsModal = $this->renderAddTicketModal();
        $output =<<<HTML
<!doctype html>
<html lang="EN">
    <head>
        <title>
           WELCOME
        </title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    </head>
    <body class="bg-secondary bg-gradient">
        <div style="width: 100%; height: 100vh;">
            <div class="container align-items-center justify-content-center" style="width:800px;padding-left: 30px;padding-right: 30px;">
                <div class="d-flex justify-content-center">
                    <h1>Bine ai venit, $name !</h1>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTicketModal">Cumpara un bilet</button>
                </div>  
                <div class="d-flex justify-content-center mt-3">
                    <table class="table table-success table-striped">
                        <thead>
                            <tr>
                                <th scope="col" class="sm-2">&nbsp;</th>
                                <th scope="col">Ticket Count</th>
                                <th scope="col">Concert Title</th>
                                <th scope="col">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        $ticketsTable
                        </tbody>
                    </table>
                </div>
                $addTicketsModal
                <div class="d-flex justify-content-center mt-3">
                    <button onclick="location.href='/user/logout'" class="btn btn-outline-danger">Logout</button>
                </div>    
            </div>
        </div>        
    </body>
</html>
HTML;
        echo $output;
    }

    private function renderTicketList(): string
    {
        $table = '';
        $tickets = $this->user->getTickets();
        /** @var Ticket $ticket */
        foreach ($tickets as $ticket) {
            $logo = $ticket->getEvent()->getLogo();
            $count = $ticket->getCount();
            $title = $ticket->getEvent()->getTitle();
            $date = $ticket->getEvent()->getEventDate()->format("d.m.Y");
            $table .= <<<HTML
                            <tr>
                                <th scope="row"><img src="$logo" width="50" height="50"></th>
                                <td>$count</td>
                                <td>$title</td>
                                <td>$date</td>
                            </tr>
HTML;
        }
        return $table;
    }

    private function renderAddTicketModal(): string
    {
        $userId = $this->user->getUserId();
        $events = $this->renderEventList();
        $modal = <<<HTML
                <div class="modal fade" id="addTicketModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalLabel">Cumpara un bilet</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <label for="ticketList" class="form-label">Alege un eveniment</label>
                                <select id="ticketList" class="form-select">
                                    <option selected>Alegeti evenimentul</option>
                                    $events
                                </select>
                                <label for="ticketCount" class="form-label">Numar de bilete</label>
                                <input type="number" class="form-control" id="ticketCount" placeholder="Numar de bilete" value="1">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Inchide</button>
                                <button type="button" class="btn btn-primary" id="buyTicketButton">Cumpara</button>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    let addTicketModal = new bootstrap.Modal(document.getElementById('addTicketModal'), {})
                    document.getElementById('buyTicketButton').addEventListener('click', function() {
                        const xhttp = new XMLHttpRequest();
                        let eventId = document.getElementById('ticketList').value;
                        let ticketCount = document.getElementById('ticketCount').value;
                        xhttp.onreadystatechange = function() {
                            if (this.readyState === 4 && this.status === 200) {
                                addTicketModal.hide();
                                location.reload();
                            }
                        };
                        xhttp.open("GET", "/user/buy_tickets?user=$userId&event="+eventId+"&count="+ticketCount, true);
                        xhttp.send();
                    })
                </script>
HTML;
        return $modal;
    }

    private function renderEventList(): string
    {
        $response = '';
        $repo = new Repository();
        $events = $repo->getAllEvents();
        foreach ($events as $event) {
            $eventId = $event['id'];
            $eventName = $event['title'];
            $response.=<<<HTML
                                    <option value="$eventId">$eventName</option>
HTML;
        }
        return $response;
    }
}