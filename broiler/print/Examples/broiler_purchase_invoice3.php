<?php
//broiler_purchase_invoice3.php
require_once('tcpdf_include.php');
include "newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;


$fetch_date = date("Y-m-d",strtotime($_POST['pdates']));
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Purchase Invoice' OR `type` = 'All'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	$img_path = $row['logopath']; 
	$cdetail = $row['cdetails'];
	$bank_name = $row['bank_name'];
	$bank_branch = $row['bank_branch'];
	$bank_accno = $row['bank_accno'];
	$bank_ifsc = $row['bank_ifsc'];
	$bank_accname = $row['bank_accname'];
	$upi_details = $row['upi_details'];
	$upi_mobile = $row['upi_mobile'];
}
$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; $item_cat[$row['code']] = $row['category']; }

$sql = "SELECT * FROM `main_contactdetails` ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vendor_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `inv_sectors` ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_employee` ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $driver_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `broiler_vehicle` ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vehicle_name[$row['code']] = $row['registration_number']; }

$sql = "SELECT * FROM `broiler_farm` ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description']; $farm_farmer[$row['code']] = $row['farmer_code']; $farm_address[$row['code']] = $row['farm_address']; }

$sql = "SELECT * FROM `main_access` ORDER BY `id` DESC"; $query  = mysqli_query($conn,$sql);
while($row  = mysqli_fetch_assoc($query)){ $db_emp_code[$row['empcode']] = $row['db_emp_code']; }

