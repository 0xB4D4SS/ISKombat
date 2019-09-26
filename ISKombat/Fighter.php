<?php
//need to deal with width and height values

class Fighter {
    function __construct($data, $STATE) {
        //$this->width = WIDTH[$this->state];
        //$this->height = HEIGHT[$this->state];
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
            print_r($key."=".$value." , ");
        }
    }
}