<?php
require(__DIR__ . '/autoload.php');

use core\{DB, Data};
$config = require(__DIR__ . '/config.php');

// initing smarty
$smarty = new Smarty();
$smarty->template_dir = 'templates';
$smarty->compile_dir  = 'templates_c';

$smarty->assign('alertMessage','');
//** un-comment the following line to show the debug console
//$smarty->debugging = true;
$smarty->display('layout/head.tpl');


/** @todo implement more secured post */
if(isset($_POST['XML'])){
    $file = $_FILES['XML'];

    //  Check if not XML file posted
    if($file['type'] != 'text/xml'){print("not valid xml file");exit;}

    // uploading file
    $z = move_uploaded_file($file['tmp_name'],'addresses.xml');

    // Checking needed Table existand, and create table if needed
    $model = new \core\Data($config);
    $model->checkScheme();

    // TASK 1: importing information from xml file into MySQL DB
    if($model->import(__DIR__ . '/addresses.xml')){
        $smarty->assign('alertMessage','<h1> imported successfully</h1>');
        $smarty->display('import.tpl');exit;
    };
}

$view = $_GET['v']??'';
if($view == 'import'){
    $smarty->display('import.tpl');
}elseif($view == 'search'){
    $smarty->display('search.tpl');
}else{
    $smarty->display('index.tpl');
}

$smarty->display('layout/foot.tpl');

?>