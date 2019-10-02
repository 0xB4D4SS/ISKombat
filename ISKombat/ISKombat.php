<?php
require_once("Fighter.php");

class ISKombat {
    const STATE = array(
        "STANDING"  => "STANDING",
        "CROUCHING" => "CROUCHING",
        "DOWN"      => "DOWN",
        "JUMP"      => "JUMP"
    );    

    const WIDTH = array(  
        "STANDING"  => 1,
        "CROUCHING" => 1.5,
        "DOWN"      => 5,
        "JUMP"      => 1
    );

    const HEIGHT = array(  
        "STANDING"  => 5,
        "CROUCHING" => 2.5,
        "DOWN"      => 1,
        "JUMP"      => 10
    );
    function __construct() {
        //fighter
        $data = new stdClass();
        $data->id = 0;
        $data->x = 0;
        $data->y = 0;
        $data->state = ISKombat::STATE["STANDING"];
        $data->width = ISKombat::WIDTH[$data->state];
        $data->height = ISKombat::HEIGHT[$data->state];
        $data->hit = false;
        $data->hitTimeStamp = 0;      // to check time of hit before next hit
        $data->hitType = "block";     //(hand, leg or block)
        $data->health = 100;
        $data->direction = "right";
        $data->movingSpeed = 10;      //?
        $data->jumpSpeed = 25;        //↓
        $data->jumpAcceleration = 50; //to count jumping parabola
        $this->Fighters = array(
                        "Fighter1" => new Fighter($data)
                    );
        //scene
        $this->scene = new stdClass();
        $this->scene->left = 0;
        $this->scene->right = 100;

    }
    
    // moving fighter
    public function move($id, $fighter, $direction) {
        if ($this->Fighters[$fighter]->id == $id) {
            switch ($direction) {
                case "right":
                    if ($this->Fighters[$fighter]->x < $this->scene->right) {
                        $this->Fighters[$fighter]->x+=$this->Fighters[$fighter]->movingSpeed;
                        $this->Fighters[$fighter]->direction = "right";
                        return true;
                    }
                    return false;
                    break;

                case "left":
                    if ($this->Fighters[$fighter]->x > $this->scene->left) {
                        $this->Fighters[$fighter]->x-=$this->Fighters[$fighter]->movingSpeed;
                        $this->Fighters[$fighter]->direction = "left";
                        return true;
                    }
                    return false;
                    break;
            }
        }
    }
    //
    public function setState($id, $fighter, $state) {
        if ($this->Fighters[$fighter]->id == $id) {
            $this->Fighters[$fighter]->state = ISKombat::STATE[$state];
            //пока всё-таки изменим габариты
            $this->Fighters[$fighter]->width = ISKombat::WIDTH[$state];
            $this->Fighters[$fighter]->height = ISKombat::HEIGHT[$state];
            return true;
        }
        return false;
    }
    //
    public function hit($id, $fighter, $hitType) {
        if ($this->Fighters[$fighter]->id == $id) {
            $this->Fighters[$fighter]->hitType = $hitType;
            return true;
        }
        return false;
    }
    
}
