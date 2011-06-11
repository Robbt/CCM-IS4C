<?php
include_once('../src/mysql_connect.php');

if (isset($_GET['q']) and $_GET['q'] != '') {
	$sql = sprintf("select brand from brands where lower(brand) like '%s'",strtolower($_GET['q']) . '%');

	$result = mysql_query($sql);
	
	while ($row = mysql_fetch_array($result)) {
		//$results[] = $row['brand'];
		echo $row['brand'] . "\n";

	}
	//echo json_encode($results);

}
