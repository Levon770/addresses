<?php

require(__DIR__ . '/autoload.php');

use core\{DB, Data};
$config = require(__DIR__ . '/config.php');

// initing smarty
$smarty = new Smarty();
$smarty->template_dir = 'templates';
$smarty->compile_dir  = 'templates_c';


// rendering  head part
$smarty->display('layout/head.tpl');
$model = new Data($config);

$id = (int)$_GET['id']??0;

if( ($choosed = $model->findOne($id))  ){

    //showing head part of view
    $smarty->assign('model',$choosed);
    $smarty->display('view/listHead.tpl');

    // showing rexords
    $smaller = $model->findSmaller($choosed['lat'], $choosed['lng']);
    $smarty->assign('title','Distance < 5Km');
    $smarty->assign('model',$smaller);
    $smarty->display('view/list.tpl');


    // showing rexords
    $middle = $model->findMiddle($choosed['lat'], $choosed['lng']);
    $smarty->assign('title','Distance > 5Km AND <30 Km ');
    $smarty->assign('model',$middle);
    $smarty->display('view/list.tpl');

    // showing rexords
    $higher = $model->findHigher($choosed['lat'], $choosed['lng']);
    $smarty->assign('title','Distance > 30 Km ');
    $smarty->assign('model',$higher);
    $smarty->display('view/list.tpl');

}else{
    $smarty->assign('error','<h1> Not Found</h1>');
    $smarty->display('error.tpl');
}


// rendering  footer part
$smarty->display('layout/foot.tpl');
?>