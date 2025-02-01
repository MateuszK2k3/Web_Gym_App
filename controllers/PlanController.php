<?php
include_once "../model/TrainingPlanModel.php";
include_once "../dao/DataAccess.php";
class PlanController
{
    private $model;
    function __construct()
    {
        $dao = new DataAccess('localhost', 'root', '', 'gym');
        $this->model = new TrainingPlanModel($dao);
    }
    public function printTrainingPlans($userId)
    {
        // Pobierz plany treningowe użytkownika
        $plans = $this->model->getPlansByUser($userId);

        if (empty($plans)) {
            echo "Brak planów treningowych.";
            return;
        }

        foreach ($plans as $plan) {
            // Pobierz treningi dla danego planu
            $workouts = $this->model->fetchWorkouts($plan['id']);

            // Wyświetl plan
            $isActive = $plan['active'] ? 'Aktywny' : 'Nieaktywny';

            echo "<div class='plan'>
                <div class='left'>
                    <span class='plan-name'>{$plan['name']}</span>
                    <span class='status $isActive'>$isActive</span>
                </div>
                <div class='right'>
                    <div class='date'>Data utworzenia: {$plan['created_at']}</div>
                    <button class='expand-btn' onClick='rozwinPlan(this)'>Rozwiń</button>
                    <button class='activate-btn' onclick='activateTrainingPlan({$plan['id']})'>Aktywuj</button>
                    <button class='delete-btn' onclick='deleteTrainingPlan({$plan['id']})'>Usuń</button>
                </div>
              </div>";

            // Wyświetl treningi
            echo "<div class='details'>
                <h3>Treningi w tygodniu:</h3>
                <ul>";
            foreach ($workouts as $day => $dayWorkouts) {
                $exerciseList = implode(', ', array_map(function ($workout) {
                    $name = $workout['name'] ?? 'Nieznane ćwiczenie';
                    $sets = $workout['sets'] ?? 0;
                    return "$name ($sets serie)";
                }, $dayWorkouts));
                $dayNum = $day + 1;
                echo "<li>
                    <span class='exercise'>Dzień $dayNum</span>
                    <div class='sets'>Ćwiczenia: $exerciseList</div>
                  </li>";
            }
            echo "</ul></div><div class='spacer'></div>";
        }
    }

