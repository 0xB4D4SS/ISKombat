<?php
class Database {
    function __construct() {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "iskombat";
        $this->connection = new mysqli($servername, $username, $password, $dbname);
        if ($this->$connection->connect_error) {
            die("Connection failed".$connection->connect_error);
        }
    }

    function __destruct() {
        $this->connection->close();
    }

    private function oneRecord($result) {
        return $result->fetch_object();
    }

    private function allRecords($result) {
        $res = array();
        while ($obj = $result->fetch_object()) { // for many records
            $res[] = $obj;
        }
    }

    public function getUserByLoginPass($login, $pass) {
        $query = "SELECT * FROM users WHERE login = '".$login."' AND password = '".$pass."'";
        $result = $this->connection->query($query);
        return $this->oneRecord($result); // for one record
    }

    public function userLogout($login, $pass) {
        $query = "UPDATE users SET token = '".null."' WHERE login = '".$login."' AND password = '".$pass."'";
        $result = $this->connection->query($query);
        return $this->oneRecord($result); // for one record
    }

    public function userLogin($login, $pass, $token) {
        $query = "UPDATE users SET token = '".$token."' WHERE login = '".$login."' AND password = '".$pass."'";
        $result = $this->connection->query($query);
        return $this->oneRecord($result);
    }

    public function getUsers() {
        $query = "SELECT * FROM users";
        $result = $this->connection->query($query);
        return $this->allRecords($result);
    }
    public function getUserByToken($token) {
        $query = "SELECT * FROM users WHERE token = '".$token."'";
        $result = $this->connection->query($query);
        return $this->allRecords($result);
    }

}
