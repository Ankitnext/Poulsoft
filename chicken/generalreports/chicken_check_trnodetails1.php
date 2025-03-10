<?php
//chicken_check_trnodetails1.php
$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
	
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../config.php";
    $dbname = $_SESSION['dbase'];
    $users_code = $_SESSION['userid'];

    $form_reload_page = "chicken_check_trnodetails1.php";
}
else{
    include "APIconfig.php";
    $dbname = $db;
    $users_code = $_GET['emp_code'];
    $form_reload_page = "chicken_check_trnodetails1.php?db=".$db;
}
include "number_format_ind.php";

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
$file_name = "User Activity Report";

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `main_contactdetails`'; $query = mysqli_query($conn,$sql); $ecn_val = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $ecn_val[$i] = $row['Field']; $i++; }
if(in_array("dflag", $ecn_val, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `dflag` INT(100) NOT NULL DEFAULT '0' AFTER `active`"; mysqli_query($conn,$sql); }

/*Company Profile*/
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' ORDER BY `id` DESC";
$query = mysqli_query($conn,$sql); $logopath = $cdetails = "";
while($row = mysqli_fetch_assoc($query)){ $logopath = $row['logopath']; $cdetails = $row['cdetails']; $cmpy_fname = $row['fullcname']; }

//Customer Details
$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $ven_code = $ven_name = array();
while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `main_access` WHERE `empcode` = '$users_code' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $loc_access = ""; $adm_aflag = 0;
while($row = mysqli_fetch_assoc($query)){ $loc_access = $row['loc_access']; if((int)$row['supadmin_access'] == 1 || (int)$row['admin_access'] == 1){ $adm_aflag = 1; } }

//Sector Access Filter
if($loc_access == "" || $loc_access == "all"){ $sec_fltr = ""; }
else{
    $loc1 = explode(",",$loc_access); $loc_list = "";
    foreach($loc1 as $loc2){ if($loc_list = ""){ $loc_list = $loc2; } else{ $loc_list = $loc_list."','".$loc2; } }
    $sec_fltr = " AND `code` IN ('$loc_list')";
}
//Sector Details
$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'".$sec_fltr." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_code = $item_name = $item_cunits = array();
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_cunits[$row['code']] = $row['cunits']; }

//Fetch User Details
if((int)$adm_aflag == 1){
    $sql = "SELECT * FROM `log_useraccess` WHERE `dblist` LIKE '$dbname' AND `dflag` = '0' ORDER BY `username` ASC";
}
else{
    $sql = "SELECT * FROM `log_useraccess` WHERE `dblist` LIKE '$dbname' AND `empcode` LIKE '$emp_code' AND `dflag` = '0' ORDER BY `username` ASC";
}
$query = mysqli_query($conns,$sql); $usr_code = $usr_name = array();
while($row = mysqli_fetch_assoc($query)){ $usr_code[$row['empcode']] = $row['empcode']; $usr_name[$row['empcode']] = $row['username']; }

//Font-Styles
$sql = "SELECT * FROM `font_style_master` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `font_name1` ASC";
$query = mysqli_query($conn,$sql); $font_id = $font_name = array();
while($row = mysqli_fetch_assoc($query)){ $font_id[$row['id']] = $row['id']; if($row['font_name2'] != ""){ $font_name[$row['id']] = $row['font_name1'].",".$row['font_name2']; } else{ $font_name[$row['id']] = $row['font_name1']; } }
if(sizeof($font_id) > 0){ $font_fflag = 1; } else { $font_fflag = 0; }
for($i = 0;$i <= 30;$i++){ $font_sizes[$i."px"] = $i."px"; }

$fdate = $tdate = date("Y-m-d"); $sectors = $users = "all"; $tr_num = "";  $fstyles = $fsizes = "default"; $exports = "display";
if(isset($_POST['submit']) == true){
    // $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    // $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    // $sectors = $_POST['sectors'];
    $tr_num = $_POST['tr_num'];
    $fstyles = $_POST['fstyles'];
    $fsizes = $_POST['fsizes'];
    $exports = $_POST['exports'];
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
                                </td>
                            </tr>
                        </thead>
						<?php if($exports == "display" || $exports == "exportpdf") { ?>
						<thead class="thead1">
							<tr>
								<td colspan="19" class="p-1">
                                    <div class="m-1 p-1 row">
                                        <!-- <div class="form-group" style="width:110px;">
                                            <label for="fdate">From Date</label>
                                            <input type="text" name="fdate" id="fdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label for="tdate">To Date</label>
                                            <input type="text" name="tdate" id="tdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:190px;">
                                            <label for="users">user</label>
                                            <select name="users" id="users" class="form-control select2" style="width:180px;">
                                                <option value="all" <?php if($users == "all"){ echo "selected"; } ?>>-All-</option>
											    <?php foreach($usr_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($users == $scode){ echo "selected"; } ?>><?php echo $usr_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:190px;">
                                            <label for="sectors">Shop/Outlet</label>
                                            <select name="sectors" id="sectors" class="form-control select2" style="width:180px;">
                                                <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>-All-</option>
											    <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($sectors == $scode){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div> -->
                                        <?php if((int)$font_fflag == 1){ ?>
                                        <div class="form-group" style="width:190px;">
                                            <label for="fstyles">Font-Family</label>
                                            <select name="fstyles" id="fstyles" class="form-control select2" style="width:180px;">
                                                <option value="default" <?php if($fstyles == "default"){ echo "selected"; } ?>>-Default-</option>
											    <?php foreach($font_id as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($fstyles == $scode){ echo "selected"; } ?>><?php echo $font_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:70px;">
                                            <label for="fsizes">Font-Size</label>
                                            <select name="fsizes" id="fsizes" class="form-control select2" style="width:60px;">
                                                <option value="default" <?php if($fsizes == "default"){ echo "selected"; } ?>>-Default-</option>
											    <?php foreach($font_sizes as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($fsizes == $scode){ echo "selected"; } ?>><?php echo $font_sizes[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <?php } ?>
                                        <!-- <div class="form-group" style="width:150px;">
                                            <label>Export</label>
                                            <select name="exports" id="exports" class="form-control select2" style="width:140px;" onchange="tableToExcel('main_table', '<?php echo $file_name; ?>','<?php echo $file_name; ?>', this.options[this.selectedIndex].value)">
                                                <option value="display" <?php if($exports == "display"){ echo "selected"; } ?>>-Display-</option>
                                                <option value="excel" <?php if($exports == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                                <option value="print" <?php if($exports == "print"){ echo "selected"; } ?>>-Print-</option>
                                            </select>
                                        </div> -->
                                        <div class="form-group" style="width: 210px;">
                                            <label for="tr_num">Search Transaction No.</label>
                                            <input type="text" name="tr_num" id="tr_num" class="form-control" style="padding:0;padding-left:2px;width:200px;" />
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
                            // $sql = "SELECT * FROM `main_access` WHERE `empcode` = '$users' AND `active` = '1'";
                            // $query = mysqli_query($conn,$sql); $cash_coa = $bank_coa = "";
                            // while($row = mysqli_fetch_assoc($query)){ $cash_coa = $row['cash_coa']; $bank_coa = $row['bank_coa']; }
                            
                            $html = '';
                            //Sales
                         
                            $html .= '<tr class="thead2">';
                            $html .= '<th style="text-align:center;">Sl.No.</th>';
                            $html .= '<th style="text-align:center;">Date</th>';
                            $html .= '<th style="text-align:center;">Transaction Number</th>';
                            $html .= '<th style="text-align:center;">Transaction Type</th>';
                            $html .= '<th style="text-align:center;">Quantity</th>';
                            $html .= '<th style="text-align:center;">Amount</th>';
                            $html .= '<th style="text-align:center;">Added Employee</th>';
                            $html .= '<th style="text-align:center;">Added Time</th>';
                            $html .= '<th style="text-align:center;">Edited Employee</th>';
                            $html .= '<th style="text-align:center;">Edited Time</th>';

                            $html .= '</tr>';

                            // $usr_fltr = ""; if($users != "all"){ $usr_fltr = " AND `addedemp` = '$users'"; }
                            // $sec_fltr = ""; if($sectors != "all"){ $sec_fltr = " AND `warehouse` = '$sectors'"; }

                            // $sql = "SELECT * FROM `retail_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$usr_fltr."".$sec_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `trnum` ASC";
                            // $query = mysqli_query($conn, $sql); $isale_qty = $isale_amt = array();
                            // while($row = mysqli_fetch_assoc($query)){
                            //     $key = $row['icode'];
                            //     $isale_qty[$key] += (float)$row['quantity'];
                            //     $isale_amt[$key] += (float)$row['amount'];
                            // }
                            $i = 0;
                            $sql1 = "SELECT * FROM `customer_sales` WHERE `invoice` = '$tr_num'";
                            $query1 = mysqli_query($conn,$sql1); $count1 = mysqli_num_rows($query1);
                            if($count1 > 0){
                                while($row = mysqli_fetch_assoc($query1)){
                                    $aemp = $row['addedemp']; $eemp = $row['updatedemp'];$quantity = $row['netweight'];$amount = $row['totalamt']; $edate = date("d.m.Y",strtotime($row['date']));
                                    $adtime1 = date("d.m.Y",strtotime($row['addedtime'])); $adtime2 = date("d.m.Y",strtotime($row['updated']));
                                    
                                        $i++;
                                        $id = ''; $path='';
                                        $id = $row['invoice'];$report_view='report';
                                        $path = "openPopup('../cus_editsales.php?id=$id&view=$report_view')";
                                        
                                        $html .= "<tr><td style='text-align:center;'>".$i."</td>
                                        <td>".$edate."</td>
                                        <td><a href='javascript:void(0);' onclick=".$path.">".$row['invoice']."</a></td>
                                        <td>Sale</td><td>".$quantity."</td><td>".$amount."</td>
                                        <td>".$usr_name[$aemp]."</td>
                                        <td>".$adtime1."</td>
                                        <td>".$usr_name[$eemp]."</td>
                                        <td>".$adtime2."</td>
                                        </tr>";
        
                                }
                            }

                            $sql1 = "SELECT * FROM `main_crdrnote` WHERE `trnum` = '$tr_num'";
                            $query1 = mysqli_query($conn,$sql1); $count1 = mysqli_num_rows($query1);
                            if($count1 > 0){
                                while($row = mysqli_fetch_assoc($query1)){
                                    $aemp = $row['addedemp']; $eemp = $row['updatedemp'];$amount = $row['amount']; $edate = date("d.m.Y",strtotime($row['date']));
                                    $adtime1 = date("d.m.Y",strtotime($row['addedtime'])); $adtime2 = date("d.m.Y",strtotime($row['updated']));
                                    
                                        $i++;
                                        $id = ''; $path='';
                                        $id = $row['trnum'];$report_view='report';
                                        $path = "openPopup('../main_editcreditdebitnote.php?id=$id&view=$report_view')";
                                        
                                        $html .= "<tr><td style='text-align:center;'>".$i."</td>
                                        <td>".$edate."</td>
                                        <td><a href='javascript:void(0);' onclick=".$path.">".$row['trnum']."</a></td>
                                        <td>Cr/Dr Notes</td><td></td><td>".$amount."</td>
                                        <td>".$usr_name[$aemp]."</td>
                                        <td>".$adtime1."</td>
                                        <td>".$usr_name[$eemp]."</td>
                                        <td>".$adtime2."</td>
                                        </tr>";
        
                                }
                            }

                            $sql1 = "SELECT * FROM `acc_vouchers` WHERE `trnum` = '$tr_num'";
                            $query1 = mysqli_query($conn,$sql1); $count1 = mysqli_num_rows($query1);
                            if($count1 > 0){
                                while($row = mysqli_fetch_assoc($query1)){
                                    $aemp = $row['addedemp']; $eemp = $row['updatedemp'];$quantity = $row['emp_cvalue'];$amount = $row['amount']; $edate = date("d.m.Y",strtotime($row['date']));
                                    $adtime1 = date("d.m.Y",strtotime($row['addedtime'])); $adtime2 = date("d.m.Y",strtotime($row['updated']));
                                    
                                        $i++;
                                        $id = ''; $path='';
                                        $id = $row['trnum'];$report_view='report';
                                        $path = "openPopup('../acc_editvouchers.php?id=$id&view=$report_view')";
                                        
                                        $html .= "<tr><td style='text-align:center;'>".$i."</td>
                                        <td>".$edate."</td>
                                        <td><a href='javascript:void(0);' onclick=".$path.">".$row['trnum']."</a></td>
                                        <td>Vouchers</td><td>".$quantity."</td><td>".$amount."</td>
                                        <td>".$usr_name[$aemp]."</td>
                                        <td>".$adtime1."</td>
                                        <td>".$usr_name[$eemp]."</td>
                                        <td>".$adtime2."</td>
                                        </tr>";
        
                                }
                            }

                            $sql1 = "SELECT * FROM `customer_receipts` WHERE `trnum` = '$tr_num'";
                            $query1 = mysqli_query($conn,$sql1); $count1 = mysqli_num_rows($query1);
                            if($count1 > 0){
                                while($row = mysqli_fetch_assoc($query1)){
                                    $aemp = $row['addedemp']; $eemp = $row['updatedemp'];$amount = $row['amount']; $edate = date("d.m.Y",strtotime($row['date']));
                                    $adtime1 = date("d.m.Y",strtotime($row['addedtime'])); $adtime2 = date("d.m.Y",strtotime($row['updated']));
                                    
                                        $i++;
                                        $id = ''; $path='';
                                        $id = $row['trnum'];$report_view='report';
                                        $path = "openPopup('../cus_editreceipts.php?id=$id&view=$report_view')";
                                        
                                        $html .= "<tr><td style='text-align:center;'>".$i."</td>
                                        <td>".$edate."</td>
                                        <td><a href='javascript:void(0);' onclick=".$path.">".$row['trnum']."</a></td>
                                        <td>Receipt</td><td></td><td>".$amount."</td>
                                        <td>".$usr_name[$aemp]."</td>
                                        <td>".$adtime1."</td>
                                        <td>".$usr_name[$eemp]."</td>
                                        <td>".$adtime2."</td>
                                        </tr>";
        
                                }
                            }

                            $sql1 = "SELECT * FROM `pur_payments` WHERE `trnum` = '$tr_num'";
                            $query1 = mysqli_query($conn,$sql1); $count1 = mysqli_num_rows($query1);
                            if($count1 > 0){
                                while($row = mysqli_fetch_assoc($query1)){
                                    $aemp = $row['addedemp']; $eemp = $row['updatedemp'];$amount = $row['amount']; $edate = date("d.m.Y",strtotime($row['date']));
                                    $adtime1 = date("d.m.Y",strtotime($row['addedtime'])); $adtime2 = date("d.m.Y",strtotime($row['updated']));
                                    
                                        $i++;
                                        $id = ''; $path='';
                                        $id = $row['trnum'];$report_view='report';
                                        $path = "openPopup('../pur_editpayments.php?id=$id&view=$report_view')";
                                        
                                        $html .= "<tr><td style='text-align:center;'>".$i."</td>
                                        <td>".$edate."</td>
                                        <td><a href='javascript:void(0);' onclick=".$path.">".$row['trnum']."</a></td>
                                        <td>Payments</td><td></td><td>".$amount."</td>
                                        <td>".$usr_name[$aemp]."</td>
                                        <td>".$adtime1."</td>
                                        <td>".$usr_name[$eemp]."</td>
                                        <td>".$adtime2."</td>
                                        </tr>";
        
                                }
                            }

                            $sql1 = "SELECT * FROM `pur_purchase` WHERE `invoice` = '$tr_num'";
                            $query1 = mysqli_query($conn,$sql1); $count1 = mysqli_num_rows($query1);
                            if($count1 > 0){
                                while($row = mysqli_fetch_assoc($query1)){
                                    $aemp = $row['addedemp']; $eemp = $row['updatedemp'];$quantity = $row['netweight'];$amount = $row['totalamt']; $edate = date("d.m.Y",strtotime($row['date']));
                                    $adtime1 = date("d.m.Y",strtotime($row['addedtime'])); $adtime2 = date("d.m.Y",strtotime($row['updated']));
                                    
                                        $i++;
                                        $id = ''; $path='';
                                        $id = $row['invoice'];$report_view='report';
                                        $path = "openPopup('../pur_editpurchases.php?id=$id&view=$report_view')";
                                        
                                        $html .= "<tr><td style='text-align:center;'>".$i."</td>
                                        <td>".$edate."</td>
                                        <td><a href='javascript:void(0);' onclick=".$path.">".$row['invoice']."</a></td>
                                        <td>Purchase</td><td>".$quantity."</td><td>".$amount."</td>
                                        <td>".$usr_name[$aemp]."</td>
                                        <td>".$adtime1."</td>
                                        <td>".$usr_name[$eemp]."</td>
                                        <td>".$adtime2."</td>
                                        </tr>";
        
                                }
                            }

                            echo $html;
                        }
                        ?>
					</table>
				</form>
			</div>
		</section>
        <script>
             function openPopup(url) {
                
                var popup = window.open(url, 'popupWindow', 'width=600,height=400,scrollbars=yes,resizable=yes');
                if (popup) {
                    popup.focus();
                }
            }
            function checkval() {
                var tr_num = document.getElementById("tr_num").value;
                // var sectors = document.getElementById("sectors").value;
                var l = true;
                if(tr_num == ""){
                    alert("Kindly Enter Transaction Number");
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
        <script src="searchbox.js"></script>
        <script type="text/javascript">
            function tableToExcel(table, name, filename, chosen){
                if(chosen === 'excel'){
                    var table = document.getElementById("main_table");
                    var workbook = XLSX.utils.book_new();
                    var worksheet = XLSX.utils.table_to_sheet(table);
                    XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
                    XLSX.writeFile(workbook, filename+".xlsx");
                    
                    $('#exports').select2();
                    document.getElementById("exports").value = "display";
                    $('#exports').select2();
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
