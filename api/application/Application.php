<?php
//responsing to client
class Application {
    public function test($params) {
        return true;
    }
    
    function __construct() {
        $this->iskombat = new ISKombat();
    }
}