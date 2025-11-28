<?php
    namespace MyAPI;

    require_once __DIR__ . '/../backend/vendor/autoload.php';
    
    use MyAPI\Create\Create;
    
    $products = new Create('marketzone');
    //leer json y convertirlo a objeto php
    $json = file_get_contents('php://input');
    $producto = json_decode($json);
    $products->add($producto);
    echo $products->getData();
?>