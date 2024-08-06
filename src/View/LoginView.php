<?php

namespace View;

readonly class LoginView
{
    public function renderLogin(): void
    {
        $error = (isset($_SESSION['error'])) ? '<div class="alert alert-danger" role="alert"><h4>' . $_SESSION['error'] . '</h4></div>' : '';
        $output = <<<HTML
<!doctype html>
<html lang="EN">
    <head>
        <title>
            LOGIN FORM
        </title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    </head>
    <body class="bg-secondary bg-gradient">
        <div style="width: 100%; height: 80vh; margin-top: 20vh;">
            <div class="container align-items-center justify-content-center" style="width:400px;padding-left: 30px;padding-right: 30px;">
                $error
                <div class="d-flex justify-content-center">
                    <h1 class="">Login</h1>
                </div>
                <form action="/user/login" method="POST">
                    <div class="mb-3">
                        <label for="user" class="visually-hidden">Nume utilizator</label>
                        <input type="text" class="form-control" id="user" name="user" placeholder="Nume utilizator"/>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="visually-hidden">Parola</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Parola"/>
                    </div>
                    <div class="d-flex gap-2 justify-content-center">
                        <input type="submit" id="submit" name="Login" class="btn btn-primary"/>
                        <input type="button" class="btn btn-primary" onclick="location.href='/user/forgot'" value="Am uitat parola" />
                    </div>    
                    <div class="d-flex justify-content-center mt-3">
                        <input type="button" class="btn btn-success" onclick="location.href='/user/register'" value="Inregistrare">
                    </div>
                </form>
            </div>
            <div class="container align-items-center justify-content-center mt-3">
                <div class="d-flex justify-content-center mt-6">
                    <h3>Magazin online de bilete</h3>
                </div>                
                <div class="d-flex justify-content-center">
                    Acest site a fost creat pentru a simula o versiune simplista a unui magazin virtual de bilete pentru evenimente.<br/>
                    Dupa instalare, puteti sa va logati cu utilizatorul "admin" si parola "admin" pentru a adauga evenimente.<br/>
                    Dupa adaugarea unor evenimente, va puteti deloga de pe admin si sa va creati un utilizator normal, apasand pe butonul "Inregistrare".<br/>
                    Dupa inregistrare, va puteti loga cu utilizatorul nou creat pentru a cumpara bilete la evenimentele create de admin.<br/>
                    Poate fi cumparat un numar nelimitat de bilete pentru un eveniment. Nu pot fi vandute biletele la un eveniment.<br/>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    <h5>Multumesc pentru atentie</h5>
                </div>
            </div>
        </div>
    </body>
</html>
HTML;
        echo $output;
        unset($_SESSION['error']);
    }

    public function renderRegister(): void
    {
        $error = (isset($_SESSION['error'])) ? '<div class="alert alert-danger" role="alert"><h4>' . $_SESSION['error'] . '</h4></div>' : '';
        $output = <<<HTML
<!doctype html>
<html lang="EN">
    <head>
        <title>
            REGISTER FORM
        </title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    </head>
    <body class="bg-secondary bg-gradient">
        <div style="width: 100%; height: 80vh; margin-top: 20vh;">
            <div class="container align-items-center justify-content-center" style="width:550px;padding-left: 30px;padding-right: 30px;">
                $error
                <div class="d-flex justify-content-center">
                    <h1>Formular inregistrare</h1>
                </div>
                <form action="/user/register" method="POST">
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label" for="username">Nume utilizator:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="username" name="username" required />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label" for="name">Nume real:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="name" name="name" required />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label" for="email">Email:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="email" name="email" required />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label" for="password">Parola:</label>
                        <div class="col-sm-8">
                            <input type="password" class="form-control" id="password" name="password" required />
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-center">
                        <input type="submit" id="submit" name="submit" value="Inregistrare" class="btn btn-primary"/>
                        <input type="reset" id="reset" value="Stergere" class="btn btn-primary"/>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        <input type="button" class="btn btn-outline-info" onclick="location.href='/'" value="Anulare inregistrare">
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>
HTML;
        echo $output;
        unset($_SESSION['error']);
    }

    public function renderForgot(): void
    {
        $error = (isset($_SESSION['error'])) ? '<div class="alert alert-danger" role="alert"><h4>' . $_SESSION['error'] . '</h4></div>' : '';
        $content = (isset($_SESSION['success_forgot'])) ? '<div class="alert alert-success" role="alert"><h4>' . $_SESSION['success_forgot'] . '</h4></div><button class="btn btn-success" onclick="location.href=\'/\'">Intoarcere la login</button>' : '';

        if(empty($content)) {
            $content = <<<HTML
        <form action="/user/forgot" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input class="form-control" type="text" id="email" name="email" required/>
            </div>
            <div class="mb-3">
                <input type="submit" id="submit" name="submit" value="Trimite parola" class="btn btn-primary"/>
                <input type="button" class="btn btn-outline-info" onclick="location.href='/'" value="< Inapoi">
            </div>
        </form>
HTML;
        }
        $output = <<<HTML
<!doctype html>
<html lang="EN">
    <head>
        <title>
            FORGOT PASSWORD FORM
        </title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    </head>
    <body class="bg-secondary bg-gradient">
        <div style="width: 100%; height: 80vh; margin-top: 20vh;">
            <div class="container align-items-center justify-content-center" style="width:550px;padding-left: 30px;padding-right: 30px;">
            $error
            <div class="d-flex justify-content-center">
                <h1>Formular parola uitata</h1>
            </div>    
            $content
    </body>
</html>
HTML;
        echo $output;
        unset($_SESSION['error']);
        unset($_SESSION['success_forgot']);
    }

    public function renderReset(): void
    {
        $error = (isset($_SESSION['error'])) ? '<div class="alert alert-danger" role="alert"><h4>' . $_SESSION['error'] . '</h4></div>' : '';
        $content = (isset($_SESSION['success_reset'])) ? '<div class="alert alert-success" role="alert"><h4>' . $_SESSION['success_reset'] . '</h4></div><button class="btn btn-success" onclick="location.href=\'/\'">Intoarcere la login</button>' : '';
        $query = http_build_query($_GET);
        if(empty($content)) {
            $content = <<<HTML
        <form action="/user/resetpassword?$query" method="POST">
            <div class="mb-3">
                <label for="pass1" class="form-label">Parola:</label>
                <input class="form-control" type="password" id="pass1" name="pass1" required />
            </div>
            <div class="mb-3">
                <label for="pass2" class="form-label">Parola din nou:</label>
                <input class="form-control" type="password" id="pass2" name="pass2" required />
            </div>
            <div class="mb-3">
                <input type="submit" id="submit" name="submit" value="Schimba parola" class="btn btn-primary"/>
            </div>
        </form>
HTML;
        }
        $output = <<<HTML
<!doctype html>
<html lang="EN">
    <head>
        <title>
            RESET PASSWORD FORM
        </title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    </head>
    <body class="bg-secondary bg-gradient">
        <div style="width: 100%; height: 80vh; margin-top: 20vh;">
            <div class="container align-items-center justify-content-center" style="width:550px;padding-left: 30px;padding-right: 30px;">
            $error
            <div class="d-flex justify-content-center">
                <h1>Formular resetare parola</h1>
            </div>    
            $content
    </body>
</html>
HTML;
        echo $output;
        unset($_SESSION['error']);
        unset($_SESSION['success_reset']);
    }
}