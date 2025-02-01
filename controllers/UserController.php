<?php

include_once "../model/UserModel.php";
include_once "../dao/DataAccess.php";

class UserController
{
    private $model;
    function __construct()
    {
        $dao = new DataAccess('localhost', 'root', '', 'gym');
        $this->model = new UserModel($dao);
    }
    function login()
    {
        $args = [
            'login' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'password' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ];
        $dane = filter_input_array(INPUT_POST, $args);

        if (!$dane || empty($dane['login']) || empty($dane['password'])) {
            echo "Login lub hasło nie może być puste.<br>";
        }
        $login = $dane["login"];
        $password = $dane["password"];

        if($this->model->loginUser($login, $password)) {
            header('Location: mainPage.php?page=trainings');
        } else {
            if(!empty($error)){
                echo $error;
            }
            echo "Błędne login lub hasło. Spróbuj ponownie";
        }
    }

    /**
     * @throws Exception
     */
    function register()
    {
        $args = [
            'login' => [
                'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
            ],
            'passwd1' => [
                'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
            ],
            'passwd2' => [
                'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
            ],
        ];

        $dane = filter_input_array(INPUT_POST, $args);

        $errors = "";
        foreach ($dane as $key => $val) {
            if ($val === false or $val === NULL) {
                $errors .= $key . " ";
            }
        }
        if ($errors === "") {
            if($this->model->registerUser($dane['login'], $dane['passwd1'], $dane['passwd2'])){
                header("Location: login.php?akcja=wyswietlInfo");
            }
        }
    }

    function checkLogged($session): int
    {
        return $this->model->getLoggedInUser($session);
    }
}