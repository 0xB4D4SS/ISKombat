<?php
class Lobby {
    function __construct($db) {
        $this->db = $db;
    } 

    public function getLobbyUsers($userId) {
        return $this->db->getLobbyUsers($userId);
    }

    public function isUserChallenged($userId) {
        $result = $this->db->isChallenge($userId);
        if ($result->id) {
          return true;  
        }
        return false;
    }

    public function newChallenge($userId1, $userId2) { 
        if ($this->db->isChallenge($userId2)) {
            return true;
        }
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

    public function acceptChallenge($userId2, $answer) {
        return $this->db->acceptChallenge($userId2, ($answer === 'yes') ? 'game' : 'close');
        
    }

    public function getLobbyInGame($userId2) {
        return $this->db->getLobbyInGame($userId2);
    }
}