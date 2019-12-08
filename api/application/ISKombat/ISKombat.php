<?php

require_once("Fighter.php");

class ISKombat {

    const STATE = array(
        "STANDING" => "STANDING",
        "CROUCHING" => "CROUCHING",
        "DOWN" => "DOWN",
        "JUMP" => "JUMP",
        "DEAD" => "DEAD"
    );    

    const WIDTH = array(  
        "STANDING" => 1,
        "CROUCHING" => 1.5,
        "DOWN" => 5,
        "JUMP" => 1,
        "DEAD" => 5
    );

    const HEIGHT = array(  
        "STANDING" => 5,
        "CROUCHING" => 2.5,
        "DOWN" => 1,
        "JUMP" => 10,
        "DEAD" => 1
    );

    const HITTYPE = array(
        "HANDKICK" => "HANDKICK",
        "LEGKICK" => "LEGKICK"
    );

    const HITLENGTH = array(
        "HANDKICK" => 5,
        "LEGKICK" => 8
    );

    const HITDAMAGE = array(
        "HANDKICK" => 10,
        "LEGKICK" => 15
    );

    function __construct($db) {
        $this->db = $db;
        //$data->hitTimeStamp = 0;      // to check time of hit before next hit
        //$data->hitType = "block";     //(hand, leg or block)
        //$data->movingSpeed = 10;
        //$data->hit = false;
        //$data->jumpSpeed = 25;        //↓
        //$data->jumpAcceleration = 50; //to count jumping parabola
        /*
        $this->Fighters = array(
                        "1" => new Fighter($data)
                    ); 
        */
    }
    
    private function createFighter($userId, $scene, $direction) {
        $data = new stdClass();
        $data->userId = $userId;
        $data->x = $scene->{ ($direction === "right") ? "left" : "right"};
        $data->y = 50;
        $data->state = ISKombat::STATE["STANDING"];
        $data->width = ISKombat::WIDTH[$data->state];
        $data->height = ISKombat::HEIGHT[$data->state];
        $data->direction = $direction;
        $data->health = 100;
        $this->db->deleteFighterByUserId($userId);
        $this->db->createFighter($data);
        return $this->db->getFighterByUserId($userId);
    }
    
    public function createKombat($userId1, $userId2) {
        $startTimestamp = round(microtime(true) * 1000);
        $scene = new stdClass();
        $scene->left = 0;
        $scene->right = 100;
        $fighter1 = $this->createFighter($userId1, $scene, "right");
        $fighter2 = $this->createFighter($userId2, $scene, "left");
        $this->db->deleteBattleByFighterId($fighter1->id); 
        $this->db->deleteBattleByFighterId($fighter2->id); 
        $this->db->createBattle($fighter1->id, $fighter2->id, $startTimestamp);
        // для бойца добавить:
        // hitTimestamp, hitType, moveTimestamp ??? 
        return true;
    }
    
    public function getBattleByUserId($userId) {
        $fighter = $this->db->getFighterByUserId($userId);
        if ($fighter) {
            return $this->db->getBattle($fighter->id);
        }
        return false;
    }
    
    public function endBattle($battle) { // method, that shows endbattle screen
        
    }
    
    private function isTimeout($battle) {
        if ($battle->timestamp - $battle->duration >= $battle->startTimestamp) {
            return true;
        }
        return false;
    }
    
