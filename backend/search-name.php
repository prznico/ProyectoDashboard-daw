<?php
    use MyAPI\Read\Read;
    require_once __DIR__ . '/../backend/vendor/autoload.php';

    $products = new Read('marketzone');
    $products->singleByName($_POST);
    echo $products->getData();
?>