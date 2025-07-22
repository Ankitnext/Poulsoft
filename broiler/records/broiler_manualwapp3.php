<?php
//broiler_manualwapp3.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
global $page_title; $page_title = "Manual WhatsApp Report";
include "header_head.php";
$user_code = $_SESSION['userid'];

$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; }
else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; }
else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; }
else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }
if($sector_access_code == "all"){ $sector_access_filter1 = ""; }
else{ $sector_access_list = implode("','", explode(",",$sector_access_code)); $sector_access_filter1 = " AND `code` IN ('$sector_access_list')"; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_name[$row['code']] = $row['name']; $vendor_mobile[$row['code']] = $row['mobile1']; }

$sql = "SELECT * FROM `inv_sectors` WHERE `dflag` = '0'  ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
        
$sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

$sql = "SELECT * FROM `extra_access` WHERE `field_name` IN ('SalesManualWapp','ReceiptManualWapp','StktransManualWapp')";
$query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
if($ccount > 0){
    while($row = mysqli_fetch_assoc($query)){
        if($row['field_name'] == "SalesManualWapp"){ $sales_wapp = $row['flag']; }
        else if($row['field_name'] == "ReceiptManualWapp"){ $rct_wapp = $row['flag']; }
        else if($row['field_name'] == "StktransManualWapp"){ $stktr_wapp = $row['flag']; }
        else{ }
    }
}
else{ $sales_wapp = $rct_wapp = $stktr_wapp = 0; }
if($sales_wapp == "" || $sales_wapp == 0 || $sales_wapp == "0.00" || $sales_wapp == NULL){ $sales_wapp = 0; }
if($rct_wapp == "" || $rct_wapp == 0 || $rct_wapp == "0.00" || $rct_wapp == NULL){ $rct_wapp = 0; }
if($stktr_wapp == "" || $stktr_wapp == 0 || $stktr_wapp == "0.00" || $stktr_wapp == NULL){ $stktr_wapp = 0; }

