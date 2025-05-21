<?php
    //chicken_closing_stock1.php
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	$requested_data = json_decode(file_get_contents('php://input'),true);
	session_start();
	
	$db = $_SESSION['db'] = $_GET['db'];
	if($db == ''){
		include "../config.php";
		include "number_format_ind.php";
		$dbname = $_SESSION['dbase'];
		$users_code = $_SESSION['userid'];

        $form_reload_page = "chicken_closing_stock1.php";
	}
	else{
		include "APIconfig.php";
		include "number_format_ind.php";
		$dbname = $db;
		$users_code = $_GET['emp_code'];
        $form_reload_page = "chicken_closing_stock1.php?db=".$db;
	}
    $file_name = "Closing Stock Report";

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

    $sql = "SELECT * FROM `item_category` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $icat_code = $icat_name = array();
    while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; }
    
	$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
	$query = mysqli_query($conn,$sql); $item_code = $item_name = array();
	while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

	$fdate = $tdate = date("Y-m-d"); $sectors = $item_cat = $items = "all";
    $exports = "display";
	if(isset($_POST['submit']) == true){
		$fdate = date("Y-m-d",strtotime($_POST['fdate']));
		$tdate = date("Y-m-d",strtotime($_POST['tdate']));
		$sectors = $_POST['sectors'];
		$item_cat = $_POST['item_cat'];
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
                                        <div class="form-group" style="width:170px;">
                                            <label>Category</label>
                                            <select name="item_cat" id="item_cat" class="form-control select2" style="width:160px;" onchange="fetch_item_list();">
                                                <option value="all" <?php if($item_cat == "all"){ echo "selected"; } ?>>-All-</option>
                                                <?php foreach($icat_code as $icats){ if($icat_name[$icats] != ""){ ?>
                                                <option value="<?php echo $icats; ?>" <?php if($item_cat == $icats){ echo "selected"; } ?>><?php echo $icat_name[$icats]; ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:150px;">
                                            <label for="items">Item</label>
                                            <select name="items" id="items" class="form-control select2" style="width:140px;">
                                                <option value="all" <?php if($items == "all"){ echo "selected"; } ?>>-All-</option>
                                                <?php if($item_cat == "all"){ ?>
                                                <?php foreach($item_code as $icode){ if($item_name[$icode] != ""){ ?>
                                                <option value="<?php echo $icode; ?>" <?php if($items == $icode){ echo "selected"; } ?>><?php echo $item_name[$icode]; ?></option>
                                                <?php } } }
                                                else{
                                                    foreach($item_code as $icode){
                                                        if($item_cat == $item_category[$icode]){
                                                        ?>
                                                        <option value="<?php echo $icode; ?>" <?php if($items == $icode){ echo "selected"; } ?>><?php echo $item_name[$icode]; ?></option>
                                                        <?php
                                                        }
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:190px;">
                                            <label for="sectors">Warehouse</label>
                                            <select name="sectors" id="sectors" class="form-control select2" style="width:180px;">
                                                <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>-All-</option>
											    <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($sectors == $scode){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="m-1 p-1 row">
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
                            if($items != "all"){ $item_filter = " AND `code` IN ('$items')"; }
                            else if($item_cat == "all"){ $item_filter = ""; }
                            else{
                                $icat_list = $item_filter = "";
                                foreach($item_code as $icode){
                                    $item_category[$icode];
                                    if(!empty($item_category[$icode]) && $item_category[$icode] == $item_cat){
                                        if($icat_list == ""){ $icat_list = $icode; } else{ $icat_list = $icat_list."','".$icode; }
                                    }
                                }
                                $item_filter = " AND `code` IN ('$icat_list')";
                            }
                            
                            if($sectors == "all"){ $sec_list = implode("','",$sector_code); $sector_filter = " AND `warehouse` IN ('$sec_list')"; }
                            else{ $sector_filter = " AND `warehouse` IN ('$sectors')"; }
                            
                            $html = '';
                            $html .= '<thead class="thead2" id="head_names">';

                            $nhead_html .= '<tr>';
                            $nhead_html .= '<th>Sl No.</th>';
                            $nhead_html .= '<th>Date</th>';
                            $nhead_html .= '<th>Trnum</th>';
                            $nhead_html .= '<th>Item</th>';
                            $nhead_html .= '<th>Quantity</th>';
                            $nhead_html .= '<th>Price</th>';
                            $nhead_html .= '<th>Amount</th>';
                            $nhead_html .= '<th>Remarks</th>';
                            $nhead_html .= '<th>Warehouse</th>';
                            $nhead_html .= '</tr>';

                            $fhead_html .= '<tr>';
                            $fhead_html .= '<th id="order">Sl No.</th>';
                            $fhead_html .= '<th id="order_date">Date</th>';
                            $fhead_html .= '<th id="order">Trnum</th>';
                            $fhead_html .= '<th id="order">Item</th>';
                            $fhead_html .= '<th id="order_num">Quantity</th>';
                            $fhead_html .= '<th id="order_num">Price</th>';
                            $fhead_html .= '<th id="order_num">Amount</th>';
                            $fhead_html .= '<th id="order">Remarks</th>';
                            $fhead_html .= '<th id="order">Warehouse</th>';
                            $fhead_html .= '</tr>';

                            $html .= $fhead_html;

                            $html .= '</thead>';
                            $html .= '<tbody class="tbody1">';
                            
                            $sql = "SELECT * FROM `item_closingstock` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$item_filter."".$sector_filter." AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`trnum` ASC";
                            $query = mysqli_query($conn,$sql); $tot_qty = $tot_amt = 0; $sl = 1;
                            while($row = mysqli_fetch_assoc($query)){
                                $date = date("d.m.Y",strtotime($row['date']));
                                $trnum = $row['trnum'];
                                $iname = $item_name[$row['code']];
                                $quantity = $row['closedquantity'];
                                $price = $row['price'];
                                $amount = $row['amount'];
                                $remarks = $row['remarks'];
                                $sname = $sector_name[$row['warehouse']];

                                $html .= '<tr>';
                                $html .= '<td style="text-align:left;">'.$sl++.'</td>';
                                $html .= '<td style="text-align:left;">'.$date.'</td>';
                                $html .= '<td style="text-align:left;">'.$trnum.'</td>';
                                $html .= '<td style="text-align:left;">'.$iname.'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($quantity).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($price).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($amount).'</td>';
                                $html .= '<td style="text-align:left;">'.$remarks.'</td>';
                                $html .= '<td style="text-align:left;">'.$sname.'</td>';
                                $html .= '</tr>';
                                
                                $tot_qty += (float)$quantity;
                                $tot_amt += (float)$amount;
                            }

                            $html .= '</tbody>';
                            $html .= '<tfoot class="tfoot1">';
                            $html .= '<tr>';
                            $html .= '<th style="text-align:left;" colspan="4">Grand Total</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($tot_qty).'</th>';
                            $html .= '<th style="text-align:right;"></th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($tot_amt).'</th>';
                            $html .= '<th></th>';
                            $html .= '<th></th>';
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
