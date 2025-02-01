# PeakProgress

**PeakProgress** to strona internetowa wspomagająca użytkowników w ćwiczeniach na siłowni. Umożliwia tworzenie kont, dodawanie planów treningowych, rejestrowanie treningów oraz przeglądanie statystyk.

## Funkcje

- **Rejestracja i logowanie:** Użytkownicy mogą zakładać własne konta, logować się i zarządzać swoimi danymi.
- **Plany treningowe:** Możliwość tworzenia, edytowania i usuwania planów treningowych.
- **Rejestrowanie treningów:** Użytkownicy mogą rejestrować swoje treningi, wprowadzając szczegółowe informacje na temat ćwiczeń, serii, powtórzeń i ciężarów.
- **Statystyki:** Przeglądanie statystyk, takich jak liczba sesji treningowych, liczba serii, powtórzeń, podniesionych kilogramów oraz zmiany ciężarów w poszczególnych ćwiczeniach.

## Uruchomienie aplikacji

1. **Pobranie bazy danych:**
   - Zaimportuj plik `gym.sql` z folderu projektu do swojej bazy danych. Nie jest to konieczne, ale zawiera on przygotowane dane do przeglądania na koncie użytkownika: login: `login`, hasło: `Haslologin1`.

2. **Konfiguracja XAMPP:**
   - Włącz XAMPP i uruchom serwery Apache oraz MySQL.

3. **Dostęp do aplikacji:**
   - Plik projektowy `projektKoncowy` należy umieścić w `XAMPP\htdocs`
   - Uruchomienie aplikacji w przeglądarce pod adresem: `localhost:80/projektKoncowy.index.php`

## Użytkowanie

1. **Rejestracja:**
   - Kliknij przycisk "Zarejestruj się" na stronie głównej.
   - Wypełnij formularz rejestracyjny, podając wymagane dane.
   - Kliknij "Zarejestruj się", aby utworzyć konto.

2. **Logowanie:**
   - Kliknij przycisk "Zaloguj się" na stronie głównej.
   - Wprowadź swoje dane logowania i kliknij "Zaloguj się".

3. **Dodawanie planu treningowego:**
   - Przejdź do zakładki "Plany treningowe".
   - Kliknij przycisk "Dodaj nowy plan".
   - Wprowadź szczegóły planu treningowego i kliknij "Zapisz".

4. **Rejestrowanie treningów:**
   - Przejdź do zakładki "Treningi".
   - Kliknij przycisk "Dodaj nowy trening".
   - Wprowadź szczegóły treningu, takie jak ćwiczenia, serie, powtórzenia i ciężary.
   - Kliknij "Zapisz", aby zarejestrować trening.

5. **Przeglądanie statystyk:**
   - Przejdź do zakładki "Statystyki".
   - Przeglądaj szczegółowe statystyki swoich treningów, takie jak liczba sesji, serii, powtórzeń i zmiany ciężarów w poszczególnych ćwiczeniach.


## Technologie

- PHP 7.0
- MySQL
- HTML
- CSS
- JavaScript