    function activateTrainingPlan($userId)
    {
        $planIdToActivate = intval($_POST['activate_plan_id']);
        if(!$this->model->activatePlan($userId, $planIdToActivate)){
            echo "Błąd podczas aktywacji nowego planu.";
        }
    }
    function deleteTrainingPlan()
    {
        if (isset($_POST['delete_plan_id'])) {
            $planIdToDelete = intval($_POST['delete_plan_id']);
            if(!$this->model->deletePlan($planIdToDelete)){
                echo "Błąd podczas usuwania planu.";
            }
        }
    }
    public function showAddPlanForm() {
        ?>
        <button class="add-plan-btn" id="start-form-button" onclick="expandFirst()">
            <i class="fas fa-plus"></i> Dodaj nowy plan treningowy
        </button>
        <div class="form-container" id="form-container">
            <form id="training-form" method="post">
                <div id="form-step-1" class="collapsible-content">
                    <label>Podaj nazwę planu</label>
                    <input type="text" class="plan-name-form" id="name" name="name" placeholder="nazwa">
        
                    <label class="dni">Wybierz ilość dni treningowych:</label>
                    <div class="radio-group" id="radio-group"></div>
        
                    <div class="form-buttons">
                        <button type="reset" class="cancel-button" onclick="cancelForm(1)">Anuluj</button>
                        <button type="button" class="next-button" onclick="showNextStep()">Dalej</button>
                    </div>
                </div>
        
                <div id="form-step-2" class="collapsible-content">
                    <div id="training-days-container"></div>
                    <div class="form-buttons">
                        <button type="reset" class="cancel-button" onclick="cancelForm(2)">Anuluj</button>
                        <button type="submit" class="next-button" onclick="submitTrainingPlan()" value="dodajPlan" name="zapisz" >Zapisz</button>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }
    public function saveNewTrainingPlan($userId)
    {
        $name = htmlspecialchars($_POST['name'] ?? 'Bez nazwy');
        $days = (int)($_POST['days'] ?? 0);
        $exercise = $this->model->formToJson($days);
        $done = true;

        if($this->model->addPlan($userId, $days, $name)){
            for($i = 0; $i < $days; $i++) {
                if (!$this->model->addWorkout($i, $exercise[$i], $userId)){
                    $_SESSION['errors'] = "Błąd podczas dodawania planu 2!";
                    $done = false;
                    break;
                }
            }
            if($done){
                ?><script>window.location.href="mainPage.php?page=plans"</script><?php
            }
        } else{
            $_SESSION['errors'] = "Błąd podczas dodawania planu 1!";
        }
    }
    public function showTrainingSessionForm_1($userId)
    {
        ?>
        <div class="form-container" id="form-container">
            <form id="training-form" method="post">
                <div id="form-step-1" class="collapsible-content">
                    <h2>Podaj dzień</h2>
                    <?php
                    $data = $this->model->getActivePlanData($userId);
                    if($data >= 0) {
                        $days = $data['days_count'];
                        $name = $data['name'];
                        echo '<label class="dni" id="which-plan">Dodawanie danych do planu: ' . $name . '</label>
                            <div class="radio-group" id="session-radio-group">';
                        for ($i = 1; $i <= $days; $i++) {
                            echo '<input type="radio" name="days" value=' . $i . ' id="day-' . $i . '">';
                            echo '<label for="day-' . $i . '">Dzień ' . $i . '</label>';
                        }
                    }
                        ?>
                    </div>

                    <div class="form-buttons">
                        <button type="reset" class="cancel-button" onclick="cancelForm(1)">Anuluj</button>
                        <button type="submit" class="next-button" name="form" value="form2">Dalej</button>
                    </div>
            </form>
        </div>
        <?php
    }
    public function showTrainingSessionForm_2($userId, $day) {
        ?>
        <div class="form-container-2" id="form-container-2">
            <form id="training-form-2" method="post">
                <div id="form-step-2" class="collapsible-content-2">
                    <div id="training-days-container">
                        <h3>Dzień <?php echo htmlspecialchars($day); ?></h3>
                        <?php
                        $activeWorkoutsJson = $this->model->getActiveWorkoutsAt($userId, $day-1, 'exercises');
                        $exercises = $this->model->getExercisesFromJson($activeWorkoutsJson);
                        foreach ($exercises as $index => $exercise): ?>
                            <div class="training-day">
                                <h4>Ćwiczenie <?php echo $index + 1; ?>: <?php echo htmlspecialchars($exercise['name']); ?></h4>
                                <?php for ($serie = 1; $serie <= $exercise['sets']; $serie++): ?>
                                    <div class="exercise-group">
                                        <label class="ex-label">Seria <?php echo $serie; ?></label>
                                        <input type="number" class="reps-input" name="reps_<?php echo $index + 1; ?>_<?php echo $serie; ?>" placeholder="Powtórzenia" min="1" required>
                                        <input type="number" class="weight-input" name="weight_<?php echo $index + 1; ?>_<?php echo $serie; ?>" placeholder="Ciężar (kg)" min="0" step="0.1" required>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        <?php endforeach; ?>
                        <div class="date-selection-container">
                            <div class="radio-option">
                                <label>
                                    <input type="radio" name="date_option" value="today" id="today-radio" checked onclick="toggleDateOptions()">
                                    Dodaj z dzisiejszą datą
                                </label>
                                <span id="today-date" class="date-display"><?= date('Y-m-d'); ?></span>
                            </div>
                            <div class="radio-option">
                                <label>
                                    <input type="radio" name="date_option" value="custom" id="custom-radio" onclick="toggleDateOptions()">
                                    Dodaj własną datę
                                </label>
                                <div id="custom-date-field" class="custom-date-field">
                                    <input type="date" id="custom-date" name="custom_date">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-buttons">
                        <button type="reset" class="cancel-button" onclick="cancelForm2()">Anuluj</button>
                        <button type="submit" class="next-button" value="rejestrujtrening" name="zapisz">Zapisz</button>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }
    public function saveNewTrainingSession($userId, $day)
    {
        // Pobranie danych z żądania POST i ich przetworzenie
        $trainingData = $this->processTrainingSessionData($userId, $day, $_POST);

        // Zmienna przechowująca JSON z ćwiczeniami
        $jsonExercises = json_encode($trainingData['exercises'], JSON_UNESCAPED_UNICODE);

        if($this->model->addSession($userId, $trainingData, $jsonExercises, $day)){
            ?><script>window.location.href="mainPage.php?page=trainings"</script><?php
        } else {
            $_SESSION['errors'] = "Błąd podczas dodawania";
        }
    }


