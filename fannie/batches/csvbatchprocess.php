<?
require_once('../src/mysql_connect.php');
$output = array('Pass' => 0, 'Fail' => 0);

ini_set('auto_detect_line_endings',1);

$filename = $_FILES["csvfile"]["tmp_name"];
//if ($_FILES["csvfile"]["type"] == "csv") {

$handle = fopen($_FILES["csvfile"]["tmp_name"], 'r');



while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
   
    $upc = mysql_real_escape_string($data[0]);
    
    $upc = substr($upc,0, 11);
    $upc = "00" . $upc;
    // ccm-rle - will need to change below to be variable
    $batchID = $_POST["batchID"];
    $salePrice = mysql_real_escape_string($data[1]);
    $batch_active = 1;

$batchInfoQ = "SELECT * FROM batches WHERE batchID = $batchID";
$batchInfoR = mysql_query($batchInfoQ);
$batchInfoW = mysql_fetch_row($batchInfoR);
    //echo "INSERT INTO `table` (`col1`,`col2`,`col3`) VALUES ('{$val1}','{$val2}','{$val3}')";

    $result = (mysql_insert_id()> 0) ? 'Pass' : 'Fail' ;
   
    $output[$result]++;

    $insBItemQ = "INSERT INTO batchList(upc,batchID,salePrice,active)
                        VALUES('$upc',$batchID,$salePrice,$batch_active)";
              echo $insBItemQ;
                $insBItemR = mysql_query($insBItemQ);
                
echo "</br>";
// echo "<h1>" . $batchInfoW[6] . "</h1>";
                if ($upc != 0 && $batch_active == 1) {
                        $prodUpdateQ = "UPDATE products AS p, batches AS b, batchList AS l 
                                SET p.special_price = $salePrice, p.start_date = b.startDate, p.end_date = b.endDate, p.discounttype = b.discounttype 
                                WHERE l.upc = p.upc AND b.batchID = l.batchID AND b.batchID = $batchID AND p.upc = $upc";
//   echo $prodUpdateQ;
                        $prodUpdateR = mysql_query($prodUpdateQ) OR DIE ("<div id=alert><p>ERROR!</p><br />" . mysql_error() . "<br /></div>");
                        if ($prodUpdateR) { echo "<div id=alert><p>Products table updated successfully (upc = $upc)</p></div>";}
                }



}
//}
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
