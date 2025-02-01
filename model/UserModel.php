<?php

class UserModel
{
    protected $dao;

    function __construct($dao)
    {
        $this->dao = $dao;
    }

    /** Pobieranie użytkownika na podstawie loginu. */
    function selectUser($login)
    {
        $this->dao->fetch("SELECT * FROM users WHERE login='" . $this->sanitize($login) . "'");
    }
    /** Dodanie nowego użytkownika do bazy danych */
    function addUser($login, $hashedPassword): bool
    {
        $sql = "INSERT INTO users (id, login, password, created_at) 
                VALUES (NULL, '" . $this->sanitize($login) . "', '" . $this->sanitize($hashedPassword) . "', NOW())";

        $this->dao->fetch($sql);
        return (bool)$this->dao->getResult();
    }

    /** Sprawdzenie poprawności hasła i zwrócenie ID użytkownika */
    function checkPassword($password): int
    {
        if ($user = $this->dao->getRow()) {
            if (password_verify($password, $user['password'])) {
                return $user['id'];
            }
            return -1;
        }
        return -1;
    }

    /** Sprawdzenie, czy użytkownik istnieje w bazie */
    function ifExists(): bool
    {
        return (bool)$this->dao->getRow();
    }

    /** Walidacja danych użytkownika przed rejestracją
     * @param $login
     * @param $password1
     * @param $password2
     * @return array - tablica z błędami walidacji (pusta, jeśli brak błędów)
     */
    function validateUserData($login, $password1, $password2): array
    {
        $errors = [];

        if (empty($login)) {
            $errors[] = "Login nie może być pusty.";
        }

        if (empty($password1) || empty($password2)) {
            $errors[] = "Hasło nie może być puste.";
        }

        if ($password1 !== $password2) {
            $errors[] = "Hasła muszą się zgadzać.";
        }

        if (!preg_match('/^(?=.*[A-Z])(?=.*\d).+$/', $password1)) {
            $errors[] = "Hasło musi zawierać przynajmniej jedną wielką literę i cyfrę.";
        }

        if (strlen($password1) < 8) {
            $errors[] = "Hasło musi mieć co najmniej 8 znaków.";
        }

        return $errors;
    }

    /** Sprawdzenie, czy login jest dostępny */
    function isLoginAvailable($login): bool
    {
        $this->dao->fetch("SELECT * FROM users WHERE login='" . $this->sanitize($login) . "'");
        return !$this->ifExists();
    }

    /** Obsługa logowania użytkownika */
    function loginUser($login, $password): bool
    {
        $this->selectUser($login);

        $userId = $this->checkPassword($password);
        if ($userId >= 0) {
            session_start();
            $sessionId = session_id();
            $_SESSION['username'] = $login;

            // Usunięcie poprzednich sesji użytkownika
            $this->dao->fetch("DELETE FROM logged_in_users WHERE userId='" . $userId . "'");

            // Dodanie nowej sesji użytkownika
            $sqlInsert = "INSERT INTO logged_in_users (sessionId, userId, lastUpdate) 
                          VALUES ('" . $sessionId . "', " . $userId . ", NOW())";
            $this->dao->fetch($sqlInsert);

            return true;
        }

        return false;
    }

    /** Rejestracja nowego użytkownika
     * @throws Exception
     */
    function registerUser($login, $password1, $password2): bool
    {
        $errors = $this->validateUserData($login, $password1, $password2);
        if (!empty($errors)) {
            throw new \Exception(implode('<br>', $errors));
        }

        if ($this->isLoginAvailable($login)) {
            $hashedPassword = password_hash($password1, PASSWORD_BCRYPT);
            return $this->addUser($login, $hashedPassword);
        } else {
            throw new \Exception("Login jest już zajęty.");
        }
    }

    /** Wylogowanie użytkownika */
    function logoutUser()
    {
        session_start();
        $sessionId = session_id();

        // Usuń ciasteczko sesji
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }

        // Zniszcz sesję
        session_destroy();

        // Usuń dane użytkownika z tabeli logged_in_users
        $this->dao->fetch("DELETE FROM logged_in_users WHERE sessionId='" . $this->sanitize($sessionId) . "'");
    }

    /** Pobranie ID zalogowanego użytkownika */
    function getLoggedInUser($sessionId): int
    {
        $this->dao->fetch("SELECT * FROM logged_in_users WHERE sessionId='" . $this->sanitize($sessionId) . "'");
        if ($row = $this->dao->getRow()) {
            return $row['userId'];
        }
        return -1;
    }

    /** Sanityzacja danych */
    private function sanitize($data): string
    {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
}
