<?php
//broiler_saleinvoice.php
require_once('tcpdf_include.php');
include "../../config.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

$tran = $_GET['id'];

$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' OR `type` = 'all'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	$img_path = $row['logopath']; $cdetail = $row['cdetails'];
	$bank_name = $row['bank_name'];
	$bank_branch = $row['bank_branch'];
	$bank_accno = $row['bank_accno'];
	$bank_ifsc = $row['bank_ifsc'];
	$bank_accname = $row['bank_accname'];
	$upi_details = $row['upi_details'];
	$upi_mobile = $row['upi_mobile'];
	$comname = $row['cname'];
}

$sql = "SELECT * FROM `employee_sal_payment` WHERE `active` = '1' AND `trnum` = '$tran'";
$query = mysqli_query($conn,$sql); 
while($row = mysqli_fetch_assoc($query)){
     $link_trnum = $row['link_trnum'];
     $date = $row['date'];
     $desig_code = $row['desig_code'];
     $emp_code  = $row['emp_code'];
     $sal_mnth = $row['salary_month'];
     $working_days = $row['working_days'];
     $esi = $row['esi'];   
     $baccno = $row['bank_accno'];
     $pay_mode = $row['pay_mode'];
     $pay_method = $row['pay_method'];
}

$sql = "SELECT * FROM `employee_sal_generator` WHERE `trnum` = '$link_trnum'";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $workd = $row['work_days'];
    $wingdays = $row['working_days'];
    $lop = (float)$workd - (float)$wingdays;
    $allowance = $row['allowances'];
    $deductions = $row['deductions'];
    $pf = $row['provident_fund'];
    $gross_salary = $row['gross_salary'];
    $p_tax = $row['professional_tax'];
    $net_sal = $row['net_salary'];
}

$sql = "SELECT * FROM `broiler_employee` WHERE `code` = '$emp_code'";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $doj = $row['join_date'];
    $formattedDate = date("d-m-Y", strtotime($doj));
    $sect = $row['warehouse'];
    $emp_id = $row['emp_id'];
    $ename = $row['name'];
}

$sql = "SELECT * FROM `salary_structures` WHERE `sector_code` = '$sect' AND `desig_code` = '$desig_code' AND `active` = '1'";
//echo $sql;
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $basic = $row['basic'];
    $basicamt = ($basic/100)*$gross_salary;
    $hra = $row['hra'];
    $hraamt = ($hra/100)*$gross_salary;
    $medical = $row['medical'];
    $medicalamt = ($medical/100)*$gross_salary;
    $con_allow = $row['con_allow'];
    $con_allow_amt = ($con_allow/100)*$gross_salary;
    $transport = $row['transport'];
    $tranamt = ($transport/100)*$gross_salary;
}

$sql = "SELECT * FROM `broiler_designation` WHERE `active` = '1'";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $desn[$row['code']] = $row['description'];
}

$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'salary_invoice.php' AND `field_function` = 'Salary Invoice Print' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $sale_flag = mysqli_num_rows($query);


$html  .= '';
$html .= '<table style="border:1px solid black;">';

$html .= '<tr style="line-height: 1.8;">';
$html .= '<th style="width:220px;border-right:1px solid black;text-align:center;"><br/><br/><br/><img src="../../'.$img_path.'" height="50px" /></th>';
$html .= '<th style="width:340px;text-align:center;">'.$cdetail.'</th>';
 $html .= '<th style="width:1px;border-left:1px solid black"></th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:560px;text-align:center;border-top:1px solid black;"><b>Pay slip for the month of '.$sal_mnth.'-2025</b></th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:140px;border-top:1px solid black;border-right:1px solid black;">Employee ID</th>';
