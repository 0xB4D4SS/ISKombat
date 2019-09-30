<?php
// need to make move, setState and hit functions

require_once("Fighter.php");

class ISKombat {
    const STATE = array(
        "STANDING" => "STANDING",
        "CROUCHING" => "CROUCHING",
        "DOWN" => "DOWN",
        "JUMP" => "JUMP"
    );    

    const WIDTH = array(  
        "STANDING" => 1,
        "CROUCHING" => 1.5,
        "DOWN" => 5,
        "JUMP" => 1
    );

    const HEIGHT = array(  
        "STANDING" => 5,
        "CROUCHING" => 2.5,
        "DOWN" => 1,
        "JUMP" => 10
    );
    function __construct() {
        //fighter
        $data = new stdClass();
        $data->{"id"} = 0;
        $data->{"x"} = 0;
        $data->{"y"} = 0;
        $data->{"state"} = ISKombat::STATE["STANDING"];
        $data->{"width"} = ISKombat::WIDTH[$this->state];  // how to put values here?
        $data->{"height"} = ISKombat::HEIGHT[$this->state];
        //$data->{"hit"} = false;
        //$data->{"hitTimeStamp"} = 0;      // to check time of hit before next hit
        $data->{"hitType"} = "block";     //(hand, leg or block)
        //$data->{"health"} = 100;
        //$data->{"direction"} = "right";
        //$data->{"movingSpeed"} = 10;      //?
        //$data->{"jumpSpeed"} = 25;        //↓
        //$data->{"jumpAcceleration"} = 50; //to count jumping parabola
        $this->Fighters = array(
                        "Fighter1" => new Fighter($data, $this->state)
                    );
        //scene
        $this->scene = new stdClass();
        $this->scene->left = 0;
        $this->scene->right = 100;

    }
    
    // moving fighter
    public function move($id, $direction) {
        if ($this->id == $id) {
            switch ($direction) {
                case "right":
                    if ($this->x < $this->scene->right) return true;
                    return false;
                    break;

                case "left":
                    if ($this->x > $this->scene->left) return true;
                    return false;
                    break;
            }
        }
    }
    //
    public function setState($id, $state) {
        if ($this->id == $id) {
            $this->state = ISKombat::STATE[$state]; // should i change width and height too?
            return true;
        }
        return false;
    }
    //
    public function hit($id, $hitType) {
        if ($this->id == $id) {
            $this->hitType = $hitType;
            return true;
        }
        return false;
    }
    
}