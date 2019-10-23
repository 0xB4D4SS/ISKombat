<?php
Class User {

    function __construct($db) {
        $this->db = $db;
    }
    
    public function login($login, $pass) {
        //get user
        $user = $this->db->getUserByLoginPass($login, $pass);
        if ($user) {
            $token = md5($login . $pass . strval(rand()));
            $user = $this->db->userLogin($login, $pass, $token);
            return array("token" => $token);
        }
        return false;
    }

    public function logout($login, $pass) {
        $user = $this->db->getUserByLoginPass($login, $pass);
        if ($user) {
            $this->user->token = null;
        }
        $user = $this->db->userLogout($login, $pass);
    }

    public function registration() {

    }
}