    public function processTrainingSessionData($userId, $day, $postData): array
    {
        // Inicjalizacja wyniku
        $result = [
            'date' => null,
            'exercises' => []
        ];

        $activeWorkoutsJson = $this->model->getActiveWorkoutsAt($userId, $day-1, 'exercises');
        $names = $this->model->getExercisesFromJson($activeWorkoutsJson);

        // Ustal datę sesji treningowej
        if (isset($postData['date_option']) && $postData['date_option'] === 'custom') {
            $result['date'] = isset($postData['custom_date']) ? $postData['custom_date'] : date('Y-m-d');
        } else {
            $result['date'] = date('Y-m-d'); // Domyślnie dzisiejsza data
        }

        // Grupowanie ćwiczeń i serii
        $exercises = [];
        $index = 0;
        foreach ($postData as $key => $value) {
            // Dopasowanie wzorców dla "reps_X_Y" oraz "weight_X_Y"
            if (preg_match('/^(reps|weight)_(\d+)_(\d+)$/', $key, $matches)) {
                $type = $matches[1]; // "reps" lub "weight"
                $exerciseIndex = (int)$matches[2]; // Numer ćwiczenia
                $setIndex = (int)$matches[3]; // Numer serii

                // Inicjalizowanie ćwiczenia, jeśli jeszcze nie istnieje
                if (!isset($exercises[$exerciseIndex])) {
                    $exercises[$exerciseIndex] = ['name' => $names[$index]['name'], 'exercises' => []];
                    $index++;
                }

                // Inicjalizowanie serii, jeśli jeszcze nie istnieje
                if (!isset($exercises[$exerciseIndex]['exercises'][$setIndex - 1])) {
                    $exercises[$exerciseIndex]['exercises'][$setIndex - 1] = [];
                }

                // Przypisanie wartości do odpowiedniego typu (powtórzenia lub waga)
                $exercises[$exerciseIndex]['exercises'][$setIndex - 1][$type] = (int)$value;
            }
        }

        $result['exercises'] = array_values($exercises); // Resetowanie indeksów ćwiczeń
        return $result;
    }
    public function showListSessions($userId)
    {
        $sessions = $this->model->getSession($userId);
        if(!empty($sessions))
        {
            foreach ($sessions as $session) {
                $sessionId = $session['session_id'];
                $sessionDate = $session['session_date'];
                $planDay = $session['plan_day'] + 1; // Zakładając, że dzień planu zaczyna się od 1
                $planName = $session['plan_name'];
                $w_exercisesJson = $session['w_exercises'];
                $w_exercises = $this->model->getExercisesFromJson($w_exercisesJson);
                $ts_exercisesJson = $session['ts_exercises'];
                $ts_exercises = $this->model->getSessionFromJson($ts_exercisesJson);


                echo "<div class='plan'>
                <div class='left'>
                       <p><strong>Plan: </strong>" . $planName . " | <strong>Dzień: </strong>" . $planDay . " | <strong>Data: </strong>" . $sessionDate . "</p>
                </div>
                <div class='right'>
                    <button class='expand-btn' id='session' onClick='rozwinPlan(this)'>Rozwiń</button>
                    <form method='post'>
                    <button type='submit' class='delete-btn' id='session-dlt-btn' value='".$sessionId."' name='delete'>Usuń</button>
                    </form>
            </div>
              </div>";
                // Łączenie danych ćwiczeń (w_exercises i ts_exercises)
                echo "<div class='details'>";
                foreach ($w_exercises as $index => $exercise) {
                    ?>
                    <table id="session-detail">
                    <thead>
                    <tr>
                        <th colspan="3" id="exercise-name"><?php echo $exercise['name']; ?></th>
                    </tr>
                    <tr>
                        <th>Seria</th>
                        <th>Powtórzenie</th>
                        <th>Ciężar</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    // Wyszukaj ćwiczenie o tej samej nazwie w ts_exercises
                    $exerciseDetails = $ts_exercises[$index] ?? null;
                    $i = 1;
                    // Wyświetlanie ćwiczeń
                    if ($exerciseDetails) {
                        // Dla każdego ćwiczenia (z `ts_exercises`) wyświetlamy serię, powtórzenia i ciężar
                        foreach ($exerciseDetails['exercises'] as $set) {
                            echo "<tr>
                        <td>$i</td>
                        <td>{$set['reps']}</td>
                        <td>{$set['weight']}</td>
                    </tr>";
                            $i++;
                        }
                    }
                    echo "</tbody>
                    </table>";
                }
                echo "</div>";
            }
        }
    }

