<?php
# src/App/Container.php

namespace App\App;

use \App\App\Router;
use \App\Connection\ConMySQL;
use \App\Invitations\Controllers\InvitationController;
use \App\Invitations\InvitationDatabase;
use \App\Auth\Controllers\AuthController;
use \App\Auth\UserDatabase;
use \App\Auth\AuthDatabase;

class Container {

    private $classInstances = [];
    private $builds = [];
    
    public function __construct() {
    
        $this->builds = [
            'router' => function() {
                return new Router($this->build('container'));
            },
            'container' => function() {
                return new Container;
            },
            'PDO' => function() {
                $connection = new ConMySQL;
                return $connection->ConToMySQL1();
            },
            'InvitationController' => function() {
                return new InvitationController($this->build('InvitationDatabase'), $this->build('AuthController'));
            }, 
            'InvitationDatabase' => function() {
                return new InvitationDatabase($this->build('PDO'));  
            },
            'AuthController' => function() {
                return new AuthController($this->build('UserDatabase'), $this->build('AuthDatabase'));
            }, 
            'UserDatabase' => function() {
                return new UserDatabase($this->build('PDO'));  
            },  
            'AuthDatabase' => function() {
                return new AuthDatabase($this->build('PDO'));  
            }            
        ];
    }
    
    public function build($object) {
    
        if(isset($this->builds["$object"])) {
            if(!empty($this->classInstances["$object"])) {
                return $this->classInstances["$object"];
            }
            else {
                $this->classInstances["$object"] = $this->builds["$object"]();
            }
            return $this->classInstances["$object"];
        }
    }
    
}

?>