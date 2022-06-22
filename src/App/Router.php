<?php
# src/App/Router.php
namespace App\App;

use \App\App\Container;

class Router {

    public function __construct(Container $container) {
    
        $this->container = $container;
    }
    
    public function add($ctrl, $function, $args = null){

        $controller = $this->container->build("$ctrl");
        $view = $function;
        $this->build($controller, $view, $args);
    }
    
    public function build($controller, $view, $args) {
        $controller->$view($args);
    }

}

?>