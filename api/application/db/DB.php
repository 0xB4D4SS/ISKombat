<?php
class DB {
    function __construct() {
        $servername = "127.0.0.1";
        $username = "root";
        $password = "";
        $dbname = "is_kombat";
        $this->connection = new mysqli($servername, $username, $password, $dbname);
        if ($this->$connection->connect_error) {
            die("Connection failed".$connection->connect_error);
        }
    }

    function __destruct() {
        $this->connection->close();
    }

    private function oneRecord($result) {
        while ($obj = $result->fetch_object()) {
            return $obj; // for one record
        }
        return null;
    }

    private function allRecords($result) {
        $res = array();
        while ($obj = $result->fetch_object()) { // for many records
            $res[] = $obj;
        }
        return $res;
    }
    
    public function registerUser($login, $pass, $token) {
        $query = "INSERT INTO users (login, password, token) VALUES ( '".$login."', '".$pass."', '".$token."')";
        $result = $this->connection->query($query);
        return $this->oneRecord($result); 
    }

    public function getUserByLoginPass($login, $pass) {
        $query = "SELECT * FROM users WHERE login = '".$login."' AND password = '".$pass."'";
        $result = $this->connection->query($query);
        return $this->oneRecord($result); 
    }
    public function updateUserToken($id, $token) {
        $query = "UPDATE users SET token = '".$token."' WHERE id = '".$id."'";
        $result = $this->connection->query($query);
        return true;
    }

    public function getLobbyUsers($userId) {
        $query = "SELECT id, login
                FROM users
                WHERE 
                    token <> '' AND 
                    id NOT IN 
                        (SELECT 
                            f.user_id 
                        FROM 
                            fighters AS f, 
                            battles AS b
                        WHERE 
                            b.status='open' AND
                            (b.fighter_id1 = f.id OR 
                             b.fighter_id2 = f.id)) AND
                    id <>" . $userId;
        $result = $this->connection->query($query);
        return $this->allRecords($result);
    }

    public function getUserByToken($token) {
        $query = "SELECT * FROM users WHERE token = '".$token."'";
        $result = $this->connection->query($query);
        return $this->oneRecord($result);
    }

}
