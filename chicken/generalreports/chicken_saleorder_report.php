<?php
    //chicken_saleorder_report.php
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	$requested_data = json_decode(file_get_contents('php://input'),true);
	session_start();
	
	$db = $_SESSION['db'] = $_GET['db'];
	if($db == ''){
		include "../config.php";
		include "number_format_ind.php";
		$dbname = $_SESSION['dbase'];
		$users_code = $_SESSION['userid'];

        $form_reload_page = "chicken_saleorder_report.php";
	}
	else{
		include "APIconfig.php";
		include "number_format_ind.php";
		$dbname = $db;
		$users_code = $_GET['emp_code'];
        $form_reload_page = "chicken_saleorder_report.php?db=".$db;
	}
    $file_name = "Sales Order Report";

	$sql = "SELECT * FROM `main_access` WHERE `empcode` = '$users_code'";
	$query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$loc_access = $row['loc_access'];
		$cgroup_access = $row['cgroup_access'];
		if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $utype = "S"; }
		else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $utype = "A"; }
		else if($row['normal_access'] == 1 || $row['normal_access'] == "1"){ $utype = "N"; }
		else{ $utype = "N"; }
	}
	if($utype == "S" || $utype == "A"){
		$sql = "SELECT * FROM `log_useraccess` WHERE `dblist` = '$dbname'"; $query = mysqli_query($conns,$sql);
		while($row = mysqli_fetch_assoc($query)){ $user_name[$row['empcode']] = $row['username']; $user_code[$row['empcode']] = $row['empcode']; }
		$addedemp = "";
	}
	else{
		$sql = "SELECT * FROM `log_useraccess` WHERE `dblist` = '$dbname'"; $query = mysqli_query($conns,$sql);
		while($row = mysqli_fetch_assoc($query)){ $user_name[$row['empcode']] = $row['username']; $user_code[$row['empcode']] = $row['empcode']; }
		$addedemp = "";
	}
    //Usr access Based Sector Filter
	if($loc_access == "all" || $loc_access == "All" || $loc_access == "" || $loc_access == NULL){ $user_sector_filter = ""; }
	else{ $wcode = str_replace(",","','",$loc_access); $user_sector_filter = " AND code IN ('$wcode')"; }
	
    $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Receipt Report' OR `type` = 'All' ORDER BY `id` DESC";
    $query = mysqli_query($conn,$sql); $logopath = $cdetails = "";
    while($row = mysqli_fetch_assoc($query)){ $logopath = $row['logopath']; $cdetails = $row['cdetails']; }

	$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'".$user_sector_filter." ORDER BY `description` ASC";
	$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
	while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `acc_modes` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $acode[$row['code']] = $row['code'];
        $adesc[$row['code']] = $row['description'];
    }

    $sql = "SELECT * FROM `item_category` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $icat_code = $icat_name = array();
    while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; }
    
	$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
	$query = mysqli_query($conn,$sql); $item_code = $item_name = array();
	while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $cus_code = $cus_name = $sup_code = $sup_name = array();
    while($row = mysqli_fetch_assoc($query)){
        if($row['contacttype'] == "C" || $row['contacttype'] == "S&C"){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; } else{ }
        if($row['contacttype'] == "S" || $row['contacttype'] == "S&C"){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; } else{ }
    }
    $sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $desc_code = $desc_name = array();
    while($row = mysqli_fetch_assoc($query)){ $desc_code[$row['code']] = $row['code']; $desc_name[$row['code']] = $row['description']; }

	$fdate = $tdate = date("Y-m-d"); $cnames = $tcoa = $items = "all";
    $exports = "display";
	if(isset($_POST['submit']) == true){
		$fdate = date("Y-m-d",strtotime($_POST['fdate']));
		$tdate = date("Y-m-d",strtotime($_POST['tdate']));
		$cnames = $_POST['cnames'];
		$tcoa = $_POST['tcoa'];
		$items = $_POST['items'];
		$exports = $_POST['exports'];
	}
	//$url = "../PHPExcel/Examples/SalesReportMaster-Excel.php?fdate=".$fdate."&tdate=".$tdate."&items=".$items."&sectors=".$sectors;
	
