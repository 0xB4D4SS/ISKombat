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
        return true;
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
                            (b.id_fighter1 = f.id OR 
                             b.id_fighter2 = f.id)) AND
                    id <> '".$userId."'";
        $result = $this->connection->query($query);
        return $this->allRecords($result);
    }

    public function getUserByToken($token) {
        $query = "SELECT * FROM users WHERE token = '".$token."'";
        $result = $this->connection->query($query);
        return $this->oneRecord($result);
    }

    public function deleteOldUserChallenge($userId) {
        $query = "DELETE FROM lobby WHERE id_user1 = '".$userId."'";
        $result = $this->connection->query($query);
        return true;
    }

    public function newChallenge($userId1, $userId2) {
        $query = "INSERT INTO lobby (id_user1, id_user2, status) VALUES ('".$userId1."', '".$userId2."', 'open')";
        $result = $this->connection->query($query);
        return true;
    }

    public function isChallenge($userId) {
        $query = "SELECT * FROM lobby WHERE id_user2 = '".$userId."'";
        $result = $this->connection->query($query);
        return $this->allRecords($result);
    }

    public function isChallengeAccepted($userId) {
        $query = "SELECT * FROM lobby WHERE id_user1 = '".$userId."' AND status = 'game'";
        $result = $this->connection->query($query);
        return $this->oneRecord($result);
    }

    public function acceptChallenge($userId, $answer) {
        $query = "UPDATE lobby SET status = '".$answer."' WHERE id_user2 = '".$userId."'";
        $result = $this->connection->query($query);
        return true;
    }

}