    function generateCharts($userId)
    {
        $mysqli = new mysqli('localhost', 'root', '', 'gym');

        // Sprawdź połączenie
        if ($mysqli->connect_error) {
            die('Błąd połączenia z bazą danych: ' . $mysqli->connect_error);
        }

        // Pobierz aktywny plan treningowy użytkownika
        $queryPlan = $mysqli->prepare("SELECT id FROM training_plans WHERE user_id = ? AND active = 1 LIMIT 1");
        $queryPlan->bind_param("i", $userId);
        $queryPlan->execute();
        $resultPlan = $queryPlan->get_result();
        $activePlan = $resultPlan->fetch_assoc();

        if (!$activePlan) {
            die('Brak aktywnego planu treningowego dla użytkownika.');
        }

        $planId = $activePlan['id'];

        // Pobierz wszystkie treningi powiązane z aktywnym planem
        $queryWorkouts = $mysqli->prepare("SELECT id, day, exercises FROM workouts WHERE training_plan_id = ?");
        $queryWorkouts->bind_param("i", $planId);
        $queryWorkouts->execute();
        $resultWorkouts = $queryWorkouts->get_result();

        $workouts = [];
        while ($row = $resultWorkouts->fetch_assoc()) {
            $row['exercises'] = json_decode($row['exercises'], true);
            $workouts[$row['id']] = $row;
        }

        // Pobierz sesje treningowe użytkownika
        $querySessions = $mysqli->prepare("SELECT ts.workout_id, data, ts.exercises 
        FROM training_session ts
        INNER JOIN workouts w ON ts.workout_id = w.id
        INNER JOIN training_plans tp ON tp.id = w.training_plan_id
        WHERE ts.user_id = ? AND tp.active = 1;
        ");
        $querySessions->bind_param("i", $userId);
        $querySessions->execute();
        $resultSessions = $querySessions->get_result();

        $sessions = [];
        while ($row = $resultSessions->fetch_assoc()) {
            $row['exercises'] = json_decode($row['exercises'], true);
            $sessions[] = $row;
        }

        $globalStats = $this->calculateGlobalStats($sessions);
        $exerciseStats = $this->calculateExerciseStats($sessions);

        ?>
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            margin: 20px;
                            background-color: #f4f4f9;
                            color: #333;
                        }
                        .summary {
                            margin-bottom: 20px;
                            padding: 15px;
                            background-color: #fff;
                            border-radius: 8px;
                            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                        }
                        .grid-container {
                            display: grid;
                            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                            gap: 20px;
                        }
                        .exercise-block {
                            background-color: #fff;
                            border-radius: 8px;
                            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                            padding: 15px;
                            text-align: center;
                        }
                        canvas {
                            max-width: 100%;
                            height: auto;
                        }
                    </style>
                    <div class="summary">
                        <p>Liczba sesji treningowych: <strong><?php echo $globalStats['sessionCount']; ?></strong></p>
                        <p>Łączna liczba serii: <strong><?php echo $globalStats['totalSets']; ?></strong></p>
                        <p>Łączna liczba powtórzeń: <strong><?php echo $globalStats['totalReps']; ?></strong></p>
                        <p>Łączna liczba podniesionych kilogramów: <strong><?php echo $globalStats['totalWeight']; ?> kg</strong></p>
                    </div>

                    <div class="grid-container">
                        <?php foreach ($exerciseStats as $exercise => $data): ?>
                            <div class="exercise-block">
                                <canvas id="chart-<?php echo $exercise; ?>"></canvas>
                                <h3><?php echo $exercise; ?></h3>
                                <p>Zmiana ciężaru: <strong><?php echo $data['lastChangePercent']; ?>%</strong></p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <script>
                        const exerciseStats = <?php echo json_encode($exerciseStats); ?>;

                        Object.keys(exerciseStats).forEach((exercise) => {
                            const ctx = document.getElementById(`chart-${exercise}`).getContext('2d');
                            const data = exerciseStats[exercise];

                            new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: data.dates,
                                    datasets: [{
                                        label: 'Średni ciężar (kg)',
                                        data: data.weights,
                                        borderColor: 'blue',
                                        backgroundColor: 'rgba(0, 0, 255, 0.1)',
                                        fill: true,
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: { display: false },
                                    }
                                }
                            });
                        });
                    </script>
        <?php
    }

    function calculateGlobalStats($sessions): array
    {
        $currentMonth = date('Y-m');
        $sessionCount = 0;
        $totalSets = 0;
        $totalReps = 0;
        $totalWeight = 0;

        foreach ($sessions as $session) {
            if (strpos($session['data'], $currentMonth) === 0) {
                $sessionCount++;
                foreach ($session['exercises'] as $exercise) {
                    foreach ($exercise['exercises'] as $set) {
                        $totalSets++;
                        $totalReps += $set['reps'];
                        $totalWeight += $set['reps'] * $set['weight'];
                    }
                }
            }
        }

        return [
            'sessionCount' => $sessionCount,
            'totalSets' => $totalSets,
            'totalReps' => $totalReps,
            'totalWeight' => $totalWeight
        ];
    }

    function calculateExerciseStats($sessions): array
    {
        $stats = [];

        foreach ($sessions as $session) {
            $sessionDate = $session['data'];

            foreach ($session['exercises'] as $exercise) {
                $exerciseName = $exercise['name'];

                // Inicjalizacja, jeśli brak danych dla ćwiczenia
                if (!isset($stats[$exerciseName])) {
                    $stats[$exerciseName] = [
                        'dates' => [],
                        'weights' => [],
                        'lastWeight' => 0,
                    ];
                }

                // Oblicz średni ciężar dla tego ćwiczenia w tej sesji
                $totalWeight = 0;
                $setCount = 0;

                foreach ($exercise['exercises'] as $set) {
                    $totalWeight += $set['weight'];
                    $setCount++;
                }

                if ($setCount > 0) {
                    $averageWeight = round($totalWeight / $setCount, 2);
                    $stats[$exerciseName]['dates'][] = $sessionDate;
                    $stats[$exerciseName]['weights'][] = $averageWeight;
                    $stats[$exerciseName]['lastWeight'] = $averageWeight; // Ostatnia waga = średnia z tej sesji
                }
            }
        }

        // Oblicz dodatkowe statystyki dla każdego ćwiczenia
        foreach ($stats as $exerciseName => &$data) {
            // Średnia ciężaru z wszystkich sesji
            $data['averageWeight'] = round(array_sum($data['weights']) / count($data['weights']), 2);

            // Procentowa zmiana wagi (ostatnia waga vs średnia)
            $data['lastChangePercent'] = $data['averageWeight'] > 0
                ? round((($data['lastWeight'] - $data['averageWeight']) / $data['averageWeight']) * 100, 2)
                : 0;
        }

        return $stats;
    }

    function deleteSession()
    {
        if (!$this->model->deleteSession($_POST['delete'])) {
            echo "Błąd podczas usuwania planu.";
        } else {
            echo "Plan usunięty.";
        }

    }
}