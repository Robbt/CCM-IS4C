<?php
include_once('../src/mysql_connect.php');

if (isset($_GET['q']) and $_GET['q'] != '') {
	$sql = sprintf("select vendor from vendors where lower(vendor) like '%s'",strtolower($_GET['q']) . '%');

	$result = mysql_query($sql);
	
	while ($row = mysql_fetch_array($result)) {
		//$results[] = $row['vendor'];
		echo $row['vendor'] . "\n";

	}
	//echo json_encode($results);

}