$sql = "SELECT * FROM `broiler_employee` ORDER BY `id` DESC"; $query  = mysqli_query($conn,$sql);
while($row  = mysqli_fetch_assoc($query)){ $emp_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `item_category` WHERE description like '%feed%';"; $query  = mysqli_query($conn,$sql); $feed_cat_code = array();
while($row  = mysqli_fetch_assoc($query)){ $feed_cat_code[$row['code']] = $row['code']; }

$bag_size = array();
$sql = "SELECT * FROM `feed_bagcapacity` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `id` DESC"; $query  = mysqli_query($conn,$sql);
while($row  = mysqli_fetch_assoc($query)){ $bag_size[$row['code']] = $row['bag_size']; }

$dbase = $_SESSION['dbase'];
$sql = "SELECT * FROM `log_useraccess` WHERE `dblist` = '$dbase' ORDER BY `id` ASC"; $query  = mysqli_query($conns,$sql);
while($row  = mysqli_fetch_assoc($query)){ $log_emp_name[$row['empcode']] = $row['username']; }

$html = "";
$inv_nos = array();
$inv_nos[0] = $_GET['trnum'];
$sector = $_GET['sector'];
$isize = sizeof($inv_nos);

foreach($inv_nos as $inv_no){
	if($inv_no != ""){
        $code = array();
		$sql = "SELECT * FROM `broiler_purchases` WHERE `trnum` = '$inv_no' AND `warehouse` = '$sector' AND `dflag` = '0'";
		$query = mysqli_query($conn,$sql); $c = 0;
		while($row = mysqli_fetch_assoc($query)){
			$c = $c + 1;
			$trnum = $row['trnum'];
			$code[$c] =  $item_name[$row['icode']];
			$icat = ""; $icat = $item_cat[$row['icode']];
			if(!empty($feed_cat_code[$icat]) &&$feed_cat_code[$icat] == $icat){
				if(!empty($bag_size[$row['icode']]) && $bag_size[$row['icode']] != 0){
					$qty_bags[$c] =  $row['rcd_qty'] / $bag_size[$row['icode']];
				}
				else if(!empty($bag_size["all"]) && $bag_size["all"] != 0){
					$qty_bags[$c] =  $row['rcd_qty'] / $bag_size["all"];  
				}
				else{
					$qty_bags[$c] =  $row['rcd_qty'] / 50;
				}
			} else{
				$qty_bags[$c] =  0;
			}
            
			$dcno = $row['billno'];
			$supplier_name = $vendor_name[$row['vcode']];
			$towarehouse = $sector_name[$row['warehouse']];
			$farmers = $farm_farmer[$row['warehouse']];
			$quantity[$c] = $row['rcd_qty'];
			$price[$c] = $row['rate'];
			$amount[$c] = $row['item_tamt'];

			$vehicle_code = $row['vehicle_code'];
			if(!empty($vehicle_name[$vehicle_code])){ $vname = $vehicle_name[$vehicle_code]; } else{ $vname = $vehicle_code; }
			if($vname == 'select'){ $vname = ''; }

			$driver_code = $row['driver_code'];
			$driver_mobile = $row['driver_mobile'];
			if(!empty($driver_name[$driver_code])){ $dname = $driver_name[$driver_code]; } else{ $dname = $driver_code; }
			if($dname == 'select'){
				$dname = '';
			}
			$date = $row['date'];
            if(!empty($emp_name[$db_emp_code[$row['addedemp']]])){
			    $addedemp = $emp_name[$db_emp_code[$row['addedemp']]];
            }
            else{
                $addedemp = $log_emp_name[$row['addedemp']];
            }
		}
		$dt = date("d.m.Y",strtotime($date));
		
		$sql = "SELECt * FROM `broiler_farmer` WHERE `code` LIKE '$farmers'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){
			$farmer_name = $row['name'];
			$farmer_mobile = $row['mobile1'];
			$farmer_address = $row['address'];
		}
			
		$html .= '<table align="center" style="width:100%" style="border: 1px solid black;">';
		$html .= '<tr>';
		$html .= '<td colspan="7" style="text-align:center;"><h2>Farmer Copy</h2></td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th colspan="2" style="text-align:right;"><br/><br/><br/>';
		$html .= '<img src="../../'.$img_path.'" height="50px" />';
		$html .= '</th>';
		$html .= '<th colspan="5" style="text-align:left;">';
		$html .= '<i style="font-size:10vw" align="left">'.$cdetail.'</i>';
		$html .= '</th>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th colspan="4" style="padding-left: 10px;text-align:left;">';
		$html .= '<b style="padding-left: 10px;text-align:left;">Dc No: </b>'.$dcno;
		$html .= '</th>';
		$html .= '<th colspan="3" style="padding-left: 10px;text-align:left;">';
		$html .= '<b style="padding-left: 10px;text-align:left;">Entry Date: </b>'.$dt;
		$html .= '</th>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th colspan="4" style="padding-left: 10px;text-align:left;">';
		$html .= '<b style="padding-left: 10px;text-align:left;">Entry By: </b>'.$addedemp;
		$html .= '</th>';
		$html .= '<th colspan="3" style="padding-left: 10px;text-align:left;">';
		$html .= '<b style="padding-left: 10px;text-align:left;">Vehicle No: </b>'.$vname;
		$html .= '</th>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th colspan="4" style="padding-left: 10px;text-align:left;">';
		$html .= '<b style="padding-left: 10px;text-align:left;">From Location: </b>'.$supplier_name;
		$html .= '</th>';
		$html .= '<th colspan="3" style="padding-left: 10px;text-align:left;">';
		$html .= '<b style="padding-left: 10px;text-align:left;">Driver: </b>'.$dname.',&ensp;<b style="padding-left: 10px;text-align:left;">Mobile: </b>'.$driver_mobile;
		$html .= '</th>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th colspan="4" style="padding-left: 10px;text-align:left;">';
		$html .= '<b style="padding-left: 10px;text-align:left;">To Location: </b>'.$towarehouse." (".$farmer_mobile.")";
		$html .= '</th>';
		$html .= '<th colspan="3" style="padding-left: 10px;text-align:left;">';
		$html .= '<b style="padding-left: 10px;text-align:left;">Invoice No: </b>'.$trnum."<br/>";
		$html .= '</th>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th colspan="7" style="padding-left: 10px;text-align:left;">';
		$html .= '<b style="padding-left: 10px;text-align:left;">Farmer Name: </b>'.$farmer_name;
		$html .= '</th>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th colspan="7" style="padding-left: 10px;text-align:left;">';
		$html .= '<b style="padding-left: 10px;text-align:left;">Delivery Address: </b>'.$farmer_address;
		$html .= '</th>';
		$html .= '</tr>';
		$html .= '</table>';

		$codes = $qty_bagss = $quantitys = $prices = $amounts = "<br/>";
		for($i = 1;$i <= $c;$i++){
			if($i <$c){
				$codes = $codes."".$code[$i]."<br/>";
				$qty_bagss = $qty_bagss."".str_replace(".00","",number_format_ind($qty_bags[$i]))."<br/>";
				$quantitys = $quantitys."".number_format_ind($quantity[$i])."<br/>";
				$prices = $prices."".number_format_ind($price[$i])."<br/>";
				$amounts = $amounts."".number_format_ind($amount[$i])."<br/>";
			}
			else {
				$br = "";
				//$k = 9 - $c;
				for($j = 4;$j >= $c; $j--){
					$br = $br."";
				}
				$codes = $codes."".$code[$i]."".$br;
				$qty_bagss = $qty_bagss."".str_replace(".00","",number_format_ind($qty_bags[$i]))."".$br;
				$quantitys = $quantitys."".number_format_ind($quantity[$i])."".$br;
				$prices = $prices."".number_format_ind($price[$i])."".$br;
				$amounts = $amounts."".number_format_ind($amount[$i])."".$br;
			}

			 $total_bags += $qty_bags[$i];
			 $total_qty += $quantity[$i];
			 $total_amt += $amount[$i];
		}

		$html .= '<table width="100%" align="center" border="1">';

		$html .= '<tr>';
		$html .= '<th colspan="5" style="border: 1px solid black;">Item</th>';
		$html .= '<th  colspan="2" style="border: 1px solid black;">Quantity</th>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td colspan="5" style="padding:5px;text-align:center;height:auto;"><br/>'.$codes.'<br/></td>';
		$html .= '<td colspan="2" style="padding:5px;text-align:center;height:auto;"><br/>'.$quantitys.'<br/></td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td colspan="5" style="padding:5px;text-align:right;margin-right: 150px;"><br/>Total Quntity&ensp;</td>';
		$html .= '<td colspan="2" style="padding:5px;text-align:center;"><br/>'.number_format_ind($total_qty).'</td>';
		$html .= '</tr>';
		$html .= '<tr >';
		$html .= '<td  colspan="5" style="padding-left: 10px;text-align:left;"><br/><br/><br/>&ensp;   Farmer Sign<br/></td>';
		$html .= '<td  colspan="2" style="text-align:right;"><br/><br/><br/>Manager Sign&ensp;&ensp;<br/></td>';
		$html .= '</tr>';
	
		$html .= '</table>';

		$html .= '<br><br><br>';

		$html .= '<table align="center" style="width:100%" style="border: 1px solid black;">';
		$html .= '<tr>';
		$html .= '<td colspan="7" style="text-align:center;"><h2>Office Copy</h2></td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th colspan="2" style="text-align:right;"><br/><br/><br/>';
		$html .= '<img src="../../'.$img_path.'" height="50px" />';
		$html .= '</th>';
		$html .= '<th colspan="5" style="text-align:left;">';
		$html .= '<i align="left">'.$cdetail.'</i><br/>';
		$html .= '</th>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th colspan="4" style="padding-left: 10px;text-align:left;">';
		$html .= '<b style="padding-left: 10px;text-align:left;">Dc No: </b>'.$dcno;
		$html .= '</th>';
		$html .= '<th colspan="3" style="padding-left: 10px;text-align:left;">';
		$html .= '<b style="padding-left: 10px;text-align:left;">Entry Date: </b>'.$dt;
		$html .= '</th>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th colspan="4" style="padding-left: 10px;text-align:left;">';
		$html .= '<b style="padding-left: 10px;text-align:left;">Entry By: </b>'.$addedemp;
		$html .= '</th>';
		$html .= '<th colspan="3" style="padding-left: 10px;text-align:left;">';
		$html .= '<b style="padding-left: 10px;text-align:left;">Vehicle No: </b>'.$vname;
		$html .= '</th>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th colspan="4" style="padding-left: 10px;text-align:left;">';
		$html .= '<b style="padding-left: 10px;text-align:left;">From Location: </b>'.$supplier_name;
		$html .= '</th>';
		$html .= '<th colspan="3" style="padding-left: 10px;text-align:left;">';
		$html .= '<b style="padding-left: 10px;text-align:left;">Driver: </b>'.$dname.',&ensp;<b style="padding-left: 10px;text-align:left;">Mobile: </b>'.$driver_mobile;
		$html .= '</th>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th colspan="4" style="padding-left: 10px;text-align:left;">';
		$html .= '<b style="padding-left: 10px;text-align:left;">To Location: </b>'.$towarehouse." (".$farmer_mobile.")";
		$html .= '</th>';
		$html .= '<th colspan="3" style="padding-left: 10px;text-align:left;">';
		$html .= '<b style="padding-left: 10px;text-align:left;">Invoice No: </b>'.$trnum.'<br/>';
		$html .= '</th>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th colspan="7" style="padding-left: 10px;text-align:left;">';
		$html .= '<b style="padding-left: 10px;text-align:left;">Farmer Name: </b>'.$farmer_name;
		$html .= '</th>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th colspan="7" style="padding-left: 10px;text-align:left;">';
		$html .= '<b style="padding-left: 10px;text-align:left;">Delivery Address: </b>'.$farmer_address;
		$html .= '</th>';
		$html .= '</tr>';
		$html .= '</table>';

		$codes = $qty_bagss = $quantitys = "<br/>";

		$total_qty = $total_bags = 0;
		for($i = 1;$i <= $c;$i++){
			if($i <$c){
				$codes = $codes."".$code[$i]."<br/>";
				$qty_bagss = $qty_bagss."".str_replace(".00","",number_format_ind($qty_bags[$i]))."<br/>";
				$quantitys = $quantitys."".number_format_ind($quantity[$i])."<br/>";
			}
			else {
				$br = "";
				//$k = 9 - $c;
				for($j = 4;$j >= $c; $j--){
					$br = $br."";
				}
				$codes = $codes."".$code[$i]."".$br;
				$qty_bagss = $qty_bagss."".str_replace(".00","",number_format_ind($qty_bags[$i]))."".$br;
				$quantitys = $quantitys."".number_format_ind($quantity[$i])."".$br;
			}

            $total_bags += $qty_bags[$i];
			$total_qty += $quantity[$i];
		}

		$html .= '<table width="100%" align="center" border="1">';

		$html .= '<tr>';
		$html .= '<th colspan="2" style="border: 1px solid black;">Item</th>';
		$html .= '<th  colspan="2" style="border: 1px solid black;">Quantity</th>';
		$html .= '<th colspan="1" style="border: 1px solid black;">Price</th>';
		$html .= '<th colspan="2" style="border: 1px solid black;">Amount</th>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td colspan="2" style="padding:5px;text-align:center;height:auto;"><br/>'.$codes.'<br/></td>';
		$html .= '<td colspan="2" style="padding:5px;text-align:center;height:auto;"><br/>'.$quantitys.'<br/></td>';
		$html .= '<td colspan="1" style="padding:5px;text-align:center;height:auto;"><br/>'.$prices.'<br/></td>';
		$html .= '<td colspan="2" style="padding:5px;text-align:center;height:auto;"><br/>'.$amounts.'<br/></td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td colspan="2" style="padding:5px;text-align:right;margin-right: 150px;"><br/>Total Quntity&ensp;</td>';
		$html .= '<td colspan="2" style="padding:5px;text-align:center;"><br/>'.str_replace(".00","",number_format_ind($total_qty)).'</td>';
		$html .= '<td colspan="1" style="padding:5px;text-align:center;"></td>';
		$html .= '<td colspan="2" style="padding:5px;text-align:center;"><br/>'.number_format_ind($total_amt).'</td>';
		$html .= '</tr>';
		$html .= '<tr >';
		$html .= '<td  colspan="5" style="padding-left: 10px;text-align:left;"><br/><br/><br/>&ensp;   Farmer Sign<br/></td>';
		$html .= '<td  colspan="2" style="text-align:right;"><br/><br/><br/>Manager Sign&ensp;&ensp;<br/></td>';
		$html .= '</tr>';
		$html .= '</table>';
	}
}
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Mallikarjuna K');
$pdf->SetTitle('StockTransfer'); 
$pdf->SetSubject('StockTransfer');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
$pdf->SetFont('dejavusans', '', 10, '', true);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
//$pdf->SetMargins(7, 7, 7, true);
$pdf->AddPage('P', 'A4');

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

$pdf->Output('StockTransfer'.$trnum.'.pdf', 'I');


?>