$fdate = $tdate = date("Y-m-d"); $item_cat = $items = $vendors = $fsector = $tsector = "all"; $module = "SALES"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $vendors = $_POST['vendors'];
    $module = $_POST['module'];
    $fsector = $_POST['fsector'];
    $tsector = $_POST['tsector'];

    if($vendors == "all"){ $vendor_filter = ""; } else{ $vendor_filter = " AND `vcode` = '$vendors'"; }

    if($items != "all"){ $item_filter = " AND `icode` IN ('$items')"; }
    else if($item_cat == "all"){ $item_filter = ""; }
    else{
        $icat_list = $item_filter = "";
        foreach($item_code as $icode){
            $item_category[$icode];
            if($item_category[$icode] == $item_cat){
                if($icat_list == ""){
                    $icat_list = $icode;
                }
                else{
                    $icat_list = $icat_list."','".$icode;
                }
            }
        }
        $item_filter = " AND `icode` IN ('$icat_list')";
    }
	$excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/SalesReport-Excel.php?fromdate=".$fdate."&todate=".$tdate."&item_cat=".$item_cat."&vendors=".$vendors."&sectors=".$sectors;
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <script>
            var exptype = '<?php echo $excel_type; ?>';
            var url = '<?php echo $url; ?>';
            if(exptype.match("excel")){ window.open(url,"_BLANK"); }
        </script>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <style>
        .thead3 th {
                top: 0;
                position: sticky;
                background-color: #9cc2d5;
 
			}
         
        </style>
       
        <?php
          if($excel_type == "print"){
            echo '<style>body { padding:10px;text-align:center; }
            .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
            .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
            .thead2 { display:none;background-image: linear-gradient(#9CC2D5,#9CC2D5); }
            .thead2_empty_row { display:none; }
            .thead3 { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
            .thead4 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
            .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
            .report_head { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
            .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); font-weight:bold; }</style>';
        }
        else{
            echo '<style>body { left:0;width:auto;overflow:auto; } table { white-space: nowrap; }
            table.tbl { left:0;margin-right: auto;visibility:visible; }
            table.tbl2 { left:0;margin-right: auto; }
            .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
            .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
            .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
            .thead2 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
            .thead3 { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
            .thead4 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
            .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
            .report_head { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
            .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); }</style>';
            
        }
        
       ?>
    </head>
    <body align="center">
        <table class="tbl" align="center">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" style="width:1212px;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="8" align="center"><?php echo $row['cdetails']; ?><h5>Manual WhatsApp Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_manualwapp3.php" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed">
                    <tr>
                        <th colspan="10">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Customer</label>
                                    <select name="vendors" id="vendors" class="form-control select2">
                                        <option value="all" <?php if($vendors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($vendor_code as $cust){ if($vendor_name[$cust] != ""){ ?>
                                        <option value="<?php echo $cust; ?>" <?php if($vendors == $cust){ echo "selected"; } ?>><?php echo $vendor_name[$cust]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>From Warehouse</label>
                                    <select name="fsector" id="fsector" class="form-control select2">
                                        <option value="all" <?php if($fsector == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($sector_code as $scode){ if($sector_name[$scode] != ""){ ?>
                                        <option value="<?php echo $scode; ?>" <?php if($fsector == $scode){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>To Warehouse</label>
                                    <select name="tsector" id="tsector" class="form-control select2">
                                        <option value="all" <?php if($tsector == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($sector_code as $scode){ if($sector_name[$scode] != ""){ ?>
                                        <option value="<?php echo $scode; ?>" <?php if($tsector == $scode){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
									<label>Module</label>
									<select name="module" id="module" class="form-control select2">
										<?php if($sales_wapp != 0 && $sales_wapp > 0){ ?><option value="SALES" <?php if($module == "SALES"){ echo 'selected'; } ?>>Sales</option><?php } ?>
									    <?php if($rct_wapp != 0 && $rct_wapp > 0){ ?><option value="RECEIPT" <?php if($module == "RECEIPT"){ echo 'selected'; } ?>>Receipts</option><?php } ?>
									    <?php if($stktr_wapp != 0 && $stktr_wapp > 0){ ?><option value="STKTRANS" <?php if($module == "STKTRANS"){ echo 'selected'; } ?>>Stock Transfer</option><?php } ?>
									</select>&ensp;&ensp;
                                <div class="m-2 form-group">
                                    <br/>
                                    <button type="submit" name="submit_report" id="submit_report" class="btn btn-sm btn-success">Submit</button>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
            </form>
            <?php
            if(isset($_POST['submit_report']) == true){
                if($module != "STKTRANS"){
            ?>
            <thead class="thead3" align="center">
                <tr align="center">
                    <th>Select<br/><input type="checkbox" name="checkall" id="checkall" onchange="checkedall()" /></th>
					<th>Date</th>
					<th>Customer</th>
					<th>Mobile No.</th>
					<th>Invoice No.</th>
					<th>Invoice Details</th>
                </tr>
            </thead>
            <?php
                }
                else{
            ?>
            <thead class="thead3" align="center">
                <tr align="center">
                    <th>Select<br/><input type="checkbox" name="checkall" id="checkall" onchange="checkedall()" /></th>
					<th>Date</th>
					<th>From Warehouse</th>
					<th>To Warehouse</th>
					<th>Transaction No.</th>
					<th>Transaction Details</th>
                </tr>
            </thead>
            <?php
                }
            ?>
            <tbody class="tbody1">
                <form action="broiler_sendmanualwapp3.php" method="post" onsubmit="return checkval()">
                <?php
                if($module == "SALES"){
                    $sql_record = "SELECT * FROM `broiler_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$vendor_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql_record); $exist_inv = 0; $item_details = array();
                    while($row = mysqli_fetch_assoc($query)){
                        if($exist_inv != $row['trnum']){
                            $exist_inv = $row['trnum'];
                            if(number_format_ind($row['birds']) != "0.00"){
                                $item_details[$row['trnum']] = $item_name[$row['icode']].": ".$row['birds']."No. ".$row['rcd_qty']."Kgs @ ". $row['rate'];
                            }
                            else{
                                $item_details[$row['trnum']] = $item_name[$row['icode']].": ".$row['rcd_qty']."Kgs @ ". $row['rate'];
                            }
                            
                        }
                        else{
                            if(number_format_ind($row['birds']) != "0.00"){
                                $item_details[$row['trnum']] = $item_details[$row['trnum']].",<br/>".$item_name[$row['icode']].": ".$row['birds']."No. ".$row['rcd_qty']."Kgs @ ". $row['rate'];
                            }
                            else{
                                $item_details[$row['trnum']] = $item_details[$row['trnum']].",<br/>".$item_name[$row['icode']].": ".$row['rcd_qty']."Kgs @ ". $row['rate'];
                            }
                            
                        }
                        $sale_amt[$row['trnum']] = number_format_ind(round($row['finl_amt'],2));
                    }
                    $seq = "SELECT DISTINCT(trnum) as invoice,vcode,date FROM `broiler_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$vendor_filter."";
					$sql = $seq."".$cc."".$orderby; $query = mysqli_query($conn,$sql); $scount = mysqli_num_rows($query);
					if($scount > 0){
						$c = 0;
						while($row = mysqli_fetch_assoc($query)){
							$c = $c + 1;
							$cus_inv = $row['invoice'];
							$date = date("d.m.Y", strtotime($row['date']));
							$customer_name = $vendor_name[$row['vcode']];
							$customer_mobile = $vendor_mobile[$row['vcode']];
							$cus_val = "";
							$cus_val = $c."&SALE&".$cus_inv;
							echo "<tr>";
							echo "<td style='text-align:center;'><input type='checkbox' name='smsdet[]' id='smsdet[]' value='$cus_val' /></td>";
							echo "<td style='text-align:left;'>".$date."</td>";
							echo "<td style='text-align:left;'>".$customer_name."</td>";
							echo "<td style='text-align:left;'>".$customer_mobile."</td>";
							echo "<td style='text-align:left;'>".$cus_inv."</td>";
							echo "<td style='text-align:left;'>".$item_details[$cus_inv]."<br/>Final Total: ".$sale_amt[$cus_inv]."/-</td>";
							echo "</tr>";
						}
					}
					else{
						echo "<td colspan='6'>No Records Found ..!</td>";
					}
                }
                else if($module == "RECEIPT"){
                    if($vendors == "all"){ $cc = ""; } else{ $cc = " AND `ccode` LIKE '$vendors'"; }
                    $orderby = " ORDER BY `date`,`trnum` ASC";
                    $seq = "SELECT * FROM `broiler_receipts` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1'";
                    $sql = $seq."".$cc."".$orderby; $query = mysqli_query($conn,$sql); $scount = mysqli_num_rows($query);
                    if($scount > 0){
                        $c = 0;
                        while($row = mysqli_fetch_assoc($query)){
                            $c = $c + 1;
                            $cus_inv = $row['trnum'];
                            $date = date("d.m.Y", strtotime($row['date']));
                            $customer_name = $vendor_name[$row['ccode']];
                            $customer_mobile = $vendor_mobile[$row['ccode']];
                            $cus_val = "";
                            $cus_val = $c."&RECEIPT&".$date."&".$customer_name."&".$customer_mobile."&".$cus_inv."&0&".$row['amount']."&".$row['ccode'];
                            echo "<tr>";
                            echo "<td style='text-align:center;'><input type='checkbox' name='smsdet[]' id='smsdet[]' value='$cus_val' /></td>";
                            echo "<td style='text-align:left;'>".$date."</td>";
                            echo "<td style='text-align:left;'>".$customer_name."</td>";
                            echo "<td style='text-align:left;'>".$customer_mobile."</td>";
                            echo "<td style='text-align:left;'>".$cus_inv."</td>";
                            echo "<td style='text-align:left;'>".$row['amount']."/-</td>";
                            echo "</tr>";
                        }
                    }
                    else{
                        echo "<td colspan='6'>No Records Found ..!</td>";
                    }
                }
                else if($module == "STKTRANS"){
                    if($fsector == "all"){ $fsector_filter = ""; } else{ $fsector_filter = " AND `fromwarehouse` LIKE '$fsector'"; }
                    if($tsector == "all"){ $tsector_filter = ""; } else{ $tsector_filter = " AND `towarehouse` LIKE '$tsector'"; }
                    $orderby = " GROUP BY `trnum` ORDER BY `date`,`trnum` ASC";
                    $seq = "SELECT * FROM `item_stocktransfers` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1'";

                    $sql = $seq."".$fsector_filter."".$tsector_filter." ORDER BY `trnum` ASC";
                    $query = mysqli_query($conn,$sql); $scount = mysqli_num_rows($query);
                    if($scount > 0){
                        $exist_inv = "";
                        while($row = mysqli_fetch_assoc($query)){
                            if($exist_inv != $row['trnum']){
                                $item_details[$row['trnum']] = $item_name[$row['code']].": ".$row['quantity']."Kgs @ ". $row['price'];
                                $exist_inv = $row['trnum'];
                            }
                            else{
                                $item_details[$row['trnum']] = $item_details[$row['trnum']].",<br/>".$item_name[$row['code']].": ".$row['quantity']."Kgs @ ". $row['price'];
                            }
                        }
                    }
                    $seq = "SELECT * FROM `item_stocktransfers` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1'";
                    $sql = $seq."".$fsector_filter."".$tsector_filter."".$orderby; $query = mysqli_query($conn,$sql); $scount = mysqli_num_rows($query);
                    if($scount > 0){
                        $c = 0;
                        while($row = mysqli_fetch_assoc($query)){
                            $c = $c + 1;
                            $trnum = $row['trnum'];
                            $date = date("d.m.Y", strtotime($row['date']));
                            $fwarehouse = $sector_name[$row['fromwarehouse']];
                            $twarehouse = $sector_name[$row['towarehouse']];
                            $cus_val = "";
                            $cus_val = $c."&STKTRANS&".$trnum;
                            echo "<tr>";
                            echo "<td style='text-align:center;'><input type='checkbox' name='smsdet[]' id='smsdet[]' value='$cus_val' /></td>";
                            echo "<td style='text-align:left;'>".$date."</td>";
                            echo "<td style='text-align:left;'>".$fwarehouse."</td>";
                            echo "<td style='text-align:left;'>".$twarehouse."</td>";
                            echo "<td style='text-align:left;'>".$trnum."</td>";
                            echo "<td style='text-align:left;'>".$item_details[$row['trnum']]."/-</td>";
                            echo "</tr>";
                        }
                    }
                    else{
                        echo "<td colspan='6'>No Records Found ..!</td>";
                    }
                }
                else{ }
                
                ?>
                <tr>
                    <td colspan="6" style="text-align:center;">
                        <button type="submit" name="submit_report2" id="submit_report2" class="btn btn-sm btn-success">Send WhatsApp</button>
                    </td>
                </tr>
                </form>
            </tbody>
            <tr class="thead4">
                <th colspan="5" style="text-align:center;">Total</th>
                <th style="text-align:right;"></th>
            </tr>
        <?php
            }
        ?>
        </table><br/><br/><br/>
        <script>
			function checkedall(){
				var a = document.getElementById("checkall");
				if(a.checked == true){
					var b = document.querySelectorAll('input[type=checkbox]');
					for(var c = 0;c <=b.length;c++){
						b[c].checked = true;
					}
				}
				else{
					var b = document.querySelectorAll('input[type=checkbox]');
					for(var c = 0;c <=b.length;c++){
						b[c].checked = false;
					}
				}
			}
			function checkval(){
				var checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
				var c = 0;
				if(checkboxes.length == 0){
					alert("Please select Transactions to send Message ..!");
					c = 0;
				}
				else {
					c = checkboxes.length;
				}
				if(c > 0){
					return true;
				}
				else{
					return false;
				}
			}
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>