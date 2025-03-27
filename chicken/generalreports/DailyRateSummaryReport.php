<?php 
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	include "../config.php";
	include "header_head.php";
	include "number_format_ind.php";
	
	$sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }
	
	// Logo Flag
	$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
	$query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
	if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }
  

	$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

	$fromdate = $todate = date("Y-m-d"); $item = $sectors = "all"; $sec_fltr = "";
	if(isset($_POST['submit']) == true){
		$fromdate = date("Y-m-d",strtotime($_POST['fromdate']));
		$todate = date("Y-m-d",strtotime($_POST['todate']));
		$item = $_POST['item'];
		$sectors = $_POST['sectors'];
	}
	if($item == "all"||$item == '') { $itemcond = ""; } else { $itemcond = " AND `code` = '$item'"; }
	if($sectors != "all"){$sec_fltr = " AND `warehouse` = '$sectors'"; }
?>
<?php $expoption = "displaypage"; if(isset($_POST['submit'])) { $expoption = $_POST['export']; } if($expoption == "displaypage") { $exoption = "displaypage"; } else { $exoption = $expoption; }; ?>
<html>
	<head><link rel="stylesheet" type="text/css"href="reportstyle.css">
		<?php
			if($exoption == "exportexcel") {
				echo header("Content-type: application/xls");
				echo header("Content-Disposition: attachment; filename=DailyRateSummaryReport($fromdate-$todate).xls");
				echo header("Pragma: no-cache"); echo header("Expires: 0");
			}
		?>
		<style>
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
						<label style="font-weight:bold;" class="reportheaderlabel">Daily Rate Summary Report</label>&ensp;
						<?php
							if($item == "all" || $item == "select" || $item == "") { } else {
						?>
							<label class="reportheaderlabel"><b style="color: green;">Item:</b>&nbsp;<?php echo $item_name[$item]; ?></label>&ensp;
						<?php
							}
						?>
						<label class="reportheaderlabel"><b style="color: green;">From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fromdate)); ?></label>&ensp;
						<label class="reportheaderlabel"><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($todate)); ?></label>
					</td>
				</tr>
			</table>
		</header>
	<?php } ?>
		<section class="content" align="center">
				<div class="col-md-12" align="center">
					<form action="DailyRateSummaryReport.php" method="post">
						<table class="table1" style="min-width:auto;line-height:23px;">
						<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?>
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<td colspan="17">
										<label class="reportselectionlabel">From date</label>&nbsp;
										<input type="text" name="fromdate" id="datepickers" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($fromdate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">To Date</label>&nbsp;
										<input type="text" name="todate" id="datepickers1" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($todate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">Item</label>&nbsp;
										<select name="item" id="item" class="form-control select2">
											<option value="all" <?php if($item == '') { echo 'selected'; } ?>>-select-</option>
											<?php
												$sql = "SELECT distinct code,description FROM `item_details` ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
											?>
													<option <?php if($item == $row['code']) { echo 'selected'; } ?> value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
											<?php
												}
											?>
										</select>
									&ensp;&ensp;
										<label class="reportselectionlabel">Warehouse</label>&nbsp;
										<select name="sectors" id="sectors" class="form-control select2">
											<option value="all" <?php if($sectors == 'all') { echo 'selected'; } ?>>-All-</option>
											<?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($sectors == $scode) { echo 'selected'; } ?>><?php echo $sector_name[$scode]; ?></option><?php } ?>
										</select>
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
						<?php } ?>
							<thead class="thead2" style="background-color: #98fb98;">
								<th id="order_num">Sl.No.</th>
								<th id="order_date">Date</th>
								<th id="order_num">Paper Rate</th>
								<th id="order_num">Purchase Quantity</th>
								<th id="order_num">Purchase Amount</th>
								<th id="order_num">Purchase Rate(Avg.)</th>
								<th id="order_num">Sales Quantity</th>
								<th id="order_num">Sales Amount</th>
								<th id="order_num">Sales Rate(Avg.)</th>
								<th id="order_num">Profit per Kg</th>
							</thead>
							<tbody class="tbody1" id="tbody1" style="background-color: #f4f0ec;">
							<?php
								if(isset($_POST['submit']) == true && $itemcond!=''){
									$fromdate = date("Y-m-d",strtotime($fromdate)); $todate = date("Y-m-d",strtotime($todate));
									$fdate = strtotime($_POST['fromdate']); $tdate = strtotime($_POST['todate']); $i = 0; $exi_inv = "";
									$totalpurchasequantity = $totalpurchaseamt = $totalsalesquantity = $totalsalesamt = $totalpaperrate = $paperratecount = 0;
									for ($currentDate = $fdate; $currentDate <= $tdate; $currentDate += (86400)) {
										$date_asc = date('Y-m-d', $currentDate);
										$sql = "SELECT distinct code,description FROM `item_details` where 1".$itemcond." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){
											$rowItem = $row['code'];
											$paperrate = 0;
											$purchaserate = 0;
											$purchaseamount = 0;
											$purchasequantity = 0;
											$salesrate = 0;
											$salesamount = 0;
											$salesquantity = 0;
											$sql_paperrate = "SELECT new_price FROM `main_dailypaperrate` WHERE `date` = '$date_asc'  AND `code` = '$rowItem'";
											$paperrate_query = mysqli_query($conn,$sql_paperrate); while($paperrate_row = mysqli_fetch_assoc($paperrate_query)){ $paperrate = $paperrate_row['new_price']; 
											}
											if ($paperrate>0){
												$paperratecount++;
											}
											$totalpaperrate = $totalpaperrate+$paperrate;
											$sql_purchaserate = "SELECT * FROM `pur_purchase` WHERE  `date` = '$date_asc'".$sec_fltr." AND `itemcode` = '$rowItem'";
											$purchaserate_query = mysqli_query($conn,$sql_purchaserate); while($purchaserate_row = mysqli_fetch_assoc($purchaserate_query)){ $purchaseamount = $purchaseamount+$purchaserate_row['totalamt']; 
											$purchasequantity = $purchasequantity+$purchaserate_row['netweight'];
											}
											$totalpurchaseamt = $totalpurchaseamt+$purchaseamount;
											$totalpurchasequantity = $totalpurchasequantity+$purchasequantity;
											if($purchasequantity > 0){
												$t1 = 0;
												$t1 = $purchaseamount / $purchasequantity;
											}
											else{
												$t1 = 0;
											}
											if($t1>0){
												$purchaserate = round($t1,2);
											}
											else{
												$purchaserate = 0;
											}
												
											$sql_salesrate = "SELECT * FROM `customer_sales` WHERE  `date` = '$date_asc'".$sec_fltr."  AND `itemcode` = '$rowItem'";
											$salesrate_query = mysqli_query($conn,$sql_salesrate); while($salesrate_row = mysqli_fetch_assoc($salesrate_query)){ $salesamount = $salesamount+$salesrate_row['totalamt']; 
											$salesquantity = $salesquantity+$salesrate_row['netweight'];
											}
											$totalsalesamt = $totalsalesamt+$salesamount;
											$totalsalesquantity = $totalsalesquantity+$salesquantity;
											if ($salesamount > 0 && $salesquantity > 0){
												$salesrate = round($salesamount/$salesquantity,2);
											}
											else{
												$salesrate = 0;
											}
												
											if($paperrate>0||$purchaserate>0||$salesrate>0){
												$slno++;
												echo "<tr>";
												echo "<td style='text-align:center;'>".$slno."</td>";
												echo "<td style='padding-left:5px;text-align:left;'>".date('d.m.Y',strtotime($date_asc))."</td>";
												echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($paperrate)."</td>";
												echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($purchasequantity)."</td>";
												echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($purchaseamount)."</td>";
												echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($purchaserate)."</td>";
												echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($salesquantity)."</td>";
												echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($salesamount)."</td>";
												echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($salesrate)."</td>";
												echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($salesrate - $purchaserate)."</td>";
												echo "</tr>";
											}
										}
									}
									if($totalpaperrate > 0 && $paperratecount > 0){
										$avgpaperrate = round($totalpaperrate / $paperratecount,2);
									}
									else{
										$avgpaperrate = 0;
									}
										
									if($totalpurchaseamt > 0 && $totalpurchasequantity > 0){
										$totalpurchaserate = round($totalpurchaseamt / $totalpurchasequantity,2);
									}
									else{
										$totalpurchaserate = 0;
									}
										
									if($totalsalesamt > 0 && $totalsalesquantity > 0){
										$totalsalesrate = round($totalsalesamt / $totalsalesquantity,2);
									}
										
									else{
										$totalsalesrate = 0;
									}
									?>
								<?php
								}
								?>
							</tbody>
							<tfoot>
								<?php
									echo "<tr class='foottr' style='background-color: #98fb98;'>";
									echo "<td colspan='2' style='text-align:center;'>Total</td>";
									echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($avgpaperrate)."</td>";
									echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($totalpurchasequantity)."</td>";
									echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($totalpurchaseamt)."</td>";
									echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($totalpurchaserate)."</td>";
									echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($totalsalesquantity)."</td>";
									echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($totalsalesamt)."</td>";
									echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($totalsalesrate)."</td>";
									echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($totalsalesrate - $totalpurchaserate)."</td>";
									echo "</tr>";
								?>
							</tfoot>
						</table>
					</form>
				</div>
		</section>
		
        <script>
            function table_sort() {
		        console.log("test");
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `.order-inactive span { visibility:hidden; } .order-inactive:hover span { visibility:visible; } .order-active span { visibility: visible; }`;
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
                    slnos();
                    asc = !asc;
                    })
                });
            }
            function convertDate(d){ var p = d.split("."); return (p[2]+p[1]+p[0]); }
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
                    slnos();
                    asc = !asc;
                    })
                });
            }

            function convertNumber(d) { var p = intval(d); return (p); }

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
                    slnos();
                    asc = !asc;
                    })
                });
            }
            function slnos(){
                var rcount = document.getElementById("tbody1").rows.length;
                var myTable = document.getElementById('tbody1');
                var j = 0;
                for(var i = 1;i <= rcount;i++){ j = i - 1; myTable.rows[j].cells[0].innerHTML = i; }
            }
            table_sort();
            table_sort2();
            table_sort3();
        </script>
		<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer><?php } ?>
		<script src="../loading_page_out.js"></script>
	</body>
	
</html>
<?php include "header_foot.php"; ?>
