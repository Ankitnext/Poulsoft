<?php
//CustomerLedgerReportAllNew_ta.php
$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
	
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../config.php";
    $dbname = $_SESSION['dbase'];
    $users_code = $_SESSION['userid'];

    $form_reload_page = "CustomerLedgerReportAllNew_ta.php";
}
else{
    include "APIconfig.php";
    $dbname = $db;
    $users_code = $_GET['emp_code'];
    $form_reload_page = "CustomerLedgerReportAllNew_ta.php?db=".$db;
}
include "number_format_ind.php";
$file_name = "Customer Balance Report";
function decimal_adjustments($a,$b){
    if($a == ""){ $a = 0; }
    $a = round($a,$b);
    $c = explode(".",$a);
    $ed = "";
    $iv = 0;
    if($c[1] == ""){ $iv = 1; }
    else{ $iv = strlen($c[1]); }
    if($iv == 0){ $iv = 1; }
    for($d = $iv;$d < $b;$d++){ if($ed == ""){ $ed = "0"; } else{ $ed .= "0"; } }
    return $a."".$ed;
}

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $etn_val = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $etn_val[$i] = $row1[$table_head]; $i++; }
if(in_array("main_regions", $etn_val, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.main_regions LIKE poulso6_admin_chickenmaster.main_regions;"; mysqli_query($conn,$sql1); }

//Logo Flag
$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query);
if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

//Company Details
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Customer Ledger Report' OR `type` = 'All' ORDER BY `id` DESC";
$query = mysqli_query($conn,$sql); $logopath = $cdetails = "";
while($row = mysqli_fetch_assoc($query)){ $logopath = $row['logopath']; $cdetails = $row['cdetails']; $cmpy_fname = $row['fullcname']; }

$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

//Customer Group Details
$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE 'C' AND `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $cgrp_code = $cgrp_name = array();
while($row = mysqli_fetch_assoc($query)){ $cgrp_code[$row['code']] = $row['code']; $cgrp_name[$row['code']] = $row['description']; }

$fdate = $tdate = date("Y-m-d"); $cgroups = "all"; $exports = "display"; $bwd_aflag = 0;
if(isset($_POST['submit']) == true){
	$fdate = date("Y-m-d",strtotime($_POST['fdate']));
	$tdate = date("Y-m-d",strtotime($_POST['tdate']));
	$cgroups = $_POST['cgroups'];
	$exports = $_POST['exports'];
	if($_POST['bwd_aflag'] == "on" || $_POST['bwd_aflag'] == 1 || $_POST['bwd_aflag'] == true){ $bwd_aflag = 1; }
    
    $sects = array(); $sec_all_flag = 0;
    foreach($_POST['sectors'] as $scts){ $sects[$scts] = $scts; if($scts == "all"){ $sec_all_flag = 1; } }
    $sects_list = implode("','", array_map('addslashes', $sects));
    $secct_fltr = ""; if($sec_all_flag == 1 ){ $secct_fltr = ""; } else { $secct_fltr = "AND `warehouse` IN ('$sects_list')";}
}

?>
<html>
	<head>
        <?php include "header_head2.php"; ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <style>
            .main-table { white-space: nowrap; }
            .tbody1{
                color: black;
            }
        </style>
	</head>
	<body>
		<section class="content" align="center">
			<div class="col-md-12" align="center">
				<form action="<?php echo $form_reload_page; ?>" method="post" onsubmit="return checkval()">
				    <table <?php if($exports == "print") { echo ' class="main-table"'; } else{ echo ' class="table-sm table-hover main-table2"'; } ?>>
                        <thead class="thead1">
                            <tr>
                                <td colspan="2"><img src="<?php echo "../".$logopath; ?>" height="150px"/></td>
                                <td colspan="2"><?php echo $cdetails; ?></td>
                                <td colspan="15" align="center">
                                    <h3><?php echo $file_name; ?></h3>
                                    <label><b style="color: green;">From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fdate)); ?></label>&ensp;&ensp;
                                    <label><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($tdate)); ?></label>
                                </td>
                            </tr>
                        </thead>
						<?php if($exports == "display" || $exports == "exportpdf") { ?>
						<thead class="thead1">
							<tr>
								<td colspan="19" class="p-1">
                                    <div class="m-1 p-1 row">
                                        <div class="form-group" style="width:110px;">
                                            <label for="fdate">From Date</label>
                                            <input type="text" name="fdate" id="fdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label for="tdate">To Date</label>
                                            <input type="text" name="tdate" id="tdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:190px;">
                                            <label for="cgroups">Group</label>
                                            <select name="cgroups" id="cgroups" class="form-control select2" style="width:180px;">
                                                <option value="all" <?php if($cgroups == "all"){ echo "selected"; } ?>>-All-</option>
											    <?php foreach($cgrp_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($cgroups == $scode){ echo "selected"; } ?>><?php echo $cgrp_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:100px;text-align:center;">
                                            <label for="bwd_aflag">B/w Days</label><br/>
                                            <input type="checkbox" name="bwd_aflag" id="bwd_aflag" <?php if($bwd_aflag == 1){ echo "checked"; } ?> />
                                        </div>
                                        <div class="form-group" style="width:190px;text-align:center;">
                                            <?php
										// Initialize selected sectors
										$selected_sectors = $_POST['sectors'] ?? ['all'];

										// Ensure it's always an array
										if (!is_array($selected_sectors)) {
											$selected_sectors = [$selected_sectors];
										}
										?>
										<label for="sectors[]">Warehouse</label>&nbsp;
										<select name="sectors[]" id="sectors[0]" class="form-control select2" style="width:180px;" multiple>
											<option value="all" <?php if(in_array("all", $selected_sectors)) echo "selected"; ?>>All</option>
											<?php foreach($sector_code as $scode) { ?>
												<option value="<?php echo $scode; ?>" <?php if(in_array($scode, $selected_sectors)) echo "selected"; ?>>
													<?php echo $sector_name[$scode]; ?>
												</option>
											<?php } ?>
										</select>
                                     </div>
                                    <!--<div class="m-1 p-1 row">-->
                                        <div class="form-group" style="width:150px;">
                                            <label>Export</label>
                                            <select name="exports" id="exports" class="form-control select2" style="width:140px;" onchange="tableToExcel('main_table', '<?php echo $file_name; ?>','<?php echo $file_name; ?>', this.options[this.selectedIndex].value)">
                                                <option value="display" <?php if($exports == "display"){ echo "selected"; } ?>>-Display-</option>
                                                <option value="excel" <?php if($exports == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                                <option value="print" <?php if($exports == "print"){ echo "selected"; } ?>>-Print-</option>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width: 210px;">
                                            <label for="search_table">Search</label>
                                            <input type="text" name="search_table" id="search_table" class="form-control" style="padding:0;padding-left:2px;width:200px;" />
                                        </div>
                                        <div class="form-group">
                                            <br/><button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
                                        </div>
                                    </div>
								</td>
							</tr>
						</thead>
                    <?php if($exports == "display" || $exports == "exportpdf"){ ?>
                    </table>
                    <table class="main-table table-sm table-hover" id="main_table">
                    <?php } ?>
						<?php
                        }
                        if(isset($_POST['submit']) == true){
                            $html = $nhtml = $fhtml = '';

                            $html .= '<thead class="thead2" id="head_names">';

                            $nhtml .= '<tr>'; $fhtml .= '<tr>';
                            $nhtml .= '<th>Sl.No.</th>'; $fhtml .= '<th id="order_num">Sl.No.</th>';
                            $nhtml .= '<th>Name</th>'; $fhtml .= '<th id="order">Name</th>';
                            $nhtml .= '<th>Mobile No</th>'; $fhtml .= '<th id="order">Mobile No</th>';
                            $nhtml .= '<th>Opening Balance</th>'; $fhtml .= '<th id="order_num">Opening Balance</th>';
                            $nhtml .= '<th>Sales Qty</th>'; $fhtml .= '<th id="order_num">Sales Qty</th>';
                            $nhtml .= '<th>Sales</th>'; $fhtml .= '<th id="order_num">Sales</th>';
                            $nhtml .= '<th>Receipt</th>'; $fhtml .= '<th id="order_num">Receipt</th>';
                            $nhtml .= '<th>B/w days balance</th>'; $fhtml .= '<th id="order_num">B/w days balance</th>';
                            $nhtml .= '<th>Balance</th>'; $fhtml .= '<th id="order_num">Balance</th>';
                            $nhtml .= '</tr>'; $fhtml .= '</tr>';

                            $html .= $fhtml;
                            $html .= '</thead>';

                            $html .= '<tbody id="tbody1" class="tbody1">';
                            
                            $cgrp_fltr = ""; if($cgroups != "all"){ $cgrp_fltr = " AND `groupcode` = '$cgroups'"; }
                            $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE 'C' AND `active` = '1'".$cgrp_fltr." ORDER BY `name` ASC";
                            $query = mysqli_query($conn,$sql); $cus_code = $cus_name = $cus_mobile = $obtype = $obamt = $creditamt = array();
                            while($row = mysqli_fetch_assoc($query)){
                                $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; $cus_mobile[$row['code']] = $row['mobileno'];
                                $creditamt[$row['code']] = $row['creditamt'];
                                if($row['obtype'] == "Cr"){ $obcramt[$row['code']] = $row['obamt']; $obdramt[$row['code']] = 0; }
                                else if($row['obtype'] == "Dr"){ $obdramt[$row['code']] = $row['obamt']; $obcram[$row['code']] = 0; }
                                else{ $obdramt[$row['code']] = $obcramt[$row['code']] = 0; }
                            }
                            $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'CustomerLedgerReportAllNew.php' AND `field_function` LIKE 'Purchase Sale Sorting' AND `user_access` LIKE 'all' AND `flag` = '1'";
                            $query = mysqli_query($conn,$sql); $sltr_flag = mysqli_num_rows($query); //$avou_flag = 1;
                            
                            //Sales
                            // if($sltr_flag > 0){ 
                            // $sql = "SELECT * FROM `customer_sales` WHERE `date` <= '$tdate' AND `trtype` NOT IN ('PST') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice`,`customercode` ASC";
                            // } else {
                            $sql = "SELECT * FROM `customer_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'".$secct_fltr." ORDER BY `date`,`invoice`,`customercode` ASC";
                            // }
                            $query = mysqli_query($conn,$sql); $opn_samt = $btw_sqty = $btw_samt = array(); $old_inv1 = "";
                            while($row = mysqli_fetch_assoc($query)){
                                if(strtotime($row['date']) < strtotime($fdate)){
                                    if($old_inv1 != $row['invoice']."@".$row['customercode']){
                                        $old_inv1 = $row['invoice']."@".$row['customercode'];
                                        $opn_samt[$row['customercode']] += (float)$row['finaltotal'];
                                    }
                                }
                                else{
                                    if($old_inv1 != $row['invoice']."@".$row['customercode']){
                                        $old_inv1 = $row['invoice']."@".$row['customercode'];
                                        $btw_samt[$row['customercode']] += (float)$row['finaltotal'];
                                    }
                                    $btw_sqty[$row['customercode']] = $btw_sqty[$row['customercode']] + $row['netweight'];
                                }
                            }
                            //Receipt
                            $sql = "SELECT * FROM `customer_receipts` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vtype` = 'C' AND `active` = '1'".$secct_fltr." AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`trnum`,`id` ASC";
                            $query = mysqli_query($conn,$sql); $opn_crct = $btw_crct = array();
                            while($row = mysqli_fetch_assoc($query)){
                                if(strtotime($row['date']) < strtotime($fdate)){
                                    $opn_crct[$row['ccode']] += (float)$row['amount'];
                                }
                                else{
                                    $btw_crct[$row['ccode']] += (float)$row['amount'];
                                }
                            }

                            //Customer Crdr
                            $sql = "SELECT * FROM `main_crdrnote` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `mode` IN ('CCN','CDN') AND `active` = '1'".$secct_fltr." AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`trnum`,`id` ASC";
                            $query = mysqli_query($conn,$sql); $opn_ccdn = $opn_cccn = $btw_ccdn = $btw_cccn = array();
                            while($row = mysqli_fetch_assoc($query)){
                                if(strtotime($row['date']) < strtotime($fdate)){
                                    if($row['mode'] == "CDN"){ $opn_ccdn[$row['ccode']] += (float)$row['amount']; }
                                    else if($row['mode'] == "CCN"){ $opn_cccn[$row['ccode']] += (float)$row['amount']; } else{ }
                                }
                                else{
                                    if($row['mode'] == "CDN"){ $btw_ccdn[$row['ccode']] += (float)$row['amount']; }
                                    else if($row['mode'] == "CCN"){ $btw_cccn[$row['ccode']] += (float)$row['amount']; } else{ }
                                }
                            }
                            
                            //Sales Return
                            $sql = "SELECT * FROM `main_itemreturns` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `mode` = 'customer' AND `active` = '1'".$secct_fltr." AND `dflag` = '0' ORDER BY `date`,`trnum`,`id` ASC";
                            $query = mysqli_query($conn,$sql); $opn_csrtn = $btw_csrtn = array();
                            while($row = mysqli_fetch_assoc($query)){
                                if(strtotime($row['date']) < strtotime($fdate)){
                                    $opn_csrtn[$row['vcode']] += (float)$row['amount'];
                                }
                                else{
                                    $btw_csrtn[$row['vcode']] += (float)$row['amount'];
                                }
                            }

                            //Customer Mortality
                            $sql = "SELECT * FROM `main_mortality` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `mtype` = 'customer' AND `active` = '1'".$secct_fltr." AND `dflag` = '0' ORDER BY `date`,`code`,`id` ASC";
                            $query = mysqli_query($conn,$sql); $opn_csmort = $btw_csmort = array();
                            while($row = mysqli_fetch_assoc($query)){
                                if(strtotime($row['date']) < strtotime($fdate)){
                                    $opn_csmort[$row['ccode']] += (float)$row['amount'];
                                }
                                else{
                                    $btw_csmort[$row['ccode']] += (float)$row['amount'];
                                }
                            }

                            $slno = $ft_obal = $ft_sqty = $ft_samt = $ft_ramt = $ft_bamt = $ft_camt = 0;
                            foreach($cus_code as $ccode){
                                //Calculate Openings
                                $o_dr = 0; if(!empty($obdramt[$ccode]) && (float)$obdramt[$ccode] != ""){ $o_dr = (float)$obdramt[$ccode]; }
                                $o_cr = 0; if(!empty($obcramt[$ccode]) && (float)$obcramt[$ccode] != ""){ $o_cr = (float)$obcramt[$ccode]; }
                                $o_samt = 0; if(!empty($opn_samt[$ccode]) && (float)$opn_samt[$ccode] != ""){ $o_samt = (float)$opn_samt[$ccode]; }
                                $o_cdn = 0; if(!empty($opn_ccdn[$ccode]) && (float)$opn_ccdn[$ccode] != ""){ $o_cdn = (float)$opn_ccdn[$ccode]; }
                                $o_rct = 0; if(!empty($opn_crct[$ccode]) && (float)$opn_crct[$ccode] != ""){ $o_rct = (float)$opn_crct[$ccode]; }
                                $o_ccn = 0; if(!empty($opn_cccn[$ccode]) && (float)$opn_cccn[$ccode] != ""){ $o_ccn = (float)$opn_cccn[$ccode]; }
                                $o_srtn = 0; if(!empty($opn_csrtn[$ccode]) && (float)$opn_csrtn[$ccode] != ""){ $o_srtn = (float)$opn_csrtn[$ccode]; }
                                $o_cmort = 0; if(!empty($opn_csmort[$ccode]) && (float)$opn_csmort[$ccode] != ""){ $o_cmort = (float)$opn_csmort[$ccode]; }
                                $o_bal = 0;
                                $o_bal = (((float)$o_samt + (float)$o_cdn + (float)$o_dr) - ((float)$o_rct + (float)$o_ccn + (float)$o_srtn + (float)$o_cmort + (float)$o_cr));

                                //Calculate B/w
                                $b_sqty = 0; if(!empty($btw_sqty[$ccode]) && (float)$btw_sqty[$ccode] != ""){ $b_sqty = (float)$btw_sqty[$ccode]; }
                                $b_samt = 0; if(!empty($btw_samt[$ccode]) && (float)$btw_samt[$ccode] != ""){ $b_samt = (float)$btw_samt[$ccode]; }
                                $b_rct = 0; if(!empty($btw_crct[$ccode]) && (float)$btw_crct[$ccode] != ""){ $b_rct = (float)$btw_crct[$ccode]; }
                                $b_cdn = 0; if(!empty($btw_ccdn[$ccode]) && (float)$btw_ccdn[$ccode] != ""){ $b_cdn = (float)$btw_ccdn[$ccode]; }
                                $b_ccn = 0; if(!empty($btw_cccn[$ccode]) && (float)$btw_cccn[$ccode] != ""){ $b_ccn = (float)$btw_cccn[$ccode]; }
                                $b_srtn = 0; if(!empty($btw_csrtn[$ccode]) && (float)$btw_csrtn[$ccode] != ""){ $b_srtn = (float)$btw_csrtn[$ccode]; }
                                $b_cmort = 0; if(!empty($btw_csmort[$ccode]) && (float)$btw_csmort[$ccode] != ""){ $b_cmort = (float)$btw_csmort[$ccode]; }

                                $t_samt = (float)$b_samt + (float)$b_cdn;
                                $t_ramt = (float)$b_rct + (float)$b_ccn + (float)$b_srtn + (float)$b_cmort;
                                $t_bamt = (float)$t_samt - (float)$t_ramt;
                                $c_bamt = (((float)$o_bal + (float)$t_samt) - (float)$t_ramt);

                                if((int)$bwd_aflag == 0 && (float)$c_bamt != 0 || (int)$bwd_aflag == 1 && (float)$t_samt != 0 && (float)$c_bamt != 0 || (int)$bwd_aflag == 1 && (float)$t_ramt != 0 && (float)$c_bamt != 0 || (float)$b_sqty != 0){
                                    $slno++;
                                    $cname = $cus_name[$ccode];
                                    $cmobl = $cus_mobile[$ccode];

                                    $html .= '<tr>';
                                    $html .= '<td style="text-align:center;">'.$slno.'</td>';
                                    $html .= '<td style="font-family:Palatino, URW Palladio L, serif">'.$cname.'</td>';
                                    $html .= '<td>'.$cmobl.'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($o_bal).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($b_sqty).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($t_samt).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($t_ramt).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($t_bamt).'</td>';
                                    $html .= '<td style="text-align:right;">'.number_format_ind($c_bamt).'</td>';
                                    $html .= '</tr>';

                                    $ft_obal += (float)$o_bal;
                                    $ft_sqty += (float)$b_sqty;
                                    $ft_samt += (float)$t_samt;
                                    $ft_ramt += (float)$t_ramt;
                                    $ft_bamt += (float)$t_bamt;
                                    $ft_camt += (float)$c_bamt;
                                }
                            }
                            $html .= '</tbody>';

                            $html .= '<thead class="tfoot1">';
                            $html .= '<tr style="color:blue;">';
                            $html .= '<th colspan="3">Total</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($ft_obal).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($ft_sqty).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($ft_samt).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($ft_ramt).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($ft_bamt).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($ft_camt).'</th>';
                            $html .= '</tr>';
                            $html .= '</thead>';

                            echo $html;
                        }
                        ?>
					</table>
				</form>
			</div>
		</section>
        <script>
            function checkval() {
                var cgroups = document.getElementById("cgroups").value;
                var l = true;
                if(cgroups == "select"){
                    alert("Kindly select customer Group");
                    l = false;
                }
                
                if(l == true){
                    return true;
                }
                else{
                    return false;
                }
            }
        </script>
        <script src="sort_table_columns_wsno.js"></script>
        <script src="searchbox.js"></script>
        <script type="text/javascript">
            function tableToExcel(table, name, filename, chosen){
                if(chosen === 'excel'){
                    cdate_format1();
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html += '<?php echo $nhtml; ?>';
                    $('#head_names').append(html);
                    
                    var table = document.getElementById("main_table");
                    var workbook = XLSX.utils.book_new();
                    var worksheet = XLSX.utils.table_to_sheet(table);
                    XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
                    XLSX.writeFile(workbook, filename+".xlsx");
                    
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html += '<?php echo $fhtml; ?>';
                    document.getElementById("head_names").innerHTML = html;
                    
                    $('#exports').select2();
                    document.getElementById("exports").value = "display";
                    $('#exports').select2();

                    cdate_format2();
                    table_sort();
                    table_sort2();
                    table_sort3();
                }
                else{ }
            }
            function cdate_format1() {
                const dateCells = document.querySelectorAll('#main_table .dates');
                var adate = [];
                dateCells.forEach(cell => {
                    let originalString = cell.textContent;
                    adate = []; adate = originalString.split(".");
                    cell.textContent = adate[2]+"-"+adate[1]+"-"+adate[0];
                });
            }
            function cdate_format2() {
                const dateCells = document.querySelectorAll('#main_table .dates');
                var adate = [];
                dateCells.forEach(cell => {
                    let originalString = cell.textContent;
                    adate = []; adate = originalString.split("-");
                    cell.textContent = adate[2]+"."+adate[1]+"."+adate[0];
                });
            }
        </script>
		<?php if($exports == "display" || $exports == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<?php include "header_foot2.php"; ?>
	</body>
	
</html>
