<?
/*******************************************************************************

    Copyright 2007 Authors: Christof Von Rabenau - Whole Foods Co-op Duluth, MN
	Joel Brock - People's Food Co-op Portland, OR

	This is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This software is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    in the file license.txt along with IS4C; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*********************************************************************************/
//	TODO -- Add javascript for batcher product entry popup window		~joel 2007-08-21

include_once($_SERVER["DOCUMENT_ROOT"].'/src/mysql_connect.php');

function itemTemplate($upc) {
	$resultItem = selectProduct($upc);
	$itemrows = itemRows($resultItem);
	$salesBatch = selectSalesBatch($upc);
	if ($itemrows == 0 && is_numeric($upc)) {
		$upc = str_pad($upc,13,0,STR_PAD_LEFT);
		return array('itemEdit.tpl',$upc);

	} elseif ($itemrows == 1) {
		return array('itemEdit.tpl',mysql_fetch_assoc($resultItem));
	} elseif ($itemrows > 1)  {
		return array('itemMulti.tpl',allResults($resultItem));
	} else {
		return array('itemMaint.tpl','');
	}	

}

function allResults($results) {
	while ($row = mysql_fetch_assoc($results)) {
		$rows[] = $row;
	}
	return $rows;

}


function itemRows($result) {
	return mysql_num_rows($result);
}


function selectProduct($upc) {
	if (is_numeric($upc)) {
		$upc = str_pad($upc,13,0,STR_PAD_LEFT);
		$queryItem = sprintf("select * from products where upc = '%s'",$upc);
	} else {
		$queryItem = sprintf("select * from products where description like '%s' order by description",'%' . $upc . '%');
	}
	return mysql_query($queryItem);
}

//ccm-rle 11-18-2010 this is test code to enable the linking of sales batches to items queried

