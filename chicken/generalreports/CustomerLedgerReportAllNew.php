<?php 
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	$requested_data = json_decode(file_get_contents('php://input'),true);
	session_start();
	
	$db = $_SESSION['db'] = $_GET['db'];
	if($db == ''){ include "../config.php"; include "header_head.php"; include "number_format_ind.php";$dbname = $_SESSION['dbase'];
		$users_code = $_SESSION['userid']; }
	else{ include "APIconfig.php"; include "number_format_ind.php"; include "header_head.php"; $dbname = $db;
		$users_code = $_GET['emp_code'];}
			
	$today = date("Y-m-d");
	$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $ifwt = $row['wt']; $ifbw = $row['bw']; $ifjbw = $row['jbw']; $ifjbwen = $row['jbwen']; $ifctype = $row['ctype']; }

    // Logo Flag
    $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
	if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

	$idisplay = ''; $ndisplay = 'style="display:none;"';
	$cname = $_POST['cname']; $iname = $_POST['iname'];
	if($cname == "all" || $cname == "select") { $cnames = ""; } else { $cnames = " AND `groupcode` = '$cname'"; }
	$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE 'C' AND `active` = '1'".$cnames." ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$pcode[$row['code']] = $row['code'];
		$pname[$row['code']] = $row['name'];
		$cus_mobile[$row['code']] = $row['mobileno'];
		$obdate[$row['code']] = $row['obdate'];
		$obtype[$row['code']] = $row['obtype'];
		$obamt[$row['code']] = $row['obamt'];
		$creditamt[$row['code']] = $row['creditamt'];
	}
	$sql = "SELECT * FROM `item_details` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $itemname[$row['code']] = $row['description']; }
	$fromdate = $_POST['fromdate'];
	$todate = $_POST['todate'];
	if($fromdate == ""){ $fromdate = $todate = $today; } else { $fromdate = $_POST['fromdate']; $todate = $_POST['todate']; }

	$exoption = "displaypage"; $bwd_aflag = 0;
	if(isset($_POST['submit'])) { $excel_type = $exoption = $_POST['export']; } else{ $excel_type = "displaypage"; }
	if(isset($_POST['submit']) == true){
		$exl_fdate = $_POST['fromdate']; $exl_tdate = $_POST['todate']; $exl_cname = $_POST['cname'];
		if($_POST['bwd_aflag'] == "on" || $_POST['bwd_aflag'] == 1 || $_POST['bwd_aflag'] == true){ $bwd_aflag = 1; }
	}
	else{
		$exl_fdate = $exl_tdate = $today; $exl_cname =  "all";
	}
	$url = "../PHPExcel/Examples/BalanceReportNew-Excel.php?fromdate=".$exl_fdate."&todate=".$exl_tdate."&cname=".$exl_cname;
