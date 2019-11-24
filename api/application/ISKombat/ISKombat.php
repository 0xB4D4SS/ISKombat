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
    //
    private function getFighterById($id) {
        for ($i=0; $i < count($this->Fighters); $i++) {
            if ($this->Fighters[$i]->id == $id) return $this->Fighters[$i];
            else return false;
        }
    }
    // создать бой
    public function createKombat($userId1, $userId2) {
        //scene
        $this->scene = new stdClass();
        $this->scene->left = 0;
        $this->scene->right = 100;
        //fighter1 initialization
        $fighter1Data = new stdClass();
        $fighter1Data->userId1 = $userId1;
        $fighter1Data->x = 0;
        $fighter1Data->y = 0;
        $fighter1Data->state = ISKombat::STATE["STANDING"];
        $fighter1Data->width = ISKombat::WIDTH[$fighter1Data->state];
        $fighter1Data->height = ISKombat::HEIGHT[$fighter1Data->state];
        $fighter1Data->direction = "right";
        $fighter1Data->health = 100;
        $this->db->deleteFighterByUserId($userId1);
        $this->db->createFighter1($fighter1Data);
        //fighter2 initialization
        $fighter2Data = new stdClass();
        $fighter2Data->userId2 = $userId2;
        $fighter2Data->x = 100;
        $fighter2Data->y = 0;
        $fighter2Data->state = ISKombat::STATE["STANDING"];
        $fighter2Data->width = ISKombat::WIDTH[$fighter2Data->state];
        $fighter2Data->height = ISKombat::HEIGHT[$fighter2Data->state];
        $fighter2Data->direction = "left";
        $fighter2Data->health = 100;
        $this->db->deleteFighterByUserId($userId2);
        $this->db->createFighter2($fighter2Data);
        $fighter1 = $this->db->getFighterByUserId($userId1);
        $fighter2 = $this->db->getFighterByUserId($userId2);
        $timestamp = date("U"); 
        $this->db->createBattle($fighter1->id, $fighter2->id, $timestamp); //which status to push in DB?
        // для бойца добавить:
        // hitTimestamp, hitType, moveTimestamp ??? 
    }
    //
    public function getFighter($fighterId){
        return $this->db->getFighter($fighterId);
    }

    public function getBattle($fighterId){
        return $this->db->getBattle($fighterId);
    }

    public function deleteBattle($battleId){
        return $this->db->deleteBattle($battleId);
    }

    public function deleteFighter($userId) {
        $fighter = $this->db->getFighterByUserId($userId);
        $this->db->deleteFighterByUserId($userId);
        $battle = $this->getBattle($fighter->id);
        if ($battle->id_fighter1 == $fighter->id) {
            if (!($this->getFighter($battle->id_fighter2))) {
                $this->deleteBattle($battle->id);
                $this->db->deleteLobby($userId);
            } 
        }
        if ($battle->id_fighter2 == $fighter->id) {
            if (!($this->getFighter($battle->id_fighter1))) {
                $this->deleteBattle($battle->id);
                $this->db->deleteLobby($userId);
            }
        }
        return true;
    }

    public function move($id = null, $direction = null) {
        if (getFighterById($id) && (getFighterById($id)->state == "STANDING" || getFighterById($id)->state == "CROUCHING")) {
            switch ($direction) {
                case "right":
                    if (getFighterById($id)->x < $this->scene->right) {
                        getFighterById($id)->x += getFighterById($id)->movingSpeed;
                        getFighterById($id)->direction = $direction;
                        return true;
                    }
                break;
                case "left":
                    if (getFighterById($id)->x > $this->scene->left) {
                        getFighterById($id)->x -= getFighterById($id)->movingSpeed;
                        getFighterById($id)->direction = $direction;
                        return true;
                    }
                break;
            }
        }
        return false;
    }
    //
    public function setState($id = null, $state = null) {
        if (getFighterById($id)) {
            getFighterById($id)->state = ISKombat::STATE[$state];
            getFighterById($id)->width = ISKombat::WIDTH[getFighterById($id)->state];
            getFighterById($id)->height = ISKombat::HEIGHT[getFighterById($id)->state];
            return true;
        }
        return false;
    }
    //
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
    //
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
    
}
