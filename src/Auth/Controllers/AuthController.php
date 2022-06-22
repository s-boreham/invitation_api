<?php
// src/Auth/Controllers/AuthController.php
namespace App\Auth\Controllers;

use \App\App\AbstractMVC\AbstractController;
use \App\Auth\UserDatabase;
use \App\Auth\AuthDatabase;

class AuthController extends AbstractController {
    
    public function __construct(UserDatabase $UserDatabase, AuthDatabase $AuthDatabase) {
        $this->UserDatabase = $UserDatabase;    
        $this->AuthDatabase = $AuthDatabase;    
    }    
    
    public function authenticateUser($identifier, $token) {
        $userAuth = $this->AuthDatabase->getUserID($identifier, $token);
        var_dump($userAuth);
        if(!$userAuth){
            $this->jsonResponse(401, array("status" => "401", "message" => "forbidden access"));
        }
        else {
            return $userAuth["userID"];
        }
    }
    
    public function validUser($userID) {
        $user = $this->UserDatabase->getUserByID($userID);
        
        return $user;
    }

    // /v1/auth POST request. data: userID
    public function generateAuthToken($data) {

        //  check user is set and exists        
        if( !isset($data["userID"]) || !$this->validUser($data["userID"]) ){
            $this->jsonResponse(400, array("message" => "bad request"));
        }
        $userID = $data["userID"];
        
        // check for unexpired authTokens
        $authentication = $this->AuthDatabase->getAuthenticationByUserID($userID);
   
        if(!$authentication){
            $identifier = bin2hex(time() . random_bytes(4) );
            $securityToken = bin2hex(time() . random_bytes(6) );

            $authentication = $this->AuthDatabase->newAuthentication($userID, $identifier, $securityToken);        
        }
        
        $this->jsonResponse(200, $authentication);
    }    
}

?>