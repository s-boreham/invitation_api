<?php
# src/Invitations/InvitationDatabase.php

namespace App\Invitations;

use \App\App\AbstractDatabase;
use PDO;

class InvitationDatabase extends AbstractDatabase {
            
    // get all sent
    public function getSentInvitations($userID = null, $sort = "DATE_ASC"){
    
        $table = "invitations";
        
        if(!empty($this->pdo)){
            $statement = $this->pdo->prepare("SELECT * FROM `$table` WHERE `senderID` = :userID ORDER BY `created` DESC");
            $statement->execute([
                "userID" => $userID
            ]);
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        return $data;       
    }
    
    // get all received
    public function getReceivedInvitations($userID, $invitationState = "ALL", $sort = "DATE_ASC"){

        $table = "invitations";
        
        if(!empty($this->pdo)){
            $statement = $this->pdo->prepare("SELECT * FROM `$table` WHERE `invitedID` = :userID ORDER BY `created` DESC");
            
            $statement->execute([
                "userID" => $userID
            ]);
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        return $data;          
    }
    
    // get
    public function getInvitationByID($uid){
        
        $table = "invitations";
        
        if(!empty($this->pdo)){
            $statement = $this->pdo->prepare("SELECT * FROM `$table` WHERE `uid` = :uid");
            $statement->execute([
                "uid" => $uid
            ]);
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $data = $statement->fetch(PDO::FETCH_ASSOC);
        }
        return $data;  
    }

    public function getByUserPairing($senderID, $invitedID){
        
        $table = "invitations";
        
        if(!empty($this->pdo)){
            $statement = $this->pdo->prepare("SELECT * FROM `$table` WHERE `senderID` = :senderID AND `invitedID` = :invitedID");
            $statement->execute([
                "senderID" => $senderID,
                "invitedID" => $invitedID           
            ]);
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $data;
    }    
    
    // post
    public function newInvitation($senderID, $invitedID){
        
        if(!empty($this->pdo)){        
            
            $table = "invitations";
            
            $statement = $this->pdo->prepare("INSERT INTO `$table` (`uid`, `senderID`, `invitedID`, `state`) VALUES(:uid, :senderID, :invitedID, :state)");
        
            
            $uid = bin2hex($senderID . $invitedID . random_bytes(2));
            $defaultState = "PENDING";            
            
            $statement->execute([
                "uid"       => $uid,
                "senderID"  => $senderID,
                "invitedID" => $invitedID,
                "state"     => $defaultState
            ]);
        }
        
        return $this->getInvitationByID($uid);
    }
    
    // put
    public function updateInvitationState($uid, $newState){
                
        $table = "invitations";
        
        if(!empty($this->pdo)){
            $statement = $this->pdo->prepare("UPDATE `$table` SET `state` = :newState WHERE `uid` = :uid");
            $statement->execute([
                "uid" => $uid,
                "newState" => $newState
            ]);
        }
        
        return $this->getInvitationByID($uid);
    }
    
    // delete
    public function deleteInvitation($uid){
        
        $table = "invitations";
        
        if(!empty($this->pdo)){
            $statement = $this->pdo->prepare("DELETE FROM `$table` WHERE `uid` = :uid");
            $statement->execute([
                "uid" => $uid,
            ]);
        }        
    }
    
}


?>