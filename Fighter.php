<?php
class Fighter {
    function __construct($data, $STATE) {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
            print_r($key."=".$value." , ");
        }
        $this->width = $WIDTH[$this->state];
        $this->height = $HEIGHT[$this->state];
}