    private function isUpdate($battle) {
        if ($battle->timestamp - $battle->startTimestamp >= $battle->delta) {
            return true;
        }
        return false;
    }
    // TODO:
    public function updateBattle($userId, $battle) {
        $this->db->updateBattleTimestamp($battle->id, round(microtime(true) * 1000));
        if ($this->isUpdate($battle)) {
            if ($this->isTimeout($battle)) { // ending battle
                $this->db->deleteLobby($userId);
                $this->db->deleteFighterById($battle->id_fighter1);
                $this->db->deleteFighterById($battle->id_fighter2);
                $this->db->deleteBattle($battle->id);
                //$this->endBattle(); // method, that shows endbattle screen
            }
            $fighter1 = $this->db->getFighter($battle->id_fighter1);
            $fighter2 = $this->db->getFighter($battle->id_fighter2);
            return array("scene" => $battle,
                         "fighters" => array($fighter1,
                                             $fighter2
                                            )
                        );
        }
        return false;
    }
    public function deleteFighter($userId) {
        $fighter = $this->db->getFighterByUserId($userId);
        $this->db->deleteFighterByUserId($userId);
        $battle = $this->db->getBattle($fighter->id);
        if ($battle->id_fighter1 == $fighter->id) {
            if (!($this->db->getFighter($battle->id_fighter2))) {
                $this->db->deleteBattle($battle->id);
                $this->db->deleteLobby($userId);
            } 
        }
        else if ($battle->id_fighter2 == $fighter->id) {
            if (!($this->db->getFighter($battle->id_fighter1))) {
                $this->db->deleteBattle($battle->id);
                $this->db->deleteLobby($userId);
            }
        }
        return true;
    }

    public function move($userId, $direction) {
        $fighter = $this->db->getFighterByUserId($userId);
        $battle = $this->getBattleByUserId($userId);
        if ($fighter->state == "STANDING" || $fighter->state == "CROUCHING" || $fighter->state == "JUMPING") {
            switch ($direction) {
                case "right":
                    if ($fighter->x < $battle->right) {
                        $x = $fighter->x + 5;
                        return $this->db->moveFighter($fighter->id, $x, $direction);
                    }
                break;
                case "left":
                    if ($fighter->x > $battle->left) {
                        $x = $fighter->x - 5;
                        return $this->db->moveFighter($fighter->id, $x, $direction);
                    }
                break;    
        }
        return false;
        }
    }
    
    /*
    public function setState($id = null, $state = null) {
        if (getFighterById($id)) {
            getFighterById($id)->state = ISKombat::STATE[$state];
            getFighterById($id)->width = ISKombat::WIDTH[getFighterById($id)->state];
            getFighterById($id)->height = ISKombat::HEIGHT[getFighterById($id)->state];
            return true;
        }
        return false;
    }
    
    private function hitCheck($initiatorId, $enemyId) {
        switch (getFighterById($initiatorId)->direction) {

            case "right":
                if (getFighterById($initiatorId)->x + ISKombat::HITLENGTH[$hitType] >= getFighterById($enemyId)->x
                    && getFighterById($initiatorId)->x + ISKombat::HITLENGTH[$hitType] <= getFighterById($enemyId)->x + getFighterById($enemyId)->width) {
                    return true;
                }else
                return false;
            break;

            case "left":
                if (getFighterById($initiatorId)->x - ISKombat::HITLENGTH[$hitType] <= getFighterById($enemyId)->x + getFighterById($enemyId)->width
                    && getFighterById($initiatorId)->x - ISKombat::HITLENGTH[$hitType] >= getFighterById($enemyId)->x) {
                    return true;
                }else
                return false;
            break;   
        }
    }
    
    public function hit($id = null, $hitType = null) {
        if (getFighterById($id) && (getFighterById($id)->state == "STANDING" || getFighterById($id)->state == "CROUCHING")) {
            getFighterById($id)->hitType = ISKombat::HITTYPE[$hitType];
            getFighterById($id)->hitTimeStamp = date("U"); // returns seconds since start of Unix epoch (1 Jan, 1970, 00:00:00 GMT)
            switch ($hitType) {
                
                case "HANDKICK":
                    if (hitCheck($id, $enemyId)) {
                        getFighterById($enemyId)->health -= ISKombat::HITDAMAGE[$hitType]; //need to understand, how to determine enemy's id
                    }
                break;

                case "LEGKICK":
                    if (hitCheck($id, $enemyId)) {
                        getFighterById($enemyId)->health -= ISKombat::HITDAMAGE[$hitType];
                    }
                break;
            }
            return true;
        }
        return false;
    }
    */
}
