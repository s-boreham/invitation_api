<?php
# src/Auth/UserDatabase.php
namespace App\Auth;

use \App\App\AbstractDatabase;
use PDO;

class UserDatabase extends AbstractDatabase {
        
    // get user by id
    public function getUserByID($id){
    
        $table = "mock_users";
        $model = \App\Invitations\Entities\Invitation::class;        
        
        if(!empty($this->pdo)){
            $statement = $this->pdo->prepare("SELECT * FROM `$table` WHERE `id` = :id");
            $statement->execute([
                "id" => $id
            ]);
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        return $data; 
    }
    
    public function getUserByEmail($email){
    
        $table = "mock_users";
        $model = \App\Invitations\Entities\Invitation::class;        
        
        if(!empty($this->pdo)){
            $statement = $this->pdo->prepare("SELECT * FROM `$table` WHERE `id` = :id");
            $statement->execute([
                "id" => $id
            ]);
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        return $data; 
    }
    
}


?>