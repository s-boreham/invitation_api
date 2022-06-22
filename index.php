<?php
#index.php

require_once "autoload.php";

use \App\App\Container;

$Container = new Container;
$router = $Container->build('router');


if(isset($_SERVER["PATH_INFO"])){
    $request = $_SERVER["PATH_INFO"];
} else {
    $request = $_SERVER["REQUEST_URI"];
}


if($_SERVER["REQUEST_METHOD"] == "GET") {
        
    /*
    * ENTRY POINT TO VIEW INVITATION
    */
    preg_match('/^\/v[\d]+\/invitations\/([\w\d]+)/', $request, $routeMatch);
    if($routeMatch){
        $data = json_decode(file_get_contents('php://input'), true);
        $data["uid"] = $routeMatch[1];
        $router->add("InvitationController", "show", $data);
        exit();
    }
    
    /* 
    * ENTRY POINT TO INVITATION LIST 
    */
    preg_match('/^\/v[\d]+\/invitations\/?$/', $request, $routeMatch);    
    if($routeMatch){
        $data = json_decode(file_get_contents('php://input'), true);

        $router->add("InvitationController", "showAll", $data);
        exit();
    }    

    
    /* 
    * ENTRY POINT TO GET AUTHENTICATION
    */
    if($request == "/v[\d]+/auth") {        
        $data = json_decode(file_get_contents('php://input'), true);

        $router->add("AuthController", "generateAuthToken", $data);
        exit();
    }
} 

    /*
    * ENTRY POINT CREATE NEW INVITATION
    * data = {invited: userID}
    */
elseif($_SERVER["REQUEST_METHOD"] == "POST") {    
    preg_match('/^\/v[\d]+\/invitations\/?/', $request, $routeMatch);
    if($routeMatch){
        $data = json_decode(file_get_contents('php://input'), true);
        
        $router->add("InvitationController", "createNew", $data);       
        exit();
    }

} 

    /*
    * ENTRY POINT RESPOND TO INVITATION
    * data = {respond: "accept"|"reject"}
    */
elseif($_SERVER["REQUEST_METHOD"] == "PUT") {
    
    preg_match('/^\/v[\d]+\/invitations\/([\w\d]+)/', $request, $routeMatch);
    if($routeMatch){
        $data = json_decode(file_get_contents('php://input'), true);
        $data["uid"] = $routeMatch[1];        
        
        $router->add("InvitationController", "respond", $data);
        exit();    
    }

}

    /*
    * ENTRY POINT CANCEL INVITATION
    */
elseif($_SERVER["REQUEST_METHOD"] == "DELETE") {
    
    preg_match('/^\/v[\d]+\/invitations\/([\w\d]+)/', $request, $routeMatch);
    if($routeMatch){
        
        $data["identifier"] = $_SERVER["HTTP_X_IDENTIFIER"];
        $data["securityToken"] = $_SERVER["HTTP_X_SECURITYTOKEN"];
        $data["uid"] = $routeMatch[1];
                
        $router->add("InvitationController", "cancel", $data);
        exit();
    }
}

else {
    $message = array("status" => "404", "message" => "requested resource not found");
    header("Content-Type: application/json");
    echo json_encode($message); 
    exit();    
}


?>
