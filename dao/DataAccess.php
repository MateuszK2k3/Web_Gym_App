<?php


class DataAccess
{
    protected $db;
    protected $result;

    function __construct($host, $user, $pass, $db)
    {
        $this->db = new mysqli($host, $user, $pass, $db);
        if ($this->db->connect_errno) {
            printf("Nie udało sie połączenie z serwerem: %s\n", $this->db->connect_error);
            exit();
        }
        $this->db->set_charset("utf8");
    }

    /** Pobranie wyniku zapytania i zapisanie w zmiennej $resoult
     * @param $sql - zapytanie sql jako ciąg znaków
     * @return void
     */
    function fetch($sql)
    {
        $this->result = $this->db->query($sql);
    }

    /** Zwraca tablicę asocjacyjną reprezentującą pojedynczy wiersz z wyniku zapytania
     *
     * @return mixed
     */
    function getRow()
    {
        if ($row = $this->result->fetch_assoc()) return $row;
        else return false;
    }

    public function getResult()
    {
        return $this->result;
    }

    function __destruct()
    {
        $this->db->close();
    }
}