function selectSalesBatch($upc) {
                $upc = str_pad($upc,13,0,STR_PAD_LEFT);
                $querySalesbatch = sprintf("select b.batchName AS batchName, b.batchID FROM batches AS b INNER JOIN batchList AS l ON b.batchID = l.batchID 
WHERE l.upc = '%s'",$upc); 
	   	$result = mysql_query($querySalesbatch);	
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		return $row;
	}







function deptDropDowns() {
			require_once($_SERVER["DOCUMENT_ROOT"].'/src/chainedSelectors.php');

			$selectorNames = array(
				CS_FORM=>"pickSubDepartment", 
				CS_FIRST_SELECTOR=>"department", 
				CS_SECOND_SELECTOR=>"subdepartment");

			$Query = "SELECT d.dept_no AS dept_no,d.dept_name AS dept_name,s.subdept_no AS subdept_no,s.subdept_name AS subdept_name	
				FROM is4c_op.departments AS d, is4c_op.subdepts AS s
				WHERE d.dept_no = s.dept_ID
				ORDER BY d.dept_no, s.subdept_no";

		    if(!($DatabaseResult = mysql_query($Query)))//, $DatabaseLink)))
		    {
		        print("The query failed!<br>\n");
		        exit();
		    }
		    while($row = mysql_fetch_object($DatabaseResult))
		    {
		    	$selectorData[] = array(
					CS_SOURCE_ID=>$row->dept_no, 
				    CS_SOURCE_LABEL=>$row->dept_name, 
				    CS_TARGET_ID=>$row->subdept_no, 
					CS_TARGET_LABEL=>$row->subdept_name);
			}            

	    	return new chainedSelectors($selectorNames, $selectorData);
}



//TODO abstract column = data 
function updateItem() {


if (!isset($_POST['wholesalecost'])) {
	$wholesalecost = $price;
} else {
	$wholesalecost = $_POST['wholesalecost'];
}


$query = sprintf("UPDATE products 
	SET description = '%s',
	normal_price= %f,
	wholesale_cost=%f,
	tax= %d,
	scale=%d,
	foodstamp=%d,
	department = '%s',
	subdept = '%s',
	inUse = %d,
    	qttyEnforced = %d,
    	discount=%d,
	modified=now(),
	deposit=%f,
	size='%s',
	vendor='%s',
	brand='%s',
	notes='%s',
	label_prints='%d',
	frontstock='%f',
	backstock='%f'
	where upc = '%s'",$_POST['descript'],$_POST['price'],$wholesalecost,$_POST['tax'],$_POST['Scale'],$_POST['FS'],$_POST['department'],$_POST['subdepartment'],$_POST['inUse'],$_POST['QtyFrc'],$_POST['NoDisc'],$_POST['deposit'],$_POST['size'],$_POST['vendor'],$_POST['brand'],$_POST['notes'],$_POST['label_prints'],$_POST['frontstock'],$_POST['backstock'],$_POST['upc']);
	if (!mysql_query($query)) {
		die("Products Update: " . mysql_error());
		exit;
	} else {
		//first lookup id from products
		
		$pid = mysql_result(mysql_query(sprintf("select id from products where upc = '%s'",$_POST['upc'])),0);
		$historysql = sprintf("insert into products_price_history (id,upc,normal_cost) values (%d,%d,%f)",$pid,$_POST['upc'],$_POST['currentprice']);
		if (!mysql_query($historysql)) {
			die("Products_cost_history:" . mysql_error());
			exit;
		} else {
			$brandresult = mysql_query(sprintf("select brandid from brands where lower(brand) = lower('%s')",$_POST['brand']));
			if (itemRows($brandresult) == 0) {
				insertBrand($_POST['brand']);
			}
                        $vendoresult = mysql_query(sprintf("select vendorid from vendors where lower(vendor) = lower('%s')",$_POST['vendor']));
                        if (itemRows($vendorresult) == 0) {
									                                insertVendor($_POST['vendor']);
			}

		       }
}


}
function insertItem() {
if (!isset($_POST['wholesalecost'])) {
	$wholesalecost = $price;
} else {
	$wholesalecost = $_POST['wholesalecost'];
}
?><pre>
</pre><?php
$brandresult = mysql_query(sprintf("select brandid from brands where lower(brand) = lower('%s')",$_POST['brand']));
if (itemRows($brandresult) == 0 && !is_null($_POST['brand'])) {
						                                insertBrand($_POST['brand']);
}
$vendoresult = mysql_query(sprintf("select vendorid from vendors where lower(vendor) = lower('%s')",$_POST['vendor']));
if (itemRows($vendorresult) == 0 && !is_null($_POST['vendor'])) {

insertVendor($_POST['vendor']);
									                        }



$query = sprintf("insert products (upc,description,normal_price,wholesale_cost,tax,scale,foodstamp,department,subdept,inUse,qttyEnforced,discount,modified,deposit,size,vendor,brand,notes,label_prints,frontstock,backstock) values ('%s','%s',%f,%f,%d,%d,%d,%d,%d,%d,%d,%d,now(),%f,'%s','%s','%s','%s','%d','%f','%f')",$_POST['upc'],$_POST['descript'],$_POST['price'],$wholesalecost,$_POST['tax'],$_POST['Scale'],$_POST['FS'],$_POST['department'],$_POST['subdepartment'],$_POST['inUse'],$_POST['QtyFrc'],$_POST['NoDisc'],$_POST['deposit'],$_POST['size'],$_POST['vendor'],$_POST['brand'],$_POST['notes'],$_POST['label_prints'],$_POST['frontstock'],$_POST['backstock']);
 echo "<br />";
	if (!mysql_query($query)) {
		die("Products Insert: " . mysql_error());
		exit;
	}

}


function deptname($deptno) {
  return mysql_result(mysql_query(sprintf("select dept_name from departments where dept_no = %d",$deptno)),0);
}

function subdeptname($subdeptno) {
  return mysql_result(mysql_query(sprintf("select subdept_name from subdepts where subdept_no = %d", $subdeptno)),0);
}


/**
item2smaryvarmap - map smarty vars to get/post/database vars
*/
function item2smartyvarmap($databasemaps = 0) {
//post/get => smarty var or array(smarty var, map function)

        if (!$databasemaps) { //from POST/GET

        $item2smarty = array("upc" => "upc",
                        "descript" => "description",
			"price" => "normal_price",
                        "wholesalecost" => "wholesale_cost",
                        "special_price" => "special_price",
                        "end_date" => "end_date",
                        "tax" => "tax",
                        "FS" => "foodstamp",
                        "Scale" => "scale",
                        "QtyFrc" => "qttyEnforced",
                        "NoDisc" => "discount",
                        "inUse" => "inUse",
                        "deposit" => "deposit",
			"size" => "size",
			"vendor" => "vendor",
			"brand" => "brand",
			"notes" => "notes",
			"label_prints" => "label_prints",
			"frontstock" => "frontstock",
			"backstock" => "backstock",
                        "department" => array("deptname",'deptname'),
                        "subdepartment" => array("subdeptname",'subdeptname'));
        } else { //from database
        $item2smarty = array("upc" => "upc",
                        "description" => "description",
                        "normal_price" => "normal_price",
                        "wholesale_cost" => "wholesale_cost",
                        "special_price" => "special_price",
                        "end_date" => "end_date",
                        "tax" => "tax",
                        "foodstamp" => "foodstamp",
                        "scale" => "scale",
                        "qttyEnforced" => "qttyEnforced",
                        "discount" => "discount",
                        "inUse" => "inUse",
                        "deposit" => "deposit",
			"size" => "size",
			"vendor" => "vendor",
			"notes" => "notes",
			"label_prints" => "label_prints",
                        "frontstock" => "frontstock",
                        "backstock" => "backstock",
			"brand" => "brand",);
        }
        return $item2smarty;
}



/**
array2smarty - assign smarty variables
$smartyvars = array("smary var name" => "html form name");
$data = associate array POST/GET
****/
function array2smarty($smarty,$smartyvars,$data) {
        foreach ($smartyvars as $datavar => $smartyvar) {

                //echo $smartyvar . ":" . $data[$datavar] . "=>" . $datavar . "<br />";
                if (!is_array($smartyvar)) {
                        $smarty->assign($smartyvar,$data[$datavar]);
                } else {
                        $smarty->assign($smartyvar[0], call_user_func($smartyvar[1], $data[$datavar]));
                }

	}
}


function vendors() {

   return allResults(mysql_query("select vendorid,vendor from vendors order by vendor"));

}

function insertVendor($vendor = '') {
        if ($vendor != '') {
	      $sql = sprintf("insert into vendors (vendor) values ('%s')", $vendor);
            mysql_query($sql);
					        }
}


function brands() {
   
   return allResults(mysql_query("select brandid,brand from brands order by brand")); 

}

function insertBrand($brand = '') {
	if ($brand != '') {
		$sql = sprintf("insert into brands (brand) values ('%s')", $brand);
		mysql_query($sql);
	}
}
?>
