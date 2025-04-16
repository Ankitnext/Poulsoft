<?php
	session_start(); include "newConfig.php";
	include "header_head.php";
	$sql='SHOW COLUMNS FROM `main_contactdetails`'; $query=mysqli_query($conn,$sql); $existing_col_names = array();
	while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
	if(in_array("fixed_qty", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `fixed_qty` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Customer Min Quantity' AFTER `active`"; mysqli_query($conn,$sql); }
		
	//Supervisor
	$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Customer Master' AND `field_function` = 'Display Supervisor selection' AND `user_access` = 'all'";
	$query = mysqli_query($conn,$sql); $d_cnt = mysqli_num_rows($query); $dsprm_flag = 0;
	if((int)$d_cnt > 0){ while($row = mysqli_fetch_assoc($query)){ $dsprm_flag = $row['flag']; } }
	else{ $sql = "INSERT INTO `extra_access` (`field_name`,`field_function`,`user_access`,`flag`) VALUES ('Customer Master','Display Supervisor selection','all','0');"; mysqli_query($conn,$sql); }
	if((int)$dsprm_flag == 1){
		//Supervisor Details
		$sql = "SELECT * FROM `chicken_designation` WHERE `description` LIKE '%supervisor%' AND `dflag`= '0' ORDER BY `description` ASC";
		$query = mysqli_query($conn,$sql); $desig_alist = array();
		while($row = mysqli_fetch_assoc($query)){ $desig_alist[$row['code']] = $row['code']; }

		$desig_list = implode("','", $desig_alist);
		$sql = "SELECT * FROM `chicken_employee` WHERE `desig_code` IN ('$desig_list') AND `dflag`= '0' ORDER BY `name` ASC";
		$query = mysqli_query($conn,$sql); $csupr_code = $csupr_name = array();
		while($row = mysqli_fetch_assoc($query)){ $csupr_code[$row['code']] = $row['code']; $csupr_name[$row['code']] = $row['name']; }
	}

	//Salesman
	$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Customer Master' AND `field_function` = 'Display Salesman selection' AND `user_access` = 'all'";
	$query = mysqli_query($conn,$sql); $d_cnt = mysqli_num_rows($query); $dsm_flag = 0;
	if((int)$d_cnt > 0){ while($row = mysqli_fetch_assoc($query)){ $dsm_flag = $row['flag']; } }
	else{ $sql = "INSERT INTO `extra_access` (`field_name`,`field_function`,`user_access`,`flag`) VALUES ('Customer Master','Display Salesman selection','all','0');"; mysqli_query($conn,$sql); }
	if((int)$dsm_flag == 1){
		//Salesman Details
		$sql = "SELECT * FROM `chicken_designation` WHERE `description` LIKE '%sales%' AND `dflag`= '0' ORDER BY `description` ASC";
		$query = mysqli_query($conn,$sql); $desig_alist = array();
		while($row = mysqli_fetch_assoc($query)){ $desig_alist[$row['code']] = $row['code']; }

		$desig_list = implode("','", $desig_alist);
		$sql = "SELECT * FROM `chicken_employee` WHERE `desig_code` IN ('$desig_list') AND `dflag`= '0' ORDER BY `name` ASC";
		$query = mysqli_query($conn,$sql); $sman_code = $sman_name = array();
		while($row = mysqli_fetch_assoc($query)){ $sman_code[$row['code']] = $row['code']; $sman_name[$row['code']] = $row['name']; }
	}

	//Area
	$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Customer Master' AND `field_function` = 'Display Area selection' AND `user_access` = 'all'";
	$query = mysqli_query($conn,$sql); $d_cnt = mysqli_num_rows($query); $dam_flag = 0;
	if((int)$d_cnt > 0){ while($row = mysqli_fetch_assoc($query)){ $dam_flag = $row['flag']; } }
	else{ $sql = "INSERT INTO `extra_access` (`field_name`,`field_function`,`user_access`,`flag`) VALUES ('Customer Master','Display Area selection','all','0');"; mysqli_query($conn,$sql); }
	if((int)$dam_flag > 0){
		$sql = "SELECT * FROM `main_areas` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
		$query = mysqli_query($conn, $sql); $area_code = $area_name = array();
		while($row = mysqli_fetch_assoc($query)){ $area_code[$row['code']] = $row['code']; $area_name[$row['code']] = $row['description']; }
	}
?>
<html>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Fill all required fields</h1>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">CoA</a></li>
				<li class="active">Create Item</li>
				<li class="active">Add</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
			<?php
				$today = date("Y-m-d");
				$fdate = date("d.m.Y",strtotime($today));
				$id = $_GET['id'];
				$description = "";
				$category = "";
				$sunits = "";
				$cunits = "";
				if($count67 > 0){
				$sql = "SELECT * FROM `inv_itemdetails` WHERE `id` = '$id'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$description = $row['description'];
					$category = $row['category'];
					$sunits = $row['sunits'];
					$cunits = $row['cunits'];
				}
				}
			?> 
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
							<form action="main_updatecustomerdetails.php" method="get" role="form" onSubmit="return checkval()" name="form_name" id = "form_id" >
								<div class="col-md-12">
									<div class="form-group col-md-2">
										<label>Name<b style="color:red;">&nbsp;*</b></label>
									<input type="text" name="cname" id="cname" class="form-control" placeholder="Enter Name..." onkeyup="validatename(this.id)" onchange="checkcontacts()">
									</div>
									<div class="form-group col-md-2">
										<label>Mobile / Phone<b style="color:red;">&nbsp;*</b></label>
									<input type="text" name="mobile" id="mobile" class="form-control" placeholder="Enter Mobile..." onkeyup="validatmultimobile(this.id)">
									</div>
									<div class="form-group col-md-2">
										<label>Contact Type<b style="color:red;">&nbsp;*</b></label>
										<select name="stype" id="stype" class="form-control select2" style="width: 100%;" onchange="setgroup();">
											<option value="select">select</option>
											<option value="C">Customer</option>
											<option value="S&C">Supplier &amp; Customer</option>
										</select>
									</div>
									<div class="form-group col-md-2">
										<label>PAN</label>
									<input type="text" name="pan_no" id="pan_no" class="form-control" placeholder="Enter PAN..." onkeyup="validatename(this.id)" >
									</div>
									<div class="form-group col-md-2">
										<label>Aadhar</label>
									<input type="text" name="aadhar_no" id="aadhar_no" class="form-control" placeholder="Enter Aadhar..." onkeyup="validatename(this.id)" >
									</div>
									<div class="form-group col-md-1" style="visibility:hidden;">
										<label>ECount<b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group col-md-2">
										<label>GSTIN</label>
									<input type="text" name="cgstin" id="cgstin" class="form-control" placeholder="Enter GSTIN..." onkeyup="validatename(this.id)">
									</div>
									<div class="form-group col-md-2">
										<label>Group<b style="color:red;">&nbsp;*</b></label>
										<select name="sgrp" id="sgrp" class="form-control select2" style="width: 100%;" <?php //if((int)$dam_flag > 0){ echo 'onchange="fetch_groupareas();"'; }?>>
											<option value="select">select</option>
										</select>
									</div>
									<?php if((int)$dam_flag > 0){ ?>
									<div class="form-group col-md-2">
										<label>Area</label>
										<select name="area_code" id="area_code" class="form-control select2" style="width: 100%;">
											<option value="select">select</option>
											<?php foreach($area_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $area_name[$scode]; ?></option><?php } ?>
										</select>
									</div>
									<?php } ?>
									<div class="form-group col-md-2">
										<label>Address</label>
									<textarea name="saddress" id="saddress" class="form-control" style="height:33px" placeholder="Enter Address..."></textarea>
									</div>
									<?php
									if((int)$dsm_flag > 0){
									?>
									<div class="form-group col-md-2">
										<label>Salesman<b style="color:red;">&nbsp;*</b></label>
										<select name="sman_code" id="sman_code" class="form-control select2" style="width: 100%;">
											<option value="select">select</option>
											<?php foreach($sman_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sman_name[$scode]; ?></option><?php } ?>
										</select>
									</div>
									<?php
									}
									?>
									<?php
									if((int)$dsprm_flag > 0){
									?>
									<div class="form-group col-md-2">
										<label>Supervisor<b style="color:red;">&nbsp;*</b></label>
										<select name="supr_code" id="supr_code" class="form-control select2" style="width: 100%;">
											<option value="select">select</option>
											<?php foreach($csupr_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $csupr_name[$scode]; ?></option><?php } ?>
										</select>
									</div>
									<?php
									}
									?>
									<!--<div class="form-group col-md-2">
										<label>Quantity</label>
									<input type="text" name="fixed_qty" id="fixed_qty" class="form-control" placeholder="Enter Quantity..." onkeyup="validatename(this.id)">
									</div>-->
								</div>
								<div class="col-md-12">
									<label>
										<input type="checkbox" name="credits" id="credits" value="CD" onClick="showDetails(this.id,this.value)">Credit Limit &amp; Terms <!--Fill Credit / Debit Details-->
									</label>
								</div>
								<div class="col-md-12" id ="creditdetails" style="display:none">
									<div class="form-group col-md-1"></div>
									<div class="form-group col-md-4" style="width:auto;">
										<label>Credit Limit<b style="color:red;">&nbsp;*</b></label>
										<input type="text" name="climit" id="climit" class="form-control" placeholder="Enter amount Limit..." onchange="validateamount(this.id)">
									</div>
									<div class="form-group col-md-4" style="width:auto;">
										<label>Credit Days<b style="color:red;">&nbsp;*</b></label>
										<input type="text" name="cterms" id="cterms" class="form-control" placeholder="Enter Days Limit..." onkeyup="validatenum(this.id)">
									</div>
								</div>
								<div class="col-md-12">
									<label>
										<input type="checkbox" name="crdrfields" id="crdrfields" value="OB" onClick="showDetails(this.id,this.value)">Opening Balances <!--Fill Credit / Debit Details-->
									</label>
								</div>
								<div class="col-md-12" id ="crdrdetails" style="display:none">
									<div class="form-group col-md-1"></div>
									<div class="form-group col-md-2" style="width:auto;">
										<label>date<b style="color:red;">&nbsp;*</b></label>
										<input type="text" class="form-control" name="crdrdate" value="<?php //echo $fdate; ?>" id="datepickers">
									</div>
									<div class="form-group col-md-2">
										<label>OB. Type<b style="color:red;">&nbsp;*</b></label>
										<select name="obtype" id="obtype" class="form-control select2" style="width: 100%;">
											<option value="select">select</option>
											<option value="Cr">Credit</option>
											<option value="Dr">Debit</option>
										</select>
									</div>
									<div class="form-group col-md-3">
										<label>Amount<b style="color:red;">&nbsp;*</b></label>
										<input type="text" name="crdramt" id="crdramt" class="form-control" placeholder="Enter Amount..." onchange="validateamount(this.id)">
									</div>
									<div class="form-group col-md-3">
										<label>Remarks</label>
										<textarea name="obremarks" id="obremarks" class="form-control" style="height:33px" placeholder="Enter Narration..."></textarea>
									</div>
								</div>
								
								<div class="col-md-12">
									<label>
										<input type="checkbox" id="bankfields" name="bankfields" value="BD" onClick="showDetails(this.id,this.value)">Bank Details <!--Fill Credit / Debit Details-->
									</label>
								</div>
								<div class="col-md-12" id="bankdetails" style="display:none">
									<div class="form-group col-md-1"></div>
									<div class="form-group col-md-2">
										<label>Bank Name</label>
										<input type="bname" name="bname" id="crdramt" class="form-control" placeholder="Enter Bank Name..." onkeyup="validatename(this.id)">
									</div>
									<div class="form-group col-md-2">
										<label>Branch</label>
										<input type="text" name="branch" id="branch" class="form-control" placeholder="Enter Branch Name..." onkeyup="validatename(this.id)">
									</div>
									<div class="form-group col-md-3">
										<label>Account No.</label>
										<input type="text" name="accno" id="accno" class="form-control" placeholder="Enter Account No. ..." onkeyup="validatename(this.id)">
									</div>
									<div class="form-group col-md-2">
										<label>IFSC Code</label>
										<input type="text" name="ifsccode" id="ifsccode" class="form-control" placeholder="Enter IFSC Code..." onkeyup="validatename(this.id)">
									</div>
									<div class="form-group col-md-2">
										<label>MICR Code</label>
										<input type="text" name="micrno" id="micrno" class="form-control" placeholder="Enter MICR Code..." onkeyup="validatename(this.id)">
									</div>
								</div>
								<div class="col-md-12" style="visibility:hidden;">
									<div class="form-group col-md-2">
										<label>Duplicate Flag</label>
										<input type="text" name="dupflag" id="dupflag" class="form-control" value="0">
									</div>
								</div>
								<div class="box-body" align="center">
									<button type="submit" name="submittrans" id="submittrans" value="addpage" class="btn btn-flat btn-social btn-linkedin">
										<i class="fa fa-save"></i> Save
									</button>&ensp;&ensp;&ensp;&ensp;
									<button type="button" name="cancelled" id="cancelled" class="btn btn-flat btn-social btn-google" onClick="redirection_page()">
										<i class="fa fa-trash"></i> Cancel
									</button>
								</div>
							</form>
						  <!-- /.form-group -->
						</div>
					</div>
				</div>
			</div>
		</section>
		<script>
			function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submittrans").style.visibility = "hidden";
				var a = document.getElementById("cname").value;
				var b = document.getElementById("mobile").value;
				var c = document.getElementById("stype").value;
				//var d = document.getElementById("cgstin").value;
				var e = document.getElementById("sgrp").value;
				var g = document.getElementById("credits");
				var j = document.getElementById("crdrfields");
				//var t = document.getElementById("dupflag");
				var aa = 0;
				if(a.length == 0){ alert("Enter contact Name ..!"); document.getElementById("cname").focus(); aa = 1; }
				else if(b.length == 0){ alert("Enter Phone / Mobile No. ..!"); document.getElementById("mobile").focus(); aa = 1; }
				else if(c.match("select")){ alert("Select Contacting Type ..!"); document.getElementById("stype").focus(); aa = 1; }
				else if(e.match("select")){ alert("Select Group Information ..!"); document.getElementById("sgrp").focus(); aa = 1; }
				else if(g.checked == true){
					var h = document.getElementById("climit").value;
					var i = document.getElementById("cterms").value;
					if(h.length == 0) { alert("Enter Credit limit ..!"); document.getElementById("climit").focus(); aa = 1; }
					else if(i.length == 0) { alert("Enter Credit Days ..!"); document.getElementById("cterms").focus(); aa = 1; }
					else if(j.checked == true){
						var k = document.getElementById("datepicker").value;
						var l = document.getElementById("obtype").value;
						var m = document.getElementById("crdramt").value;
						var n = document.getElementById("obremarks").value;
						if(k.length == 0) { alert("Select Appropriate Date..!"); document.getElementById("datepicker").focus(); aa = 1; }
						else if(l.match("select")) { alert("Select OB. Type..!"); document.getElementById("obtype").focus(); aa = 1; }
						else if(m.length == 0) { alert("Enter Opening Amount..!"); document.getElementById("crdramt").focus(); aa = 1; }
						else if(n.lenght == 0) { alert("Enter Remarks..!"); document.getElementById("obremarks").focus(); aa = 1; }
						else { aa = 0; }
					}
				}
				else if(j.checked == true){
					var k = document.getElementById("datepicker").value;
					var l = document.getElementById("obtype").value;
					var m = document.getElementById("crdramt").value;
					var n = document.getElementById("obremarks").value;
					if(k.length == 0) { alert("Select Appropriate Date..!"); document.getElementById("datepicker").focus(); aa = 1; }
					else if(l.match("select")) { alert("Select OB. Type..!"); document.getElementById("obtype").focus(); aa = 1; }
					else if(m.length == 0) { alert("Enter Opening Amount..!"); document.getElementById("crdramt").focus(); aa = 1; }
					else if(n.lenght == 0) { alert("Enter Remarks..!"); document.getElementById("obremarks").focus(); aa = 1; }
					else { aa = 0; }
				}
					/*
				if(o.checked == true){
					var o = document.getElementById("bankfields");
					var p = document.getElementById("bname").value;
					var q = document.getElementById("branch").value;
					var r = document.getElementById("accno").value;
					var s = document.getElementById("ifsccode").value;
					if(p.lenght == 0) { alert("Enter Bank Name..!"); document.getElementById("bname").focus(); aa = 1; }
					if(q.lenght == 0) { alert("Enter Branch Name..!"); document.getElementById("branch").focus(); aa = 1; }
					if(r.lenght == 0) { alert("Enter IFSC Code..!"); document.getElementById("accno").focus(); aa = 1; }
					if(s.lenght == 0) { alert("Enter Remarks..!"); document.getElementById("ifsccode").focus(); aa = 1; }
					else { aa = 0; }
				}*/
				var df = document.getElementById("dupflag").value;
				if(aa == 1){
					document.getElementById("ebtncount").value = "0";
					document.getElementById("submittrans").style.visibility = "visible";
					return false;
				}
				else {
					if(df == 0){
						return true;
					}
					else {
						document.getElementById("ebtncount").value = "0";
						document.getElementById("submittrans").style.visibility = "visible";
						alert("Customer/Supplier Details are available with the same name.\n Kindly change the name");
						return false;
					}
				}
			}
			function checkcontacts(){
				var b = document.getElementById("cname").value;
				var c = "new";
				if(!b.length == 0){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "main_getcontactdetails.php?cname="+b+"&cid="+c;
					var asynchronous = true;
					oldqty.open(method, url, asynchronous);
					oldqty.send();
					oldqty.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var f = this.responseText;
							if(f.match("ok")){
								document.getElementById("dupflag"). value = 0;
							}
							else {
								alert("Customer/Supplier Details are available with the same name.\n Kindly change the name");
								document.getElementById("dupflag"). value = 1;
							}
						}
					}
				}
				else { }
			}
			function validatename(x) {
				expr = /^[a-zA-Z0-9 (.)_-]*$/;
				var a = document.getElementById(x).value;
				if(a.length > 50){
					a = a.substr(0,a.length - 1);
				}
				if(!a.match(expr)){
					a = a.replace(/[^a-zA-Z0-9 (.)_-]/g, '');
				}
				document.getElementById(x).value = a;
			}
			function validatenum(x) {
				expr = /^[0-9]*$/;
				var a = document.getElementById(x).value;
				if(a.length > 50){
					a = a.substr(0,a.length - 1);
				}
				if(!a.match(expr)){
					a = a.replace(/[^0-9]/g, '');
				}
				document.getElementById(x).value = a;
			}
			function validatmultimobile(x) {
				expr = /^[0-9,]*$/;
				var a = document.getElementById(x).value;
				if(a.length > 50){
					a = a.substr(0,a.length - 1);
				}
				if(!a.match(expr)){
					a = a.replace(/[^0-9,]/g, '');
				}
				document.getElementById(x).value = a;
			}
			function validateamount(x) {
				expr = /^[0-9.]*$/;
				var a = document.getElementById(x).value;
				if(a.length > 50){
					a = a.substr(0,a.length - 1);
				}
				while(!a.match(expr)){
					a = a.replace(/[^0-9.]/g, '');
				}
				if(a == ""){ a = 0; } else { }
				var b = parseFloat(a).toFixed(2);
				document.getElementById(x).value = b;
			}
			function showDetails(a,b) {
				var checkBox = document.getElementById(a);
				if(b.match("CD")){
					var text = document.getElementById("creditdetails");
					if (checkBox.checked == true){
						text.style.display = "block";
					}
					else {
						text.style.display = "none";
					}
				}
				else if(b.match("OB")){
					var text = document.getElementById("crdrdetails");
					if (checkBox.checked == true){
						text.style.display = "block";
					}
					else {
						text.style.display = "none";
					}
				}
				else if(b.match("BD")){
					var text = document.getElementById("bankdetails");
					if (checkBox.checked == true){
						text.style.display = "block";
					}
					else {
						text.style.display = "none";
					}
				}
				else {}
				
			}
			function setgroup() {
				var a = document.getElementById("sgrp").value;
				var b = document.getElementById("stype").value;
				removeAllOptions(document.getElementById("sgrp"));
				
				myselect1 = document.getElementById("sgrp"); 
				theOption1=document.createElement("OPTION"); 
				theText1=document.createTextNode("Select"); 
				theOption1.value = "select"; 
				theOption1.appendChild(theText1); 
				myselect1.appendChild(theOption1);
				<?php
					$sql="SELECT * FROM `main_groups` WHERE `active` = '1' ORDER BY `description` ASC";
					$query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ echo "if(b == '$row[gtype]'){"; ?> 
						theOption1=document.createElement("OPTION");
						theText1=document.createTextNode("<?php echo $row['description']; ?>"); 
						theOption1.value = "<?php echo $row['code']; ?>"; 
						theOption1.appendChild(theText1); myselect1.appendChild(theOption1);	
					<?php echo "}"; } ?>
					
					
			}
			function fetch_groupareas(){
				var grp_code = document.getElementById("sgrp").value;
				removeAllOptions(document.getElementById("area_code"));
				var type = "add";
				if(grp_code != "select"){
					var areas = new XMLHttpRequest();
					var method = "GET";
					var url = "chicken_fetch_groupareas.php?grp_code="+grp_code+"&type="+type;
					var asynchronous = true;
					areas.open(method, url, asynchronous);
					areas.send();
					areas.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var area_opt = this.responseText;
							$('#area_code').append(area_opt);
						}
					}
				}
				else { }
			}
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
			/*document.getElementById("form_id").onkeypress = function(e) {
				var key = e.charCode || e.keyCode || 0;     
				if (key == 13) {
				//alert("No Enter!");
				e.preventDefault();
				}
			}*/
			document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function () { $('#submittrans').click(); }); } } else{ } });
		</script>
		<?php include "header_foot.php"; ?>
		<script>
			function redirection_page(){
				window.location.href = "main_displaycustomers.php";
			}
		</script>
	</body>
</html>