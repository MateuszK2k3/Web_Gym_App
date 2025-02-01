<?php
$mysqli = new mysqli('localhost', 'root', '', 'gym');

// Sprawdź połączenie
if ($mysqli->connect_error) {
    die('Błąd połączenia z bazą danych: ' . $mysqli->connect_error);
}

$userId = 8; // ID użytkownika

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
var_dump($resultSessions->fetch_assoc());

$sessions = [];
while ($row = $resultSessions->fetch_assoc()) {
    $row['exercises'] = json_decode($row['exercises'], true);
    $sessions[] = $row;
}

// Oblicz statystyki globalne i dla ćwiczeń
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

$globalStats = calculateGlobalStats($sessions);
$exerciseStats = calculateExerciseStats($sessions);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statystyki Treningowe</title>
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
</head>
<body>

<div class="summary">
    <h2>Statystyki Miesiąca</h2>
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

</body>
</html>