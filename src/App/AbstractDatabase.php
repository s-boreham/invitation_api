<?php
# src/App/AbstractDatabase.php

namespace App\App;

use PDO;

abstract class AbstractDatabase {

    protected $pdo;
    
    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }
    
}

?>