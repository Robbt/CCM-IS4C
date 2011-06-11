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

include('../src/functions.php');
require_once('../src/mysql_connect.php');

// if(isset($_GET['sort'])){
// 	if(isset($_GET['XL'])){
// 		header("Content-Disposition: inline; filename=deptSales.xls");
// 		header("Content-Description: PHP3 Generated Data");
// 		header("Content-type: application/vnd.ms-excel; name='excel'");
// 	}
// }

if (isset($_POST['submit'])) {
	
	echo "<html><head><title>Department Sales Movement</title>
		<script type=\"text/javascript\" src=\"../src/tablesort.js\"></script>
		<link rel='stylesheet' href='../src/style.css' type='text/css' />
		<link rel='stylesheet' href='../src/tablesort.css' type='text/css' /></head>";

	foreach ($_POST AS $key => $value) {
		$$key = $value;
	}	
	
	echo "<BODY>";

	$today = date("Y-n-d");
	$firstdayofmonth = date("Y-n-1");
// ccm-rle 9-28-09 - the department array shouldn't be hardcoded like this, modified it to match the 18 departments we have set-up at ccm, this will need to be modified to be auto-generated if a department add/remove interface is set-up in fannie 
	if (isset($allDepts)) {
		$deptArray = "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18";
		$arrayName = "ALL DEPARTMENTS";
	} else {
		if (isset($_POST['dept'])) {$deptArray = implode(",",$_POST['dept']);}
		elseif (isset($_GET['dept'])) {$deptArray = $_GET['dept'];}
		$arrayName = $deptArray;
	}

	if (empty($date1)) echo "You did not select any date so a department report for the current month was done.";
	if (empty($date1)) { $date1 = $firstdayofmonth; }
	if (empty($date2)) { $date2 = $today; }
	echo "<center><h1>Department Sales Movement</h1>\n
		<h2>Product Movement for $date1 thru $date2</h2></center>";

	// Check year in query, match to a dlog table
	$year1 = idate('Y',strtotime($date1));
	$year2 = idate('Y',strtotime($date2));

	if ($year1 != $year2) {
		echo "<div id='alert'><h4>Reporting Error</h4>
			<p>Fannie cannot run reports across multiple years.<br>
			Please retry your query.</p></div>";
		exit();
	}
//	elseif ($year1 == date('Y')) { $table = 'dtransactions'; }
	else { $table = 'dlog_' . $year1; }

	$date2a = $date2 . " 23:59:59";
	$date1a = $date1 . " 00:00:00";	

	if(isset($inUse)) {
		$inUseA = "AND p.inUse = 1";
	} else {
		$inUseA = "AND p.inUse IN(0,1)";
	}
	echo "<table border=0><tr><td>";

	if (isset($salesTotal)) {
		$query1 = "SELECT d.dept_name,ROUND(SUM(t.total),2) AS total
			FROM is4c_op.departments AS d, is4c_log.$table AS t
			WHERE d.dept_no = t.department
			AND t.datetime >= '$date1a' AND t.datetime <= '$date2a'
			AND t.department IN($deptArray)
			AND t.trans_subtype NOT IN ('MC','IC')
			AND t.trans_status <> 'X'
			AND t.emp_no <> 9999
			GROUP BY t.department";
				
		$result1 = mysql_query($query1);
//echo $query1;	
		

		if (!$result1) {
			$message  = 'Invalid query: ' . mysql_error() . "\n";
			$message .= 'Whole query: ' . $query1;
			die($message);
		}
		echo "<table border=1>
			<tr>
				<td><b>Department</b></td>
				<td><b>Total Sales</b></td>
			</tr>";
			
		while($myrow = mysql_fetch_row($result1)) {
			echo "<tr><td>$myrow[0]</td><td align=right>" . money_format('%n',$myrow[1]) . "</td></tr>";
                        $totalsl = $myrow[1];

		}
		echo "</table>\n";
		
	}
	echo "</td><td>";
		
	if(isset($openRing)) {
		//$query2 - Total open dept. ring
		$query2 = "SELECT d.dept_name AS Department,ROUND(SUM(t.total),2) AS open_dept
			FROM is4c_op.departments AS d,is4c_log.$table AS t 
			WHERE d.dept_no = t.department
			AND t.datetime >= '$date1a' AND t.datetime <= '$date2a' 
			AND t.department IN($deptArray)
			AND t.trans_type = 'D' 
			AND t.trans_subtype NOT IN ('MC','IC')
			AND t.emp_no <> 9999 AND t.trans_status <> 'X' 
			GROUP BY t.department";

		$result2 = mysql_query($query2);
//	        echo $query2;	
		if (!$result2) {
			$message  = 'Invalid query: ' . mysql_error() . "\n";
			$message .= 'Whole query: ' . $query2;
					die($message);
		}
		
		echo "<table border=1>\n
			<tr>
				<td><b>Department</b></td>
				<td><b>Open Ring</b></td>
			</tr>\n";

		while($myrow = mysql_fetch_row($result2)) {
			echo "<tr><td>" . $myrow[0] . "</td><td align=right>" . money_format('%n',$myrow[1]) . "</td></tr>";
		}
		echo "</table>\n";
	} 
        echo "</td><td>";


	if(isset($deptDiscount)) {	
// $query 6 determines the discount total for a department need to place in its own part of the code
                $query6 = "SELECT d.dept_name AS Department, ROUND( SUM( t.total * ( t.percentDiscount /100 ) ) , 2 ) AS discounttotal
                FROM is4c_op.departments AS d, is4c_log.$table AS t
                WHERE d.dept_no = t.department
                AND t.datetime >= '$date1a' AND t.datetime <= '$date2a'
                AND t.department IN ($deptArray)
                AND t.trans_subtype NOT IN ('MC', 'IC')
                AND t.trans_status <> 'X'
                AND t.emp_no <> 9999
                AND t.discountable <> 0 
		GROUP BY t.department";

                $result6 = mysql_query($query6);
//              echo $query6;
                if (!$result6) {
                        $message  = 'Invalid query: ' . mysql_error() . "\n";
                        $message .= 'Whole query: ' . $query6;
                                        die($message);
                }

                echo "<table border=1>\n
                        <tr>
                                <td><b>Department</b></td>
                                <td><b>Total Discount</b></td>
                        </tr>\n";

                while($myrow = mysql_fetch_row($result6)) {
                        echo "<tr><td>" . $myrow[0] . "</td><td align=right>" . money_format('%n',$myrow[1]) . "</td></tr>";
                        $discountsl = $myrow[1];

                }
                echo "</table>\n";
        } 
        echo "</td><td>";



        if(isset($deptTax)) {

// ccm-rle 6-15-2010 changing this to query the total sales and minus the discount and then factor the tax  
// $query 7 determines the sales tax total for a department need to place in its own part of the code
/*                $query7 = "SELECT d.dept_name AS Department, ROUND( SUM( t.total * 0.0675 ) , 2 ) AS taxtotal 
                FROM is4c_op.departments AS d, is4c_log.$table AS t
                WHERE d.dept_no = t.department
                AND t.datetime >= '$date1a' AND t.datetime <= '$date2a'
                AND t.department IN ($deptArray)
		AND t.tax = 1
                AND t.trans_subtype NOT IN ('MC', 'IC')
                AND t.trans_status <> 'X'
                AND t.emp_no <> 9999
                GROUP BY t.department";

                $result7 = mysql_query($query7);
//                echo $query7;
                if (!$result7) {
                        $message  = 'Invalid query: ' . mysql_error() . "\n";
                        $message .= 'Whole query: ' . $query7;
                                        die($message);
                
*/


$taxed = $totalsl - $discountsl;
$taxsl = $taxed * 0.0675;



}

                echo "<table border=1>\n
                        <tr>
                                <td><b>Department</b></td>
                                <td><b>Total Sales Tax</b></td>
                        </tr>\n";

                        echo "<tr><td>Simply Living</td><td align=right>" . money_format('%n',$taxsl) . "</td></tr>";
                
                echo "</table>\n";
        

        echo "</td><td>";



	echo "</td></tr></table>";		




	if(isset($pluReport)){
		// $query3 - Sales per PLU  -- ccm-rle added SUBSTR comparison to ignore the D appended on items given a percentage discount and count them on the report, otherwise they were ignored.
		$query3 = "SELECT DISTINCT 
			p.upc AS PLU,
			t.upc AS UPC,
			p.description AS Description,
			t.unitPrice AS Price,
			p.wholesale_cost AS Wholesale_Cost,
			p.department AS Dept,
			p.subdept AS Subdept,
			p.brand AS brand,
			p.vendor AS vendor,
			SUM(t.quantity) AS Qty,
			ROUND(SUM(t.total),2) AS Total,
			p.scale as Scale
			FROM is4c_log.$table t, is4c_op.products p
			WHERE SUBSTR(t.upc,2) = SUBSTR(p.upc,2)
			AND t.department IN($deptArray) 
			AND t.datetime >= '$date1a' AND t.datetime <= '$date2a' 
			AND t.emp_no <> 9999
			AND t.trans_status <> 'X'
			AND t.upc NOT LIKE '%DP%'
			$inUseA
			GROUP BY t.unitPrice,t.upc";
	
//		 echo $query3;
		$result3 = mysql_query($query3);
		$num = mysql_num_rows($result3);
		
		if (!$result3) {
			$message  = 'Invalid query: ' . mysql_error() . "\n";
			$message .= 'Whole query: ' . $query3;
				die($message);
		}





		echo "<table id=\"output\" cellpadding=0 cellspacing=0 border=0 class=\"sortable-onload-8 rowstyle-alt colstyle-alt\">\n
		  <caption>Department range: ".$arrayName.". Search yielded (".$num.") results. Generated on " . date('n/j/y \a\t h:i A') . "</caption>\n
		  <thead>\n
		    <tr>\n
		      <th class=\"sortable-numeric\">UPC</th>\n
		      <th class=\"sortable-text\">Description</th>\n
		      <th class=\"sortable-text\">Brand</th>\n
			  <th class=\"sortable\">Cost</th>\n
		      <th class=\"sortable-currency\">Price</th>\n
			  <th class=\"sortable\">Margin</th>\n
		      <th class=\"sortable-numeric\">Dept.</th>\n
		      <th class=\"sortable-numeric\">Subdept.</th>\n
		      <th class=\"sortable-numeric favour-reverse\">Qty.</th>\n
		      <th class=\"sortable-currency favour-reverse\">SALES</th>\n
		      <th class=\"sortable-text\">Scale</th>\n		
		      <th class=\"sortable-text\">Vendor</th>\n
		    </tr>\n
		  </thead>\n
		  <tbody>\n";
		
		while ($row = mysql_fetch_array ($result3, MYSQL_ASSOC)) {
                		if (($row["Price"] != 0.01) && ($row["Price"] != -0.01)) {
				$margin = (1 - ($row["Wholesale_Cost"] / $row["Price"])) * 100;
				if ($margin <= 0) {($margin = 0);}
				$margin = round($margin);
				// if an item in the transaction log was percentage discounted it has a D in the front of it and thus all items sold at a discount will have a orange background color
				echo "<td ";
				if (!strncmp($row["UPC"],'D',1)) { echo " bgcolor=#F7BE81"; } 
				echo "align=center><a href='../index.php?q=" . $row["PLU"] . "'>" . $row["PLU"] . "</a></td>\n
				<td align=left>" . $row["Description"] . "</td>\n
				<td align=left>" . $row["brand"] . "</td>\n
				<td align=right>" . money_format('%n', $row["Wholesale_Cost"]) . "</td>\n
				<td ";
                                if (!strncmp($row["UPC"],'D',1)) { echo " bgcolor=#F7BE81 "; }
				echo "align=right>" . money_format('%n',$row["Price"]) . "</td>\n
				<td align=right>" . $margin . "</td>\n
				<td align=left>" . $row["Dept"] . "</td>\n
				<td align=left>" . $row["Subdept"] . "</td>\n
				<td align=right>" . number_format($row["Qty"],2) . "</td>\n
				<td align=right>" . money_format('%n',$row["Total"]) . "</td>\n";
				if($row["Scale"] == 1){
					echo "<td align=center>#</td>";
				} else {
					echo "<td align=center>ea.</td>";
				}
			                
				echo "<td align=left>" . $row["vendor"] . "</td>\n";
				

			echo "</tr>\n";

		}
		}

                echo "</table>\n";

		        }

        if(isset($variablepricedReport)){
                // $query9 - Number of items sold at an individual discount per PLU
                $query9 = "SELECT DISTINCT 
                        p.upc AS PLU,
                        p.description AS Description,
                        t.unitPrice AS Price,
                        p.wholesale_cost AS Wholesale_Cost,
                        p.department AS Dept,
                        p.subdept AS Subdept,
                        p.brand AS brand,
                        p.vendor AS vendor,
                        COUNT(t.upc) AS Qty,
                        SUM(t.total) AS Total,
                        p.scale as Scale
                        FROM is4c_log.$table t, is4c_op.products p
                        WHERE t.upc = p.upc
			AND t.unitPrice = 0.01
                        AND t.department IN($deptArray) 
			AND t.datetime >= '$date1a' AND t.datetime <= '$date2a' 
                        AND t.emp_no <> 9999
                        AND t.trans_status <> 'X'
                        AND t.upc NOT LIKE '%DP%'
                        $inUseA
                        GROUP BY t.unitPrice,t.upc";

                //echo $query9;
                $result9 = mysql_query($query9);
                $num = mysql_num_rows($result9);

                if (!$result9) {
                        $message  = 'Invalid query: ' . mysql_error() . "\n";
                        $message .= 'Whole query: ' . $query9;
                                die($message);
                }





                echo "<table id=\"output\" cellpadding=0 cellspacing=0 border=0 class=\"sortable-onload-8 rowstyle-alt colstyle-alt\">\n
                  <thead>\n
                    <tr>\n
                      <th class=\"sortable-numeric\">UPC</th>\n
                      <th class=\"sortable-text\">Description</th>\n
                      <th class=\"sortable-text\">Brand</th>\n
                      <th class=\"sortable-numeric\">Dept.</th>\n
                      <th class=\"sortable-numeric\">Subdept.</th>\n
                      <th class=\"sortable-numeric favour-reverse\"># of Trans.</th>\n
                      <th class=\"sortable-currency favour-reverse\">SALES TOTAL</th>\n
                      <th class=\"sortable-text\">Scale</th>\n
                      <th class=\"sortable-text\">Vendor</th>\n
                    </tr>\n
                  </thead>\n
                  <tbody>\n";

                while ($row = mysql_fetch_array ($result9, MYSQL_ASSOC)) {
                                $margin = (1 - ($row["Wholesale_Cost"] / $row["Price"])) * 100;
                                if ($margin <= 0) {($margin = 0);}
                                $margin = round($margin);
                                echo "<td align=center><a href='../index.php?q=" . $row["PLU"] . "'>" . $row["PLU"] . "</a></td>\n
                                <td align=left>" . $row["Description"] . "</td>\n
                                <td align=left>" . $row["brand"] . "</td>\n

                                <td align=left>" . $row["Dept"] . "</td>\n
                                <td align=left>" . $row["Subdept"] . "</td>\n
                                <td align=right>" . number_format($row["Qty"],2) . "</td>\n
                                <td align=right>" . money_format('%n',$row["Total"]) . "</td>\n";
                                if($row["Scale"] == 1){
                                        echo "<td align=center>#</td>";
                                } else {
                                        echo "<td align=center>ea.</td>";
                                }

                                echo "<td align=left>" . $row["vendor"] . "</td>\n";


                        echo "</tr>\n";
                
                }

                echo "</table>\n";

                        }


        if(isset($variablepricesalesReport)){
                // $query10 - Individual Sales per Variable Priced Item
                $query10 = "SELECT DISTINCT 
                        t.total AS Price,
                        t.description AS Description,
                        t.datetime AS Datetime
                        FROM is4c_log.$table t
                        WHERE t.department IN($deptArray) 
                        AND t.datetime >= '$date1a' AND t.datetime <= '$date2a' 
                        AND t.emp_no <> 9999
                        AND t.unitPrice = '0.01'
                        AND t.trans_status <> 'X'
                        ";

                // echo $query10;
                $result10 = mysql_query($query10);
                $num = mysql_num_rows($result10);

                if (!$result10) {
                        $message  = 'Invalid query: ' . mysql_error() . "\n";
                        $message .= 'Whole query: ' . $query3;
                                die($message);
                }
                echo "<table>
                  <caption>Variable Priced Item Sales Department range: ".$arrayName.". Search yielded (".$num.") results. </caption>\n 
                  <thead>\n
                    <tr>\n
                      <th class=\"sortable-text\">Description</th>\n
                      <th class=\"sortable-currency\">Price</th>\n
                      <th class=\"sortable\">Date of Transaction</th>\n
                    </tr>\n
                  </thead>\n
                  <tbody>\n";

                while ($row = mysql_fetch_array ($result10, MYSQL_ASSOC)) {

                                echo"<tr><td align=left>" . $row["Description"] . "</td>\n
                                <td align=right>" . money_format('%n',$row["Price"]) . "</td>\n
                                <td align=left>" . $row["Datetime"] . "</td>\n";

                        echo "</tr>\n";
                }


                echo "</table>\n";  }


        if(isset($openringsalesReport)){
                // $query4 - Sales per PLU
                $query4 = "SELECT DISTINCT 
                        t.unitPrice AS Price,
                        t.description AS Description,
                        t.datetime AS Datetime
                        FROM is4c_log.$table t
                        WHERE t.department IN($deptArray) 
                        AND t.datetime >= '$date1a' AND t.datetime <= '$date2a' 
                        AND t.emp_no <> 9999
                        AND t.trans_type = 'D'
                        AND t.trans_status <> 'X'
                        ";

//                 echo $query4;
                $result4 = mysql_query($query4);
                $num = mysql_num_rows($result4);

                if (!$result4) {
                        $message  = 'Invalid query: ' . mysql_error() . "\n";
                        $message .= 'Whole query: ' . $query3;
                                die($message);
                }
                echo "<table>
                  <caption>Open Ring Sales Department range: ".$arrayName.". Search yielded (".$num.") results. </caption>\n 
                  <thead>\n
                    <tr>\n
                      <th class=\"sortable-text\">Description</th>\n
                      <th class=\"sortable-currency\">Price</th>\n
                      <th class=\"sortable\">Date of Transaction</th>\n
                    </tr>\n
                  </thead>\n
                  <tbody>\n";

                while ($row = mysql_fetch_array ($result4, MYSQL_ASSOC)) {

				echo"<tr><td align=left>" . $row["Description"] . "</td>\n
                                <td align=right>" . money_format('%n',$row["Price"]) . "</td>\n
				<td align=left>" . $row["Datetime"] . "</td>\n";
                               
                        echo "</tr>\n";
                }


                echo "</table>\n";  }

         

		



	// PHP INPUT DEBUG SCRIPT  -- very helpful!
	//

	// function debug_p($var, $title) 
	// {
	//     print "<p>$title</p><pre>";
	//     print_r($var);
	//     print "</pre>";
	// }  
	// 
	// debug_p($_REQUEST, "all the data coming in");
} else {

$page_title = 'Fannie - Reporting';
$header = 'Movement Report';
include('../src/header.html');

echo '<link href="../src/style.css" rel="stylesheet" type="text/css">
<script src="../src/CalendarControl.js" language="javascript"></script>

<form method="post" action="simplylivingSales.php" target="_blank">		

<div id="box">
	<table border="0" cellspacing="3" cellpadding="3">
		<tr> 
            <th align="center"> <p><b>Select dept.*</b></p></th>
		</tr>
		<tr valign=top>';

include('../src/departments.php');

echo '</tr>
	</table>
</div>
<div id="box">
	<table border="0" cellspacing="3" cellpadding="3">
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
				<p>Date format is YYYY-MM-DD</br>(e.g. 2004-04-01 = April 1, 2004)</p>
			</td>
		</tr>
	</table>
</div>
<div id="box">
	<table border="0" cellspacing="3" cellpadding="3">
	<tr>
			<td align="right"><p><b>Sales totals</b></p></td>
			<td><input type="checkbox" value="1" name="salesTotal" CHECKED></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right"><p><b>Open ring totals</b></p>
			</td><td><input type="checkbox" value="1" name="openRing" CHECKED></td>
			<td>&nbsp;</td>
		</tr>
                <tr>
                       <td align="right"><p><b>Open Ring report</b></p></td>
                         <td><input type="checkbox" value="1" name="openringsalesReport">
                        </td>
                </tr>
                <tr>
                     <td align="right"><p><b>Department Tax Report</b></p></td>
                     <td><input type="checkbox" value="1" name="deptTax">
                     </td>
               </tr>

		<tr>
			<td align="right"><p><b>PLU report</b></p></td>
			<td>
				<input type="checkbox" value="1" name="pluReport" CHECKED>
			</td>
		</tr>
		<tr>
		       <td align="right"><p><b>Department Discount report</b></p></td>
		       <td>
		       <input type="checkbox" value="1" name="deptDiscount">
		       </td>
                </tr>
		<tr> 
                     <td align="right"><p><b>Variable Priced Items report</b></p></td>
                            <td>
                            <input type="checkbox" value="1" name="variablepricedReport">
                            </td>
                  </tr>
                <tr> 
                     <td align="right"><p><b>Variable Priced Sales report</b></p></td>
                            <td>
                            <input type="checkbox" value="1" name="variablepricesalesReport">
                            </td>
                  </tr>

		<tr>
			<td colspan="3" align="center">
				<p>* -- indicates required field</p>
			</td>
		</tr>
		<tr> 
			<td>&nbsp;</td>
			<td> <input type=submit name=submit value="Submit"> </td>
			<td> <input type=reset name=reset value="Start Over"> </td>
		</tr>
	</table>
</div>
</form>';

include('../src/footer.html');
}
?>

