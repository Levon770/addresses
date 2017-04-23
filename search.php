<?php
require(__DIR__ . '/autoload.php');
use core\{DB, Data};

$config = require(__DIR__ . '/config.php');

/** @todo implement more secured post */
if(isset($_POST['search'])){
    $txt = $_POST['search'];

    $model = new \core\Data($config);

    $result = $model->search(trim($txt));
    echo  json_encode($result);exit;
}else{
    header('Location: index.php');
}