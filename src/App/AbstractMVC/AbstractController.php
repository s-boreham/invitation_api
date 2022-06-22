<?php
# src/App/AbstractMVC/AbstractController.php

namespace App\App\AbstractMVC;

abstract class AbstractController {

    public function jsonResponse($code, $data){
    
        http_response_code($code);
        header("Content-Type: application/json");
        echo json_encode($data);
        exit();
    }
    
}
