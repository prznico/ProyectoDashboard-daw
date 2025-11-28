<?php


    require_once __DIR__ . '/../backend/vendor/autoload.php';
    use MyAPI\Read\Read;

    $products = new Read('marketzone');
    $products->listProduct();
    echo $products->getData();

    ?>