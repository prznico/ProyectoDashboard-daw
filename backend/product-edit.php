<?php
    use MyAPI\Update\Update;

    require_once __DIR__ . '/../backend/vendor/autoload.php';
    
    $products = new Update('marketzone');
    //leer json y convertirlo a objeto php
    $json = file_get_contents('php://input');
    $producto = json_decode($json);
    $products->edit($producto);
    echo $products->getData();
    
?>