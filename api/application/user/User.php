<?php
Class User {

    function __construct($db) {
        $this->db = $db;
    }
    
    private function createToken($login , $pass) {
        return md5($login . $pass . strval(rand()));
    }

    public function getUserByToken($token) {
        return $this->db->getUserByToken($token);
    }
    
    public function login($login, $pass) {
        //get user
        $user = $this->db->getUserByLoginPass($login, $pass);
        if ($user) {
            $token = $this->createToken($login , $pass);
            $user = $this->db->updateUserToken($user->id, $token);
            return array("token" => $token);
        }
        return false;
    }

    public function logout($token) {
        $user = $this->db->getUserByToken($token);
        if ($user) {
           return $this->db->updateUserToken($user->id, null);
        }
        return false;
    }

    public function register($login, $pass) {
        $token = $this->createToken($login, $pass);
        $user = $this->db->registerUser($login, $pass, $token);
        return array("token" => $token);
    }

    

}