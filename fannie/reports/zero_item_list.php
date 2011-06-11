<?php
require_once('../src/mysql_connect.php');

if(isset($_POST['submit']) || isset($_GET['sort'])) {

echo "<html><head><title>Department Zero Item Sales Product List</title>
	<script type=\"text/javascript\" src=\"../src/tablesort.js\"></script>
	<link rel='stylesheet' href='../src/style.css' type='text/css' />
	<link rel='stylesheet' href='../src/tablesort.css' type='text/css' /></head>";

	
if (isset($_GET['sort'])) {
	foreach ($_GET AS $key => $value) {
		$$key = $value;
		//echo $key ." : " .  $value."<br>";
	}
} else {
	foreach ($_POST AS $key => $value) {
		$$key = $value;
	}	
}
echo "<body>";

$today = date("F d, Y");	
// ccm-rle 9-28-09 deptArray should be dynamically generated vs. hard-coded
if (isset($allDepts)) {
	$deptArray = "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23";
	$arrayName = "ALL DEPARTMENTS";
} else {
	if (isset($_POST['dept'])) {$deptArray = implode(",",$_POST['dept']);}
	elseif (isset($_GET['dept'])) {$deptArray = $_GET['dept'];}
	$arrayName = $deptArray;
}

// ccm-rle 9-28-09 need to modify query below to add in whole-sale cost and brand
if ($inUse==1) {$inUseQ = 'AND inUse = 1';} else {$inUseQ = '';}


// ccm-rle 12-10-2010 added below to get date from form and default to year round if not selected

                $today = date("F d, Y");
                if (empty($date1)) $date1 = date('Y') . '-01-01';
                if (empty($date2)) $date2 = date('Y-m-d');

                echo "Zero Item Report run on:";
                echo $today;
                echo "</br>";
                echo "From ";
                print $date1;
                echo " to ";
                print $date2;
                echo "</br>";
                echo "</br>";

                // Check year in query, match to a dlog table
                $year1 = idate('Y',strtotime($date1));
                $year2 = idate('Y',strtotime($date2));

if ($year1 != $year2) {
                        echo "<div id='alert'><h4>Reporting Error</h4>
                                <p>Fannie cannot run reports across multiple years.<br>
                                Please retry your query.</p></div>";
                        exit();
                }
                elseif (isset($todayonly)) { $table = 'dtransactions    '; }
//              elseif ($year1 == date('Y')) { $table = 'dtransactions'; }
                else { $table = 'dlog_' . $year1; }
                if (isset($todayonly)) {
                $date2a = date('Y-m-d') . " 23:59:59";
                $date1a = date('Y-m-d') . " 00:00:00"; }
                else {
                $date2a = $date2 . " 23:59:59";
                $date1a = $date1 . " 00:00:00";
                }





$query1 =" 
CREATE TEMPORARY TABLE is4c_log.zeroitem(
upc BIGINT( 13 ) ,
INDEX ( upc )
);
";

$query2 = "


INSERT INTO is4c_log.zeroitem( upc )
SELECT DISTINCT SUBSTR(t.upc, 2)    
FROM is4c_log.$table t
WHERE t.datetime BETWEEN '$date1a' AND '$date2a'
;
";

$query = "
	SELECT p.upc AS UPC, 
	p.description AS description,
	p.normal_price AS price, 
	d.dept_name AS dept, 
	s.subdept_name AS subdept, 
	p.foodstamp AS fs, 
	p.scale AS scale, 
	p.inuse AS inuse, 
	p.special_price AS sale,
	p.wholesale_cost AS wholesale,
        p.brand AS brand,
	p.vendor AS vendor
    FROM is4c_op.products AS p INNER JOIN is4c_op.subdepts AS s ON s.subdept_no = p.subdept INNER JOIN is4c_op.departments as d ON d.dept_no = p.department
    LEFT JOIN is4c_log.zeroitem z ON p.upc = z.upc WHERE z.upc IS NULL AND p.department IN ($deptArray)
    $inUseQ";
     echo $query2;
     echo $query;
$result1 = mysql_query($query1);
$result2 = mysql_query($query2);

$result = mysql_query($query);
$num = mysql_num_rows($result);

echo "<center><h1>Zero Item Sales Product List</h1></center>";

echo "<table id=\"output\" cellpadding=0 cellspacing=0 border=0 class=\"sortable-onload-1 rowstyle-alt colstyle-alt\">\n
  <caption>Department range: ".$arrayName.". Search yielded (".$num.") results. Generated on " . date('n/j/y \a\t h:i A') . "</caption>\n
  <thead>\n
    <tr>\n
      <th class=\"sortable-numeric\">UPC</th>\n
      <th class=\"sortable-text\">Description</th>\n
      <th class=\"sortable-text\">Brand</th>\n
      <th class=\"sortable-text\">Vendor</th>\n
      <th class=\"sortable-currency\">Price</th>\n
      <th class=\"sortable-currency\">Wholesale</th>\n
      <th class=\"sortable-text\">Dept.</th>\n
      <th class=\"sortable-text\">Subdept.</th>\n
      <th class=\"sortable-text\">FS</th>\n
      <th class=\"sortable-text\">wgh.</th>\n
      <th class=\"sortable-text\">Sale</th>\n		
    </tr>\n
  </thead>\n
  <tbody>\n";

// Fetch and print all the records.
// $bg = '#eeeeee'; // Set background color.
// ccm-rle 9-28-09 modified this to add product list and whole-sale cost, also will add link to UPC to edit_product directly if possible
while ($row = mysql_fetch_array ($result, MYSQL_ASSOC)) {
	// $bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee'); // Switch the background color.
	echo '<tr>
		<td align=right><a href="../index.php?q=' . $row["UPC"] . '">' . $row["UPC"] . '</a></td>
		<td>' . $row["description"] . '</td>
		<td>' . $row["brand"] . '</td>
		<td>' . $row["vendor"] . '</td>
		<td align=right>' . money_format('%n',$row["price"]) . '</td>
		<td align=right>' . money_format('%n',$row["wholesale"]) . '</td>
		<td>' . substr($row["dept"],0,10) . '</td>
		<td>' . substr($row["subdept"],0,20) . '</td>
		<td>'; 
	if ($row["fs"] == 1) { echo 'FS';} else { echo "X";}
	echo '</td><td align=center>';
	if($row["scale"] == 1) { echo '#';} else { echo 'ea.';}
	echo '</td><td align=right><font color=green>';
	if($row["sale"] == 0) { echo '';} else { echo $row["sale"];}
	echo '</font></td></tr>';


}

echo '</table>';

//
// PHP INPUT DEBUG SCRIPT  -- very helpful!
//
/*
function debug_p($var, $title) 
{
    print "<p>$title</p><pre>";
    print_r($var);
    print "</pre>";
}  

debug_p($_REQUEST, "all the data coming in");
*/
} else {
	
$page_title = 'Fannie - Reporting';
$header = 'Zero Item Sales- Product List';
include('../src/header.html');

echo '<script src="../src/CalendarControl.js" language="javascript"></script>
    <SCRIPT LANGUAGE="JavaScript">
      function putFocus(formInst, elementInst) {
       if (document.forms.length > 0) {
         document.forms[formInst].elements[elementInst].focus();
       }
      }
    </script>';



echo '<form method = "post" action="zero_item_list.php" target="_blank">
	<table border="0" cellspacing="3" cellpadding="5" align="center">
		<tr> 
            <th colspan="2" align="center"> <p><b>Select dept.</b></p></th>
		</tr>
		<tr>';

include('../src/departments.php');

echo '</tr>
        <tr>
			<td>
			<font size="-1"><input type="checkbox" name="inUse" value=1><b>Filter PLUs that aren&apos;t "In Use"?</b></font><br />
			</td>
		</tr>

                  <tr>
                          <td align="right">
                                  <p><b>Date Start</b> </p>
                          <p><b>End</b></p>
                          </td>
                          <td>
                                  <p><input type=text size=10 name=date1 onfocus="showCalendarControl(this);">&nbsp;&nbsp;*</p>
                                  <p><input type=text size=10 name=date2 onfocus="showCalendarControl(this);">&nbsp;&nbsp;*</p>
                          </td>
                          <td colspan=2>
                                  &nbsp;
                          </td>

                  </tr>



	<tr> 
			<td><input type=submit name=submit value="Submit"> </td>
			<td><input type=reset name=reset value="Start Over"> </td>
			<td>&nbsp;</td>
		</tr>
	</table>
</form>';

include('../src/footer.html');
}
?>
