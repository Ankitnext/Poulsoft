<?php
	session_start(); include "newConfig.php";
	include "header_head.php";
?>
<html>
	<head>
		<style>
			.select2-container .select2-selection--single{ box-sizing:border-box; cursor:pointer; display:block; height:23px; user-select:none; -webkit-user-select:none; }
			.select2-container--default .select2-selection--single{background-color:#fff;border:1px solid #aaa;border-radius:4px}
			.select2-container--default .select2-selection--single .select2-selection__rendered{color:#444;line-height:18px}
			.select2-container--default .select2-selection--single .select2-selection__clear{cursor:pointer;float:right;font-weight:bold}
			.select2-container--default .select2-selection--single .select2-selection__placeholder{color:#999}
			.select2-container--default .select2-selection--single .select2-selection__arrow{height:23px;position:absolute;top:1px;right:1px;width:20px}
			.select2-container--default .select2-selection--single .select2-selection__arrow b{border-color:#888 transparent transparent transparent;border-style:solid;border-width:5px 4px 0 4px;height:0;left:50%;margin-left:-4px;margin-top:-2px;position:absolute;top:50%;width:0}
			.form-control { width: 100%; height: 23px; }
			label { line-height: 20px; }
			.disabledbutton{ pointer-events: none; opacity: 0.4; }
			#tab3 tbody td {
				padding: 0px 5px;
			}
			th {
				width: 10%;
				text-align:center;
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Edit Stock Transfer</h1>
			<h5>Please fill mandatory (<b style="color:red;"> * </b>) fields</h5>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Transaction</a></li>
				<li class="active">Stock Transfer Display</li>
				<li class="active">Edit</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
			<?php
				$invoiceid = $_GET['id'];
				$today = date("Y-m-d");
				$fdate = date("d.m.Y",strtotime($today));
				$y = date("y"); $y2 = $y + 1; $m = date("m"); $d = date("d");
				$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$wcode[$row['code']] = $row['code'];
					$wdesc[$row['code']] = $row['description'];
				}
				$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$icode[$row['code']] = $row['code'];
					$idesc[$row['code']] = $row['description'];
				}
				
				$sql = "SELECT * FROM `main_transactionfields` WHERE `field` LIKE 'Stock Transfer' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $jals_flag = $row['jals_flag']; $birds_flag = $row['birds_flag']; }
				if($jals_flag == "" || $jals_flag == NULL){ $jals_flag = 0; }
				if($birds_flag == "" || $birds_flag == NULL){ $birds_flag = 0; }
				
				$visibles = 'style="visibility:visible;"'; $nvisibles = 'style="visibility:hidden;"';
				$sql = "SELECT * FROM `dataentry_daterange` WHERE `active` = '1'";
				$query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$from_date = date("d.m.Y",strtotime($row['fromdate']));
					$to_date = date("d.m.Y",strtotime($row['todate']));
				}
			?>
			
				<div class="box-body" style="min-height:400px;"><br/><br/><br/>
					<div class="row">
						<div class="col-md-18">
							<div class="col-md-18" align="center">
								<form action="main_updatestocktransfer.php" method="post" role="form" onsubmit="return checkval()" name="form_name" id = "form_id" >
									<table style="width:95%;line-height:30px;" id="tab3" align="center">
										<thead>
											<tr style="line-height:30px;">
												<th><label>Trnum<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Date<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>DC No.</label></th>
												<th><label>From Warehouse<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Item<b style="color:red;">&nbsp;*</b></label></th>
												<?php if($jals_flag == 1) { echo '<th><label>Jals<b style="color:red;">&nbsp;*</b></label></th>'; } ?>
												<?php if($birds_flag == 1) { echo '<th><label>Birds<b style="color:red;">&nbsp;*</b></label></th>'; } ?>
												<th><label>Quantity<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Price<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>To Warehouse<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Remarks</label></th>
											</tr>
										</thead>
										<?php
											$cedit = 0;
											$sqledit = "SELECT * FROM `item_stocktransfers` WHERE `trnum` LIKE '$invoiceid' AND `flag` = '0'"; $queryedit = mysqli_query($conn,$sqledit);
											while($rowedit = mysqli_fetch_assoc($queryedit)){
										?>
											<tr style="line-height:30px;">
												<td><input type="text" name="trnum" id="trnum" value="<?php echo $rowedit['trnum']; ?>" class="form-control" readonly ></td>
												<td><input type="text"  name="pdate" id="pdate"class="form-control str_datepickers" value="<?php echo date("d.m.Y",strtotime($rowedit['date'])); ?>" readonly></td>
												<td><input type="text" name="dcno" id="dcno" value="<?php echo $rowedit['dcno']; ?>" class="form-control" style="width:80px;"></td>
												<td><select name="fsector" id="fsector" class="form-control select2" > <option value="select">select</option><?php foreach($wcode as $fcode){ ?> <option <?php if($rowedit['fromwarehouse'] == $fcode){ echo 'selected'; } ?> value="<?php echo $wcode[$fcode]; ?>"><?php echo $wdesc[$fcode]; ?></option> <?php } ?> </select></td>
												<td><select name="code" id="code" class="form-control select2" ><option value="select">select</option><?php foreach($icode as $fcode){ ?> <option <?php if($rowedit['code'] == $fcode){ echo 'selected'; } ?> value="<?php echo $icode[$fcode]; ?>"><?php echo $idesc[$fcode]; ?></option> <?php } ?></select></td>
												<?php if($jals_flag == 1) { echo '<td><input name="jalqty" id="jalqty" class="form-control" value="'.$rowedit['jals'].'" /></td>'; } ?>
												<?php if($birds_flag == 1) { echo '<td><input name="birdqty" id="birdqty" class="form-control" value="'.$rowedit['birds'].'" /></td>'; } ?>
												<td><input type="text" name="qty" id="qty" class="form-control" value="<?php echo $rowedit['quantity']; ?>" onkeyup="granttotalamount()"></td>
												<td><input type="text" name="price" id="price" class="form-control" value="<?php echo $rowedit['price']; ?>" onkeyup="granttotalamount()"></td>
												<td><select name="tsector" id="tsector" class="form-control select2" > <option value="select">select</option> <?php foreach($wcode as $fcode){ ?> <option <?php if($rowedit['towarehouse'] == $fcode){ echo 'selected'; } ?> value="<?php echo $wcode[$fcode]; ?>"><?php echo $wdesc[$fcode]; ?></option> <?php } ?> </select></td>
												<td><textarea name="remark" id="remark" class="form-control" style="height: 23px;"><?php echo $rowedit['remarks']; ?></textarea></td>
											</tr>
										<?php 
											}
										?>
									</table><br/><br/><br/>
									<div class="box-body" align="center">
										<button type="submit" name="submittrans" id="submittrans" value="updatepage" class="btn btn-flat btn-social btn-linkedin">
											<i class="fa fa-save"></i> Save
										</button>&ensp;&ensp;&ensp;&ensp;
										<button type="button" name="cancelled" id="cancelled" class="btn btn-flat btn-social btn-google" onclick="redirection_page()">
											<i class="fa fa-trash"></i> Cancel
										</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php include "header_foot.php"; ?>
		<script src="dist/js/adminlte.min.js"></script>
		<script src="dist/js/demo.js"></script>
		<script>
			function redirection_page(){ window.location.href = "main_displaystocktransfer.php"; }
			function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function checkval(){
				document.getElementById("submittrans").style.visibility = "hidden";
				var b = document.getElementById("pname").value;
				var c = document.getElementById("mode").value;
				var d = document.getElementById("code").value;
				var e = document.getElementById("amount").value;
				var f = document.getElementById("dcno").value;
				var g = document.getElementById("sector").value;
				var h = document.getElementById("cno").value;
				var k = true;
				if(b.match("select")){
					alert("Please select supplier name");
					k = false;
				}
				else if(c.match("select")){
					alert("Please select mode of payment");
					k = false;
				}
				else if(d.match("select")){
					alert("Please select Paying method");
					k = false;
				}
				else if(e == 0 || e == "" || e.lenght == 0){
					alert("Please enter amount");
					k = false;
				}
				else if(f.lenght == 0 || f == ""){
					alert("Please enter Document No.");
					k = false;
				}
				else if(g.match("select")){
					alert("Please select sector");
					k = false;
				}
				else if(c.match("MOD-002")){
					if(h.length == 0 || h == 0){
						alert("Please enter cheque No.");
						k = false;
					}
					else {
						k = true;
					}
				}
				else {
					k = true;
				}
				if(k === true){
					//alert(k);
					return true;
				}
				else if(k == false){
					document.getElementById("submittrans").style.visibility = "visible";
					return false;
				}
				else {
					document.getElementById("submittrans").style.visibility = "visible";
					return false;
				}
			}
			function updatecode(){
				var b = document.getElementById("mode").value;
				removeAllOptions(document.getElementById("code"));
				
				myselect = document.getElementById("code"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("select"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				if(b.match("MOD-001")){
					document.getElementById("cno").style.visibility = "hidden";
					document.getElementById("cdate").style.visibility = "hidden";
					<?php
					$sql="SELECT * FROM `acc_coa` WHERE `ctype` LIKE 'Cash' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?> 
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['description']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
					<?php } ?>
				}
				else if(b.match("MOD-002") || b.match("MOD-004") || b.match("MOD-005")){
					if(b.match("MOD-002")){
						document.getElementById("cno").style.visibility = "visible";
						document.getElementById("cdate").style.visibility = "visible";
					}
					else {
						document.getElementById("cno").style.visibility = "hidden";
						document.getElementById("cdate").style.visibility = "hidden";
					}
					
					<?php
					$sql="SELECT * FROM `acc_coa` WHERE `ctype` LIKE 'Bank' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?> 
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['description']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
					<?php } ?>
				}
				else {
					theOption1=document.createElement("OPTION"); theText1=document.createTextNode("Other"); theOption1.value = "Other"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				}
			}
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
			document.getElementById("form_id").onkeypress = function(e) {
				var key = e.charCode || e.keyCode || 0;     
				if (key == 13) {
				//alert("No Enter!");
				e.preventDefault();
				}
			} 
		</script>
		 <script src="handle_ebtn_as_tbtn.js"></script>
		<footer align="center"> <?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer>
	</body>
</html>