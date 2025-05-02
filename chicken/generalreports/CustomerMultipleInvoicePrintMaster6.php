<?php
	//CustomerMultipleInvoicePrintMaster6.php
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	include "../config.php";
	include "header_head.php";
	include "number_format_ind.php";
	$cid = $_GET['cid'];
	$today = date("Y-m-d");
	if(isset($_POST['submit']) == true){
		if($_POST['grpcode'] == "all"){
			$grpdetails = "";
		}
		else{
			$gcode = $_POST['grpcode'];
			$grpdetails = " AND `groupcode` = '$gcode'";
		}
	}
	$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%'".$grpdetails." AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $cus_name[$row['code']] = $row['name']; $cus_code[$row['code']] = $row['code']; $cus_mob[$row['code']] = $row['mobileno']; }
	$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%C%' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $grp_name[$row['code']] = $row['description']; $grp_code[$row['code']] = $row['code']; }
	$fdate = $tdate = date("d.m.Y");
	$sql = "SELECT * FROM `master_loadingscreen` WHERE `project` LIKE 'CTS' AND `type` LIKE 'PDF' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conns,$sql);
	while($row = mysqli_fetch_assoc($query)){ $loading_title = $row['title']; $loading_stitle = $row['sub_title']; }
?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="reportstyle.css">
		<link rel="stylesheet" type="text/css" href="loading_screen.css">
		<style>
			.thead2,.tbody1 {
				padding: 1px;
				font-size: 12px;
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
		<header align="center">
			<table align="center" class="reportheadermenu">
				<tr>
				<?php
					$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?>
					<td><img src="../<?php echo $row['logopath']; ?>" height="150px"/></td>
					<td><?php echo $row['cdetails']; ?></td> <?php } ?>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<h3>Customer Multi-Invoice Print</h3>
					</td>
				</tr>
			</table>
		</header>
		<section class="content" align="center">
			<div class="col-md-12" align="center">
				<table class="table1" style="min-width:auto;line-height:23px;">
					<thead class="thead1" style="background-color: #98fb98;">
						<form action="CustomerMultipleInvoicePrintMaster6.php?cid=<?php echo $cid; ?>" method="post" onsubmit="return checkval()">
							<tr>
								<td colspan="4">
									<label class="reportselectionlabel">Group</label>&nbsp;
									<select name="grpcode" id="grpcode" class="formcontrol">
									<option value="all" <?php if($_POST['grpcode'] == "all"){ echo 'selected'; } ?>>-All-</option>
									<?php
									foreach($grp_code as $gc){
									?>
									<option value="<?php echo $gc; ?>" <?php if($_POST['grpcode'] == $gc){ echo 'selected'; } ?>><?php echo $grp_name[$gc]; ?></option>
									<?php
									}
									?>
									</select>&ensp;&ensp;
									<label class="reportselectionlabel">Select All</label>&nbsp;
									<input type="checkbox" name="checkall" id="checkall" onchange="checkedall()"/>&ensp;&ensp;
									<button type="submit" class="btn btn-success" name="submit" id="submit">Submit</button>&ensp;&ensp;
								</td>
							</tr>
						</form>
					</thead>
					<form action="../printformatlibrary/Examples/CustomerMultipleInvoicePrint6.php?cid=<?php echo $cid; ?>" method="post" onsubmit="return checkval()">
						<thead class="thead1" style="background-color: #98fb98;">
							<tr>
								<td colspan="4">
									<label class="reportselectionlabel">Date</label>&nbsp;
									<input type="text" name="pdates" id="datepickers" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>"/>&ensp;&ensp;
								</td>
							</tr>
						</thead>
						<thead class="thead2" style="background-color: #98fb98;">
							<tr>
								<th>Sl.No.</th>
								<th>Selection</th>
								<th>Customer Name</th>
								<th>Customer Mobile</th>
							</tr>
						</thead>
						<tbody class="tbody1" id="myTable" style="background-color: #f4f0ec;">
							
						<?php
							$c = 0;
							foreach($cus_code as $ccode){
								$c = $c + 1;
                                $name = $cus_name[$ccode];
                                $mobe = $cus_mob[$ccode];
                                echo "<tr>";
                                echo "<td style='width:100px;text-align:center;'>".$c."</td>";
                                echo "<td style='width:100px;text-align:center;'><input type='checkbox' name='ccode[$c]' id='ccode[$c]' value='$ccode' /></td>";
                                echo "<td style='padding-left:10px;text-align:left;'><input type='text' name='cusname[$c]' id='cusname[$c]' class='form-control' value='$name' style='padding:0; padding-left:1px;width:250px;height:16px;border:none;background:inherit;text-decoration:none;font-size:13px;' readonly /></td>";
                                echo "<td style='padding-left:10px;text-align:left;'><input type='text' name='cmobile[$c]' id='cmobile[$c]' class='form-control' value='$mobe' style='padding:0; padding-left:1px;width:450px;height:16px;border:none;background:inherit;text-decoration:none;font-size:13px;' readonly /></td>";
                                echo "</tr>";
							}
						?>
							<tr>
								<td colspan="4" style="text-align:center;"><button type="submit" class="btn btn-success" name="submit" id="submit">Submit</button></td>
							</tr>
						</tbody>
					</form>
				</table>
			</div>
			<div class="ring"><?php echo $loading_title; ?><span></span></div>
			<div class="ring_status" id = "disp_val"></div> 
		</section>
		<script>
            function checkval(){
                var incr = '<?php echo $c; ?>';
                var l = true; var c = d = ""; var a = g = 0; var e = [];
                var send_type = "send_pdf";
                for(var b = 1;b <= incr;b++){
                    if(l == true){
                        c = document.getElementById("ccode["+b+"]");
                        if(c.checked == true){
                            a++;
                            if(send_type == "send_pdf"){
                                d = document.getElementById("cmobile["+b+"]").value;
                                if(d.match(",")){
                                    e = d.split(",");
                                    g = 0;
                                    for(var f = 0;f < e.length;f++){ if(e[f].length == 10){ g++; } }
                                    if(g == 0){
                                        alert("Please enter appropriate Mobile No to send Invoices");
                                        document.getElementById("cusname["+b+"]").style.color = "red";
                                        document.getElementById("cmobile["+b+"]").style.color = "red";
                                        document.getElementById("cmobile["+b+"]").focus();
                                        l = false;
                                    }
                                }
                                else{
                                    d = document.getElementById("cmobile["+b+"]").value;
                                    if(d.length != 10){
                                        alert("Please enter appropriate Mobile No to send Invoices");
                                        document.getElementById("cusname["+b+"]").style.color = "red";
                                        document.getElementById("cmobile["+b+"]").style.color = "red";
                                        document.getElementById("cmobile["+b+"]").focus();
                                        l = false;
                                    }
                                }
                            }
                        }
                    }
				}
                if(l == true){
                    if(a > 0){
                        l = true;
                    }
                    else{
                        alert("select atlest one customer to send Invoices");
                        l = false;
                    }
                }
                if(l == true){
					document.getElementsByClassName("ring")[0].style.display = "block";
					document.getElementsByClassName("ring_status")[0].style.display = "block";
					document.getElementById("disp_val").innerHTML = '<?php echo $loading_stitle; ?>';
                    return true;
                }
                else{
					document.getElementsByClassName("ring")[0].style.display = "none";
					document.getElementsByClassName("ring_status")[0].style.display = "none";
					document.getElementById("disp_val").innerHTML = "";
                    return false;
                }
            }
			function checkedall(){
                var incr = '<?php echo $c; ?>';
				var a = document.getElementById("checkall");
                var send_type = "send_pdf";
                var c = d = ""; var e = []; var g = 0;
				if(a.checked == true){
					for(var b = 1;b <= incr;b++){
					    c = document.getElementById("ccode["+b+"]");
						
                        if(send_type == "send_pdf"){
                            d = document.getElementById("cmobile["+b+"]").value;
                            if(d.match(",")){
                                e = d.split(",");
                                g = 0;
                                for(var f = 0;f < e.length;f++){ if(e[f].length == 10){ g++; } }
                                if(g == 0){ }
                                else{
                                    c.checked = true;
                                }
                            }
                            else{
                                d = document.getElementById("cmobile["+b+"]").value;
                                if(d.length != 10){ }
                                else{
                                    c.checked = true;
                                }
                            }
                        }
                        else{
                            c.checked = true;
                        }
					}
				}
				else{
					for(var b = 1;b <= incr;b++){
					    c = document.getElementById("ccode["+b+"]");
						c.checked = false;
					}
				}
			}
		</script>
		<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<script src="../loading_page_out.js"></script>
	</body>
	
</html>
<?php include "header_foot.php"; ?>
