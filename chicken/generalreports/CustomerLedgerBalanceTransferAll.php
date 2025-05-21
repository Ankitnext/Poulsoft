<?php 
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	$requested_data = json_decode(file_get_contents('php://input'),true);
	session_start();
	
	$db = $_SESSION['db'] = $_GET['db'];
	if($db == ''){ include "../config.php"; include "header_head.php"; include "number_format_ind.php"; }
	else{ include "APIconfig.php"; include "number_format_ind.php"; include "header_head.php"; }
			
	$today = date("Y-m-d");
    if(isset($_POST['submit']) == true){
        $todate = $_POST['todate'];
        $status = $_POST['status'];
        $cname = $_POST['cname'];
    }
    else{
        $todate = $today;
        $status = "display";
        $cname = "all";
    }
	$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' AND`contacttype` LIKE '%C%' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$pcode[$row['code']] = $row['code'];
		$pname[$row['code']] = $row['name'];
		$obdate[$row['code']] = $row['obdate'];
		$obtype[$row['code']] = $row['obtype'];
		$obamt[$row['code']] = $row['obamt'];
	}
	$exoption = $excel_type = "displaypage";
?>
<html>
	<head><link rel="stylesheet" type="text/css"href="reportstyle.css">

		<script>
			var exptype = '<?php echo $excel_type; ?>';
			var url = '<?php echo $url; ?>';
			var url2 = '<?php echo $url2; ?>';
			if(exptype.match("exportexcel")){
				window.open(url,'_BLANK');
			}
			else if(exptype.match("exportnodecimal")){
				window.open(url2,'_BLANK');
			}
		</script>
		<style>
			.thead2 th {
 				top: 0;
 				position: sticky;
 				background-color: #98fb98;
			}		
		</style>
		<style>
			body {
				font-size: 15px;
				font-weight: bold;
			}
			.thead2,.tbody1 {
				padding: 1px;
				font-weight: bold;
				font-size: 15px;
			}
			.formcontrol {
				height: 23px;
				font-weight: bold;
				border: 0.1vh solid gray;
			}
			.formcontrol:focus {
				height: 23px;
				font-weight: bold;
				border: 0.1vh solid gray;
				outline: none;
			}
			.tbody1 td {
				font-size: 15px;
				font-weight: bold;
				padding-right: 5px;
				text-align: right;
			}
			.table1, .table1 thead, .table1 tbody, .table1 tr, .table1 th, .table1 td {
				font-size: 15px;
				font-weight: bold;
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
	<?php if($exoption == "displaypage" || $exoption == "printerfriendly") { ?>
		<header align="center">
			<table align="center" class="reportheadermenu">
				<tr>
				<?php
					$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?>
					<td><img src="../<?php echo $row['logopath']; ?>" height="150px"/></td>
					<td><?php echo $row['cdetails']; ?></td> <?php } ?>
					<td align="center">
						<h3>Customer Ledger</h3>
						<?php
							if($cname == "all" || $cname == "select" || $cname == "") { } else {
						?>
							<label class="reportheaderlabel"><b style="color: green;">Customer:</b>&nbsp;<?php echo $pname[$cname]; ?></label><br/>
						<?php
							}
						?>
						<label class="reportheaderlabel"><b style="color: green;">From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fromdate)); ?></label>&ensp;&ensp;&ensp;&ensp;
						<label class="reportheaderlabel"><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($todate)); ?></label>
					</td>
					<td>
					
					</td>
				</tr>
			</table>
		</header>
	<?php } ?>
		<section class="content" align="center">
				<div class="col-md-12" align="center">
				<?php if($db == ''){?>
				<form action="CustomerLedgerBalanceTransferAll.php" method="post"  onsubmit="return checkval()">
					<?php } else { ?>
					<form action="CustomerLedgerBalanceTransferAll.php?db=<?php echo $db; ?>" method="post"  onsubmit="return checkval()">
					<?php } ?>
						<table class="table1" style="width:auto;line-height:23px;">
						<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?>
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<!--<td style='visibility:hidden;'></td>-->
									<td colspan="16">
										<label class="reportselectionlabel">Till Date</label>&nbsp;
										<input type="text" name="todate" id="datepickers1" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($todate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">Customer</label>&nbsp;
										<select name="cname" id="checkcname" class="form-control select2">
											<option value="all" selected>-All-</option>
										</select>&ensp;&ensp;
										<label class="reportselectionlabel">Status Type</label>&nbsp;
										<select name="status" id="status" class="form-control select2">
											<option value="display" <?php if($status == "display"){ echo "selected"; } ?>>-Display-</option>
											<option value="modify" <?php if($status == "modify"){ echo "selected"; } ?>>-Modify-</option>
										</select>&ensp;&ensp;
										<button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
									</td>
								</tr>
							</thead>
						<?php } ?>
							<thead class="thead2" style="background-color: #98fb98;">
								<tr>
									<th>Sl No.</th>
									<th>Name</th>
									<th>Balance</th>
								</tr>
							</thead>
							<tbody class="tbody1" id="myTable" style="background-color: #f4f0ec;">
							<?php
								if(isset($_POST['submit']) == true){
									if($_POST['export'] != "exportexcel"){
										if($cname == "" || $cname == "all" || $cname == "select"){
											$todate = date("Y-m-d",strtotime($_POST['todate']));

											//sales invoice
											$sql = "SELECT * FROM `customer_sales` WHERE `date` <= '$todate' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice`,`customercode` ASC";
											$query = mysqli_query($conn,$sql); $old_inv = ""; $sales = array();
											while($row = mysqli_fetch_assoc($query)){ if($old_inv != $row['invoice']){ $sales[$row['customercode']] += (float)$row['finaltotal']; $old_inv = $row['invoice']; } }
											//Customer Receipt
											$sql = "SELECT * FROM `customer_receipts` WHERE `date` <= '$todate' AND `active` = '1' ORDER BY `ccode` ASC";
											$query = mysqli_query($conn,$sql); $receipts = array();
											while($row = mysqli_fetch_assoc($query)){ $receipts[$row['ccode']] += (float)$row['amount']; }
											//Customer CrDr Note
											$sql = "SELECT * FROM `main_crdrnote` WHERE `date` <= '$todate' AND `mode` IN ('CCN','CDN') AND `active` = '1' ORDER BY `ccode` ASC";
											$query = mysqli_query($conn,$sql); $ccns = $cdns = array();
											while($row = mysqli_fetch_assoc($query)){ if($row['mode'] == "CCN"){ $ccns[$row['ccode']] += (float)$row['amount']; } else{ $cdns[$row['ccode']] += (float)$row['amount']; } }
                                            //Mortality
                                            $mortsql = "SELECT * FROM `main_mortality` WHERE `date` <= '$todate' AND `mtype` = 'customer' AND `active` = '1' AND `dflag` = '0'";
                                            $mortquery = mysqli_query($conn,$mortsql); $mortality = array();
                                            while($row = mysqli_fetch_assoc($mortquery)){ $mortality[$row['ccode']] += (float)$row['amount']; }
                                            //Returns
                                            $rtnsql = "SELECT * FROM `main_itemreturns` WHERE `date` <= '$todate' AND `mode` = 'customer' AND `active` = '1' AND `dflag` = '0' ORDER BY `vcode` ASC";
                                            $rtnquery = mysqli_query($conn,$rtnsql); $returns = array(); $sl = 1;
                                            while($row = mysqli_fetch_assoc($rtnquery)){ $returns[$row['vcode']] += (float)$row['amount']; }
                                           // sort($pcode);
											foreach($pcode as $pcodes){
												echo "<tr>";
												echo "<td style='text-align:left;'>".$sl++."</td>";
												echo "<td style='text-align:left;'>".$pname[$pcodes]."</td>";
												$ob_cramt = $ob_dramt = $balance = 0;
                                                if($obtype[$pcodes] == "Cr"){ $ob_cramt = $obamt[$pcodes]; } else { $ob_dramt = $obamt[$pcodes]; }
                                                $balance = (($sales[$pcodes] + $cdns[$pcodes] + $ob_dramt) - ($receipts[$pcodes] + $mortality[$pcodes] + $returns[$pcodes] + $ccns[$pcodes] + $ob_cramt));
                                                //echo "<br/>$balance = (($sales[$pcodes] + $cdns[$pcodes] + $ob_dramt) - ($receipts[$pcodes] + $mortality[$pcodes] + $returns[$pcodes] + $ccns[$pcodes] + $ob_cramt))";
                                                $total_bal_amt += $balance;
												echo "<td>".number_format_ind($balance)."</td>";
												echo "</tr>";
                                                
                                                if($status == "modify"){
                                                    if($balance < 0){ $bal_type = "Cr"; $balance = (float)str_replace("-","",$balance); } else{ $bal_type = "Dr"; }
                                                    $sql1 = "UPDATE `main_contactdetails` SET `obdate` = '$todate',`obtype` = '$bal_type',`obamt` = '$balance',`obremarks` = 'Customer Book Closure as on $todate' WHERE `code` = '$pcodes'";
                                                    if(!mysqli_query($conn,$sql1)){ echo "<br/>".mysqli_error($conn).""; } else{ }
                                                }
											}
                                            if($status == "modify"){
                                                $database_name = $_SESSION['dbase'];
                                                $table_head = "Tables_in_".$database_name;

                                                //Sale Table
                                                $sql1 = "SHOW TABLES WHERE ".$table_head." LIKE 'customer_sales_bkpc';";
                                                $query1 = mysqli_query($conn,$sql1); $count1 = mysqli_num_rows($query1);
                                                if($count1 > 0){
                                                    $exist_col_names = $exist_col_types = $exist_col_nulls = $exist_col_keys = $exist_col_defaults = $exist_col_extras = array(); $i = 0;
                                                    $sql2 = 'SHOW COLUMNS FROM `customer_sales`'; $query2 = mysqli_query($conn,$sql2);
                                                    while($row2 = mysqli_fetch_assoc($query2)){ $i++; $exist_col_names[$i] = $row2['Field']; $exist_col_types[$i] = $row2['Type']; $exist_col_nulls[$i] = $row2['Null']; $exist_col_keys[$i] = $row2['Key']; $exist_col_defaults[$i] = $row2['Default']; $exist_col_extras[$i] = $row2['Extra']; }
                                                    
                                                    $sql2 = 'SHOW COLUMNS FROM `customer_sales`'; $query2 = mysqli_query($conn,$sql2); $new_col_names = array(); $j = 0;
                                                    while($row2 = mysqli_fetch_assoc($query2)){ $j++; $new_col_names[$j] = $row2['Field']; }
    
                                                    for($incr = 1;$incr <= $i;$incr++){
                                                        $oincr = $incr - 1;
                                                        if(in_array($new_col_names[$incr], $exist_col_names, TRUE) == ""){
    
                                                            if($exist_col_nulls[$incr] == "YES"){ $default_vals = ""; $default_vals = " NULL DEFAULT NULL"; }
                                                            else if($exist_col_nulls[$incr] == "NO" && $exist_col_defaults[$incr] == NULL){ $default_vals = ""; $default_vals = " NULL DEFAULT NULL"; }
                                                            else if($exist_col_nulls[$incr] == "NO" && $exist_col_defaults[$incr] != NULL){ $default_vals = ""; $default_vals = " NOT NULL ".$exist_col_defaults[$incr]." ".$exist_col_extras[$incr]; }
                                                            else{ $default_vals = ""; $default_vals = " NULL DEFAULT NULL"; }
    
                                                            $sql2 = "ALTER TABLE `broiler_reportfields` ADD `$new_col_names[$incr]` $exist_col_types[$i] $default_vals AFTER `$new_col_names[$oincr]`";
                                                            if(!mysqli_query($conn,$sql2)){ echo "<br/>".mysqli_error($conn).""; } else{ }
                                                        }
                                                    }
                                                    $sql2 = "INSERT INTO customer_sales_bkpc SELECT * FROM customer_sales WHERE `date` <= '$todate';";
                                                    if(!mysqli_query($conn,$sql2)){ echo "<br/>".mysqli_error($conn).""; } else{ }
                                                }
                                                else{
                                                    $sql2 = "CREATE TABLE customer_sales_bkpc as SELECT * FROM customer_sales WHERE `date` <= '$todate';";
                                                    if(!mysqli_query($conn,$sql2)){ echo "<br/>".mysqli_error($conn).""; } else{ }
                                                }
                                                $sql3 = "DELETE FROM `customer_sales` WHERE `date` <= '$todate'"; mysqli_query($conn,$sql3);

                                                //Receipt Table
                                                $sql1 = "SHOW TABLES WHERE ".$table_head." LIKE 'customer_receipts_bkpc';";
                                                $query1 = mysqli_query($conn,$sql1); $count1 = mysqli_num_rows($query1);
                                                if($count1 > 0){
                                                    $exist_col_names = $exist_col_types = $exist_col_nulls = $exist_col_keys = $exist_col_defaults = $exist_col_extras = array(); $i = 0;
                                                    $sql2 = 'SHOW COLUMNS FROM `customer_receipts`'; $query2 = mysqli_query($conn,$sql2);
                                                    while($row2 = mysqli_fetch_assoc($query2)){ $i++; $exist_col_names[$i] = $row2['Field']; $exist_col_types[$i] = $row2['Type']; $exist_col_nulls[$i] = $row2['Null']; $exist_col_keys[$i] = $row2['Key']; $exist_col_defaults[$i] = $row2['Default']; $exist_col_extras[$i] = $row2['Extra']; }
                                                    
                                                    $sql2 = 'SHOW COLUMNS FROM `customer_receipts`'; $query2 = mysqli_query($conn,$sql2); $new_col_names = array(); $j = 0;
                                                    while($row2 = mysqli_fetch_assoc($query2)){ $j++; $new_col_names[$j] = $row2['Field']; }
    
                                                    for($incr = 1;$incr <= $i;$incr++){
                                                        $oincr = $incr - 1;
                                                        if(in_array($new_col_names[$incr], $exist_col_names, TRUE) == ""){
    
                                                            if($exist_col_nulls[$incr] == "YES"){ $default_vals = ""; $default_vals = " NULL DEFAULT NULL"; }
                                                            else if($exist_col_nulls[$incr] == "NO" && $exist_col_defaults[$incr] == NULL){ $default_vals = ""; $default_vals = " NULL DEFAULT NULL"; }
                                                            else if($exist_col_nulls[$incr] == "NO" && $exist_col_defaults[$incr] != NULL){ $default_vals = ""; $default_vals = " NOT NULL ".$exist_col_defaults[$incr]." ".$exist_col_extras[$incr]; }
                                                            else{ $default_vals = ""; $default_vals = " NULL DEFAULT NULL"; }
    
                                                            $sql2 = "ALTER TABLE `broiler_reportfields` ADD `$new_col_names[$incr]` $exist_col_types[$i] $default_vals AFTER `$new_col_names[$oincr]`";
                                                            if(!mysqli_query($conn,$sql2)){ echo "<br/>".mysqli_error($conn).""; } else{ }
                                                        }
                                                    }
                                                    $sql2 = "INSERT INTO `customer_receipts_bkpc` SELECT * FROM `customer_receipts` WHERE `date` <= '$todate';";
                                                    if(!mysqli_query($conn,$sql2)){ echo "<br/>".mysqli_error($conn).""; } else{ }
                                                }
                                                else{
                                                    $sql2 = "CREATE TABLE `customer_receipts_bkpc` as SELECT * FROM `customer_receipts` WHERE `date` <= '$todate';";
                                                    if(!mysqli_query($conn,$sql2)){ echo "<br/>".mysqli_error($conn).""; } else{ }
                                                }
                                                $sql3 = "DELETE FROM `customer_receipts` WHERE `date` <= '$todate'"; mysqli_query($conn,$sql3);
                                                
                                                //CRDR Table
                                                $sql1 = "SHOW TABLES WHERE ".$table_head." LIKE 'main_crdrnote_bkpc';";
                                                $query1 = mysqli_query($conn,$sql1); $count1 = mysqli_num_rows($query1);
                                                if($count1 > 0){
                                                    $exist_col_names = $exist_col_types = $exist_col_nulls = $exist_col_keys = $exist_col_defaults = $exist_col_extras = array(); $i = 0;
                                                    $sql2 = 'SHOW COLUMNS FROM `main_crdrnote`'; $query2 = mysqli_query($conn,$sql2);
                                                    while($row2 = mysqli_fetch_assoc($query2)){ $i++; $exist_col_names[$i] = $row2['Field']; $exist_col_types[$i] = $row2['Type']; $exist_col_nulls[$i] = $row2['Null']; $exist_col_keys[$i] = $row2['Key']; $exist_col_defaults[$i] = $row2['Default']; $exist_col_extras[$i] = $row2['Extra']; }
                                                    
                                                    $sql2 = 'SHOW COLUMNS FROM `main_crdrnote`'; $query2 = mysqli_query($conn,$sql2); $new_col_names = array(); $j = 0;
                                                    while($row2 = mysqli_fetch_assoc($query2)){ $j++; $new_col_names[$j] = $row2['Field']; }
    
                                                    for($incr = 1;$incr <= $i;$incr++){
                                                        $oincr = $incr - 1;
                                                        if(in_array($new_col_names[$incr], $exist_col_names, TRUE) == ""){
    
                                                            if($exist_col_nulls[$incr] == "YES"){ $default_vals = ""; $default_vals = " NULL DEFAULT NULL"; }
                                                            else if($exist_col_nulls[$incr] == "NO" && $exist_col_defaults[$incr] == NULL){ $default_vals = ""; $default_vals = " NULL DEFAULT NULL"; }
                                                            else if($exist_col_nulls[$incr] == "NO" && $exist_col_defaults[$incr] != NULL){ $default_vals = ""; $default_vals = " NOT NULL ".$exist_col_defaults[$incr]." ".$exist_col_extras[$incr]; }
                                                            else{ $default_vals = ""; $default_vals = " NULL DEFAULT NULL"; }
    
                                                            $sql2 = "ALTER TABLE `broiler_reportfields` ADD `$new_col_names[$incr]` $exist_col_types[$i] $default_vals AFTER `$new_col_names[$oincr]`";
                                                            if(!mysqli_query($conn,$sql2)){ echo "<br/>".mysqli_error($conn).""; } else{ }
                                                        }
                                                    }
                                                    $sql2 = "INSERT INTO `main_crdrnote_bkpc` SELECT * FROM `main_crdrnote` WHERE `date` <= '$todate' AND `mode` IN ('CCN','CDN');";
                                                    if(!mysqli_query($conn,$sql2)){ echo "<br/>".mysqli_error($conn).""; } else{ }
                                                }
                                                else{
                                                    $sql2 = "CREATE TABLE `main_crdrnote_bkpc` as SELECT * FROM `main_crdrnote` WHERE `date` <= '$todate' AND `mode` IN ('CCN','CDN');";
                                                    if(!mysqli_query($conn,$sql2)){ echo "<br/>".mysqli_error($conn).""; } else{ }
                                                }
                                                $sql3 = "DELETE FROM `main_crdrnote` WHERE `date` <= '$todate' AND `mode` IN ('CCN','CDN')"; mysqli_query($conn,$sql3);
                                                
                                                //Mortality Table
                                                $sql1 = "SHOW TABLES WHERE ".$table_head." LIKE 'main_mortality_bkpc';";
                                                $query1 = mysqli_query($conn,$sql1); $count1 = mysqli_num_rows($query1);
                                                if($count1 > 0){
                                                    $exist_col_names = $exist_col_types = $exist_col_nulls = $exist_col_keys = $exist_col_defaults = $exist_col_extras = array(); $i = 0;
                                                    $sql2 = 'SHOW COLUMNS FROM `main_mortality`'; $query2 = mysqli_query($conn,$sql2);
                                                    while($row2 = mysqli_fetch_assoc($query2)){ $i++; $exist_col_names[$i] = $row2['Field']; $exist_col_types[$i] = $row2['Type']; $exist_col_nulls[$i] = $row2['Null']; $exist_col_keys[$i] = $row2['Key']; $exist_col_defaults[$i] = $row2['Default']; $exist_col_extras[$i] = $row2['Extra']; }
                                                    
                                                    $sql2 = 'SHOW COLUMNS FROM `main_mortality`'; $query2 = mysqli_query($conn,$sql2); $new_col_names = array(); $j = 0;
                                                    while($row2 = mysqli_fetch_assoc($query2)){ $j++; $new_col_names[$j] = $row2['Field']; }
    
                                                    for($incr = 1;$incr <= $i;$incr++){
                                                        $oincr = $incr - 1;
                                                        if(in_array($new_col_names[$incr], $exist_col_names, TRUE) == ""){
    
                                                            if($exist_col_nulls[$incr] == "YES"){ $default_vals = ""; $default_vals = " NULL DEFAULT NULL"; }
                                                            else if($exist_col_nulls[$incr] == "NO" && $exist_col_defaults[$incr] == NULL){ $default_vals = ""; $default_vals = " NULL DEFAULT NULL"; }
                                                            else if($exist_col_nulls[$incr] == "NO" && $exist_col_defaults[$incr] != NULL){ $default_vals = ""; $default_vals = " NOT NULL ".$exist_col_defaults[$incr]." ".$exist_col_extras[$incr]; }
                                                            else{ $default_vals = ""; $default_vals = " NULL DEFAULT NULL"; }
    
                                                            $sql2 = "ALTER TABLE `broiler_reportfields` ADD `$new_col_names[$incr]` $exist_col_types[$i] $default_vals AFTER `$new_col_names[$oincr]`";
                                                            if(!mysqli_query($conn,$sql2)){ echo "<br/>".mysqli_error($conn).""; } else{ }
                                                        }
                                                    }
                                                    $sql2 = "INSERT INTO `main_mortality_bkpc` SELECT * FROM `main_mortality` WHERE `date` <= '$todate' AND `mtype` = 'customer';";
                                                    if(!mysqli_query($conn,$sql2)){ echo "<br/>".mysqli_error($conn).""; } else{ }
                                                }
                                                else{
                                                    $sql2 = "CREATE TABLE `main_mortality_bkpc` as SELECT * FROM `main_mortality` WHERE `date` <= '$todate' AND `mtype` = 'customer';";
                                                    if(!mysqli_query($conn,$sql2)){ echo "<br/>".mysqli_error($conn).""; } else{ }
                                                }
                                                $sql3 = "DELETE FROM `main_mortality` WHERE `date` <= '$todate' AND `mtype` = 'customer'"; mysqli_query($conn,$sql3);
                                                
                                                //Return Table
                                                $sql1 = "SHOW TABLES WHERE ".$table_head." LIKE 'main_itemreturns_bkpc';";
                                                $query1 = mysqli_query($conn,$sql1); $count1 = mysqli_num_rows($query1);
                                                if($count1 > 0){
                                                    $exist_col_names = $exist_col_types = $exist_col_nulls = $exist_col_keys = $exist_col_defaults = $exist_col_extras = array(); $i = 0;
                                                    $sql2 = 'SHOW COLUMNS FROM `main_itemreturns`'; $query2 = mysqli_query($conn,$sql2);
                                                    while($row2 = mysqli_fetch_assoc($query2)){ $i++; $exist_col_names[$i] = $row2['Field']; $exist_col_types[$i] = $row2['Type']; $exist_col_nulls[$i] = $row2['Null']; $exist_col_keys[$i] = $row2['Key']; $exist_col_defaults[$i] = $row2['Default']; $exist_col_extras[$i] = $row2['Extra']; }
                                                    
                                                    $sql2 = 'SHOW COLUMNS FROM `main_itemreturns`'; $query2 = mysqli_query($conn,$sql2); $new_col_names = array(); $j = 0;
                                                    while($row2 = mysqli_fetch_assoc($query2)){ $j++; $new_col_names[$j] = $row2['Field']; }
    
                                                    for($incr = 1;$incr <= $i;$incr++){
                                                        $oincr = $incr - 1;
                                                        if(in_array($new_col_names[$incr], $exist_col_names, TRUE) == ""){
    
                                                            if($exist_col_nulls[$incr] == "YES"){ $default_vals = ""; $default_vals = " NULL DEFAULT NULL"; }
                                                            else if($exist_col_nulls[$incr] == "NO" && $exist_col_defaults[$incr] == NULL){ $default_vals = ""; $default_vals = " NULL DEFAULT NULL"; }
                                                            else if($exist_col_nulls[$incr] == "NO" && $exist_col_defaults[$incr] != NULL){ $default_vals = ""; $default_vals = " NOT NULL ".$exist_col_defaults[$incr]." ".$exist_col_extras[$incr]; }
                                                            else{ $default_vals = ""; $default_vals = " NULL DEFAULT NULL"; }
    
                                                            $sql2 = "ALTER TABLE `broiler_reportfields` ADD `$new_col_names[$incr]` $exist_col_types[$i] $default_vals AFTER `$new_col_names[$oincr]`";
                                                            if(!mysqli_query($conn,$sql2)){ echo "<br/>".mysqli_error($conn).""; } else{ }
                                                        }
                                                    }
                                                    $sql2 = "INSERT INTO `main_itemreturns_bkpc` SELECT * FROM `main_itemreturns` WHERE `date` <= '$todate' AND `mode` = 'customer'";
                                                    if(!mysqli_query($conn,$sql2)){ echo "<br/>".mysqli_error($conn).""; } else{ }
                                                }
                                                else{
                                                    $sql2 = "CREATE TABLE `main_itemreturns_bkpc` AS SELECT * FROM `main_itemreturns` WHERE `date` <= '$todate' AND `mode` = 'customer'";
                                                    if(!mysqli_query($conn,$sql2)){ echo "<br/>".mysqli_error($conn).""; } else{ }
                                                }
                                                $sql3 = "DELETE FROM `main_itemreturns` WHERE `date` <= '$todate' AND `mode` = 'customer'"; mysqli_query($conn,$sql3);
                                                
                                            }
										}
										else { }
									}
								}
							?>
                            <tr>
                                <th style="padding-left:5px;text-align:left;" colspan="2">Total Balance</th>
                                <th style="padding-right:5px;text-align:right;"><?php echo number_format_ind($total_bal_amt); ?></th>
                            </tr>
							</tbody>
						</table>
					</form>
				</div>
		</section>
		<script type="text/javascript" lahguage="javascript">
			function checkval(){
				var a = document.getElementById("datepickers1").value;
				var b = document.getElementById("status").value;
				if(b == "display"){
					return true;
				}
				else{
					var x = confirm("Are You Sure You Want to Delete The Data, This Will Delete The Data Up to"+a);
					if(x == true){ return true; } else{ return false; }
				}
			}
			function sortTable(n) {
			  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
			  table = document.getElementById("myTable");
			  switching = true;
			  //Set the sorting direction to ascending:
			  dir = "asc"; 
			  /*Make a loop that will continue until
			  no switching has been done:*/
			  while (switching) {
				//start by saying: no switching is done:
				switching = false;
				rows = table.rows;
				/*Loop through all table rows (except the
				first, which contains table headers):*/
				for (i = 1; i < (rows.length - 1); i++) {
				  //start by saying there should be no switching:
				  shouldSwitch = false;
				  /*Get the two elements you want to compare,
				  one from current row and one from the next:*/
				  x = rows[i].getElementsByTagName("TD")[n];
				  y = rows[i + 1].getElementsByTagName("TD")[n];
				  /*check if the two rows should switch place,
				  based on the direction, asc or desc:*/
				  if (dir == "asc") {
					if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
					  //if so, mark as a switch and break the loop:
					  shouldSwitch= true;
					  break;
					}
				  } else if (dir == "desc") {
					/*if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
					  //if so, mark as a switch and break the loop:
					  shouldSwitch = true;
					  break;
					}*/
					if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
					  //if so, mark as a switch and break the loop:
					  shouldSwitch= true;
					  break;
					}
				  }
				}
				if (shouldSwitch) {
				  /*If a switch has been marked, make the switch
				  and mark that a switch has been done:*/
				  rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
				  switching = true;
				  //Each time a switch is done, increase this count by 1:
				  switchcount ++;
				  
				} else {
				  /*If no switching has been done AND the direction is "asc",
				  set the direction to "desc" and run the while loop again.*/
				  if (switchcount == 0 && dir == "asc") {
					dir = "desc";
					switching = true;
				  }
				}
			  }
			}
		</script>
		<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<script src="../loading_page_out.js"></script>
		<?php
			if($cname == ""){
				
			}
			else {
				echo "<script> sortTable(0); </script>";
			}
		?>
	</body>
	
</html>
<?php include "header_foot.php"; ?>
