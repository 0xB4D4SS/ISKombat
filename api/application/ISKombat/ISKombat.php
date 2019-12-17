<?php

require_once("Fighter.php");

class ISKombat {

    const STATE = array(
        "STANDING" => "STANDING",
        "CROUCHING" => "CROUCHING",
        "DOWN" => "DOWN",
        "JUMP" => "JUMP",
        "DEAD" => "DEAD",
        "HITARM" => "HITARM",
        "HITLEG" => "HITLEG"
    );    

    const WIDTH = array(  
        "STANDING" => 300,
        "CROUCHING" => 300,
        "DOWN" => 400,
        "JUMP" => 300,
        "DEAD" => 400,
        "HITARM" => 500,
        "HITLEG" => 600
    );

    const HEIGHT = array(  
        "STANDING" => 150,
        "CROUCHING" => 75,
        "DOWN" => 50,
        "JUMP" => 75,
        "DEAD" => 50,
        "HITARM" => 150,
        "HITLEG" => 150
    );
    
    function __construct($db) {
        $this->db = $db;
        //$data->hitTimeStamp = 0;      // to check time of hit before next hit
        //$data->hitType = "block";     //(arm, leg or block)
        //$data->jumpSpeed = 25;        //↓
        //$data->jumpAcceleration = 50; //to count jumping parabola
    }

    public function getBattleByUserId($userId) {
        $fighter = $this->db->getFighterByUserId($userId);
        if ($fighter) {
            return $this->db->getBattle($fighter->id);
        }
        return false;
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

    private function isFighterDead($fighterId) {
        if ($this->db->getFighter($fighterId)->health < 1) {
            return true;
        }
        return false;
    }

    private function isStateChangeable($fighter) { // should i create so many variables, or better just call properties of objects instead?
        $state = $fighter->state;
        $stateTimestamp = $fighter->stateTimestamp;
        $stateDuration = $this->db->getStateDuration($state)->duration;
        $currentTimestamp = $this->db->getMillisTime();
        if ($currentTimestamp - $stateTimestamp >= $stateDuration) {
            return true;
        }
        return false;
    }
    
    public function createKombat($userId1, $userId2) {
        $scene = new stdClass();
        $scene->left = 50;
        $scene->right = 1100;
        $fighter1 = $this->createFighter($userId1, $scene, "right");
        $fighter2 = $this->createFighter($userId2, $scene, "left");
        $this->db->deleteBattleByFighterId($fighter1->id); 
        $this->db->deleteBattleByFighterId($fighter2->id); 
        $this->db->createBattle($fighter1->id, $fighter2->id);
        return true;
    }
    
    public function endBattle($battle, $userId) {
        $this->db->deleteLobby($userId);
        $this->db->deleteFighterById($battle->id_fighter1);
        $this->db->deleteFighterById($battle->id_fighter2);
        $this->db->deleteBattle($battle->id);
        // call endbattle screen ( maybe at client side )
        return true;
    }
    
    public function updateBattle($userId, $battle) {
        $this->db->updateBattleTimestamp($battle->id);
        if ($this->isUpdate($battle)) {
            if ($this->isTimeout($battle)) {
                return $this->endBattle($battle, $userId);
            }
            $fighter = $this->db->getFighterByUserId($userId);
            if ($this->isStateChangeable($fighter)) {
                switch ($fighter->state) {
                    case "DOWN":
                    case "HITARM":
                    case "HITLEG":
                    case "JUMP":
                        $this->db->setFighterState($fighter->id, "STANDING");
                    break;
                }
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

    private function createFighter($userId, $scene, $direction) {
        $data = new stdClass();
        $data->userId = $userId;
        $data->x = $scene->{ ($direction === "right") ? "left" : "right"};
        $data->y = 400;
        $data->state = ISKombat::STATE["STANDING"];
        $data->width = ISKombat::WIDTH[$data->state];
        $data->height = ISKombat::HEIGHT[$data->state];
        $data->direction = $direction;
        $data->health = 100;
        $this->db->deleteFighterByUserId($userId);
        $this->db->createFighter($data);
        return $this->db->getFighterByUserId($userId);
    }

    // TODO:
    /*
    maybe create method, that completely updates fighter record in DB, depending on parameters that are given to this method?
    like: public function updateFighter($fighter, 
                                        $x = $fighter->x, 
                                        $y = $fighter->y, 
                                        $width = $fighter->width, 
                                        $height = $fighter->height, 
                                        $health = $fighter->health) {}

    public function updateFighter($fighter) {
    // set width, height and health
        switch ($fighter->state) {
            case "STANDING":

            case "CROUCHING":

            case "DOWN":

            case "JUMP":

            case "DEAD":

            case "HITARM":
            
            case "HITLEG":
                
            break;
        }
        return $this->db->updateFighter($fighter);
    }
    */
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

    private function hitCheck($fighter, $target) {
        print_r($fighter);
        print_r($target);
        switch ($fighter->direction) {
            case "right":
                if (($fighter->x + $fighter->width >= $target->x) && ($fighter->x <= $target->x + $target->width)) {
                    return true;  
                }
            break;
            case "left":
                if (($fighter->x + $fighter->width <= $target->x) && ($fighter->x >= $target->x + $target->width)) {
                    return true;  
                }
            break;
        }
        return false;
    }
    //TODO: ( health doesn't decrease somewhy )
    public function hit($userId, $hitType) {
        $fighter = $this->db->getFighterByUserId($userId);
        $battle = $this->getBattleByUserId($userId);

        if ($this->isStateChangeable($fighter)) {
            $this->db->setFighterState($fighter->id, $hitType);
        }
        if ($fighter->id == $battle->id_fighter1) {
            $target = $this->db->getFighter($battle->id_fighter2);
        }
        else {
            $target = $this->db->getFighter($battle->id_fighter1);
        }
        if ($this->hitCheck($fighter, $target)) {
            if ($hitType == "HITARM") {
                $healthDecrease = 5;
            }
            if ($hitType == "HITLEG") {
                $healthDecrease = 10;
            }
            $newTargetHealth = $target->health - $healthDecrease;
            $this->db->hitFighter($target->id, $newTargetHealth);
            return true;
        }
        //$hitTimestamp = $battle->timestamp;
        return false;
    }
    
}
