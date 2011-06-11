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
if (!function_exists("printReceipt")) include("printReceipt.php");
if (!function_exists("mDataConnect")) include("connect.php");

function suspendedCheck() {

	$db_a = mDataConnect();
        $eosQ = "select max(tdate) from dlog where register_no = " .$_SESSION["laneno"]. " and upc = 'ENDOFSHIFT'";
        $eosR = mysql_query($eosQ);
        $row = mysql_fetch_row($eosR);
        $EOS = $row[0];


        $suspendedtranQ = "SELECT COUNT(DISTINCT trans_no)
                from suspendedtoday
                where datetime > '" .$EOS. "'
                and register_no = " .$_SESSION['laneno']. "
                and emp_no <> 9999";

	$suspendedtranR = mysql_query($suspendedtranQ);
        $row = mysql_fetch_row($suspendedtranR);
	$suspendedNum = $row[0];
	return($suspendedNum);
	}	
	


function tenderReport() {

	$db_a = mDataConnect();
	
	$blank = "             ";
	
	$eosQ = "select max(tdate) from dlog where register_no = " .$_SESSION["laneno"]. " and upc = 'ENDOFSHIFT'";
	$eosR = mysql_query($eosQ);
	$row = mysql_fetch_row($eosR);
	$EOS = $row[0];
//	$EOS = '2007-08-01 12:00:00';
	
	$query_ckq = "select * from cktenders where tdate > '" .$EOS. "' and register_no = ".$_SESSION["laneno"]." order by emp_no, tdate";
	$query_ccq = "select * from cctenders where tdate > '" .$EOS. "' and register_no = ".$_SESSION["laneno"]." order by emp_no, tdate";
	$query_dcq = "select * from dctenders where tdate > '" .$EOS. "' and register_no = ".$_SESSION["laneno"]." order by emp_no, tdate";
	$query_miq = "select * from mitenders where tdate > '" .$EOS. "' and register_no = ".$_SESSION["laneno"]." order by emp_no, tdate";
        $query_fsq = "select * from fstenders where tdate > '" .$EOS. "' and register_no = ".$_SESSION["laneno"]." order by emp_no, tdate";
	$query_bp = "select * from buspasstotals where tdate > '" .$EOS. "' and register_no = ".$_SESSION["laneno"]." order by emp_no, tdate";

	$fieldNames = "  ".substr("Time".$blank, 0, 10)
			.substr("Lane".$blank, 0, 7)
			.substr("Trans #".$blank, 0, 6)
			.substr("Emp #".$blank, 0, 8)
			.substr("Change".$blank, 0, 10)
			.substr("Amount".$blank, 0, 10)."\n";

	$ref = centerString(trim($_SESSION["CashierNo"])."-".trim($_SESSION['laneno'])." ".trim($_SESSION["cashier"])." ".build_time(time()))."\n";
// ----------------------------------------------------------------------------------------------------

	$receipt .= chr(27).chr(33).chr(5).centerString("T E N D E R  R E P O R T")."\n";

	$receipt .= $ref;
	$receipt .=	centerString("------------------------------------------------------");
	$receipt .= str_repeat("\n", 2);
// --------------------------------
// ccm-rle 10-12-2009 adding a total gross query that calculates the total gross sales
// removed the sales tax from the gross total by removing IN (,'A') from trans_type 

        $grossQ = "SELECT SUM(total) AS gross 
                from dlog
                where tdate > '" .$EOS. "'
                and register_no = " .$_SESSION['laneno']. "
                and trans_type IN('I','D')
                and trans_subtype NOT IN('IC','MC')
                and trans_status <> 'X'
                and UPC <> 'DISCOUNT'
                AND emp_no <> 9999";

// ccm-rle delete this
/*        $fp=fopen('cancel-log.txt','w');

        fwrite($fp,$grossQ);
        fclose($fp);
*/



        $grossR = mysql_query($grossQ);
        $row = mysql_fetch_row($grossR);

        $receipt .= "  ".substr("Gross Total: ".$blank.$blank,0,20);
        $receipt .= substr($blank.number_format(($row[0]),2),-8)."\n";
        $receipt .= "\n";

// --- ccm-rle adding total discounts for tracking purposes to tenderReport
        $disc_tot = "SELECT ROUND(SUM(total),2) AS gross
                FROM dlog
                WHERE tdate > '" .$EOS. "'
                AND register_no = ".$_SESSION['laneno']."               
                AND upc = 'DISCOUNT'
		AND emp_no <> 9999";
        $results_tot = mysql_query($disc_tot);
        $row = mysql_fetch_row($results_tot);

        $receipt .= "  ".substr("Discount Total: ".$blank.$blank,0,20);
        $receipt .= substr($blank.number_format(($row[0] * -1),2),-8)."\n";

// ccm-rle adding up all of the items that were taxable as sales - this number won't include items that were food stamp tendered... so I'm commenting it out.
/*
        $taxablesalesQ = "SELECT SUM(total) AS gross 
                from dlog
                where tdate > '" .$EOS. "'
                and register_no = " .$_SESSION['laneno']. "
                and trans_type IN('I','D')
                and trans_subtype <> 'IC'
                and tax = 1
                and trans_status <> 'X'
                AND emp_no <> 9999";

        $taxablesalesR = mysql_query($taxablesalesQ);
        $row = mysql_fetch_row($taxablesalesR);

        $receipt .= "  ".substr("Taxable Sales Total: ".$blank.$blank,0,20);
        $receipt .= substr($blank.number_format(($row[0]),2),-8)."\n";
        $receipt .= "\n";
*/



// ccm-rle added total tax to tender tape as well 


        $tax_tot = "SELECT ROUND(SUM(total),2) AS gross
                FROM dlog
                WHERE tdate > '" .$EOS. "'
                AND register_no = ".$_SESSION['laneno']."               
		AND emp_no <> 9999
                AND trans_type = 'A'";
        $results_tot = mysql_query($tax_tot);
        $row = mysql_fetch_row($results_tot);

        $receipt .= "  ".substr("Sales Tax Total: ".$blank.$blank,0,20);
        $receipt .= substr($blank.number_format(($row[0]),2),-8)."\n";


// ----------------------------------------------------------------------------------------------------
// ccm-rle 10-8-2009 added trans_status <> 'X' to below so that transactions that are cancelled are not counted in the register. This may not be the best idea. This is actually already done by dlog automatically.



	$netQ = "SELECT SUM(total) AS net
		from dlog
		where tdate > '" .$EOS. "'
		and register_no = " .$_SESSION['laneno']. "
		and trans_type IN('I','D','A')
		and trans_subtype NOT IN('IC','MC')
		AND emp_no <> 9999";

	$netR = mysql_query($netQ);
	$row = mysql_fetch_row($netR);

	$receipt .= "  ".substr("NET Total: ".$blank.$blank,0,20);
	$receipt .= substr($blank.number_format(($row[0]),2),-8)."\n";
	$receipt .= "\n";

// ccm-rle 4-20-2010 Coupons that are tendered vs. scanned don't show up under net, but do under tender totals. So in order to balance the register we need the unscanned coupon total added up. This is a work around but should balance the reports.
        $coupontenderQ = "SELECT SUM(total) AS coupontender
                from dlog
                where tdate > '" .$EOS. "'
                and register_no = " .$_SESSION['laneno']. "
                and trans_type IN('T')
                and trans_subtype = 'MC'
                AND emp_no <> 9999";

        $coupontenderR = mysql_query($coupontenderQ);
        $row = mysql_fetch_row($coupontenderR);

        $receipt .= "  ".substr("Unscanned Coupon Total: ".$blank.$blank,0,20);
        $receipt .= substr($blank.number_format(($row[0]),2),-8)."\n";
        $receipt .= "\n";




// ccm-rle this is where the various tendered items are added to the receipt such as debit card and WIC, in order to remove unused tenders the database needs to be modified to remove them and also is4c needs to have the commented out as potential tender sources. For the purposes of CCM we will be removed debit cards, EBT cash, WIC, prehkeys.php is where the tenders are recognized by is4c. ignoring this for now to code in needed aspects. 

	$tendertotalsQ = "SELECT t.TenderName as tender_type,ROUND(-sum(d.total),2) as total,COUNT(*) as count
		FROM dlog d RIGHT JOIN is4c_op.tenders t
		ON d.trans_subtype = t.TenderCode
		AND tdate > '" .$EOS. "'
		AND register_no = ".$_SESSION['laneno']." 
		AND d.emp_no <> 9999
                AND trans_status <> 'X'
		GROUP BY t.TenderName";

	$results_ttq = mysql_query($tendertotalsQ);

	while($row = mysql_fetch_row($results_ttq))	{
		if(!isset($row[0]))	{
			$receipt .= "NULL";
		}else{
			$receipt .= "  ".substr($row[0].$blank.$blank,0,20);
		}
		if(!isset($row[1])) { 
			$receipt .= "    0.00";
		}else{
			$receipt .= substr($blank.number_format($row[1],2),-8);
		}
		if(!isset($row[2])) { 
			$receipt .= "NULL";
		}else{
			if(!isset($row[1])) {
				$row[2] = 0;
			}
			$receipt .= substr($blank.$row[2],-4,4);
		}
		$receipt .= "\n";
	} $receipt .= "\n";

	$cack_tot = "SELECT ROUND(SUM(total),2) AS gross
		FROM dlog
		WHERE tdate > '" .$EOS. "'
		AND register_no = ".$_SESSION['laneno']."
		AND trans_subtype IN ('CA','CK')
		AND emp_no <> 9999";
	$results_tot = mysql_query($cack_tot);
	$row = mysql_fetch_row($results_tot);

	$receipt .= "  ".substr("CA & CK Total: ".$blank.$blank,0,20);
	$receipt .= substr($blank.number_format(($row[0] * -1),2),-8)."\n";
	
	$card_tot = "SELECT ROUND(SUM(total),2) AS gross
		FROM dlog
		WHERE tdate > '" .$EOS. "'
		AND register_no = ".$_SESSION['laneno']."
		AND emp_no <> 9999		
		AND trans_subtype IN ('DC','CC','FS','EC')";
	$results_tot = mysql_query($card_tot);
	$row = mysql_fetch_row($results_tot);

	$receipt .= "  ".substr("DC / CC / EBT Total: ".$blank.$blank,0,20);
	$receipt .= substr($blank.number_format(($row[0] * -1),2),-8)."\n";

	$hchrg_tot = "SELECT ROUND(SUM(total),2) AS gross
		FROM dlog
		WHERE tdate > '" .$EOS. "'
		AND register_no = ".$_SESSION['laneno']."
		AND trans_subtype = 'MI'
		AND emp_no <> 9999
		AND card_no <> 9999";
	$results_tot = mysql_query($hchrg_tot);
	$row = mysql_fetch_row($results_tot);
// ccm-rle commenting out the House and Storage Charge totals as they are not used by ccm
/*
	$receipt .= "  ".substr("House Charge Total: ".$blank.$blank,0,20);
	$receipt .= substr($blank.number_format(($row[0] * -1),2),-8)."\n";

	$schrg_tot = "SELECT ROUND(SUM(total),2) AS gross
		FROM dlog
		WHERE tdate > '" .$EOS. "'
		AND register_no = ".$_SESSION['laneno']."
		AND trans_subtype = 'MI'
		AND card_no = 9999";
	$results_tot = mysql_query($schrg_tot);
	$row = mysql_fetch_row($results_tot);

	$receipt .= "  ".substr("Store Charge Total: ".$blank.$blank,0,20);
	$receipt .= substr($blank.number_format(($row[0] * -1),2),-8)."\n";

*/


	$receipt .= str_repeat("\n", 5);	// apbw/tt 3/16/05 Franking II

// ccm-rle adding a place here to count the number of cancelled transactions, no sales & hanging suspended
// This adds the number of cancelled transactions for this tender report
        $cancelledtranQ = "SELECT COUNT(DISTINCT trans_no, emp_no) 
                from dtransactions 
                where datetime > '" .$EOS. "'
                and register_no = " .$_SESSION['laneno']. "
                and trans_status = 'X'
                AND emp_no <> 9999";
/*        $fp=fopen('cancel-log.txt','w');
	
	fwrite($fp,$cancelledtranQ);
	fclose($fp);
*/
        $cancelledtranR = mysql_query($cancelledtranQ);
        $row = mysql_fetch_row($cancelledtranR);

        $receipt .= "  ".substr("# of Cancelled Transactions: ".$blank.$blank,0,20);

        $receipt .= substr($blank.number_format(($row[0]),2),-8)."\n";
        $receipt .= "\n";

/*   ccm-rle This code was used to ensure there were no suspended transactions 	
	$suspendedtranQ = "SELECT COUNT(DISTINCT trans_no)
		from suspendedtoday
		where datetime > '" .$EOS. "'
                and register_no = " .$_SESSION['laneno']. "
		and emp_no <> 9999";
	$suspendedtranR = mysql_query($suspendedtranQ);
	$row = mysql_fetch_row($suspendedtranR);
	
        $receipt .= "  ".substr("# of Suspended Transactions: ".$blank.$blank,0,20);
        $receipt .= substr($blank.number_format(($row[0]),2),-8)."\n";
	$receipt .= "\n";

*/

        $tranQ = "SELECT COUNT(DISTINCT trans_no, emp_no) 
                from dlog
                where tdate > '" .$EOS. "'
                and register_no = " .$_SESSION['laneno']. "
                and trans_status <> 'X'
                AND emp_no <> 9999";

        $tranR = mysql_query($tranQ);
        $row = mysql_fetch_row($tranR);

        $receipt .= "  ".substr("Total Completed  Transactions: ".$blank.$blank,0,20);
        $receipt .= substr($blank.number_format(($row[0]),2),-8)."\n";
        $receipt .= "\n";




        $receipt .= str_repeat("\n", 5);        // apbw/tt 3/16/05 Franking II

// ccm-rle Next need to write code to look up number of suspended transactions that are left hanging.


// ----------------------------------------------------------------------------------------------------

	$receipt .= chr(27).chr(33).chr(5).centerString("C H E C K   T E N D E R S")."\n";

	$receipt .=	centerString("------------------------------------------------------");
 
	$result_ckq = sql_query($query_ckq, $db_a);
	$num_rows_ckq = sql_num_rows($result_ckq);

	if ($num_rows_ckq > 0) {

		$receipt .= $fieldNames;

		for ($i = 0; $i < $num_rows_ckq; $i++) {

			$row_ckq = sql_fetch_array($result_ckq);
			$timeStamp = timeStamp($row_ckq["tdate"]);
			$receipt .= "  ".substr($timeStamp.$blank, 0, 10)
				.substr($row_ckq["register_no"].$blank, 0, 7)
				.substr($row_ckq["trans_no"].$blank, 0, 6)
				.substr($row_ckq["emp_no"].$blank, 0, 6)
				.substr($blank.number_format($row_ckq["changeGiven"], 2), -10)
				.substr($blank.number_format($row_ckq["ckTender"], 2), -10)."\n";
		}

		$receipt.= centerString("------------------------------------------------------");

//		$query_ckq = "select * from cktendertotal where register_no = ".$_SESSION["laneno"];
//		$result_ckq = sql_query($query_ckq, $db_a);
//		$row_ckq = sql_fetch_array($result_ckq);

		$query_ckq = "select SUM(ckTender) from cktenders where tdate > '".$EOS."' and register_no = ".$_SESSION["laneno"];
		$result_ckq = sql_query($query_ckq, $db_a);
		$row_ckq = sql_fetch_array($result_ckq);

		$receipt .= substr($blank.$blank.$blank.$blank."Total: ".number_format($row_ckq[0],2), -56)."\n";

	}
	else {
		$receipt .= "\n\n".centerString(" * * *   N O N E   * * * ")."\n\n"
			.centerString("------------------------------------------------------");
	}

	$receipt .= str_repeat("\n", 3);	// apbw/tt 3/16/05 Franking II
//ccm-rle commented out debit card tenders because for ccm they are combined with credit card tenders


	$receipt .= chr(27).chr(33).chr(5).centerString("D E B I T  C A R D  T E N D E R S")."\n";

	$receipt .=	centerString("------------------------------------------------------");
 
	$result_dcq = sql_query($query_dcq, $db_a);
	$num_rows_dcq = sql_num_rows($result_dcq);

	if ($num_rows_dcq > 0) {

		$receipt .= $fieldNames;

		for ($i = 0; $i < $num_rows_dcq; $i++) {

			$row_dcq = sql_fetch_array($result_dcq);
			$timeStamp = timeStamp($row_dcq["tdate"]);
			$receipt .= "  ".substr($timeStamp.$blank, 0, 10)
				.substr($row_dcq["register_no"].$blank, 0, 7)
				.substr($row_dcq["trans_no"].$blank, 0, 6)
				.substr($row_dcq["emp_no"].$blank, 0, 6)
				.substr($blank.number_format($row_dcq["changeGiven"], 2), -10)
				.substr($blank.number_format($row_dcq["dcTender"], 2), -10)."\n";
		}

		$receipt.= centerString("------------------------------------------------------");

//		$query_dcq = "select * from dctendertotal where emp_no = ".$_SESSION["CashierNo"];
//		$result_dcq = sql_query($query_dcq, $db_a);
//		$row_dcq = sql_fetch_array($result_dcq);

		$query_dcq = "select SUM(dcTender) from dctenders where tdate > '".$EOS."' and register_no = ".$_SESSION["laneno"];
		$result_dcq = sql_query($query_dcq, $db_a);
		$row_dcq = sql_fetch_array($result_dcq);

		$receipt .= substr($blank.$blank.$blank.$blank."Total: ".number_format($row_dcq[0],2), -56)."\n";
	}
	else {
		$receipt .= "\n\n".centerString(" * * *   N O N E   * * * ")."\n\n"
			.centerString("------------------------------------------------------");
	}

	$receipt .= str_repeat("\n", 3);	// apbw/tt 3/16/05 Franking II

	$receipt .= chr(27).chr(33).chr(5).centerString("C R E D I T   C A R D   T E N D E R S")."\n";
	$receipt .=	centerString("------------------------------------------------------");
 
	$result_ccq = sql_query($query_ccq, $db_a);
	$num_rows_ccq = sql_num_rows($result_ccq);

	if ($num_rows_ccq > 0) {

		$receipt .= $fieldNames;

		for ($i = 0; $i < $num_rows_ccq; $i++) {

			$row_ccq = sql_fetch_array($result_ccq);
			$timeStamp = timeStamp($row_ccq["tdate"]);
			$receipt .= "  ".substr($timeStamp.$blank, 0, 10)
				.substr($row_ccq["register_no"].$blank, 0, 7)
				.substr($row_ccq["trans_no"].$blank, 0, 6)
				.substr($row_ccq["emp_no"].$blank, 0, 6)
				.substr($blank.number_format($row_ccq["changeGiven"], 2), -10)
				.substr($blank.number_format($row_ccq["ccTender"], 2), -10)."\n";
		}

		$receipt.= centerString("------------------------------------------------------");

//		$query_ccq = "select * from cctendertotal where register_no = ".$_SESSION["laneno"];
//		$result_ccq = sql_query($query_ccq, $db_a);
//		$row_ccq = sql_fetch_array($result_ccq);

		$query_ccq = "select SUM(ccTender) from cctenders where tdate > '".$EOS."' and register_no = ".$_SESSION["laneno"];
		$result_ccq = sql_query($query_ccq, $db_a);
		$row_ccq = sql_fetch_array($result_ccq);

		$receipt .= substr($blank.$blank.$blank.$blank."Total: ".number_format($row_ccq[0],2), -56)."\n";
	}
	else {
		$receipt .= "\n\n".centerString(" * * *   N O N E   * * * ")."\n\n"
			.centerString("------------------------------------------------------");
	}

	$receipt .= str_repeat("\n", 3);	// apbw/tt 3/16/05 Franking II

//test



	$receipt .= chr(27).chr(33).chr(5).centerString("E B T  T E N D E R S")."\n";

	$receipt .=	centerString("------------------------------------------------------");
 
	$result_fsq = sql_query($query_fsq, $db_a);
	$num_rows_fsq = sql_num_rows($result_fsq);

	if ($num_rows_fsq > 0) {

		$receipt .= $fieldNames;

		for ($i = 0; $i < $num_rows_fsq; $i++) {

			$row_fsq = sql_fetch_array($result_fsq);
			$timeStamp = timeStamp($row_fsq["tdate"]);
			$receipt .= "  ".substr($timeStamp.$blank, 0, 10)
				.substr($row_fsq["register_no"].$blank, 0, 7)
				.substr($row_fsq["trans_no"].$blank, 0, 6)
				.substr($row_fsq["emp_no"].$blank, 0, 6)
				.substr($blank.number_format($row_fsq["changeGiven"], 2), -10)
				.substr($blank.number_format($row_fsq["FsTender"], 2), -10)."\n";
		}

		$receipt.= centerString("------------------------------------------------------");

//		$query_fsq = "select * from fstendertotal where emp_no = ".$_SESSION["CashierNo"];
//		$result_fsq = sql_query($query_fsq, $db_a);
//		$row_fsq = sql_fetch_array($result_fsq);

		$query_fsq = "select SUM(fsTender) from fstenders where tdate > '".$EOS."' and register_no = ".$_SESSION["laneno"];
		$result_fsq = sql_query($query_fsq, $db_a);
		$row_fsq = sql_fetch_array($result_fsq);

		$receipt .= substr($blank.$blank.$blank.$blank."Total: ".number_format($row_fsq[0],2), -56)."\n";
	}
	else {
		$receipt .= "\n\n".centerString(" * * *   N O N E   * * * ")."\n\n"
			.centerString("------------------------------------------------------");
	}

	$receipt .= str_repeat("\n", 3);	// apbw/tt 3/16/05 Franking II




//ccm-rle commented out house store charges because ccm doesn't currently use them
/*
	$receipt .= centerString("H O U S E / S T O R E   C H A R G E   T E N D E R S")."\n";
	$receipt .=	centerString("------------------------------------------------------");

	$result_miq = sql_query($query_miq, $db_a);
	$num_rows_miq = sql_num_rows($result_miq);

	if ($num_rows_miq > 0) {
		
		$chgFieldNames = "  ".substr("Time".$blank, 0, 10)
				.substr("Lane".$blank, 0, 7)
				.substr("Trans #".$blank, 0, 6)
				.substr("Emp #".$blank, 0, 8)
				.substr("Member #".$blank, 0, 10)
				.substr("Amount".$blank, 0, 10)."\n";
		
		$receipt .= $chgFieldNames;

		for ($i = 0; $i < $num_rows_miq; $i++) {
			$row_miq = sql_fetch_array($result_miq);
			$timeStamp = timeStamp($row_miq["tdate"]);
			$receipt .= "  ".substr($timeStamp.$blank, 0, 10)
				.substr($row_miq["register_no"].$blank, 0, 7)
				.substr($row_miq["trans_no"].$blank, 0, 6)
				.substr($row_miq["emp_no"].$blank, 0, 6)
				.substr($row_miq["card_no"].$blank, 0, 6)
				.substr($blank.number_format($row_miq["MiTender"], 2), -10)."\n";

		}

		$receipt.= centerString("------------------------------------------------------");

//		$query_miq = "select * from mitendertotal where register_no = ".$_SESSION["laneno"];
//		$result_miq = sql_query($query_miq, $db_a);
//		$row_miq = sql_fetch_array($result_miq);

		$query_miq = "select SUM(miTender) from mitenders where tdate > '".$EOS."' and register_no = ".$_SESSION["laneno"];
		$result_miq = sql_query($query_miq, $db_a);
		$row_miq = sql_fetch_array($result_miq);

		$receipt .= substr($blank.$blank.$blank.$blank."Total: ".number_format($row_miq[0],2), -56)."\n";
	}
	else {
		$receipt .= "\n\n".centerString(" * * *   N O N E   * * * ")."\n\n"
			.centerString("------------------------------------------------------");
	}

	$receipt .= str_repeat("\n", 3);	// apbw/tt 3/16/05 Franking II
*/
//--------------------------------------------------------------------
//ccm-rle commented out TRI MET passes because CCM doesn't use them
/*
		$receipt .= chr(27).chr(33).chr(5).centerString("T R I - M E T  P A S S E S   S O L D")."\n";
	$receipt .=	centerString("------------------------------------------------------");

	$result_bp = sql_query($query_bp, $db_a);
	$num_rows_bp = sql_num_rows($result_bp);

	if ($num_rows_bp > 0) {

		$receipt .= $fieldNames;

		for ($i = 0; $i < $num_rows_bp; $i++) {

			$row_bp = sql_fetch_array($result_bp);
			$timeStamp = timeStamp($row_bp["tdate"]);
			$receipt .= "  ".substr($timeStamp.$blank, 0, 10)
				.substr($row_bp["register_no"].$blank, 0, 7)
				.substr($row_bp["trans_no"].$blank, 0, 6)
				.substr($row_bp["emp_no"].$blank, 0, 6)
				.substr($blank.($row_bp["upc"]), -10)
				.substr($blank.number_format($row_bp["total"], 2), -10)."\n";
		}

		$receipt.= centerString("------------------------------------------------------");
	}
	else {
		$receipt .= "\n\n".centerString(" * * *   N O N E   * * * ")."\n\n"
			.centerString("------------------------------------------------------");
	}
*/
	$receipt .= str_repeat("\n", 8);	// apbw/tt 3/16/05 Franking II
// ccm-rle - this creates a txt log on the IS4C computer of every tender report for logging purposes
	$tender_date =  date('Y-m-d-H-i-s');
	$tender_log_file = "/pos/logs/tenderlog_" . $tender_date . "_" . $_SESSION["laneno"].".txt";
	$fp=fopen($tender_log_file,'w');
        fwrite($fp,$receipt.chr(27).chr(105));
        fclose($fp);



	writeLine($receipt.chr(27).chr(105));	// apbw/tt 3/16/05 Franking II
	sql_close($db_a);

	$_SESSION["msgrepeat"] = 1;
	$_SESSION["strRemembered"] = "ES";
        gohome();

}


function timeStamp($time) {

	return strftime("%I:%M %p", strtotime($time));
}
?>
