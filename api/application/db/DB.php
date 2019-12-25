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

    public function getMillisTime() {
        return round(microtime(true) * 1000);
    }

    /* USER */
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
    
    public function getUserByToken($token) {
        $query = "SELECT * FROM users WHERE token = '".$token."'";
        $result = $this->connection->query($query);
        return $this->oneRecord($result);
    }

    public function getUserById($Id) {
        $query = "SELECT * FROM users WHERE id = '".$Id."'";
        $result = $this->connection->query($query);
        return $this->oneRecord($result);
    }

    public function updateUserToken($Id, $token) {
        $query = "UPDATE users SET token = '".$token."' WHERE id = '".$Id."'";
        $result = $this->connection->query($query);
        return true;
    }
    /* LOBBY */
    public function newChallenge($userId1, $userId2) {
        $query = "INSERT INTO lobby (id_user1, id_user2, status) VALUES ('".$userId1."', '".$userId2."', 'open')";
        $result = $this->connection->query($query);
        return true;
    }

    public function isChallenge($userId) {
        $query = "SELECT * 
                  FROM lobby 
                  WHERE id_user2 = ".$userId." AND status = 'open' OR id_user1 = '".$userId."' AND status = 'open'";
        $result = $this->connection->query($query);
        return $this->oneRecord($result);
    }

    public function isChallengeAccepted($userId) {
        $query = "SELECT * FROM lobby WHERE id_user1 = '".$userId."' AND status = 'game'";
        $result = $this->connection->query($query);
        return $this->oneRecord($result);
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
    
    public function getLobbyInGame($userId2){
        $query = "SELECT * FROM lobby WHERE id_user2 = '".$userId2."' AND status = 'game'";
        $result = $this->connection->query($query);
        return $this->oneRecord($result); 
    }

    public function acceptChallenge($userId2, $answer) {
        $query = "UPDATE lobby SET status = '".$answer."' WHERE id_user2 = '".$userId2."'";
        $result = $this->connection->query($query);
        return true;
    }

    public function deleteOldUserChallenge($userId) {
        $query = "DELETE FROM lobby WHERE id_user1 = '".$userId."'";
        $result = $this->connection->query($query);
        return true;
    }

    public function deleteLobby($userId) {
        $query = "DELETE FROM lobby WHERE id_user1 = '".$userId."' OR id_user2 = '".$userId."'";
        $result = $this->connection->query($query);
        return true;
    }
    /* FIGHTER */
    public function createFighter($data) {
        $query = "INSERT INTO fighters 
                  (user_id, x, y, width, height, state, direction, health)
                  VALUES (".$data->userId.", 
                          ".$data->x.", 
                          ".$data->y.", 
                          ".$data->width.",
                          ".$data->height.",
                         '".$data->state."',
                         '".$data->direction."', 
                          ".$data->health.")";
        $result = $this->connection->query($query);
        return true;
    }

    public function getFighter($fighterId) {
        $query = "SELECT * FROM fighters WHERE id = ".$fighterId."";
        $result = $this->connection->query($query);
        return $this->oneRecord($result);
    }

    public function getFighterByUserId($userId) {
        $query = "SELECT * FROM fighters WHERE user_id = ".$userId."";
        $result = $this->connection->query($query);
        return $this->oneRecord($result);
    }

    public function getStateDuration($stateName) {
        $query = "SELECT duration FROM state WHERE name = '".$stateName."'";
        $result = $this->connection->query($query);
        return $this->oneRecord($result);
    }

    public function setFighterState($fighterId, $stateName) {
        $stateTimestamp = $this->getMillisTime();
        $query = "UPDATE fighters 
                  SET state = '".$stateName."', stateTimestamp = ".$stateTimestamp." 
                  WHERE id = ".$fighterId."";
        $result = $this->connection->query($query);
        return true;
    }

    public function moveFighter($fighterId, $x, $direction) {
        $query = "UPDATE fighters SET x = '".$x."', direction = '".$direction."' WHERE id = '".$fighterId."'";
        $result = $this->connection->query($query);
        return true;
    }
    
    public function hitFighter($targetId, $health) {
        $query = "UPDATE fighters SET health = '".$health."' WHERE id = '".$targetId."'";
        $result = $this->connection->query($query);
        return true;
    }

    public function deleteFighterById($fighterId) {
        $query = "DELETE FROM fighters WHERE id = ".$fighterId."";
        $result = $this->connection->query($query);
        return true;
    }

    public function deleteFighterByUserId($userId) {
        $query = "DELETE FROM fighters WHERE user_id = ".$userId."";
        $result = $this->connection->query($query);
        return true;
    }
    /* BATTLE */
    public function createBattle($fighterId1, $fighterId2) {
        $startTimestamp = $this->getMillisTime();
        $query = "INSERT INTO battles
                  (id_fighter1, id_fighter2, startTimestamp ) 
                  VALUES (".$fighterId1.",
                         ".$fighterId2.",
                         ".$startTimestamp.")";
        $result = $this->connection->query($query);
        return true;
    }

    public function getBattle($fighterId) {
        $query = "SELECT * FROM battles WHERE id_fighter1 = $fighterId OR id_fighter2 = $fighterId";
        $result = $this->connection->query($query);
        return $this->oneRecord($result);
    }

    public function updateBattleTimestamp($battleId) {
        $newTimestamp = $this->getMillisTime();
        $query = "UPDATE battles SET timestamp = '".$newTimestamp."' WHERE id = '".$battleId."'";
        $result = $this->connection->query($query);
        return true;
    }

    public function setBattleStatus($battleId, $status) {
        $query = "UPDATE battles SET status = '".$status."' WHERE id = '".$battleId."'";
        $result = $this->connection->query($query);
        return true;
    }

    public function deleteBattle($battleId) {
        $query = "DELETE FROM battles WHERE id = ".$battleId."";
        $result = $this->connection->query($query);
        return true;
    }

    public function deleteBattleByFighterId($fighterId) {
        $query = "DELETE FROM battles
                  WHERE id_fighter1 = $fighterId
                  OR id_fighter2 = $fighterId";
        $result = $this->connection->query($query);
        return true;
    }

    public function addResult($winner_id, $loser_id) {
        $query = "INSERT INTO results (winner_id, loser_id) VALUES ('".$winner_id."', '".$loser_id."')";
        $result = $this->connection->query($query);
        return true;
    }

    public function getResult($winner_id, $loser_id) {
        $query = "SELECT * 
                  FROM results 
                  WHERE winner_id = '".$winner_id."' AND loser_id = '".$loser_id."' 
                  ORDER BY DESC LIMIT 1";
        $result = $this->connection->query($query);
        return $this->oneRecord($result);
    }

}