?>
<html>
	<head>
		<!--<script>
			var exptype = '<?php //echo $exports; ?>';
			var url = '<?php //echo $url; ?>';
			if(exptype.match("excel")){
				window.open(url,'_BLANK');
			}
		</script>-->
        <?php include "header_head2.php"; ?>
	</head>
	<body>
	    <?php if($exports == "display" || $exports == "print") { ?>
			<table align="center">
				<tr>
					<td><img src="<?php echo "../".$logopath; ?>" height="150px"/></td>
					<td><?php echo $cdetails; ?></td>
					<td align="center">
						<h3><?php echo $file_name; ?></h3>
						<label><b style="color: green;">From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fdate)); ?></label>&ensp;&ensp;&ensp;&ensp;
						<label><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($tdate)); ?></label>
					</td>
				</tr>
			</table>
	    <?php } ?>
		<section class="content" align="center">
			<div class="col-md-12" align="center">
				<form action="<?php echo $form_reload_page; ?>" method="post" onsubmit="return checkval()">
				    <table class="main-table table-sm table-hover">
						<?php if($exports == "display" || $exports == "exportpdf") { ?>
						<thead class="thead1">
							<tr>
								<td colspan="11" class="p-1" style="width:75%;">
                                    <div class="m-1 p-1 row" style="width:75%;">
                                        <div class="form-group" style="width:110px;">
                                            <label for="fdate">From Date</label>
                                            <input type="text" name="fdate" id="fdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label for="tdate">To Date</label>
                                            <input type="text" name="tdate" id="tdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                       
                                        <div class="form-group" style="width:190px;">
                                            <label for="cnames">Vehicle No.</label>
                                            <select name="cnames" id="cnames" class="form-control select2" style="width:180px;">
                                                <option value="all" <?php if($cnames == "all"){ echo "selected"; } ?>>-All-</option>
											    <?php foreach($cus_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($cnames == $scode){ echo "selected"; } ?>><?php echo $cus_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                    <!-- </div>
                                    <div class="m-1 p-1 row"> -->
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
                    </table>
                    <table class="main-table table-sm table-hover" id="main_table">
						<?php
                        }
                        if(isset($_POST['submit']) == true){
                           
                            $tcoa_fltr = ""; if($tcoa != "all"){ $tcoa_fltr = " AND `tcoa` = '$tcoa'"; }
                            $cus_fltr = ""; if($cnames != "all"){ $cus_fltr = " AND `ccode` = '$cnames'"; }

                            
                            $html = '';
                            $html .= '<thead class="thead2" id="head_names">';

                            $nhead_html .= '<tr>';
                            $nhead_html .= '<th>Sl No.</th>';
                            $nhead_html .= '<th>Date</th>';
                            $nhead_html .= '<th>Supplier</th>';
                            $nhead_html .= '<th>Warehouse</th>';
                            $nhead_html .= '<th>Customer</th>';
                            $nhead_html .= '<th>Item</th>';
                            $nhead_html .= '<th>Quantity</th>';
                            $nhead_html .= '<th>Vehicle</th>';
                            $nhead_html .= '<th>Place</th>';
                            $nhead_html .= '<th>Supervisor</th>';
                            $nhead_html .= '<th>Remarks</th>';
                            $nhead_html .= '</tr>';

                            $fhead_html .= '<tr>';
                            $fhead_html .= '<th id="order">Sl No.</th>';
                            $fhead_html .= '<th id="order_date">Date</th>';
                            $fhead_html .= '<th id="order">Supplier</th>';
                            $fhead_html .= '<th id="order">Warehouse</th>';
                            $fhead_html .= '<th id="order">Customer</th>';
                            $fhead_html .= '<th id="order">Item</th>';
                            $fhead_html .= '<th id="order_num">Quantity</th>';
                            $fhead_html .= '<th id="order">Vehicle</th>';
                            $fhead_html .= '<th id="order">Place</th>';
                            $fhead_html .= '<th id="order">Supervisor</th>';
                            $fhead_html .= '<th id="order">Remarks</th>';
                            $fhead_html .= '</tr>';

                            $html .= $fhead_html;

                            $html .= '</thead>';
                            $html .= '<tbody class="tbody1">';
                            
                            $sql = "SELECT * FROM `salesorder` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$cus_fltr." AND `isDelete` = '0' ORDER BY `date`,`trnum` ASC";
                            $query = mysqli_query($conn,$sql); $tot_qty = $tot_amt = 0; $Sl = 1;
                            while($row = mysqli_fetch_assoc($query)){
                                $date = date("d.m.Y",strtotime($row['date']));
                                $supl_name = $sup_name[$row['supplier']];
                                $sec_name = $sector_name[$row['warehouse']];
                                $cust_name = $cus_name[$row['ccode']];
                                $it_name = $item_name[$row['itemcode']];
                                $Quantity = $row['twt'];
                                $vehicleno = $row['vehicleno'];
                                $place = $row['place'];
                                $supervisor = $row['supervisor'];
                                $remarks = $row['remarks'];
                          


                                $html .= '<tr>';
                                $html .= '<td style="text-align:left;">'.$Sl++.'</td>';
                                $html .= '<td style="text-align:left;">'.$date.'</td>';
                                $html .= '<td style="text-align:left;">'.$supl_name.'</td>';
                                $html .= '<td style="text-align:left;">'.$sec_name.'</td>';
                                $html .= '<td style="text-align:left;">'.$cust_name.'</td>';
                                $html .= '<td style="text-align:left;">'.$it_name.'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($Quantity).'</td>';
                                $html .= '<td style="text-align:left;">'.$vehicleno.'</td>';
                                $html .= '<td style="text-align:left;">'.$place.'</td>';
                                $html .= '<td style="text-align:left;">'.$supervisor.'</td>';
                                $html .= '<td style="text-align:left;">'.$remarks.'</td>';
                                $html .= '</tr>';
                                
                               
                                $tot_qty += (float)$Quantity;
                            }

                            $html .= '</tbody>';
                            $html .= '<tfoot class="tfoot1">';
                            $html .= '<tr>';
                            $html .= '<th style="text-align:left;" colspan="6">Total</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($tot_qty).'</th>';
                            $html .= '<th colspan="4"></th>';
                            $html .= '</tr>';
                            $html .= '</tfoot>';

                            echo $html;
                        }
                            ?>
						</table>
					</form>
				</div>
		</section>
        <script>
            function checkval(){
                var groups = document.getElementById('groups[0]').value;
                var l = true;
                if(groups == ""){
                    alert("Please select Group");
                    document.getElementById('groups[0]').focus();
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
        <script type="text/javascript">
            function tableToExcel(table, name, filename, chosen){
                if(chosen === 'excel'){
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html += '<?php echo $nhead_html; ?>';
                    $('#head_names').append(html);

                    var uri = 'data:application/vnd.ms-excel;base64,'
                    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
                    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
                    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
                    //  return function(table, name, filename, chosen) {
                
                    if (!table.nodeType) table = document.getElementById(table)
                    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
                    //window.location.href = uri + base64(format(template, ctx))
                    var link = document.createElement("a");
                    link.download = filename+".xls";
                    link.href = uri + base64(format(template, ctx));
                    link.click();
                    //}
                    
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html += '<?php echo $fhead_html; ?>';
                    document.getElementById("head_names").innerHTML = html;
                    table_sort();
                    table_sort2();
                    table_sort3();
                }
                else{ }
            }
        </script>
        <script src="sort_table_columns.js"></script>
        <script src="searchbox.js"></script>
		<?php if($exports == "display" || $exports == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<?php include "header_foot2.php"; ?>
	</body>
	
</html>
