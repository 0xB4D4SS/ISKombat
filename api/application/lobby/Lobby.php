<?php
class Lobby {
    function __construct($db) {
        $this->db = $db;
    } 

    public function getLobbyUsers($userId) {
        return $this->db->getLobbyUsers($userId);
    }

    public function newChallenge($userId1, $iserId2) {
        $this->db->deleteOldUserChallenge($userId1);
        $this->db->newChallenge($userId1, $userId2);
        return true;
    }
}