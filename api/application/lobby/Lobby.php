<?php
class Lobby {
    function __construct($db) {
        $this->db = $db;
    } 

    public function getLobbyUsers($userId) {
        return $this->db->getLobbyUsers($userId);
    }

    public function newChallenge($userId1, $userId2) {
        $this->db->deleteOldUserChallenge($userId1);
        $this->db->newChallenge($userId1, $userId2);
        return true;
    }

    public function isChallenge($userId) {
       return $this->db->isChallenge($userId);
    }

    public function isChallengeAccepted($userId) {
        return $this->db->isChallengeAccepted($userId);
    }

    public function acceptChallenge($userId, $answer) {
        $this->db->acceptChallenge($userId, $answer);
        return true;
    }
}