<?php
	//cus_tcssummary.php
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	include "../config.php";
	include "header_head.php";
	include "number_format_ind.php";
    
	$today = date("Y-m-d");
	$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }
    
    if(isset($_POST['submit']) == true){ $fromdate = $_POST['fromdate']; $todate = $_POST['todate']; } else{ $fromdate = $todate = $today; }
	
	$exoption = "displaypage";
	$url = "../PHPExcel/Examples/tcsSummary-Excel.php?fromdate=".$fromdate."&todate=".$todate;
	
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
					<td><?php echo $row['cdetails']; ?></td> <?php } ?></td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<label style="font-weight:bold;" class="reportheaderlabel">Freight on Jals Summary Report</label>&ensp;
						<label class="reportheaderlabel"><b style="color: green;">From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fromdate)); ?></label>&ensp;
						<label class="reportheaderlabel"><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($todate)); ?></label>
					</td>
				</tr>
			</table>
		</header>
	<?php } ?>
		<section class="content" align="center">
				<div class="col-md-12" align="center">
					<form action="cus_tcssummary.php" method="post">
						<table class="table1" style="min-width:auto;line-height:23px;">
						<?php if($exoption == "displaypage") { ?>
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<td colspan="20">
										<label class="reportselectionlabel">From date</label>&nbsp;
										<input type="text" name="fromdate" id="datepickers" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($fromdate)); ?>"/>
									    &ensp;&ensp;
										<label class="reportselectionlabel">To Date</label>&nbsp;
										<input type="text" name="todate" id="datepickers1" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($todate)); ?>"/>
									    &ensp;&ensp;
										<button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
									</td>
								</tr>
							</thead>
						<?php } ?>
							<thead class="thead2" style="background-color: #98fb98;">
								<th>Sl No.</th>
								<th>Customer</th>
								<th>TCS Cost</th>
							</thead>
							<tbody class="tbody1" style="background-color: #f4f0ec;">
							<?php
								if($_POST['export'] != "exportexcel"){
									$fromdate = date("Y-m-d",strtotime($fromdate)); $todate = date("Y-m-d",strtotime($todate));
                                    $sql = "SELECT SUM(tcdsamt) as amount,customercode as ccode FROM `customer_sales` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `tcdsamt` != '0' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' GROUP BY `customercode` ORDER BY `customercode` ASC";
									$query = mysqli_query($conn,$sql); $old_inv = ""; $finaltotal = 0; $sl = 1;
									while($row = mysqli_fetch_assoc($query)){ $cus_amt[$row['ccode']] = $row['amount']; }
                                    foreach($cus_code as $ccode){
                                        if(!empty($cus_amt[$ccode])){
                                            echo "<tr>";
                                            echo "<td style='text-align:left;'>".$sl++."</td>";
                                            echo "<td style='text-align:left;'>".$cus_name[$ccode]."</td>";
                                            $vals = ""; $vals = "?fromdate=$fromdate&todate=$todate&ccode=$ccode";
                                            echo "<td style='text-align:right;'><a href='cus_tcsdetailed.php".$vals."' target='_BLANK'>".number_format_ind($cus_amt[$ccode])."</a></td>";
                                            echo "</tr>";
                                            $finaltotal += (float)$cus_amt[$ccode];
                                        }
									}
								?>
									<tr class="foottr" style="background-color: #98fb98;">
										<td colspan="2" style='text-align:center;'><b>Total</b></td>
										<td><?php echo number_format_ind($finaltotal); ?></td>
									</tr>
								<?php
								}
								?>
							</tbody>
						</table>
					</form>
				</div>
		</section>
		<?php if($exoption == "displaypage") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer><?php } ?>
		<script src="../loading_page_out.js"></script>
	</body>
	
</html>
<?php include "header_foot.php"; ?>
