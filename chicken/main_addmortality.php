<?php
	//main_addmortality
	session_start(); include "newConfig.php";
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
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
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Add Mortality</h1>
			<h5>Please fill mandatory (<b style="color:red;"> * </b>) fields</h5>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Transaction</a></li>
				<li class="active">Mortality Display</li>
				<li class="active">Add</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
			<?php
				$today = date("Y-m-d"); $fdate = date("d.m.Y",strtotime($today));
				$cus_code = $cus_name = $sup_code = $sup_name = $sector_code = $sector_name = $item_code = $item_name = array();
				$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$cus_code[$row['code']] = $row['code'];
					$cus_name[$row['code']] = $row['name'];
				}
				$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$sup_code[$row['code']] = $row['code'];
					$sup_name[$row['code']] = $row['name'];
				}
				$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$sector_code[$row['code']] = $row['code'];
					$sector_name[$row['code']] = $row['description'];
				}
				$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Birds' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$item_code[$row['code']] = $row['code'];
					$item_name[$row['code']] = $row['description'];
				}
			?>
				<div class="box-body" style="min-height:400px;">
					<div class="row">
						<div class="col-md-18" align="center">
							<div class="col-md-18" align="center">
								<form action="main_updatemortality.php" method="post" role="form" onsubmit="return checkval()" name="form_name" id = "form_id" >
									<div class="row">
										<div class="form-group col-md-1" style="visibility:hidden;">
											<label>incr<b style="color:red;">&nbsp;*</b></label>
											<input type="text" class="form-control" name="incr" id="incr" value="0">
										</div>
										<div class="form-group col-md-1" style="visibility:hidden;">
											<label>incrs<b style="color:red;">&nbsp;*</b></label>
											<input type="text" class="form-control" name="incrs" id="incrs" value="0">
										</div>
										<div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
											<label>Enter Count<b style="color:red;">&nbsp;*</b></label>
											<input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
										</div>
									</div>
									<table style="width:100%;line-height:30px;" id="tab3" align="center">
										<thead>
											<tr style="line-height:30px;">
												<th style="text-align:center;"><label>Date<b style="color:red;">&nbsp;*</b></label></th>
												<th style="text-align:center;"><label>Item<b style="color:red;">&nbsp;*</b></label></th>
												<th style="text-align:center;"><label>Birds<b style="color:red;">&nbsp;*</b></label></th>
												<th style="text-align:center;"><label>Weight<b style="color:red;">&nbsp;*</b></label></th>
												<th style="text-align:center;"><label>Price</label></th>
												<th style="text-align:center;"><label>Amount</label></th>
												<th style="text-align:center;"><label>Mortality On</label></th>
												<th style="text-align:center;"><label>Customer / Warehouse</label></th>
												<th style="text-align:center;"><label>Remarks</label></th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<tr id="tblrow[0]" style="line-height:30px;">
												<td><input type="text" name="pdate[]" id="pdate[0]" class="form-control datepickers" value="<?php echo $fdate; ?>" style="min-width:90px;"></td>
												<td><select name="item[]" id="item[0]" class="form-control select2" style="width:180px;"><option value="select">select</option><?php foreach($item_code as $icode){ ?><option value="<?php echo $icode; ?>"><?php echo $item_name[$icode]; ?></option><?php } ?></select></td>
												<td><input type="text" name="birds[]" id="birds[0]" class="form-control" style="min-width:90px;" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)"></td>
												<td><input type="text" name="quantity[]" id="quantity[0]" class="form-control" style="min-width:90px;" onkeyup="validatenum(this.id);calculateamount(this.id);" onchange="validateamount(this.id)"></td>
												<td><input type="text" name="price[]" id="price[0]" class="form-control" style="min-width:90px;" onkeyup="validatenum(this.id);calculateamount(this.id);" onchange="validateamount(this.id)"></td>
												<td><input type="text" name="amount[]" id="amount[0]" class="form-control" style="min-width:90px;" readonly ></td>
												<td><select name="mtype[]" id="mtype[0]" class="form-control select2" style="width: 180px;" onchange="setgroup(this.id)"><option value="select">select</option><option value="customer">Customer</option><option value="supplier">Supplier</option><option value="sector">Warehouse</option></select></td>
												<td><select name="ccode[]" id="ccode[0]" class="form-control select2"style="width:180px;"><option value="select">select</option></select></td>
												<td><textarea name="remark[]" id="remark[0]" class="form-control" style="height: 23px;"></textarea></td>
												<td><a href="JavaScript:Void(0);" name="addval[]" id="addval[0]" onclick="rowgen()"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval[0]" onclick="rowdes()" style="visibility:hidden;"><i class="fa fa-minus" style="color:red;"></i></a></td>
											</tr>
										</tbody>
									</table><br/><br/><br/>
									<div class="box-body" align="center">
										<button type="submit" name="submittrans" id="submittrans" value="addpage" class="btn btn-flat btn-social btn-linkedin">
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
			function calculateamount(x){
				var a = x.split("["); var b = a[1].split("]"); var c = b[0];
				var qty = document.getElementById("quantity["+c+"]").value;
				var price = document.getElementById("price["+c+"]").value;
				if(qty == "" || qty.length == 0){ qty = 0; }
				if(price == "" || price.length == 0){ price = 0; }
				var amount = parseFloat(qty) * parseFloat(price);
				document.getElementById("amount["+c+"]").value = amount.toFixed(2);
			}
			function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submittrans").style.visibility = "hidden";
				var a = document.getElementById("incr").value;
				var l = true; var b = h = 0; var c = d = e = f = g = i = j = k = "";
				for (b = 0;b <= a;b++){
					if(l == true){
						c = document.getElementById("pdate["+b+"]").value;
						d = document.getElementById("item["+b+"]").value;
						e = document.getElementById("quantity["+b+"]").value;
						f = document.getElementById("price["+b+"]").value;
						g = document.getElementById("mtype["+b+"]").value;
						i = document.getElementById("ccode["+b+"]").value;
						j = document.getElementById("birds["+b+"]").value;
						h = b + 1;
						if(c.length == 0 || c == ""){
							alert("Please select Date in row : "+h);
							document.getElementById("pdate["+b+"]").focus();
							l = false;
						}
						else if(d.match("select")){
							alert("Please select Item in row : "+h);
							document.getElementById("item["+b+"]").focus();
							l = false;
						}
						else if(j.length == 0 || j == 0){
							alert("Please enter Mortality Birds in row : "+h);
							document.getElementById("birds["+b+"]").focus();
							l = false;
						}
						else if(e.length == 0 || e == 0){
							alert("Please enter Mortality Weight in row : "+h);
							document.getElementById("quantity["+b+"]").focus();
							l = false;
						}
						else if(f.length == 0 || f == 0){
							alert("Please enter Mortality Price in row : "+h);
							document.getElementById("price["+b+"]").focus();
							l = false;
						}
						else if(g.match("select")){
							alert("Please select Mortality On in row : "+h);
							document.getElementById("mtype["+b+"]").focus();
							l = false;
						}
						else if(i.match("select")){
							alert("Please select Customer / Warehouse in row : "+h);
							document.getElementById("ccode["+b+"]").focus();
							l = false;
						}
						else {
							l = true;
						}
					}
				}
				if(l == true){
					return true;
				}
				else {
					document.getElementById("ebtncount").value = "0"; document.getElementById("submittrans").style.visibility = "visible";
					return false;
				}
			}
			function rowgen(){
				var c = document.getElementById("incr").value;
				document.getElementById("addval["+c+"]").style.visibility = "hidden";
				document.getElementById("rmval["+c+"]").style.visibility = "hidden";
				c++;
				var html = '';
				html+= '<tr id="tblrow['+c+']" style="line-height:30px;">';
					html+= '<td><input type="text" name="pdate[]" id="pdate['+c+']" class="form-control datepickers" value="<?php echo $fdate; ?>" style="min-width:90px;"></td>';
					html+= '<td><select name="item[]" id="item['+c+']" class="form-control select2" style="width:180px;"><option value="select">select</option><?php foreach($item_code as $icode){ ?><option value="<?php echo $icode; ?>"><?php echo $item_name[$icode]; ?></option><?php } ?></select></td>';
					html+= '<td><input type="text" name="birds[]" id="birds['+c+']" class="form-control" style="min-width:90px;" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)"></td>';
					html+= '<td><input type="text" name="quantity[]" id="quantity['+c+']" class="form-control" style="min-width:90px;" onkeyup="validatenum(this.id);calculateamount(this.id);" onchange="validateamount(this.id)"></td>';
					html+= '<td><input type="text" name="price[]" id="price['+c+']" class="form-control" style="min-width:90px;" onkeyup="validatenum(this.id);calculateamount(this.id);" onchange="validateamount(this.id)"></td>';
					html+= '<td><input type="text" name="amount[]" id="amount['+c+']" class="form-control" style="min-width:90px;" readonly ></td>';
					html+= '<td><select name="mtype[]" id="mtype['+c+']" class="form-control select2" style="width: 180px;" onchange="setgroup(this.id)"><option value="select">select</option><option value="customer">Customer</option><option value="supplier">Supplier</option><option value="sector">Warehouse</option></select></td>';
					html+= '<td><select name="ccode[]" id="ccode['+c+']" class="form-control select2" style="width:180px;"><option value="select">select</option></select></td>';
					html+= '<td><textarea name="remark[]" id="remark['+c+']" class="form-control" style="height: 23px;"></textarea></td>';
					html+= '<td style="width: 100%;"><a href="JavaScript:Void(0);" name="addval[]" id="addval['+c+']" onclick="rowgen()"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval['+c+']" class="delete" onclick="removerow(this.id)" style="visibility:visible;"><i class="fa fa-minus" style="color:red;"></i></a></td>';
				html+= '</tr>';
				$('#tab3 tbody').append(html);
				document.getElementById("incr").value = c;
				$('.select2').select2();
				$('.datepickers').datepicker({ dateFormat:'dd.mm.yy',changeMonth:true,changeYear:true});
			}
			function removerow(a){
				var b = a.split("["); var c = b[1].split("]"); var d = c[0];
				document.getElementById('tblrow['+d+']').remove();
				d--;
				if(d == 0){
					document.getElementById("addval["+d+"]").style.visibility = "visible";
				}
				else{
					document.getElementById("addval["+d+"]").style.visibility = "visible";
					document.getElementById("rmval["+d+"]").style.visibility = "visible";
				}
				document.getElementById("incr").value = d;
			}
			function redirection_page(){ window.location.href = "main_displaymortality.php"; }
			function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function setgroup(x){
				var a = x.split("["); var b = a[1].split("]"); var c = b[0];
				var ctype = document.getElementById(x).value;
				removeAllOptions(document.getElementById("ccode["+c+"]"));
				myselect1 = document.getElementById("ccode["+c+"]"); 
				theOption1=document.createElement("OPTION"); 
				theText1=document.createTextNode("select"); 
				theOption1.value = "select"; 
				theOption1.appendChild(theText1); 
				myselect1.appendChild(theOption1);
				if(ctype == "customer"){
					<?php
						foreach($cus_code as $icode){
					?>
					theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $cus_name[$icode]; ?>"); theOption1.value = "<?php echo $icode; ?>"; theOption1.appendChild(theText1); myselect1.appendChild(theOption1);
					<?php
						}
					?>
				}
				else if(ctype == "supplier"){
					<?php
						foreach($sup_code as $icode){
					?>
					theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $sup_name[$icode]; ?>"); theOption1.value = "<?php echo $icode; ?>"; theOption1.appendChild(theText1); myselect1.appendChild(theOption1);
					<?php
						}
					?>
				}
				else if(ctype == "sector"){
					<?php
						foreach($sector_code as $icode){
					?>
					theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $sector_name[$icode]; ?>"); theOption1.value = "<?php echo $icode; ?>"; theOption1.appendChild(theText1); myselect1.appendChild(theOption1);
					<?php
						}
					?>
				}
				else{ }
			}
			
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
			document.addEventListener("keydown", (e) => { var key_search = document.activeElement.id.includes("["); if(key_search == true){ var b = document.activeElement.id.split("["); var c = b[1].split("]"); var d = c[0]; document.getElementById("incrs").value = d; } if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function () { $('#submittrans').click(); }); } } else{ } });
		</script>
		<script src="main_numbertoamount.js"></script>
		<footer align="center"> <?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer>
	</body>
</html>

