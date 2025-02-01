<?php
include_once "../controllers/UserController.php";
include_once "../controllers/PlanController.php";
include_once "pageView.php";

$userController = new UserController();

session_start();
$session = session_id();

$userId = $userController->checkLogged($session);

if($userId >= 0)
{
?>
    <!DOCTYPE html>
    <html lang="pl">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>PeakProgress</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="homePage/css/styles.css" rel="stylesheet" />
        <link href="homePage/css/customStyles.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css" />
        <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
        <script src="homePage/js/scripts.js"></script>
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="mainPage.php?page=trainings">PeakProgress</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" ><i class="fas fa-bars"></i></button>
        <div class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0"></div>
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="welcomePage.php?akcja=wyloguj">Wyloguj</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <a class="nav-link" href="mainPage.php?page=trainings">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Treningi
                        </a>
                        <a class="nav-link" href="mainPage.php?page=stats">
                            <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                            Statystyki
                        </a>
                        <a class="nav-link" href="mainPage.php?page=plans">
                            <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                            Plany treningowe
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Zalogowano jako:</div>
                    <?php echo $_SESSION['username'];?>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <?php
                if (filter_input(INPUT_GET, 'page') == 'trainings') {
                    trainingsView($userId);
                }
                if (filter_input(INPUT_GET, 'page') == 'stats') {
                    statsView($userId);
                }
                if (filter_input(INPUT_GET, 'page') == 'plans') {
                    plansView($userId);
                }
                ?>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Mateusz Kozieł &copy; PeakProgress 2025</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="homePage/assets/demo/chart-area-demo.js"></script>
    <script src="homePage/assets/demo/chart-bar-demo.js"></script>
    <script src="homePage/js/datatables-simple-demo.js"></script>
    </body>
    </html>
<?php
}
else{
    header("Location:welcomePage.php");
}
?>
