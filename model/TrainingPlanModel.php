<?php

class TrainingPlanModel
{
    private $dao;

    public function __construct($dao)
    {
        $this->dao = $dao;
    }

    /** Pobiera plany treningowe użytkownika z bazy */
    public function getPlansByUser($userId): array
    {
        $this->dao->fetch("SELECT * FROM training_plans WHERE user_id = " . intval($userId));
        $plans = [];
        while ($row = $this->dao->getRow()) {
            $plans[] = $row;
        }
        return $plans;
    }

    /** Pobiera treningi powiązane z danym planem */
    public function fetchWorkouts($planId): array
    {
        $this->dao->fetch("SELECT * FROM workouts WHERE training_plan_id = " . intval($planId));
        $workouts = [];
        while ($row = $this->dao->getRow()) {
            $exercises = json_decode($row['exercises'], true)['exercises'] ?? [];
            foreach ($exercises as $exercise) {
                $workouts[$row['day']][] = $exercise;
            }
        }
        return $workouts;
    }


    /** Usuwa plan treningowy */
    public function deletePlan($planId): bool
    {
        // Usuń treningi powiązane z planem
        $this->dao->fetch("DELETE FROM workouts WHERE training_plan_id = " . intval($planId));
        // Usuń plan
        $this->dao->fetch("DELETE FROM training_plans WHERE id = " . intval($planId));
        return $this->dao->getResult();
    }

    /** Aktualizuje status planu na aktywny */
    public function activatePlan($userId, $planId): bool
    {
        // Dezaktywuj wszystkie plany użytkownika
        $this->dao->fetch("UPDATE training_plans SET active = 0 WHERE user_id = " . intval($userId));
        // Aktywuj wybrany plan
        $this->dao->fetch("UPDATE training_plans SET active = 1 WHERE id = " . intval($planId));
        return $this->dao->getResult();
    }

    /** Metoda formatowania ćwiczeń do Json */
    public function formToJson($days)
    {
        $trainingDays = [];

        for ($i = 1; $i <= $days; $i++) {
            $dayExercises = [
                "exercises" => []
            ];

            foreach ($_POST as $key => $value) {
                if (preg_match("/^exercise_day_{$i}_(\d+)$/", $key, $matches)) {
                    $exerciseNumber = $matches[1];
                    $exerciseName = htmlspecialchars($value);
                    $sets = (int)($_POST["series_day_{$i}_{$exerciseNumber}"] ?? 0);

                    $dayExercises["exercises"][] = [
                        "name" => $exerciseName,
                        "sets" => $sets
                    ];
                }
            }

            $trainingDays[] = json_encode($dayExercises, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        return $trainingDays;
    }


    public function getTheNewestPlanId($userId)
    {
        $sql = "SELECT * FROM training_plans WHERE user_id=" . $this->sanitize($userId) . " ORDER BY created_at DESC LIMIT 1;";
        $row = $this->dao->fetch($sql);
        if ($result = $this->dao->getRow()) {
            return $result['id'];
        } else {
            return -1;
        }
    }

    public function addPlan($userId, $dayCount, $planName): bool
    {
        $sql = "INSERT INTO training_plans 
                VALUES (NULL, " . $this->sanitize($userId) . ", '" . $this->sanitize($planName) . "', NOW(), " . $this->sanitize($dayCount) . ", 0);";
        $this->dao->fetch($sql);
        return (bool)$this->dao->getResult();
    }

    public function addWorkout($day, $exercises, $userId): bool
    {
        $training_plan_id = $this->getTheNewestPlanId($userId);
        if($training_plan_id >= 0){
            $sql = "INSERT INTO workouts VALUES(NULL, " . $training_plan_id . ", " . $day . ", '" . $exercises . "');";
            $this->dao->fetch($sql);
            return (bool)$this->dao->getResult();
        } else {
            return false;
        }
    }


    //=================

    public function getActivePlanData($userId)
    {
        $sql = "SELECT * FROM training_plans WHERE user_id = " . $userId . " AND active = 1;";
        $this->dao->fetch($sql);
        if($result = $this->dao->getRow()) {
            return $result;
        } else {
            return -1;
        }
    }
    public function getActiveWorkoutsAt($userId, $day, $data)
    {
        $sql='SELECT w.* FROM workouts w JOIN training_plans tp ON w.training_plan_id = tp.id
                JOIN users u ON tp.user_id = u.id WHERE u.id = "'.$userId.'" AND tp.active = 1 AND w.day = '.$day.';';
        $this->dao->fetch($sql);
        if($result = $this->dao->getRow()){
            return $result[$data];
        }
        else {
            $_SESSION['errors'] = "Błąd pobierania treningow";
            return [];
        }
    }

    function getExercisesFromJson($json) {
        // Dekodowanie JSON na tablicę asocjacyjną
        $data = json_decode($json, true);

        // Sprawdzamy, czy klucz "exercises" istnieje w danych
        if (isset($data['exercises'])) {
            return $data['exercises']; // Zwracamy tablicę ćwiczeń
        }

        return []; // Zwracamy pustą tablicę, jeśli nie ma ćwiczeń
    }

    function getSessionFromJson($json) {
        // Dekodowanie JSON na tablicę asocjacyjną
        return json_decode($json, true);
        return []; // Zwracamy pustą tablicę, jeśli nie ma ćwiczeń
    }

    /** Pomocnicza funkcja sanitizująca dane */
    private function sanitize($data): string
    {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    function addSession($userId, $trainingData, $jsonExercises, $day): bool
    {
        // Sprawdź, czy mamy poprawny trening_id (jeśli istnieje w POST)
        $trainingId = $this->getActiveWorkoutsAt($userId, $day-1, 'id');

        // Określenie daty treningu (już została pobrana i przetworzona wcześniej)
        $trainingDate = $trainingData['date'];

        // Tworzymy zapytanie SQL do zapisania sesji treningowej
        $sql = "INSERT INTO training_session (workout_id, user_id, data, exercises)
        VALUES (" . $this->sanitize($trainingId) . ", " . $this->sanitize($userId) . ", '" . $this->sanitize($trainingDate) . "', '" . $jsonExercises . "');";
        // Wykonujemy zapytanie
        $this->dao->fetch($sql);

        // Zwracamy true, jeśli zapytanie zostało pomyślnie wykonane
        return (bool)$this->dao->getResult();
    }

    function getSession($userId)
    {
        // Pobranie sesji treningowych użytkownika wraz z ćwiczeniami, dniem z planu i nazwą planu
        $sql = "SELECT ts.id AS session_id, ts.data AS session_date, w.day AS plan_day, tp.name AS plan_name, w.exercises AS w_exercises, ts.exercises AS ts_exercises
            FROM training_session ts
            JOIN workouts w ON ts.workout_id = w.id
            JOIN training_plans tp ON w.training_plan_id = tp.id
            WHERE tp.user_id = " . $this->sanitize($userId) . "
            ORDER BY ts.data DESC";
        $this->dao->fetch($sql);

        $sessions = [];
        while ($row = $this->dao->getRow()) {
            $sessions[] = $row;
        }

        if(empty($sessions)) {
            echo "nie masz zarejestrowanych sesji treningowych";
            return [];
        }
        else {
            echo "</div>";
            return $sessions;
        }
    }

    function deleteSession($id)
    {
        $this->dao->fetch("DELETE FROM training_session WHERE id = " . intval($id));
        return $this->dao->getResult();
    }
}
