<?php

namespace View;

use Model\Repository;
use Model\User;

readonly class AdminView
{
    private array $events;
    public function __construct(private User $user)
    {
        $repo = new Repository();
        $this->events = $repo->getAllEvents();
    }
    public function render(): void
    {
        $name = $this->user->getName();
        $eventsTable = $this->renderEventTable();
        $addEventModal = $this->renderAddEventModal();
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
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addEventModal" data-bs-titleEvent="" data-bs-dateEvent="" data-bs-logoEvent="" data-bs-eventId="0">Creaza un nou event</button>
                </div>  
                <div class="d-flex justify-content-center mt-3">
                    <table class="table table-success table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Titlu eveniment</th>
                                <th scope="col">Data eveniment</th>
                                <th scope="col">Poster</th>
                                <th scope="col">&nbsp;</th>
                                <th scope="col">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                        $eventsTable
                        </tbody>
                    </table>
                </div>
                $addEventModal
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

    private function renderEventTable(): string
    {
        $table = '';
        foreach ($this->events as $event) {
            $eventId = $event['id'];
            $eventName = $event['title'];
            $eventDate = $event['event_date'];
            $logo = $event['logo'];
            $table .= <<<HTML
                            <tr>
                                <th scope="row">$eventId</th>
                                <td>$eventName</td>
                                <td>$eventDate</td>
                                <td><img src="$logo" width="50" height="50" alt="$eventName"></td>
                                <td><button id="edit_"$eventId type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal" data-bs-titleEvent="$eventName" data-bs-dateEvent="$eventDate" data-bs-logoEvent="$logo" data-bs-eventId="$eventId">Editeaza</button></td>
                                <td><button id="delete_"$eventId type="button" class="btn btn-danger" onclick="deleteTicket($eventId)">Sterge</button></td>
                            </tr>
HTML;
        }
        return $table;
    }

    private function renderAddEventModal(): string
    {
        $userId = $this->user->getUserId();
        $modal = <<<HTML
                <div class="modal fade" id="addEventModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalLabel">Editeaza evenimentul</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <label for="eventName" class="form-label">Titlu</label>
                                <input type="text" class="form-control" id="eventName" placeholder="Titlul evenimentului" />
                                <label for="eventDate" class="form-label">Data</label>
                                <input type="datetime-local" class="form-control" id="eventDate" placeholder="Data evenimentului" />
                                <label for="eventLogo" class="form-label">Poster</label>
                                <input type="text" class="form-control" id="eventLogo" placeholder="URL-ul imaginii poster" />
                                <input type="hidden" id="eventId" />
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Inchide</button>
                                <button type="button" class="btn btn-primary" id="editButton">Salveaza</button>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    let addTicketModal = new bootstrap.Modal(document.getElementById('addEventModal'), {})
                    let editEventModal = document.getElementById('addEventModal');
                    editEventModal.addEventListener('show.bs.modal', function (event) {
                        let button = event.relatedTarget
                        let eventName = button.getAttribute('data-bs-titleEvent')
                        let eventDate = button.getAttribute('data-bs-dateEvent')
                        let eventLogo = button.getAttribute('data-bs-logoEvent')
                        let eventId = button.getAttribute('data-bs-eventId')
                        if (eventName === '') {
                            editEventModal.querySelector('.modal-title').textContent = 'Creaza eveniment'
                        } else {
                            editEventModal.querySelector('.modal-title').textContent = 'Editeaza evenimentul'
                        }
                        editEventModal.querySelector('#eventName').setAttribute('value', eventName)
                        editEventModal.querySelector('#eventDate').setAttribute('value', eventDate)
                        editEventModal.querySelector('#eventLogo').setAttribute('value', eventLogo)
                        editEventModal.querySelector('#eventId').setAttribute('value', eventId)
                    })
                    
                    document.getElementById('editButton').addEventListener('click', function() {
                        const xhttp = new XMLHttpRequest();
                        let eventId = document.getElementById('eventId').value;
                        let eventName = document.getElementById('eventName').value;
                        let eventDate = document.getElementById('eventDate').value;
                        let eventLogo = document.getElementById('eventLogo').value;
                        xhttp.onreadystatechange = function() {
                            if (this.readyState === 4 && this.status === 200) {
                                addTicketModal.hide();
                                location.reload();
                            }
                        };
                        xhttp.open("POST", "/admin/edit_event?id="+eventId, true);
                        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        xhttp.send('event_name='+eventName+'&event_date='+eventDate+'&event_logo='+eventLogo);
                    })
                    function deleteTicket(eventId) {
                        if (confirm('Sunteti sigur ca vreti sa stergeti evenimentul?') === true) {
                            const xhttp = new XMLHttpRequest();
                            xhttp.onreadystatechange = function() {
                                if (this.readyState === 4 && this.status === 200) {
                                    location.reload();
                                }
                            };
                            xhttp.open("GET", "/admin/delete_event?id="+eventId, true);
                            xhttp.send();
                        }
                    }
                </script>
HTML;
        return $modal;
    }
}