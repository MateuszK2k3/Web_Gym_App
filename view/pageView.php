<?php

function plansView($userId)
{
    ?>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Plany treningowe</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Dodaj swój własny plan</li>
        </ol>
    </div>
    <div class="plan-view">
    <?php

    $trainingPlanController = new PlanController();
    $trainingPlanController->showAddPlanForm();
    $trainingPlanController->printTrainingPlans($userId);
    if (isset($_SESSION['errors'])){
        echo $_SESSION['errors'];
        unset($_SESSION['errors']);
    }
    if (filter_input(INPUT_POST, 'zapisz') == 'dodajPlan') {
        $trainingPlanController->saveNewTrainingPlan($userId);
    }
    if (filter_input(INPUT_POST, 'activate_plan_id')) {
        $trainingPlanController->activateTrainingPlan($userId);
    }
    if (filter_input(INPUT_POST, 'delete_plan_id')) {
        $trainingPlanController->deleteTrainingPlan($userId);
    }
    ?>
    </div>
    <div class="spacer"></div>
    <?php
}
function statsView($userId)
{
    ?>
    <div class="container-fluid px-4">
        <h1 class="table-form">Statystyki</h1>
        <ol class="breadcrumb mb-">
            <li class="breadcrumb-item active">Przeglądaj statystyki swoich treningów</li>
        </ol>
        <?php
        $planController = new PlanController();
        $planController->generateCharts($userId);
        ?>
    </div>
    <?php
}
function trainingsView($userId)
{
    ?>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Treningi</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Uzupełnij szczegóły treningu dla wybranego dnia!</li>
        </ol>
    </div>
    <div class="plan-view">
        <?php

        $planController = new PlanController();

        if (isset($_SESSION['errors'])) {
            echo $_SESSION['errors'];
            unset($_SESSION['errors']);
        }
        if (isset($_POST['delete'])) {
            echo '<script>console.log("dziala")</script>';
            $planController->deleteSession();
        }
        if (filter_input(INPUT_POST, 'form') == 'form2') {
            if(isset($_POST['days'])){
                $day = $_POST['days'];
                $_SESSION['day'] = $day;
                $planController->showTrainingSessionForm_2($userId, $day);
            } else {
                $_SESSION['errors'] = "Błąd pobierania treningow";
            }
        }
        else {
            echo '<button class="add-plan-btn" id="start-form-button" onclick="expandFirst()">
                    <i class="fas fa-plus"></i> Dodaj trening
                  </button>';
            $planController->showTrainingSessionForm_1($userId);
            if (filter_input(INPUT_POST, 'zapisz') == 'rejestrujtrening'){
                $day = $_SESSION['day'];
                $planController->saveNewTrainingSession($userId, $day);
            }
        }
        $planController->showListSessions($userId);
        ?>
    </div>

    <?php
}