
<?php
    use MyAPI\Delete\Delete;

    require_once __DIR__ . '/../backend/vendor/autoload.php';

    $products = new Delete('marketzone');
    $products->delete($_POST);
    echo $products->getData();

?>
