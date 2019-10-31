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
        "LEGKICK" => "LEGKICK",
        "JUMPKICK" => "JUMPKICK",
        "CROUCHKICK" => "CROUCHKICK"
    );

    function __construct() {
        //fighter
        $data = new stdClass();
        $data->{"id"} = 0;
        $data->{"x"} = 0;
        $data->{"y"} = 0;
        $data->{"state"} = ISKombat::STATE["STANDING"];
        $data->{"width"} = ISKombat::WIDTH[$data->state];
        $data->{"height"} = ISKombat::HEIGHT[$data->state];
        $data->{"hitTimeStamp"} = 0;      // to check time of hit before next hit
        $data->{"hitType"} = "block";     //(hand, leg or block)
        $data->{"movingSpeed"} = 10;
        $data->{"direction"} = "right";
        $data->{"health"} = 100;
        //$data->{"hit"} = false;
        //$data->{"jumpSpeed"} = 25;        //↓
        //$data->{"jumpAcceleration"} = 50; //to count jumping parabola

        $this->Fighters = array(
                        "Fighter1" => new Fighter($data)
                    );
                    
        //scene
        $this->scene = new stdClass();
        $this->scene->left = 0;
        $this->scene->right = 100;

    }
    
    // TODO: нужен объект со связями id->fighter или типа того (Часть авторизации?),
    //       а пока мучаем Fighter1.
    public function move($id = null, $direction = null) {
        if ($this->Fighters["Fighter1"]->id == $id) {
            switch ($direction) {
                case "right":
                    if ($this->Fighters["Fighter1"]->x < $this->scene->right) {
                        $this->Fighters["Fighter1"]->x += $this->Fighters["Fighter1"]->movingSpeed;
                        $this->Fighters["Fighter1"]->direction = $direction;
                        return true;
                    }
                case "left":
                    if ($this->Fighters["Fighter1"]->x > $this->scene->left) {
                        $this->Fighters["Fighter1"]->x -= $this->Fighters["Fighter1"]->movingSpeed;
                        $this->Fighters["Fighter1"]->direction = $direction;
                        return true;
                    }
            }
        }
        return false;
    }
    //
    public function setState($id = null, $state = null) {
        if ($this->Fighters["Fighter1"]->id == $id) {
            $this->Fighters["Fighter1"]->state = ISKombat::STATE[$state];
            $this->Fighters["Fighter1"]->width = ISKombat::WIDTH[$this->Fighters["Fighter1"]->state];
            $this->Fighters["Fighter1"]->height = ISKombat::HEIGHT[$this->Fighters["Fighter1"]->state];
            return true;
        }
        return false;
    }
    //
    public function hit($id = null, $hitType = null) {
        if ($this->Fighters["Fighter1"]->id == $id) {
            $this->Fighters["Fighter1"]->hitType = ISKombat::HITTYPE[$hitType];
            $this->Fighters["Fighter1"]->hitTimeStamp = date("U"); // returns seconds since start of Unix epoch (1 Jan, 1970, 00:00:00 GMT)
            return true;
        }
        return false;
    }
    
}
