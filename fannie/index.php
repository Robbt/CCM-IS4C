<?php
$itemindex=1;

include_once('src/mysql_connect.php');
include('item/prodFunction.php');

require('/pos/Smarty/Smarty.class.php');

$smarty = new Smarty();
//$smarty->debugging = true;
$smarty->template_dir = '/pos/smarty/templates';
$smarty->compile_dir = '/pos/smarty/templates_c';
$smarty->cache_dir = '/pos/smarty/cache';
$smarty->config_dir = '/pos/smarty/configs';
$smarty->assign('jquery',1);
require_once('src/chainedSelectors.php');
//This is kludged to the max dude.

if ($_GET['q'] && !$_POST['submit']) {
	$itemtemplate = itemTemplate($_GET['q']);
	$item_template = $itemtemplate[0];
	$items = $itemtemplate[1];
   	$match = $_GET['q'];
	array2smarty($smarty,item2smartyvarmap(1),$items);
        $smarty->assign('javascriptonload',"onload='putFocus(0,10);'");

	$subdept = deptDropDowns();
	$smarty->assign('items',$items);
	$smarty->assign_by_ref('subdept',$subdept);
	$smarty->assign('buttontext','Update');
}

if ($_POST['submit']) {
	$itemtemplate = itemTemplate($_POST['upc']);
	$item_template = $itemtemplate[0];
	$items = $itemtemplate[1];
    	$match = $_POST['upc'];

	switch ($_POST['submit']) {
		case "Search":
		if (is_array($items) && $item_template == 'itemEdit.tpl') { //edit
			//setup variables
		        $smarty->assign('javascriptonload',"onload='putFocus(0,10);'");

			array2smarty($smarty,item2smartyvarmap(1),$items);
			$subdept = deptDropDowns();
			$smarty->assign('items',$items);
			$salesbatch = selectSalesBatch($match);
			$smarty->assign('salesbatch',$salesbatch);
			$smarty->assign_by_ref('subdept',$subdept);
			$smarty->assign('buttontext','Update');

		} elseif ($item_template == 'itemMulti.tpl') { //Multi
			$smarty->assign('match',$match);
			$smarty->assign('matches',$items);
		} else { //new
			$subdept = deptDropDowns();
			$smarty->assign('items','');
			$smarty->assign_by_ref('subdept',$subdept);
			$smarty->assign('buttontext','Insert');
			$smarty->assign('upc',$items);
		}
		break;
		case 'Update': //update database
	    	        updateItem();
			array2smarty($smarty,item2smartyvarmap(),$_POST);
			$smarty->assign('displayupdates',1);
			$item_template = 'itemMaint.tpl';
	    	break;
		case 'Insert': //
	    	        insertItem();
			array2smarty($smarty,item2smartyvarmap(),$_POST);
			$smarty->assign('displayupdates',1);
			$item_template = 'itemMaint.tpl';
		break;
	}
} elseif (!$_GET['q']) {
	$item_template = 'itemMaint.tpl';
 	$smarty->assign('javascriptonload',"onLoad='putFocus(0,0);'");
}

$smarty->assign('content', $item_template);

$smarty->display('index.tpl');
