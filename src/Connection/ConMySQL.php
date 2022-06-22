<?php
# src/Connection/ConMySQL.php

namespace App\Connection;

use PDO;

class ConMySQL {

    public function ConToMySQL1() {
        $pdo = new PDO('mysql:host=localhost;dbname=invitations_api_db;charset=utf8','admin','admin');
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    }
    
}

?>