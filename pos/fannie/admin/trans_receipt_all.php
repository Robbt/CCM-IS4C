<?php
/*******************************************************************************

    Copyright 2007 People's Food Co-op, Portland, Oregon.

    This file is part of Fannie.

    IS4C is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    IS4C is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    in the file license.txt along with IS4C; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*********************************************************************************/
include('../src/password_protect_admin.php');
setlocale(LC_MONETARY, 'en_US');
require_once('../src/mysql_connect.php');

if(isset($_POST['submit'])){
	foreach ($_POST AS $key => $value) {
		$$key = $value;
	}
}else{
      foreach ($_GET AS $key => $value) {
          $$key = $value;
      }
}

echo '<html>
	<head>
	<Title>Receipt</Title>
	<link rel="stylesheet" href="../src/style.css" type="text/css" />
	</head>
	<body>';

$trans_array = explode('-',$t_id);
	$year = $trans_array[0];
	$month = $trans_array[1];
	$day = $trans_array[2];
	$emp_no = $trans_array[3];
	$register_no = $trans_array[4];
	$trans_no = $trans_array[5];

 if ($year == date('Y') && $month == date('m') && $day == date('d')) { $table = "dtransactions";}
 else { $table = "dlog_" . date('Y');}
$table = "dlog_" . $year;

// ccm-rle 9-25-09 - is4c vs. fannie use a different table, the new version of fannie code references dlog_2009 in various places whereas there is no code to change or create a new table for each year yet. will communicate with joel about this. the below commented out variable declaration was done by me to try to override this but it occurs at other places so I should figure out a solution and write code to generate a new table for each year. will duplicate current dlog table to a year based one initially for testing purposes.

// $table = "dlog";


$query = "SELECT * FROM is4c_log.$table 
	WHERE DATE(datetime) = '" . $year."-".$month."-".$day . "'
	AND register_no = $register_no 
	ORDER BY datetime";
// echo $query;
$result = mysql_query ($query);

echo "<table border=0 cellpadding=0 width=500px>";
//echo $month."/".$day."/".substr($year,2,2)." ".$time;
echo "<b>This is a print out of all receipts at Register # " . $register_no . " on " .  $month."/".$day."/".substr($year,2,2)."</b>";
echo "</h3></center><br><br>";

echo "</td></tr>";

while ($row = mysql_fetch_array ($result, MYSQL_ASSOC)) {
	echo '<tr>
		<td align="left">' . $row['description'] . '</td>
		<td align="center">';
	if ($row['Scale'] == 1) {
		echo $row['quantity'] . " @ " . $row['unitPrice'];
	} else { echo "&nbsp;"; }
	echo '</td><td align="right">';
	if ($row['total'] == 0) { echo "&nbsp;"; } 
	else { echo money_format('%n',$row['total']); } 
	echo '</td><td>';
	if ($row['trans_status'] == 'V') { echo " VD";}
	elseif ($row['foodstamp'] == 1) { echo " F";}
	else { echo "&nbsp;";}
	echo '</td>';
	echo '<td>';
	echo  $row['emp_no']."-".$row['register_no']."-".$row['trans_no']; 
	echo '</td>';
	echo '<td>';
	echo $row['datetime'];
	echo '</td>';
	echo '</tr>';
}

echo '<td colspan=6 align=center><br><br><center><p>';
if ($card_no != 99999) { echo "Thank You Member # " . $card_no . "."; }
else { echo 'Thank You.'; }
echo '</p><p>THANKS FOR SHOPPING<br>www.communitymarket.org
	</center>';
echo '<br><br><center>Receipt generated on: ' . date("D M j G:i:s T Y") . '</center>';
echo "</td></tr></table>";
?>