?>
<html>
	<head><link rel="stylesheet" type="text/css"href="reportstyle.css">
		<script>
			var exptype = '<?php echo $excel_type; ?>';
			var url = '<?php echo $url; ?>';
			if(exptype.match("exportexcel")){
				window.open(url,'_BLANK');
			}
		</script>
		<style>
			.thead2 th {
				top: 0;
				position: sticky;
				background-color: #98fb98;
			}
			.thead3 th {
				text-align: center;
				background-color: #98fb98;
				border: 0.1vh solid gray;
			}
			body{
				color: black;
			}
			.formcontrol {
				height: 23px;
				border: 0.1vh solid gray;
			}
			.formcontrol:focus {
				height: 23px;
				border: 0.1vh solid gray;
				outline: none;
			}
			.tbody1 td {
				padding-right: 5px;
				text-align: right;
			}
			.tfoot1 {
				top: 0;
				position: sticky;
				background-color: #98fb98;
				bottom: 0;
				border: 0.1vh solid gray;
				z-index: 1;
			}
			.tfoot1 th{
				border: 0.1vh solid gray;
			}
			.tfoot2 {
				background-color: #98fb98;
				border: 0.1vh solid gray;
			}
			.tfoot2 th{
				border: 0.1vh solid gray;
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
	<?php if($exoption == "displaypage" || $exoption == "printerfriendly") { ?>
		<header align="center">
			<table align="center" class="reportheadermenu">
				<tr>
				<?php
					if($dlogo_flag > 0) { ?>
						<td><img src="../<?php echo $logo1; ?>" height="150px"/></td>
					<?php }
					else{ 
					$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?>
					<td><img src="../<?php echo $row['logopath']; ?>" height="150px"/></td>
					<td><?php echo $row['cdetails']; ?></td> <?php } }?></td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<label style="font-weight:bold;" class="reportheaderlabel">Customer Ledger</label>&ensp;
						<label class="reportheaderlabel"><b style="color: green;">From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fromdate)); ?></label>&ensp;
						<label class="reportheaderlabel"><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($todate)); ?></label>
					</td>
				</tr>
			</table>
		</header>
	<?php } ?>
		<section class="content">
				<div class="col-md-12">
				<?php if($db == ''){?>
				<form action="CustomerLedgerReportAllNew.php" method="post" onsubmit="return checkval()" >
					<?php } else { ?>
					<form action="CustomerLedgerReportAllNew.php?db=<?php echo $db; ?>" method="post" onsubmit="return checkval()">
					<?php } ?>
						<table class="table1" style="min-width:100%;line-height:23px;">
						<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?>
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<!--<td style='visibility:hidden;'></td>-->
									<td colspan="17">
										<label class="reportselectionlabel">From date</label>&nbsp;
										<input type="text" name="fromdate" id="datepickers" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($fromdate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">To Date</label>&nbsp;
										<input type="text" name="todate" id="datepickers1" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($todate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">Group</label>&nbsp;
										<select name="cname" id="checkcname" class="form-control select2">
											<option value="all" selected>-All-</option>
											<?php
											$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE 'C' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
											while($row = mysqli_fetch_assoc($query)){
											?>
												<option value="<?php echo $row['code']; ?>" <?php if($cname == $row['code']){ echo 'selected'; } ?>><?php echo $row['description']; ?></option>
											<?php
											}
											?>
										</select>&ensp;&ensp;
										<label class="reportselectionlabel">B/w Days</label>&nbsp;
										<input type="checkbox" name="bwd_aflag" id="bwd_aflag" <?php if($bwd_aflag == 1){ echo "checked"; } ?> />
									&ensp;&ensp;
										<label class="reportselectionlabel">Export To</label>&nbsp;
										<select name="export" id="export" class="form-control select2">
											<option <?php if($exoption == "displaypage") { echo 'selected'; } ?> value="displaypage">Display</option>
											<option <?php if($exoption == "exportexcel") { echo 'selected'; } ?> value="exportexcel">Excel</option>
											<option <?php if($exoption == "printerfriendly") { echo 'selected'; } ?> value="printerfriendly">Printer friendly</option>
										</select>&ensp;&ensp;
										<button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
									</td>
								</tr>
							</thead>
						<?php }
						if($exoption == "printerfriendly"){ echo '<thead class="thead3" style="background-color: #98fb98;">'; }
						else{ echo '<thead class="thead2" style="background-color: #98fb98;">'; }
						?>
								<tr>
									<th id="order">Name</th>
									<th id="order">Mobile No</th>
									<th id="order_num">Opening Balance</th>
									<th id="order_num">Sales Qty</th>
									<th id="order_num">Sales</th>
									<th id="order_num">Receipt</th>
									<th id="order_num">B/w days balance</th>
									<th id="order_num">Balance</th>
								</tr>
							</thead>
							<tbody class="tbody1" id="myTable" style="background-color: #f4f0ec;">
							<?php
								if(isset($_POST['submit']) == true){
									//if($cname == "" || $cname == "all" || $cname == "select"){
										$fromdate = $_POST['fromdate'];
										$todate = $_POST['todate'];
										if($fromdate == ""){ $fromdate = $todate = $today; } else { $fromdate = date("Y-m-d",strtotime($_POST['fromdate'])); $todate = date("Y-m-d",strtotime($_POST['todate'])); }
										$cname = $_POST['cname']; $iname = $_POST['iname'];
										if($cname == "all" || $cname == "select") { $cnames = ""; } else { $cnames = " AND `customercode` = '$cname'"; }
										
										
										//sales invoice
										$ob_sales = $ob_receipts = $ob_ccn = $ob_cdn = array();
										$sql = "SELECT * FROM `customer_sales` WHERE `date` < '$fromdate' AND `active` = '1' ORDER BY `date`,`invoice`,`customercode` ASC";
										$query = mysqli_query($conn,$sql); $old_inv = "";
										while($row = mysqli_fetch_assoc($query)){
											if($old_inv != $row['invoice']){
												$ob_sales[$row['customercode']] = $ob_sales[$row['customercode']] + $row['finaltotal'];
												$old_inv = $row['invoice'];
											}
										}
										//Customer Receipt
										$sql = "SELECT * FROM `customer_receipts` WHERE `date` < '$fromdate' AND `active` = '1' ORDER BY `ccode` ASC"; $query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){
											$ob_receipts[$row['ccode']] = $ob_receipts[$row['ccode']] + $row['amount'];
										}
										//Customer Returns
										$ob_returns = array();
										$obsql = "SELECT * FROM `main_itemreturns` WHERE `date` < '$fromdate' AND `mode` = 'customer' AND `active` = '1' AND `dflag` = '0'";
										$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_returns[$obrow['vcode']] += (float)$obrow['amount']; }

										//Customer Mortality
										$ob_smortality = array();
										$obsql = "SELECT * FROM `main_mortality` WHERE `date` < '$fromdate' AND `mtype` = 'customer' AND `active` = '1' AND `dflag` = '0'";
										$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_smortality[$obrow['ccode']] += (float)$obrow['amount']; }

										//Customer CrDr Note
										$sql = "SELECT * FROM `main_crdrnote` WHERE `date` < '$fromdate' AND `mode` IN ('CCN','CDN') AND `active` = '1' ORDER BY `ccode` ASC"; $query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){
											if($row['mode'] == "CCN"){
												$ob_ccn[$row['ccode']] = $ob_ccn[$row['ccode']] + $row['amount'];
											}
											else{
												$ob_cdn[$row['ccode']] = $ob_cdn[$row['ccode']] + $row['amount'];
											}
										}
										//sales invoice
										$sql = "SELECT * FROM `customer_sales` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `active` = '1' ORDER BY `date`,`invoice`,`customercode` ASC";
										$query = mysqli_query($conn,$sql); $old_inv = "";
										while($row = mysqli_fetch_assoc($query)){
											if($old_inv != $row['invoice']){
												$bt_sales[$row['customercode']] = $bt_sales[$row['customercode']] + $row['finaltotal'];
												$old_inv = $row['invoice'];
											}
											$bt_sales_qty[$row['customercode']] = $bt_sales_qty[$row['customercode']] + $row['netweight'];
										}
										//Customer Receipt
										$sql = "SELECT * FROM `customer_receipts` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `active` = '1' ORDER BY `ccode` ASC";
										$query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){
											$bt_receipts[$row['ccode']] = $bt_receipts[$row['ccode']] + $row['amount'];
										}
										//Customer Returns
										$bt_returns = array();
										$obsql = "SELECT * FROM `main_itemreturns` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `mode` = 'customer' AND `active` = '1' AND `dflag` = '0'";
										$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $bt_returns[$obrow['vcode']] += (float)$obrow['amount']; }

										//Customer Mortality
										$bt_smortality = array();
										$obsql = "SELECT * FROM `main_mortality` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `mtype` = 'customer' AND `active` = '1' AND `dflag` = '0'";
										$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $bt_smortality[$obrow['ccode']] += (float)$obrow['amount']; }

										//Customer CrDr Note
										$sql = "SELECT * FROM `main_crdrnote` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `mode` IN ('CCN','CDN') AND `active` = '1' ORDER BY `ccode` ASC";
										$query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){
											if($row['mode'] == "CCN"){
												$bt_ccn[$row['ccode']] = $bt_ccn[$row['ccode']] + $row['amount'];
											}
											else{
												$bt_cdn[$row['ccode']] = $bt_cdn[$row['ccode']] + $row['amount'];
											}
										}
										$ftotal = $ft_ob =  $ft_sq =  $ft_sa =  $ft_rt =  $ft_bb = 0;
										foreach($pcode as $pcodes){
											if((int)$bwd_aflag == 0 || (int)$bwd_aflag == 1 && ((float)$bt_sales_qty[$pcodes] > 0 || ((float)$bt_sales[$pcodes] + (float)$bt_cdn[$pcodes]) > 0) || ((float)$bt_receipts[$pcodes] + (float)$bt_returns[$pcodes] + (float)$bt_smortality[$pcodes] + (float)$bt_ccn[$pcodes]) > 0){
												echo "<tr>";
												echo "<td style='text-align:left;'>".$pname[$pcodes]."</td>";
												echo "<td style='text-align:left;'>".$cus_mobile[$pcodes]."</td>";
												$ob_cramt = $ob_dramt = $ob_dr = $ob_cr = $ob_fcr = $ob_fdr = $bt_dr = $bt_cr = $bt_fcr = $bt_fdr = $balance = 0;
												if($obtype[$pcodes] == "Cr"){
												$ob_cramt = $obamt[$pcodes];
												}
												else {
												$ob_dramt = $obamt[$pcodes];
												}
												$ft_ob = $ft_ob + (((float)$ob_sales[$pcodes] + (float)$ob_cdn[$pcodes] + (float)$ob_dramt) - ((float)$ob_receipts[$pcodes] + (float)$ob_returns[$pcodes] + (float)$ob_smortality[$pcodes] + (float)$ob_ccn[$pcodes] + (float)$ob_cramt));
												$ft_sq = $ft_sq + (float)$bt_sales_qty[$pcodes];
												$ft_sa = $ft_sa + ((float)$bt_sales[$pcodes] + (float)$bt_cdn[$pcodes]);
												$ft_rt = $ft_rt + ((float)$bt_receipts[$pcodes] + (float)$bt_returns[$pcodes] + (float)$bt_smortality[$pcodes] + (float)$bt_ccn[$pcodes]);
												$ft_bb = $ft_bb + (((float)$bt_sales[$pcodes] + (float)$bt_cdn[$pcodes]) - ((float)$bt_receipts[$pcodes] + (float)$bt_returns[$pcodes] + (float)$bt_smortality[$pcodes] + (float)$bt_ccn[$pcodes]));
												
												echo "<td>".number_format_ind(((float)$ob_sales[$pcodes] + (float)$ob_cdn[$pcodes] + (float)$ob_dramt) - ((float)$ob_receipts[$pcodes] + (float)$ob_returns[$pcodes] + (float)$ob_smortality[$pcodes] + (float)$ob_ccn[$pcodes] + (float)$ob_cramt))."</td>";
												echo "<td>".number_format_ind($bt_sales_qty[$pcodes])."</td>";
												echo "<td>".number_format_ind((float)$bt_sales[$pcodes] + (float)$bt_cdn[$pcodes])."</td>";
												echo "<td>".number_format_ind((float)$bt_receipts[$pcodes] + (float)$bt_returns[$pcodes] + (float)$bt_smortality[$pcodes] + (float)$bt_ccn[$pcodes])."</td>";
												echo "<td>".number_format_ind(((float)$bt_sales[$pcodes] + (float)$bt_cdn[$pcodes]) - ((float)$bt_receipts[$pcodes] + (float)$bt_returns[$pcodes] + (float)$bt_smortality[$pcodes] + (float)$bt_ccn[$pcodes]))."</td>";
												$ob_dr = (float)$ob_sales[$pcodes] + (float)$ob_cdn[$pcodes] + (float)$ob_dramt;
												$ob_cr = (float)$ob_receipts[$pcodes] + (float)$ob_returns[$pcodes] + (float)$ob_smortality[$pcodes] + (float)$ob_ccn[$pcodes] + (float)$ob_cramt;
												if($ob_cr > $ob_dr){
												$ob_fcr = $ob_cr - $ob_dr;
												}
												else{
												$ob_fdr = $ob_dr - $ob_cr;
												}
												$bt_dr = (float)$bt_sales[$pcodes] + (float)$bt_cdn[$pcodes];
												$bt_cr = (float)$bt_receipts[$pcodes] + (float)$bt_returns[$pcodes] + (float)$bt_smortality[$pcodes] + (float)$bt_ccn[$pcodes];
												if($bt_cr > $bt_dr){
												$bt_fcr = (float)$bt_cr - (float)$bt_dr;
												}
												else{
												$bt_fdr = (float)$bt_dr - (float)$bt_cr;
												}
												$balance = ((float)$ob_fdr + (float)$bt_fdr) - ((float)$ob_fcr + (float)$bt_fcr);
												$ftotal = (float)$ftotal + (float)$balance;
												if(!empty($creditamt[$pcodes]) && (float)$creditamt[$pcodes] != 0 && (float)$creditamt[$pcodes] < (float)$balance){
													echo "<td style='color:red;'>".number_format_ind($balance)."</td>";
												}
												else{
													echo "<td>".number_format_ind($balance)."</td>";
												}
												
												echo "</tr>";
											}
										}
										
									//}
									//else {
									//}
								}
							?>
							</tbody>
							<?php
							if($exoption == "printerfriendly"){
								echo '<tfoot class="tfoot2">';
							}
							else{
								echo '<tfoot class="tfoot1">';
							}
							?>
							
								<tr style="background-color: #98fb98;">
									<th align="center" colspan="2"><b>Total</b></th>
									<th style='padding-right: 5px;text-align:right;'><?php echo number_format_ind($ft_ob); ?></th>
									<th style='padding-right: 5px;text-align:right;'><?php echo number_format_ind($ft_sq); ?></th>
									<th style='padding-right: 5px;text-align:right;'><?php echo number_format_ind($ft_sa); ?></th>
									<th style='padding-right: 5px;text-align:right;'><?php echo number_format_ind($ft_rt); ?></th>
									<th style='padding-right: 5px;text-align:right;'><?php echo number_format_ind($ft_bb); ?></th>
									<th style='padding-right: 5px;text-align:right;'><?php echo number_format_ind($ftotal); ?></th>
								</tr>
							</tfoot>
						</table>
					</form>
				</div>
		</section>
		<script type="text/javascript" lahguage="javascript">
			function checkval(){
				var a = document.getElementById("checkcname").value;
				if(a.match("select") || a.match("-select-")){
					alert("Please select customer ..!");
					return false;
				}
				else {
					return true;
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
            
			function convertDate(d) {
				var p = d.split(".");
				return (p[2]+p[1]+p[0]);
		}
function table_sort() {
		console.log("test");
  const styleSheet = document.createElement('style');
  styleSheet.innerHTML = `
        .order-inactive span {
            visibility:hidden;
        }
        .order-inactive:hover span {
            visibility:visible;
        }
        .order-active span {
            visibility: visible;
        }
    `;
  document.head.appendChild(styleSheet);

  document.querySelectorAll('#order').forEach(th_elem => {
	console.log("test1");

    let asc = true;
    const span_elem = document.createElement('span');
    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
    span_elem.innerHTML = "▼";
    th_elem.appendChild(span_elem);
    th_elem.classList.add('order-inactive');

    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
    th_elem.addEventListener('click', (e) => {
      document.querySelectorAll('#order').forEach(elem => {
        elem.classList.remove('order-active')
        elem.classList.add('order-inactive')
      });
      th_elem.classList.remove('order-inactive');
      th_elem.classList.add('order-active');

      if (!asc) {
        th_elem.querySelector('span').innerHTML = '▲';
      } else {
        th_elem.querySelector('span').innerHTML = '▼';
      }
      const arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
      arr.sort((a, b) => {
        const a_val = a.children[index].innerText;
        const b_val = b.children[index].innerText;
        return (asc) ? a_val.localeCompare(b_val) : b_val.localeCompare(a_val)
      });
      arr.forEach(elem => {
        th_elem.closest("table").querySelector("tbody").appendChild(elem)
      });
      asc = !asc;
    })

	

  });
}
function table_sort3() {
		console.log("test");
  const styleSheet = document.createElement('style');
  styleSheet.innerHTML = `
        .order-inactive span {
            visibility:hidden;
        }
        .order-inactive:hover span {
            visibility:visible;
        }
        .order-active span {
            visibility: visible;
        }
    `;
  document.head.appendChild(styleSheet);

  document.querySelectorAll('#order_date').forEach(th_elem => {
	console.log("test1");

    let asc = true;
    const span_elem = document.createElement('span');
    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
    span_elem.innerHTML = "▼";
    th_elem.appendChild(span_elem);
    th_elem.classList.add('order-inactive');

    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
    th_elem.addEventListener('click', (e) => {
      document.querySelectorAll('#order_date').forEach(elem => {
        elem.classList.remove('order-active')
        elem.classList.add('order-inactive')
      });
      th_elem.classList.remove('order-inactive');
      th_elem.classList.add('order-active');

      if (!asc) {
        th_elem.querySelector('span').innerHTML = '▲';
      } else {
        th_elem.querySelector('span').innerHTML = '▼';
      }
      const arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
      arr.sort((a, b) => {
        const a_val = convertDate(a.children[index].innerText);
        const b_val = convertDate(b.children[index].innerText);
        return (asc) ? a_val.localeCompare(b_val) : b_val.localeCompare(a_val)
      });
      arr.forEach(elem => {
        th_elem.closest("table").querySelector("tbody").appendChild(elem)
      });
      asc = !asc;
    })

	

  });
}

function convertNumber(d) {
				var p = intval(d) ;
				return (p);
			}

function table_sort2() {
		console.log("test");
  const styleSheet = document.createElement('style');
  styleSheet.innerHTML = `
        .order-inactive span {
            visibility:hidden;
        }
        .order-inactive:hover span {
            visibility:visible;
        }
        .order-active span {
            visibility: visible;
        }
    `;
  document.head.appendChild(styleSheet);

  document.querySelectorAll('#order_num').forEach(th_elem => {
	console.log("test1");

    let asc = true;
    const span_elem = document.createElement('span');
    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
    span_elem.innerHTML = "▼";
    th_elem.appendChild(span_elem);
    th_elem.classList.add('order-inactive');

    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
    th_elem.addEventListener('click', (e) => {
      document.querySelectorAll('#order_num').forEach(elem => {
        elem.classList.remove('order-active')
        elem.classList.add('order-inactive')
      });
      th_elem.classList.remove('order-inactive');
      th_elem.classList.add('order-active');

      if (!asc) {
        th_elem.querySelector('span').innerHTML = '▲';
      } else {
        th_elem.querySelector('span').innerHTML = '▼';
      }
      
      var arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
      arr.sort((a, b) => {
        const a_val = a.children[index].innerText;    
        if(isNaN(a_val)){
        a_val1 = a_val.split(',').join(''); }
        else {
            a_val1 = a_val; }
        const b_val = b.children[index].innerText;
        if(isNaN(b_val)){
        b_val1 = b_val.split(',').join('');}
        else {
            b_val1 = b_val; }
        return (asc) ? b_val1 - a_val1:  a_val1 - b_val1 
      });
      arr.forEach(elem => {
        th_elem.closest("table").querySelector("tbody").appendChild(elem)
      });
      asc = !asc;
    })

	

  });
}

table_sort();
table_sort2();
table_sort3();
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
