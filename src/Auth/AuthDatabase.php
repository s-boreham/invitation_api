<?php
# src/Auth/AuthDatabase.php
namespace App\Auth;

use \App\App\AbstractDatabase;
use PDO;

class AuthDatabase extends AbstractDatabase {
    
    // get authentication details by userID
    public function getAuthenticationByUserID($id){

        $table = "auth_tokens";
        
        if(!empty($this->pdo)){
            $statement = $this->pdo->prepare("SELECT * FROM `$table` WHERE `userID` = :id");
            $statement->execute([
                "id" => $id
            ]);
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $data = $statement->fetch(PDO::FETCH_ASSOC);
        }
        return $data; 
        
    }
    
    // get user by id
    public function getUserID($identifier, $securityToken){
    
        $table = "auth_tokens";
        
        if(!empty($this->pdo)){
            $statement = $this->pdo->prepare("SELECT `userID` FROM `$table` WHERE `identifier` = :identifier AND `securityToken` = :securityToken");
            $statement->execute([
                "identifier" => $identifier,
                "securityToken" => $securityToken
            ]);
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $data = $statement->fetch(PDO::FETCH_ASSOC);
        }
        return $data;
    }
 
    // persist new authentication
    public function newAuthentication($userID, $identifier, $securityToken){
    
        $table = "auth_tokens";
        
        if(!empty($this->pdo)){
            $statement = $this->pdo->prepare("INSERT INTO `$table` (`identifier`, `userID`, `securityToken`) VALUES ( :identifier, :userID, :securityToken)");
            $statement->execute([
                "identifier" => $identifier,
                "userID" => $userID,
                "securityToken" => $securityToken
            ]);

        }
        
        return array("identifier" => $identifier, "userID" => $userID, "securityToken" => $securityToken);
    }
    
}


?>