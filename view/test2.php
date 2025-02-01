<?php

// Połączenie z bazą danych
$pdo = new PDO("mysql:host=localhost;dbname=gym", "", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Pobranie danych treningowych dla użytkownika
$userId = 8; // Ustaw ID użytkownika
$query = $pdo->prepare("
    SELECT id, data, exercises 
    FROM training_session 
    WHERE MONTH(data) = MONTH(CURDATE()) 
      AND YEAR(data) = YEAR(CURDATE()) 
      AND user_id = 8
");
$query->execute(['userId' => $userId]);

// Przetwarzanie wyników
$totalSessions = 0;
$totalReps = 0;
$totalWeight = 0;
$totalSets = 0;

$results = $query->fetchAll(PDO::FETCH_ASSOC);
foreach ($results as $row) {
    $totalSessions++;

    // Dekodowanie danych JSON
    $exercises = json_decode($row['exercises'], true);
    foreach ($exercises as $exercise) {
        foreach ($exercise['exercises'] as $set) {
            $totalReps += $set['reps'];
            $totalWeight += $set['reps'] * $set['weight'];
            $totalSets++;
        }
    }
}

// Wyświetlenie statystyk
echo "Liczba odbytych sesji treningowych: $totalSessions<br>";
echo "Łączna liczba powtórzeń: $totalReps<br>";
echo "Łączna liczba kilogramów: $totalWeight kg<br>";
echo "Łączna liczba serii: $totalSets<br>";

