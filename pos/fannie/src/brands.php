<?php
require_once('mysql_connect.php');
// ccm-rle 9-28-09 - not sure why departments with discounts can't get reports generated on them but with the query below set to department <> 0. It makes it so that they won't show up under the list. I commented that out and added a query that simply selected from departments

//$query = "SELECT * FROM departments WHERE dept_discount <> 0";
$query = "SELECT * FROM brands ORDER BY brand";
$result = mysql_query($query);

echo "<td><font size='-1'>
	<p><input type='checkbox' value=1 name='allBrands' CHECKED><b>All Departments</b><br>";
while ($row = mysql_fetch_assoc($result)) {
	echo "<input type='checkbox' name='brand[]' value='".$row['brandid']."'>".ucwords(strtolower($row['brand']))."<br>";
}
echo "</p></font></td>";

?>
