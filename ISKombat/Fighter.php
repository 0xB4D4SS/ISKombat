<?php
//need to deal with width and height values

class Fighter {
    function __construct($data) {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
            print_r($key."=".$value." , ");
        }
        echo "\n"; // перевод строки в стандартном выводе (консоли, документе html)
    }
}
