<?php
//need to deal with width and height values

class Fighter {
    function __construct($data, $STATE) {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
            print_r($key."=".$value." , ");
        }
        //$this->width = ISKombat::WIDTH[$this->$STATE->state];
        //$this->height = ISKombat::HEIGHT[$this->$STATE->state];
    }
}