$html .= '<th style="width:140px;border-top:1px solid black;border-right:1px solid black;">'.$emp_id.'</th>';
$html .= '<th style="width:140px;border-top:1px solid black;border-right:1px solid black;">Bank Account</th>';
$html .= '<th style="width:140px;border-top:1px solid black;border-right:1px solid black;">'.$baccno.'</th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:140px;border-top:1px solid black;border-right:1px solid black;">Employee Name</th>';
$html .= '<th style="width:140px;border-top:1px solid black;border-right:1px solid black;">'.$ename.'</th>';
$html .= '<th style="width:140px;border-top:1px solid black;border-right:1px solid black;">Date of Joining</th>';
$html .= '<th style="width:140px;border-top:1px solid black;border-right:1px solid black;">'.$formattedDate.'</th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:140px;border-top:1px solid black;border-right:1px solid black;">Designation</th>';
$html .= '<th style="width:140px;border-top:1px solid black;border-right:1px solid black;">'.$desn[$desig_code].'</th>';
$html .= '<th style="width:140px;border-top:1px solid black;border-right:1px solid black;">No. of Working Days</th>';
$html .= '<th style="width:140px;border-top:1px solid black;border-right:1px solid black;">'.$working_days.'</th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:140px;border-top:1px solid black;border-right:1px solid black;"></th>';
$html .= '<th style="width:140px;border-top:1px solid black;border-right:1px solid black;"></th>';
$html .= '<th style="width:140px;border-top:1px solid black;border-right:1px solid black;">LOP Days</th>';
$html .= '<th style="width:140px;border-top:1px solid black;border-right:1px solid black;">'.$lop.'</th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:280px;border-top:1px solid black;border-right:1px solid black;text-align:center;"><b>Earnings</b></th>';
$html .= '<th style="width:280px;border-top:1px solid black;border-right:1px solid black;text-align:center;"><b>Deductions</b></th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:200px;border-top:1px solid black;border-right:1px solid black;">Basic</th>';
$html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;">'.$basicamt.'</th>';
$html .= '<th style="width:200px;border-top:1px solid black;border-right:1px solid black;">PF</th>';
$html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;">'.$pf.'</th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:200px;border-top:1px solid black;border-right:1px solid black;">HRA</th>';
$html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;">'.$hraamt.'</th>';
$html .= '<th style="width:200px;border-top:1px solid black;border-right:1px solid black;">Professional Tax</th>';
$html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;">'.$p_tax.'</th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:200px;border-top:1px solid black;border-right:1px solid black;">Medical Allowance</th>';
$html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;">'.$medicalamt.'</th>';
$html .= '<th style="width:200px;border-top:1px solid black;border-right:1px solid black;">Variable Pay</th>';
$html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;">0.00</th>';
$html .= '</tr>';

if($sale_flag > 0 ){} else {
$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:200px;border-top:1px solid black;border-right:1px solid black;">City Compensatory Allowance</th>';
$html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;">'.$con_allow_amt.'</th>';
$html .= '<th style="width:200px;border-top:1px solid black;border-right:1px solid black;">TDS</th>';
$html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;">0.00</th>';
$html .= '</tr>';
}

if($sale_flag > 0 ){
$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:200px;border-top:1px solid black;border-right:1px solid black;">Transportation Allowance</th>';
$html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;">'.$tranamt.'</th>';
$html .= '<th style="width:200px;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;">TDS</th>';
$html .= '<th style="width:80px;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;">0.00</th>';
$html .= '</tr>';

} else {
$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:200px;border-top:1px solid black;border-right:1px solid black;">Transportation Allowance</th>';
$html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;">'.$tranamt.'</th>';
$html .= '<th style="width:200px;border-top:1px solid black;"></th>';
$html .= '<th style="width:80px;border-top:1px solid black;"></th>';
$html .= '</tr>';
}

$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:200px;border-top:1px solid black;border-right:1px solid black;">variable Pay</th>';
$html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;">0.00</th>';
$html .= '<th style="width:200px;"></th>';
$html .= '<th style="width:80px;"></th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:200px;border-top:1px solid black;border-right:1px solid black;">Other Allowance(+)</th>';
$html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;">0.00</th>';
$html .= '<th style="width:200px;border-top:1px solid black;border-right:1px solid black;">Other Deduction(-)</th>';
$html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;">0.00</th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:200px;border-top:1px solid black;border-right:1px solid black;">Variable Pay Out Quarterly</th>';
$html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;">0</th>';
$html .= '<th style="width:200px;border-top:1px solid black;border-right:1px solid black;"><b>Total Deductions</b></th>';
$html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;">'.$deductions.'</th>';
$html .= '</tr>';

$html .= '<tr style="line-height: 2.8;">';
$html .= '<th style="width:200px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;"><b>Gross Earnings</b></th>';
$html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;">'.$gross_salary.'</th>';
$html .= '<th style="width:200px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;"><b>Net Pay</b></th>';
$html .= '<th style="width:80px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;">'.$net_sal.'</th>';
$html .= '</tr>';




$html .= '</table>';

$html .= "<br/><br/>";

$html .= '<span style="text-align:center;">This is a computer generated salary slip. Signature is not required.</span>';





//echo $html;

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Mallikarjuna K');
$pdf->SetTitle('Famrer RC generate');
$pdf->SetSubject('Famrer RC generate');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
$pdf->SetFont('dejavusans', '', 9, '', true);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetMargins(5, 5, 10, true);
//$pdf->setCellHeightRatio(1.5);
$pdf->AddPage('P', 'A4');

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

$pdf->Output('example_028.pdf', 'I');

?>