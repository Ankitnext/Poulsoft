<?php
//CustomerMultipleInvoicePrint6.php
require_once('tcpdf_include.php');
include "../../newConfig.php";
include "number_format_ind.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
$addedemp = $_SESSION['userid'];
global $sms_type; $sms_type = "WappKey"; include "../../chicken_wapp_connectionmaster.php";

if((int)$wapp_error_flag == 0){
	/*Check for Table Availability*/
	$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name;
	$sql1 = "SHOW TABLES WHERE ".$table_head." LIKE 'extra_access';"; $query1 = mysqli_query($conn,$sql1); $tcount = mysqli_num_rows($query1);
	if($tcount > 0){ } else{ $sql1 = "CREATE TABLE $database_name.extra_access LIKE vpspoulsoft_admin_chickenmaster.extra_access;"; mysqli_query($conn,$sql1); }

	$sql1 = "SELECT * FROM `extra_access` WHERE `field_name` = 'Send WhatsApp Timer' AND `field_function` = 'CustomerLedgerWithTdsPDF4.php' AND `user_access` = 'all'";
	$query1 = mysqli_query($conn,$sql1); $tcount = mysqli_num_rows($query1); $wapp_timer_flag = 0;
	if($tcount > 0){ while($row1 = mysqli_fetch_assoc($query1)){ $wapp_timer_flag = $row1['flag']; } }
	else{ $sql1 = "INSERT INTO `extra_access` (`field_name`,`field_function`,`user_access`,`flag`) VALUES ('Send WhatsApp Timer','CustomerLedgerWithTdsPDF4.php','all','0');"; mysqli_query($conn,$sql1); }

    function convert_number_to_words($amount) {
        $words = array();
        $words[0] = '';
        $words[1] = 'One'; 
        $words[2] = 'Two';
        $words[3] = 'Three';
        $words[4] = 'Four';
        $words[5] = 'Five';
        $words[6] = 'Six';
        $words[7] = 'Seven';
        $words[8] = 'Eight';
        $words[9] = 'Nine';
        $words[10] = 'Ten';
        $words[11] = 'Eleven';
        $words[12] = 'Twelve';
        $words[13] = 'Thirteen';
        $words[14] = 'Fourteen';
        $words[15] = 'Fifteen';
        $words[16] = 'Sixteen';
        $words[17] = 'Seventeen';
        $words[18] = 'Eighteen';
        $words[19] = 'Nineteen';
        $words[20] = 'Twenty';
        $words[30] = 'Thirty';
        $words[40] = 'Forty';
        $words[50] = 'Fifty';
        $words[60] = 'Sixty';
        $words[70] = 'Seventy';
        $words[80] = 'Eighty';
        $words[90] = 'Ninety';
    
        $amount = strval($amount);
    
        $atemp = explode(".",$amount);
        $number = str_replace(",","",$atemp[0]);
        $n_length = strlen($number);
        $words_string = "";
    
        if($n_length <= 9){
            $received_n_array = array(); $n_array = array(0, 0, 0, 0, 0, 0, 0, 0, 0);
    
            for ($i = 0; $i < $n_length; $i++) {
                $received_n_array[$i] = substr($number,$i, 1);
            }
            for ($i = 9 - $n_length, $j = 0; $i < 9; $i++, $j++) {
                $n_array[$i] = $received_n_array[$j];
            }
            for ($i = 0, $j = 1; $i < 9; $i++, $j++) {
                if ($i == 0 || $i == 2 || $i == 4 || $i == 7) {
                    if ($n_array[$i] == 1) {
                        $n_array[$j] = 10 + (int)$n_array[$j];
                        $n_array[$i] = 0;
                    }
                }
            }
            $value = "";
            for ($i = 0; $i < 9; $i++) {
                if ($i == 0 || $i == 2 || $i == 4 || $i == 7) {
                    $value = $n_array[$i] * 10;
                } else {
                    $value = $n_array[$i];
                }
                if ($value != 0) {
                    $words_string .= $words[$value]." ";
                }
                if (($i == 1 && $value != 0) || ($i == 0 && $value != 0 && $n_array[$i + 1] == 0)) {
                    $words_string .= "Crores ";
                }
                if (($i == 3 && $value != 0) || ($i == 2 && $value != 0 && $n_array[$i + 1] == 0)) {
                    $words_string .= "Lakhs ";
                }
                if (($i == 5 && $value != 0) || ($i == 4 && $value != 0 && $n_array[$i + 1] == 0)) {
                    $words_string .= "Thousand ";
                }
                if ($i == 6 && $value != 0 && ($n_array[$i + 1] != 0 && $n_array[$i + 2] != 0)) {
                    $words_string .= "Hundred and ";
                }
                else if ($i == 6 && $value != 0) {
                    $words_string .= "Hundred ";
                }
            }
            $words_string = str_replace("  "," ",$words_string);
            if((int)$atemp[1] > 0){
                $paisa = " and ".$words[$atemp[1]*10]." paisa only";
            }
            else{
                $paisa = "rupees only";
            }
            $words_string .= $paisa;
        }
        return $words_string;
    }
    $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $img_path = $row['logopath']; $cdetail = $row['cdetails'];
        $bank_name = $row['bank_name'];
        $bank_branch = $row['bank_branch'];
        $bank_accno = $row['bank_accno'];
        $bank_ifsc = $row['bank_ifsc'];
        $bank_accname = $row['bank_accname'];
        $upi_details = $row['upi_details'];
        $upi_mobile = $row['upi_mobile'];
        $client_name = $row['cname'];
    }
    $sql = "SELECT * FROM `master_itemfields` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $bank_flag = $row['bank_flag']; } if($bank_flag == "" || $bank_flag == NULL || $bank_flag == 0 || $bank_flag == "0"){ $bank_flag = 0; }
    
    $sql = "SELECT * FROM `item_details` ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }
    
    $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%broiler bird%' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; }
    
    $fetch_date = date("Y-m-d",strtotime($_POST['pdates']));
    foreach($_POST['ccode'] as $ccode){
        $sql = "SELECT * FROM `main_dailypaperrate` WHERE `date` = '$fetch_date' AND `code` = '$bird_code' AND `active` = '1' AND `dflag` = '0'";
        $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $paper_rate = $row['new_price']; }
    
        $sql = "SELECT * FROM `main_contactdetails` WHERE `code` = '$ccode'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $cname = $row['name']; $cmobl = $row['mobileno']; if($row['obtype'] == "Cr") { $obcramt = $row['obamt']; $obdramt = 0; } else { $obdramt = $row['obamt']; $obcramt = 0; } }
    
        $old_inv = ""; $oinv = $orct = $current_orct = $ocdn = $occn = $ob_mortality = $ob_returns = $cus_bal = 0;
        $sql = "SELECT invoice,finaltotal FROM `customer_sales` WHERE `date` < '$fetch_date' AND `customercode` LIKE '$ccode' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `invoice` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ if($old_inv != $row['invoice']){ $oinv = (float)$oinv + (float)$row['finaltotal']; $old_inv = $row['invoice']; } }
        $sql = "SELECT SUM(amount) as tamt FROM `customer_receipts` WHERE  `date` < '$fetch_date' AND `ccode` LIKE '$ccode' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $orct = $row['tamt']; }
        $sql = "SELECT SUM(amount) as tamt FROM `customer_receipts` WHERE  `date` = '$fetch_date' AND `ccode` LIKE '$ccode' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $current_orct = (float)$row['tamt']; }
        $sql = "SELECT SUM(amount) as tamt,mode FROM `main_crdrnote` WHERE  `date` < '$fetch_date' AND `ccode` LIKE '$ccode' AND `mode` IN ('CCN','CDN') AND `active` = '1' GROUP BY `mode` ORDER BY `mode` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ if($row['mode'] == "CDN") { $ocdn = $row['tamt']; } else { $occn = $row['tamt']; } }
        $obsql = "SELECT SUM(amount) as amount FROM `main_mortality` WHERE `date` < '$fetch_date' AND `ccode` = '$ccode' AND `mtype` = 'customer' AND `active` = '1' AND `dflag` = '0'";
        $obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_mortality = (float)$obrow['amount']; }
        $obsql = "SELECT SUM(amount) as amount FROM `main_itemreturns` WHERE `date` < '$fetch_date' AND `vcode` = '$ccode' AND `mode` = 'customer' AND `active` = '1' AND `dflag` = '0'";
        $obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_returns = (float)$obrow['amount']; }
        $cus_bal = (((float)$oinv + (float)$ocdn + (float)$obdramt) - ((float)$orct + (float)$occn + (float)$obcramt + (float)$ob_returns + (float)$ob_mortality));
        //echo "<br/>$cus_bal = (($oinv + $ocdn + $obdramt) - ($orct - $occn - $obcramt - $ob_returns - $ob_mortality))";
    
        $sql = "SELECT DISTINCT(invoice) as invoice FROM `customer_sales` WHERE `date` = '$fetch_date' AND `customercode` IN ('$ccode') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `invoice` ASC";
        $query = mysqli_query($conn,$sql); $inv_nos = array();
        while($row = mysqli_fetch_assoc($query)){ $inv_nos[$row['invoice']] = $row['invoice']; }
    
        $c = $tot_birds = $tot_qty = $tot_amt = 0; $slno = $iname = $bird = $qty = $price = $amt = array();
        foreach($inv_nos as $invs){
            $sql = "SELECT * FROM `customer_sales` WHERE `invoice` = '$invs' AND `customercode` IN ('$ccode') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `id` ASC";
            $query = mysqli_query($conn,$sql); $old_inv = "";
            while($row = mysqli_fetch_assoc($query)){
                $c = $c + 1;
                $slno[$c] = $c;
                $iname[$c] = $item_name[$row['itemcode']];
                $bird[$c] = $row['birds'];
                $qty[$c] = $row['netweight'];
                $price[$c] = $row['itemprice'];
                $amt[$c] = $row['totalamt'];
    
                $tot_birds += (float)$row['birds'];
                $tot_qty += (float)$row['netweight'];
                if($old_inv != $row['invoice']){
                    
                    $tot_amt += (float)$row['finaltotal'];
                    $old_inv = $row['invoice'];
                }
                
            }
        }
        $html = "";
        $html .= '<table align="center" style="border: 1px solid black;">';
        $html .= '<tr>';
        $html .= '<th colspan="2" style="text-align:center;"><br/><br/><br/><img src="../../'.$img_path.'" height="60px" /></th>';
        $html .= '<th colspan="5" style="text-align:center;"><p align="left">'.$cdetail.'</p></th>';
        $html .= '</tr>';
        if($dbname == "vpspoulsoft_chicken_ka_bestbroilers"){
            $html .= '<tr>';
            $html .= '<th colspan="4" style="padding-left: 10px;text-align:left;"><b align="left">Date: '.date("d.m.Y",strtotime($fetch_date)).'</b></th>';
            $html .= '<th colspan="3" style="padding-right: 10px;text-align:right;"><b align="right">Paper Rate: '.number_format_ind($paper_rate).'</b></th>';
            $html .= '</tr>';
        }
        else{
            $html .= '<tr>';
            $html .= '<th colspan="7" style="padding-left: 10px;text-align:left;"><b align="left">Date: '.date("d.m.Y",strtotime($fetch_date)).'</b></th>';
            $html .= '</tr>';
        }
        
        $html .= '<tr>';
        $html .= '<th colspan="7" style="padding-left: 10px;text-align:left;"><b style="padding-left: 10px;text-align:left;">Billing Name: '.$cname.'</b><br/></th>';
        $html .= '</tr>';
    
        $html .= '</table>';
        $slnos = $inames =  $birds = $qtys = $prices = $amts = "<br/><br/>";
        for($i = 1;$i <= $c;$i++){
            if($i <$c){
                $slnos = $slnos."".$slno[$i]."<br/><br/>";
                $birds = $birds."".round($bird[$i])."&nbsp;&nbsp;<br/><br/>";
                $inames = $inames."".$iname[$i]."<br/><br/>";
                $qtys = $qtys."".number_format_ind($qty[$i])."&nbsp;&nbsp;<br/><br/>";
                $prices = $prices."".number_format_ind($price[$i])."&nbsp;&nbsp;<br/><br/>";
                $amts = $amts."".number_format_ind($amt[$i])."&nbsp;&nbsp;<br/><br/>";
            }
            else {
                $br = "";
                //$k = 9 - $c;
                for($j = 4;$j >= $c; $j--){
                    $br = $br."<br/><br/>";
                }
                $slnos = $slnos."".$slno[$i]."".$br;
                $birds = $birds."".round($bird[$i])."&nbsp;&nbsp;".$br;
                $inames = $inames."".$iname[$i]."".$br;
                $qtys = $qtys."".number_format_ind($qty[$i])."&nbsp;&nbsp;".$br;
                $prices = $prices."".number_format_ind($price[$i])."&nbsp;&nbsp;".$br;
                $amts = $amts."".number_format_ind($amt[$i])."&nbsp;&nbsp;".$br;
            }
        }
        $camt = (($cus_bal + $tot_amt) - $current_orct);
        //echo "<br/>$camt = (($cus_bal + $tot_amt) - $current_orct)";
        
        $html .= '<table align="center" height="100%" border="1">';
        $html .= '<tr>';
        $html .= '<th colspan="3" style="border: 1px solid black;">Items</th>';
        //$html .= '<th colspan="1" style="border: 1px solid black;">Birds</th>';
        $html .= '<th colspan="1" style="border: 1px solid black;">Net Wt.</th>';
        $html .= '<th colspan="1" style="border: 1px solid black;">Rate</th>';
        $html .= '<th colspan="2" style="border: 1px solid black;">Amount</th>';
        $html .= '</tr>';
        $html .= '<tr>';
            $html .= '<td colspan="3" style="padding:5px;text-align:center;">'.$inames.'</td>';
            //$html .= '<td colspan="1" style="padding:5px;text-align:right;">'.$birds.'&nbsp;&nbsp;</td>';
            $html .= '<td colspan="1" style="padding:5px;text-align:right;">'.$qtys.'&nbsp;&nbsp;</td>';
            $html .= '<td colspan="1" style="padding:5px;text-align:right;">'.$prices.'&nbsp;&nbsp;</td>';
            $html .= '<td colspan="2" style="padding:5px;text-align:right;">'.$amts.'&nbsp;&nbsp;</td>';
        $html .= '</tr>';
        $html .= '<tr>';
            $html .= '<th colspan="3"><br/>Invoice Total</th>';
            //$html .= '<td colspan="1" style="padding:5px;text-align:right;"><br/>'.round(number_format_ind($tot_birds)).'&nbsp;&nbsp;</td>';
            $html .= '<td colspan="1" style="padding:5px;text-align:right;"><br/>'.number_format_ind($tot_qty).'&nbsp;&nbsp;</td>';
            $html .= '<td colspan="1" style="padding:5px;text-align:right;"><br/></td>';
            $html .= '<td colspan="2" style="padding:5px;text-align:right;"><br/>'.number_format_ind($tot_amt).'&nbsp;&nbsp;</td>';
        $html .= '</tr>';
        $html .= '<tr>';
            $html .= '<td colspan="3"><br/><br/><b>Amount in words:</b> '.ucfirst(strtolower(convert_number_to_words($tot_amt))).'.<br/></td>';
            $html .= '<td colspan="4">';
            $html .= '<table>';
            $html .= '<tr>';
            $html .= '<td style="padding:5px;text-align:left;">&nbsp;Previous Balance:&nbsp;</td>';
            $html .= '<td style="padding:5px;text-align:right;">'.number_format_ind($cus_bal).'&nbsp;&nbsp;</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td style="padding:5px;text-align:left;">&nbsp;This Bill Amount:&nbsp;</td>';
            $html .= '<td style="padding:5px;text-align:right;">'.number_format_ind($tot_amt).'&nbsp;&nbsp;</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td style="padding:5px;text-align:left;">&nbsp;Received Amount:&nbsp;</td>';
            $html .= '<td style="padding:5px;text-align:right;">'.number_format_ind($current_orct).'&nbsp;&nbsp;</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td style="padding:5px;text-align:left;border-top:1px dotted black;">&nbsp;<br/>Closing Balance:&nbsp;</td>';
            $html .= '<td style="padding:5px;text-align:right;border-top:1px dotted black;"><b><br/>'.number_format_ind($camt).'</b>&nbsp;&nbsp;</td>';
            $html .= '</tr>';
            $html .= '</table>';
            $html .= '</td>';
        $html .= '</tr>';
        
        if($bank_flag == 1){
            $html .= '<tr>';
                $html .= '<td colspan="3" style="text-align:left;font-size:8px;">';
                    $html .= '&nbsp;<b>Bank Name : </b>'.$bank_name.'<br/>';
                    $html .= '&nbsp;<b>Branch : </b>'.$bank_branch.'<br/>';
                    $html .= '&nbsp;<b>IFSC Code: </b>'.$bank_ifsc.'<br/>';
                $html .= '</td>';
                $html .= '<td colspan="4" style="text-align:left;font-size:8px;">';
                    $html .= '&nbsp;<b>Acc. Holder name : </b>'.$bank_accname.'<br/>';
                    $html .= '&nbsp;<b>Account Number : </b>'.$bank_accno.'<br/>';
                    if($upi_details != "" || $upi_details != NULL){
                    $html .= '&nbsp;<b>'.$upi_details.': </b>'.$upi_mobile.'<br/>';
                    }
                $html .= '</td>';
            $html .= '</tr>';
        }
    
        $html .= '<tr>';
        $html .= '<td colspan="7" style="text-align:right;font-size:10px;"><br/><br/><b>'.$client_name.'&ensp;&ensp;</b></td>';
        $html .= '</tr>';
        $html .= '</table>';
        if($icount != $isize){
            $html .= '<div style="page-break-before:always"></div>';
        }
    
        require_once('tcpdf_include.php');
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Mallikarjuna K');
        $pdf->SetTitle('Sales Invoice');
        $pdf->SetSubject('Invoice PDF');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $pdf->SetFont('dejavusans', '', 10, '', true);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->AddPage('P', 'A5');
        $file_name1 = $client_name."_Sales_Invoice_".date("dmyHisA");
        $file_name = str_replace(" ","_",$file_name1);
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
        $file = $pdf->Output(__DIR__."/".$file_name.".pdf",'F');
        $filepath = "https://chicken.poulsoft.org/printformatlibrary/Examples/".$file_name.".pdf";
        $filepath2 = __DIR__."/".$file_name.".pdf";
        
        $message = "Sales Invoice ".$cname;
        $message = str_replace(" ","+",$message);
        $cus_send_mobile = $cmobl;
        $wapp_date = date("Y-m-d");
        
        if((int)$url_id == 1 || (int)$url_id == 2){
            $media_url = $filepath; $filename = $file_name.".pdf"; $number = "91".$cus_send_mobile; $type = "media";
            $msg_info = $curlopt_url.'number='.$number.'&type='.$type.'&message='.$message.'&media_url='.$media_url.'&filename='.$filename.'&instance_id='.$instance_id.'&access_token='.$access_token;
        
            if((int)$wapp_timer_flag == 0 && $wapp_error_flag == 0){

                $curl = curl_init();
                curl_setopt_array($curl, array(
                CURLOPT_URL => $msg_info,
                CURLOPT_RETURNTRANSFER => $curlopt_returntransfer,
                CURLOPT_ENCODING => $curlopt_encoding,
                CURLOPT_MAXREDIRS => $curlopt_maxredirs,
                CURLOPT_TIMEOUT => $curlopt_timeout,
                CURLOPT_FOLLOWLOCATION => $curlopt_followlocation,
                CURLOPT_HTTP_VERSION => $curlopt_http_version,
                CURLOPT_CUSTOMREQUEST => $curlopt_customrequest,
                ));
        
                $response = curl_exec($curl);
                curl_close($curl);

                if($response != ""){
                    $d1 = explode(",",$response); $d2 = explode(":",$d1[0]); $d3 = explode('"',$d2[1]);

                    $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $wapp = $row['wapp']; } $incr_wapp = $wapp + 1;
                    
                    $sql = "UPDATE `master_generator` SET `wapp` = '$incr_wapp' WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'";
                    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
                    
                    if($incr_wapp < 10){ $incr_wapp = '000'.$incr_wapp; } else if($incr_wapp >= 10 && $incr_wapp < 100){ $incr_wapp = '00'.$incr_wapp; } else if($incr_wapp >= 100 && $incr_wapp < 1000){ $incr_wapp = '0'.$incr_wapp; } else { }
                    $wapp_code = "WAPP-".$incr_wapp;
                    $wsfile_path = $_SERVER['REQUEST_URI'];
                    $sql = "INSERT INTO `sms_details` (trnum,ccode,mobile,sms_sent,sms_status,msg_response,smsto,file_name,addedemp,addedtime,updatedtime,client)
                    VALUES ('$wapp_code','$ccode','$number','$msg_info','$d3[1]','$response','BB-Invoices','$wsfile_path','$addedemp','$addedtime','$addedtime','$client')";
                    if(!mysqli_query($conn,$sql)) { die("Error:- WhApp sending error: ".mysqli_error($conn)); } else{  }
                }
                else{
                    $wapp_error_flag = 1;
                }
                unlink($filepath2);
                //sleep(8);
            }
            else{
                $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $wapp = $row['wapp']; } $incr_wapp = $wapp + 1;
                
                $sql = "UPDATE `master_generator` SET `wapp` = '$incr_wapp' WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'";
                if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
                
                if($incr_wapp < 10){ $incr_wapp = '000'.$incr_wapp; } else if($incr_wapp >= 10 && $incr_wapp < 100){ $incr_wapp = '00'.$incr_wapp; } else if($incr_wapp >= 100 && $incr_wapp < 1000){ $incr_wapp = '0'.$incr_wapp; } else { }
                $wapp_code = "WAPP-".$incr_wapp;
                $wsfile_path = $_SERVER['REQUEST_URI'];

                $database = $_SESSION['dbase'];
                $trtype = "Invoice PDF";
                $trnum = NULL;
                $vendor = $ccode;
                $mobile = $number;
                $msg_trnum = $wapp_code;
                $msg_type = "WAPP";
                $msg_project = "CTS";
                $status = "CREATED";
                $trlink = $_SERVER['REQUEST_URI'];
                $sql = "INSERT INTO `master_pendingmessages` (`database`,`url_id`,`trtype`,`trnum`,`vendor`,`mobile`,`msg_trnum`,`msg_type`,`msg_info`,`msg_project`,`status`,`trlink`,`addedemp`,`addedtime`,`updatedtime`)
                VALUES ('$database','$url_id','$trtype','$trnum','$vendor','$mobile','$msg_trnum','$msg_type','$msg_info','$msg_project','$status','$trlink','$addedemp','$addedtime','$addedtime')";
                if(!mysqli_query($conns,$sql)) { } else{ }
            }
        }
        else if((int)$url_id == 3){
            $media_url = $filepath; $filename = $file_name.".pdf"; $number = "91".$cus_send_mobile;
            $msg_info = $curlopt_url.''.$instance_id.'/messages/document?token='.$access_token.'&to='.$number.'&filename='.$filename.'&document='.$media_url.'&caption='.$message;
        
            if((int)$wapp_timer_flag == 0 && $wapp_error_flag == 0){
                $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_URL => $msg_info,
                  CURLOPT_RETURNTRANSFER =>$curlopt_returntransfer,
                  CURLOPT_ENCODING => $curlopt_encoding,
                  CURLOPT_MAXREDIRS => $curlopt_maxredirs,
                  CURLOPT_TIMEOUT => $curlopt_timeout,
                  CURLOPT_FOLLOWLOCATION => $curlopt_followlocation,
                  CURLOPT_HTTP_VERSION => $curlopt_http_version,
                  CURLOPT_CUSTOMREQUEST => $curlopt_customrequest,
                ));
                
                $response = curl_exec($curl);
                curl_close($curl);
                if($response != ""){
                    $d1 = explode(",",$response); $d2 = explode(":",$d1[0]); $d3 = explode('"',$d2[1]);
                    if($d3[1] == "true"){ $d3[1] = "success"; }

                    $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $wapp = $row['wapp']; } $incr_wapp = $wapp + 1;
                    
                    $sql = "UPDATE `master_generator` SET `wapp` = '$incr_wapp' WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'";
                    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
                    
                    
                    if($incr_wapp < 10){ $incr_wapp = '000'.$incr_wapp; } else if($incr_wapp >= 10 && $incr_wapp < 100){ $incr_wapp = '00'.$incr_wapp; } else if($incr_wapp >= 100 && $incr_wapp < 1000){ $incr_wapp = '0'.$incr_wapp; } else { }
                    $wapp_code = "WAPP-".$incr_wapp;
                    $wsfile_path = $_SERVER['REQUEST_URI'];
                    $sql = "INSERT INTO `sms_details` (trnum,ccode,mobile,sms_sent,sms_status,msg_response,smsto,file_name,addedemp,addedtime,updatedtime,client)
                    VALUES ('$wapp_code','$ccode','$number','$msg_info','$d3[1]','$response','BB-Invoices','$wsfile_path','$addedemp','$addedtime','$addedtime','$client')";
                    if(!mysqli_query($conn,$sql)) { die("Error:- WhApp sending error: ".mysqli_error($conn)); } else{  }
                }
                else{
                    $wapp_error_flag = 1;
                }
                unlink($filepath2);
                //sleep(8);
            }
            else{
                $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $wapp = $row['wapp']; } $incr_wapp = $wapp + 1;
                
                $sql = "UPDATE `master_generator` SET `wapp` = '$incr_wapp' WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'";
                if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
                
                if($incr_wapp < 10){ $incr_wapp = '000'.$incr_wapp; } else if($incr_wapp >= 10 && $incr_wapp < 100){ $incr_wapp = '00'.$incr_wapp; } else if($incr_wapp >= 100 && $incr_wapp < 1000){ $incr_wapp = '0'.$incr_wapp; } else { }
                $wapp_code = "WAPP-".$incr_wapp;
                $wsfile_path = $_SERVER['REQUEST_URI'];

                $database = $_SESSION['dbase'];
                $trtype = "Invoice PDF";
                $trnum = NULL;
                $vendor = $ccode;
                $mobile = $number;
                $msg_trnum = $wapp_code;
                $msg_type = "WAPP";
                $msg_project = "CTS";
                $status = "CREATED";
                $trlink = $_SERVER['REQUEST_URI'];
                $sql = "INSERT INTO `master_pendingmessages` (`database`,`url_id`,`trtype`,`trnum`,`vendor`,`mobile`,`msg_trnum`,`msg_type`,`msg_info`,`msg_project`,`status`,`trlink`,`addedemp`,`addedtime`,`updatedtime`)
                VALUES ('$database','$url_id','$trtype','$trnum','$vendor','$mobile','$msg_trnum','$msg_type','$msg_info','$msg_project','$status','$trlink','$addedemp','$addedtime','$addedtime')";
                if(!mysqli_query($conns,$sql)) { } else{ }
            }
        }
        else{ }
    }
}

//header('location: ../../generalreports/CustomerMultipleInvoicePrintMaster5.php');

?>
<script>
    window.location.href = "../../generalreports/CustomerMultipleInvoicePrintMaster6.php";
</script>