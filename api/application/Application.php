<?php
//responsing to client
require_once('ISKombat.php');
class Application {

    function __construct() {
        $this->iskombat = new ISKombat();
    } 
    
    public function test($params) {
        return true;
    }

    public function move($params) {
        return $this->iskombat->move(
            $params['id'],
            $params['direction']
        );
    }
    
    
}
