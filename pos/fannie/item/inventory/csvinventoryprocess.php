<?
require_once('../../src/mysql_connect.php');
$output = array('Pass' => 0, 'Fail' => 0);

ini_set('auto_detect_line_endings',1);

$filename = $_FILES["csvfile"]["tmp_name"];
//if ($_FILES["csvfile"]["type"] == "csv") {

$handle = fopen($_FILES["csvfile"]["tmp_name"], 'r');



while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
   
    $upc = mysql_real_escape_string($data[0]);
    
    $upc = substr($upc,0, -1);
    $upc = "00" . $upc;
    // ccm-rle - will need to change below to be variable
    $inventorytype = $_POST["inventorytype"];
    $inventorycount = mysql_real_escape_string($data[1]);


//    $result = (mysql_insert_id()> 0) ? 'Pass' : 'Fail' ;
   
//    $output[$result]++;
/*
    $insBItemQ = "INSERT INTO batchList(upc,batchID,salePrice,active)
                        VALUES('$upc',$batchID,$salePrice,$batch_active)";
              echo $insBItemQ;
                $insBItemR = mysql_query($insBItemQ);
*/              
echo "</br>";
// echo $inventorytype;

// echo "<h1>" . $batchInfoW[6] . "</h1>";
                if ($upc != 0) {
			$prodSelectQ = "SELECT description from products where upc = $upc";
			$prodSelectR = mysql_query($prodSelectQ);
		        $num = mysql_num_rows($prodSelectR);
			if($num == 0) {
                echo "<font color=red><div id='alert'><p>No item found for upc: $upc  - couldn't update $inventorytype to $inventorycount </p></div></font>";
			}
			else 
			{
			$rowItem= mysql_fetch_array($prodSelectR);
			$prodname =  $rowItem['description'];

                        $prodUpdateQ = "UPDATE products AS p 
                                SET p.$inventorytype = $inventorycount 
                                WHERE p.upc = $upc";
//   		echo $prodUpdateQ;
                        $prodUpdateR = mysql_query($prodUpdateQ) OR DIE ("<div id=alert><p>ERROR!</p><br />" . mysql_error() . "<br /></div>");
                        if ($prodUpdateR) { echo "<div id=alert>$inventorytype inventory updated successfully for $prodname (upc = $upc) set to $inventorycount</div>";}
                	else {echo "<div id=alert><p><font color=red>Product table  $inventorytype  not updated UPC $upc missing</p></div>";}
			}
			}
/* decided to do inventory type in the mysql code rather than two separate sql tables
		elseif ($upc != 0 && $inventorytype = "backstock") {
                        $prodUpdateQ = "UPDATE products AS p 
                                SET p.backstock = $inventorycount 
                                WHERE p.upc = $upc";
   echo $prodUpdateQ;
//                        $prodUpdateR = mysql_query($prodUpdateQ) OR DIE ("<div id=alert><p>ERROR!</p><br />" . mysql_error() . "<br /></div>");
 //                       if ($prodUpdateR) { echo "<div id=alert><p>Products table Back Stock inventory updated successfully (upc = $upc)</p></div>";}
*/



}
//else {
//echo "Only CSV (comma separated value) files can be processed";
//}


echo "<table border=\"1\">";
echo "<tr><td>Client Filename: </td>
   <td>" . $_FILES["csvfile"]["name"] . "</td></tr>";
echo "<tr><td>File Type: </td>
   <td>" . $_FILES["csvfile"]["type"] . "</td></tr>";
echo "<tr><td>File Size: </td>
   <td>" . ($_FILES["csvfile"]["size"] / 1024) . " Kb</td></tr>";
echo "<tr><td>Name of Temporary File: </td>
   <td>" . $_FILES["csvfile"]["tmp_name"] . "</td></tr>";
echo "</table>";



?>
