<?php


require_once "../model/UserModel.php";
require_once '../dao/DataAccess.php';

if (filter_input(INPUT_GET, "akcja")=="wyloguj") {
    $dao = new DataAccess('localhost', 'root', '', 'gym');
    $userModel = new UserModel($dao);
    $userModel->logoutUser();
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>PeakProgress</title>
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Catamaran:100,200,300,400,500,600,700,800,900" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Lato:100,100i,300,300i,400,400i,700,700i,900,900i" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="welcome/css/styles.css" rel="stylesheet" />
    </head>
    <body id="page-top">
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
            <div class="container px-5">
                <a class="navbar-brand" href="#page-top">PeakProgress</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="signin.php">Zarejestruj</a></li>
                        <li class="nav-item"><a class="nav-link" href="login.php">Zaloguj</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Header-->
        <header class="masthead text-center text-white">
            <div class="masthead-content">
                <div class="container px-5">
                    <h1 class="masthead-heading mb-0">PeakProgress</h1>
                    <h2 class="masthead-subheading mb-0">Planuj, rejestruj, analizuj</h2>
                    <a class="btn btn-primary btn-xl rounded-pill mt-5" href="#scroll">Dowiedz się więcej</a>
                </div>
            </div>
            <div class="bg-circle-1 bg-circle"></div>
            <div class="bg-circle-2 bg-circle"></div>
            <div class="bg-circle-3 bg-circle"></div>
            <div class="bg-circle-4 bg-circle"></div>
        </header>
        <!-- Content section 1-->
        <section id="scroll">
            <div class="container px-5">
                <div class="row gx-5 align-items-center">
                    <div class="col-lg-6 order-lg-2">
                        <div class="p-5"><img class="img-fluid rounded-circle" src="welcome/assets/img/01.png" alt="..." /></div>
                    </div>
                    <div class="col-lg-6 order-lg-1">
                        <div class="p-5">
                            <h2 class="display-4">Rejestruj każdy szczegół swojego treningu!</h2>
                            <p>
                                Z PeakProgress możesz dokładnie monitorować swoje treningi – zapisuj ciężar, liczbę powtórzeń i serii w każdym ćwiczeniu.
                                Dzięki łatwemu w obsłudze interfejsowi nigdy nie stracisz z oczu swoich postępów. Twoje dane są zawsze pod ręką, gotowe,
                                abyś mógł dostrzec swoje osiągnięcia i wyznaczyć nowe cele!
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Content section 2-->
        <section>
            <div class="container px-5">
                <div class="row gx-5 align-items-center">
                    <div class="col-lg-6">
                        <div class="p-5"><img class="img-fluid rounded-circle" src="welcome/assets/img/02.png" alt="..." /></div>
                    </div>
                    <div class="col-lg-6">
                        <div class="p-5">
                            <h2 class="display-4">Twój plan, Twój sukces!</h2>
                            <p>Dostosuj swoje treningi do własnych potrzeb! Z PeakProgress możesz stworzyć spersonalizowany plan treningowy,
                                dopasowany do Twoich celów i poziomu zaawansowania. Określ dni treningowe, wybierz ćwiczenia i zorganizuj swoje sesje,
                                aby ćwiczyć efektywnie i z motywacją.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Content section 3-->
        <section>
            <div class="container px-5">
                <div class="row gx-5 align-items-center">
                    <div class="col-lg-6 order-lg-2">
                        <div class="p-5"><img class="img-fluid rounded-circle" src="welcome/assets/img/03.jpg" alt="..." /></div>
                    </div>
                    <div class="col-lg-6 order-lg-1">
                        <div class="p-5">
                            <h2 class="display-4">Śledź swój postęp w liczbach i wykresach</h2>
                            <p>Zobacz, jak zmieniają się Twoje wyniki dzięki szczegółowym statystykom w PeakProgress. Śledź wzrost siły, liczbę
                                wykonanych powtórzeń i wiele więcej na intuicyjnych wykresach. Analizuj swoje osiągnięcia i planuj kolejne kroki,
                                aby stale iść do przodu!</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Footer-->
        <footer class="py-5 bg-black">
            <div class="container px-5"><p class="m-0 text-center text-white small">Mateusz Kozieł &copy; PakProgress 2025</p></div>
        </footer>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="welcome/js/scripts.js"></script>
    </body>
</html>