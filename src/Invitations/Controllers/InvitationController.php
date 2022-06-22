<?php
# src/Invitations/Controllers/InvitationController.php
namespace App\Invitations\Controllers;

use \App\App\AbstractMVC\AbstractController;
use \App\Invitations\InvitationDatabase;
use \App\Auth\Controllers\AuthController;

class InvitationController extends AbstractController {
    
    public function __construct(InvitationDatabase $InvitationDatabase, AuthController $AuthController) {
        $this->InvitationDatabase = $InvitationDatabase;    
        $this->AuthController = $AuthController;    
    }    
    
    
    private function authenticate($data) {
        
        if( !isset($data["identifier"]) || !isset($data["securityToken"]) ){
            $this->jsonResponse(400, array("request" => $data, "message" => "unable to authenticate client"));
        }
        
        $userID = $this->AuthController->authenticateUser($data["identifier"], $data["securityToken"]);
        if(!$userID) {
            $this->jsonResponse(400, array("request" => $data, "message" => "authentication failed"));        
        }
        
        return $userID;
    }
    
        
    public function showAll($data) {
        
        // authenticate User
        $userID = $this->authenticate($data);
        
        // get invitations grouped by sent and received
        $invitations["sent"] = $this->InvitationDatabase->getSentInvitations($userID);
        $invitations["received"] = $this->InvitationDatabase->getReceivedInvitations($userID);
        
        $this->jsonResponse(200, array($invitations));
    }
    
    
    public function show($data) {

        // authenticate User        
        $userID = $this->authenticate($data);      
        
        if(!isset($data["uid"])){
            $this->jsonRespone(400, array("status" => "400", "message" => "invitation uid is required"));
        }
        
        $invitation = $this->InvitationDatabase->getInvitationByID($data["uid"]);
        if(!$invitation){
            $this->jsonResponse(400, array("status" => "400", "message" => "resource does not exist"));
        }
        
        $this->jsonResponse(200, array($invitation));

    }
    
    
    public function createNew($data) {
        
        // - authenticate user
        $userID = $this->authenticate($data);
        
        // - authenticate invited
        if(!isset($data["invitedID"]) ){
            $this->jsonResponse(400, array("request" => $data, "message" => "invitation recipient required")); 
        }
        
        // - check invitedID is different from senderID
        if($userID == $data["invitedID"]){
            $this->jsonResponse(400, array("request" => $data, "message" => "invalid invitation recipient", "sender" => $userID, "invited" => $data["invitedID"]));
        }
        
        // - check invitedID belongs to a registered User
        $invitedUser = $this->AuthController->validUser($data["invitedID"]);            
        if(!$invitedUser){
            $this->jsonResponse(400, array("request" => $data, "message" => "invitation recipient has to be a registered user"));
        }
        
        
        // - check for existing invitations
        $invitation = $this->InvitationDatabase->getByUserPairing($userID, $data["invitedID"]);
        if(!$invitation){
            $invitation = $this->InvitationDatabase->newInvitation($userID, $data["invitedID"]);            
        }
        
        // just a 201 code would also suffice
        $this->jsonResponse(201, array($newInvitation));
    }
    
    
    public function respond($data){

        // authenticate User
        $userID = $this->authenticate($data);
        
        //  request has invitation uid
        if(!isset($data["uid"])){
            $this->jsonRespone(400, array("status" => "400", "message" => "invitation uid is required"));
        }
        $uid = $data["uid"];
        
        //  invitation exists
        $invitation = $this->InvitationDatabase->getInvitationByID($uid);
        if(!$invitation){
            $this->jsonResponse(400, array("status" => "400", "message" => "resource does not exist"));
        }
        
        //  authorize request, check userID == invitedID
        if($invitation["invitedID"] != $userID){
            $this->jsonResponse(403, array("status" => "403", "message" => "forbidden"));
        }
        
        //  invitation has not been rsvped yet
        if($invitation["state"] != "PENDING") {
            $this->jsonResponse(400, array("status" => "400", "message" => "invitation is already rsvped"));
        }
        
        //  validate response data
        if( !isset($data["rsvp"]) || !in_array($data["rsvp"], ["ACCEPTED", "REJECTED"], true) ){
            $this->jsonResponse(400, array("status" => "400", "message" => "json object key 'rsvp' must be 'ACCEPTED' or 'REJECTED'"));
        }
        $rsvp = $data["rsvp"];
        
        // update database entry
        $this->InvitationDatabase->updateInvitationState($uid, $rsvp);
        
        $this->jsonResponse(201, array("message" => "invitation has been accepted/rejected"));
    }
    
    
    public function cancel($data){
        
        // authenticate User
        $userID = $this->authenticate($data);        

        //  request has invitation uid
        if(!isset($data["uid"])){
            $this->jsonRespone(400, array("status" => "400", "message" => "invitation uid is required"));
        }
        $uid = $data["uid"];
        
        //  invitation exists
        $invitation = $this->InvitationDatabase->getInvitationByID($uid);
        if(!$invitation){
            $this->jsonResponse(400, array("status" => "400", "message" => "resource does not exist"));
        }        
        
        //  authorize request, check userID == senderID
        if($invitation["senderID"] != $userID){
            $this->jsonResponse(403, array("status" => "403", "message" => "forbidden"));
        }

        //  delete invitaion
        $this->InvitationDatabase->deleteInvitation($uid);
        
        
        $this->jsonResponse(204, array("message" => "invitation deleted"));
    }
}

?>