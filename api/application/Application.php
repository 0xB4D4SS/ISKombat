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
            $params['id'],              // Notice: Undefined index: id in C:\openserver\OSPanel\domains\localhost\api\application\Application.php on line 16
            $params['direction']        // Notice: Undefined index: direction in C:\openserver\OSPanel\domains\localhost\api\application\Application.php on line 17
        );
    }

    public function hit($params) {
        return $this->iskombat->hit(
            $params['id'],              // Notice: Undefined index: id in C:\openserver\OSPanel\domains\localhost\api\application\Application.php on line 23
            $params['hitType']          // Notice: Undefined index: hitType in C:\openserver\OSPanel\domains\localhost\api\application\Application.php on line 24
        );
    }
    public function setState($params) {
        return $this->iskombat->setState(
            $params['id'],                  // Notice: Undefined index: id in C:\openserver\OSPanel\domains\localhost\api\application\Application.php on line 29
            $params['state']                // Notice: Undefined index: state in C:\openserver\OSPanel\domains\localhost\api\application\Application.php on line 30
        );
    }
}
