<?php
/*******************************************************************************

    Copyright 2001, 2004 Wedge Community Co-op

    This file is part of IS4C.

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
 // session_start(); 
if (!function_exists("tDataConnect")) include("connect.php");
if (!function_exists("memberID")) include("prehkeys.php");
if (!function_exists("receipt")) include("clientscripts.php");
if (!function_exists("gohome")) include("maindisplay.php");

// lines 40-45 edited by apbw 7/12/05 to resolve "undefined index" error message

if (isset($_POST["selectlist"])) {
	$resume_trans = strtoupper(trim($_POST["selectlist"]));
}
else {
	$resume_trans = "";
}

if (!$resume_trans || strlen($resume_trans) < 1) gohome();
else {
	$resume_spec = explode("::", $resume_trans);
	$suspendedtoday = "suspendedtoday";
	$suspended = "suspended";
//ccm-rle added echo below to try to get see whats happening with the transaction
        

	$register_no = $resume_spec[0];
	$emp_no = $resume_spec[1];
	$trans_no = $resume_spec[2];

// ccm-rle 10-1-2009 the data from each of these is being passed on fine
	$db_a = tDataConnect();
	$m_conn = mDataConnect();
	resumesuspended($register_no, $emp_no, $trans_no);

/*	

	if ( $_SESSION["standalone"] == 0 && $_SESSION["remoteDBMS"] == "mssql" ) {

		$suspendedtoday = $_SESSION["remoteDB"]."suspendedtoday";
		$suspended = $_SESSION["remoteDB"]."suspended";
	} 


	if ($_SESSION["standalone"] == 0 && $_SESSION["remoteDBMS"] != "mssql" ) {
		
		$m_conn = mDataConnect();
		$db_a = tDataConnect();

		$downloadfile = $_SESSION["downloadPath"]."resume.out";
		if (file_exists($downloadfile)) exec("rm ".$downloadfile);
		$out = "select * into outfile '".$downloadfile."' from suspendedtoday "
			."where register_no = ".$resume_spec[0]
			." and emp_no = ".$resume_spec[1]
			." and trans_no = ".$resume_spec[2];
		if (mysql_query($out, $m_conn)) {
			if (file_exists($downloadfile)) {
				$resume = "load data infile '".$downloadfile."' into table resume";
				if (mysql_query($resume, $db_a)) $suspendedtody = "resume";
			}
		}
	}
	$query = "insert localtemptrans "


		."datetime, register_no, emp_no, trans_no, upc, "
		."description, trans_type, trans_subtype, trans_status, department, quantity, scale, "
		."unitPrice, total, regPrice, tax, foodstamp, discount, memDiscount, discountable, "
		."discounttype, voided, percentDiscount, ItemQtty, volDiscType, volume, VolSpecial, mixMatch, "
		."matched, card_no "
		."from ".$suspendedtoday." where register_no = ".$resume_spec["0"]
		." and emp_no = ".$resume_spec["1"]." and trans_no = ".$resume_spec["2"];


	$query_del = "delete from ".$suspended." where register_no = ".$resume_spec["0"]." and emp_no = "
		.$resume_spec["1"]." and trans_no = ".$resume_spec["2"];

	$db_a = tDataConnect();
	$m_conn = mDataConnect();

	$query_a = "select * from localtemptrans";
	$result_a = sql_query($query_a, $db_a);
	$num_rows_a = sql_num_rows($result_a);

	if ($num_rows_a == 0) {


		if ($_SESSION["remoteDBMS"] == "mssql") {
			mssql_query($query, $db_a);
			mssql_query($query_del, $db_a);

		}
		else {
			$loadresume = "load data infile '".$downloadfile."' into table localtemptrans";
			mysql_query($loadresume, $db_a);
			mysql_query($query_del, $db_a);
			if ($_SESSION["standalone"] == 0) { mysql_query($query_del, $m_conn); }
		}

	}

*/

	$query_update = "update localtemptrans set register_no = ".$_SESSION["laneno"].", emp_no = ".$_SESSION["CashierNo"]
		.", trans_no = ".$_SESSION["transno"];
	
        sql_query($query_update, $db_a);
	sql_close($db_a);
	getsubtotals();
	$_SESSION["unlock"] = 1;

	if ($_SESSION["memberID"] != 0 && strlen($_SESSION["memberID"]) > 0 && $_SESSION["memberID"]) {
		memberID($_SESSION["memberID"]);
	}

	$_SESSION["msg"] =0;
//	receipt("resume");
	goodbeep();
	gohome();
}
// ccm-rle 2009 noticed that this is only pulling in the t_conn and not the m_conn, from what I can tell m_conn connects to the server and t_conn the local
function resumesuspended($register_no, $emp_no, $trans_no) {
	$t_conn = tDataConnect();

	mysql_query("truncate table is4c_log.suspended");
	$output = "";
	openlog("is4c_connect", LOG_PID | LOG_PERROR, LOG_LOCAL0);
	exec('mysqldump -u '.$_SESSION['mUser'].' -h '.$_SESSION["mServer"].' -t '.$_SESSION['mDatabase'].' '.'suspended'.' | mysql -u '.$_SESSION["localUser"].' '.'is4c_log'." 2>&1", $result, $return_code);
	foreach ($result as $v) {$output .= "$v\n";}
	if ($return_code == 0) {
		if (insertltt($register_no, $emp_no, $trans_no) == 1) {
			trimsuspended($register_no, $emp_no, $trans_no);
			return 1;
		}
	} else {
		syslog(LOG_WARNING, "resumesuspended() failed; rc: '$return_code', output: '$output'");
		return 0;
	}
}

function insertltt($register_no, $emp_no, $trans_no) {
	$inserted = 0;
	$conn = tDataConnect();
	mysql_query("truncate table localtemptrans", $conn);

	$query = "insert into localtemptrans "
		."(datetime, register_no, emp_no, trans_no, upc, description, trans_type, trans_subtype, "
		."trans_status, department, quantity, scale, unitPrice, total, regPrice, tax, foodstamp, "
		."discount, memDiscount, discountable, discounttype, voided, percentDiscount, ItemQtty, "
		."volDiscType, volume, VolSpecial, mixMatch, matched, card_no, memType, staff) "
		."select "
		."datetime, register_no, emp_no, trans_no, upc, description, trans_type, trans_subtype, "
		."trans_status, department, quantity, scale, unitPrice, total, regPrice, tax, foodstamp, "
		."discount, memDiscount, discountable, discounttype, voided, percentDiscount, ItemQtty, "
		."volDiscType, volume, VolSpecial, mixMatch, matched, card_no, memType, staff "
		."from is4c_log.suspended where register_no = ".$register_no
		." and emp_no = ".$emp_no." and trans_no = ".$trans_no;


// ccm-rle 10-1-2009  This is trying to capture the code from above to see if it is being ran
//        echo $query;
//        $myFile = "querytestlog.txt";
//        $fh = fopen($myFile, 'w');
//        fwrite($fh, $query);
//        fclose($fh);



	if (mysql_query($query, $conn)) {
		if (mysql_query("truncate table is4c_log.suspended", $conn)) $inserted = 1;
	}
	return $inserted;
}

function trimsuspended($register_no, $emp_no, $trans_no) {

	$conn = mDataConnect();
	$query = "delete from suspended "
		." where register_no = ".$register_no
		." and emp_no = ".$emp_no." and trans_no = ".$trans_no; 
	mysql_query($query, $conn);

}







?>

<FORM name='hidden'>
<INPUT Type='hidden' name='alert' value='noScan'>
</FORM>
