<?php
Class User {
    public function login($login, $pass) {
        //get user
        if ($login && $pass) {
            $token = md5($login . $pass . strval(rand()));
            return array("token" => $token);
        }
        return false;
    }

    public function logout() {

    }

    public function registration() {

    }
}