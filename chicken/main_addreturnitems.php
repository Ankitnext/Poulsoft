<?php
	//main_addreturnitems
	session_start(); include "newConfig.php";
	include "header_head.php";
	$cid = $_SESSION['dispitmrtn'];
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
			.form-control { width: 85%; height: 23px; }
			label { line-height: 20px; }
			.disabledbutton{ pointer-events: none; opacity: 0.4; }
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Create Item Return</h1>
			<h5>Please fill mandatory (<b style="color:red;"> * </b>) fields</h5>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Item Return</a></li>
				<li class="active">Display</li>
				<li class="active">Add</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
			<?php
				$today = date("Y-m-d");
				$fdate = date("d.m.Y",strtotime($today));
				$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
				
				$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
				
				$sql = "SELECT * FROM `main_transactionfields` WHERE `field` LIKE 'Item Return' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $jals_flag = $row['jals_flag']; $birds_flag = $row['birds_flag']; }
				if($jals_flag == "" || $jals_flag == NULL){ $jals_flag = 0; }
				if($birds_flag == "" || $birds_flag == NULL){ $birds_flag = 0; }
				
				$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; $cus_type[$row['code']] = $row['contacttype']; }
				
			?>
			
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-18">
								<form action="main_updatereturnitems.php" method="post" role="form" onsubmit="return checkval()" name="form_name" id = "form_id" >
									<div class="form-group col-md-2">
										<input type="radio" name="vtype" id="vtype1" value="customer" onclick="fetchvendordetails(this.id)" checked />
										<label>Customer</label>&ensp;&ensp;
										<input type="radio" name="vtype" id="vtype2" value="supplier" onclick="fetchvendordetails(this.id)" />
										<label>Supplier</label>
									</div>
									<div class="form-group col-md-2">
										<label>Invoiced Date<b style="color:red;">&nbsp;*</b></label>
										<input type="text" class="form-control" name="inv_date" value="<?php echo $fdate; ?>" id="datepickers1" onchange="fetchinvoice()" />
									</div>
									<div class="form-group col-md-2">
										<label>Return Date<b style="color:red;">&nbsp;*</b></label>
										<input type="text" class="form-control" name="rtn_date" value="<?php echo $fdate; ?>" id="datepickers2" />
									</div>
									<div class="form-group col-md-2">
										<label>Customer/Supplier<b style="color:red;">&nbsp;*</b></label>
										<select name="vendor" id="vendor" class="form-control select2" style="width: 100%;" onchange="fetchinvoice()"><option value="select">-select-</option><?php foreach($cus_code as $it){ if($cus_type[$it] == "C" || $cus_type[$it] == "S&C"){ ?><option value="<?php echo $cus_code[$it]; ?>"><?php echo $cus_name[$it]; ?></option><?php } } ?></select>
									</div>
									<div class="form-group col-md-2">
										<label>Invoice No.<b style="color:red;">&nbsp;*</b></label>
										<select name="invno" id="invno" class="form-control select2" style="width: 100%;" onchange="fetchinvoicedetails()"></select>
									</div>
									<div class="col-md-12">
										<table class="m-0 p-0 table table-bordered" style="width:100%;line-height:30px;" id="tab3">
											<tr style="line-height:30px;">
												<th colspan="2" style="text-align:center;">Invoice Details</th>
												<th style="border:none;"></th>
												<?php $c = 5; if($jals_flag == 1){ $c++; } if($birds_flag == 1){ $c++; } ?>
												<th colspan="<?php echo $c; ?>" style="text-align:center;">Return Details</th>
											</tr>
											<tr style="line-height:30px;">
												<th><label>Item Description<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Sold Quantity<b style="color:red;">&nbsp;*</b></label></th>
												<th style="padding: 0 50px;border:none;"></th>
												<?php if($jals_flag == 1){ echo '<th>Jals</th>'; } ?>
												<?php if($birds_flag == 1){ echo '<th>Birds</th>'; } ?>
												<th><label>Quantity<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Price<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Amount</label></th>
												<th><label>Return Type</label></th>
												<th><label>Warehouse</label></th>
											</tr>
											<tbody id="tbl_row">
											
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
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php include "header_foot.php"; ?>
		<script>
			function redirection_page(){
				var a = '<?php echo $cid; ?>';
				window.location.href = "main_displayreturnitems.php?cid="+a;
			}
			function checkval(){
				var a = document.getElementById("datepickers1").value;
				var b = document.getElementById("datepickers2").value;
				var c = document.getElementById("vendor").value;
				var d = document.getElementById("invno").value;
				var l = true; var nqty = nprice = namt = ""; var rval = rprc = ramt = 0;
				if(a.length == 0 || a == 0 || a.length == ""){
					alert("Please enter Invoiced Date ...!");
					document.getElementById("datepickers1").focus();
					l = false;
				}
				else if(b.length == 0 || b == 0 || b.length == ""){
					alert("Please enter Return Date ...!");
					document.getElementById("datepickers2").focus();
					l = false;
				}
				else if(c.match("select")){
					alert("Please select Customer/Supplier Name ...!");
					document.getElementById("vendor").focus();
					l = false;
				}
				else if(d.match("select")){
					alert("Please select Invoice ...!");
					document.getElementById("invno").focus();
					l = false;
				}
				else{
					var rows = document.getElementById("tbl_row").rows.length;
					for(var e = 1;e <= rows;e++){
						nqty = document.getElementById("rqty["+e+"]").value;
						nprice = document.getElementById("rprice["+e+"]").value;
						namt = document.getElementById("ramount["+e+"]").value;
						if(nqty == "" || nqty == "0.00" || nqty == 0 || nqty == 0.00 || nqty == "0" || nqty.length == 0){
							
						}
						else{
							rval++;
						}
						if(nprice == "" || nprice == "0.00" || nprice == 0 || nprice == 0.00 || nprice == "0" || nprice.length == 0){
							
						}
						else{
							rprc++;
						}
						if(namt == "" || namt == "0.00" || namt == 0 || namt == 0.00 || namt == "0" || namt.length == 0){
							
						}
						else{
							ramt++;
						}
					}
					if(rval == 0){
						alert("Kindly select atleast one item quantity to collect return ...!");
						l = false;
					}
					else if(rprc == 0){
						alert("Kindly enter price of return Quantity ...!");
						l = false;
					}
					else if(ramt == 0){
						alert("Quantity/Price is not matching \n Kindly check and try again ...!");
						l = false;
					}
					else{
						l = true;
					}
				}
				
				if(l == true){
					return true;
				}
				else{
					return false;
				}
			}
			function calculateamount(x){
				var a = x.split("["); var b = a[1].split("]"); var c = b[0];
				var d = document.getElementById("rqty["+c+"]").value;
				if(d == "" || d.length == 0){ d = 0; }
				var e = document.getElementById("rprice["+c+"]").value;
				if(e == "" || e.length == 0){ e = 0; }
				var f = parseFloat(d) * parseFloat(e);
				document.getElementById("ramount["+c+"]").value = f.toFixed(2);
			}
			function fetchvendordetails(x){
				var a = document.getElementById(x).value;
				removeAllOptions(document.getElementById("vendor"));
				myselect = document.getElementById("vendor"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("-select-"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				if(a.match("customer")){
					var b = "C"; var c = "S&C";
					<?php
					foreach($cus_code as $it){
						$ctype = $cus_type[$it];
						echo "if(b == '$ctype' || c == '$ctype'){";
					?>
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $cus_name[$it]; ?>"); theOption1.value = "<?php echo $it; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
					<?php
						echo "}";
					}
					?>
				}
				else if(a.match("supplier")){
					var b = "S"; var c = "S&C";
					<?php
					foreach($cus_code as $it){
						$ctype = $cus_type[$it];
						echo "if(b == '$ctype' || c == '$ctype'){";
					?>
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $cus_name[$it]; ?>"); theOption1.value = "<?php echo $it; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
					<?php
						echo "}";
					}
					?>
				}
				else{ }
			}
			function fetchinvoice(){
				if(document.getElementById("vtype1").checked == true){
					var a = "customer";
				}
				else if(document.getElementById("vtype2").checked == true){
					var a = "supplier";
				}
				else{
					var a = "";
				}
				if(a.length != 0){
					var b = document.getElementById("vendor").value;
					var c = document.getElementById("datepickers1").value;
					
					if(!b.match("select") && c.length > 0){
						removeAllOptions(document.getElementById("invno"));
						myselect = document.getElementById("invno"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("-select-"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				
						var prices = new XMLHttpRequest();
						var method = "GET";
						var url = "main_fetchinvoices.php?vtype="+a+"&vcode="+b+"&vdate="+c;
						var asynchronous = true;
						prices.open(method, url, asynchronous);
						prices.send();
						prices.onreadystatechange = function(){
							if(this.readyState == 4 && this.status == 200){
								var f = this.responseText;
								if(f == "") {
									$("#tbl_row tr").remove();
									alert("There are no invoice to select \n Please check and try again ..!");
								}
								else {
									var d = f.split("&");
									var g = d[1];
									if(g == 1){
										theOption1=document.createElement("OPTION"); theText1=document.createTextNode(d[0]); theOption1.value = d[0]; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
									}
									else{
										var h = d[0].split("@");
										for(var e = 0;e < h.length;e++){
											if(h[e] != ""){
												theOption1=document.createElement("OPTION"); theText1=document.createTextNode(h[e]); theOption1.value = h[e]; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
											}
										}
									}
								}
							}
						}
					}
					else{
						//alert("Invoice date and Customer/Supplier is not appropriate \n Kindly check and try again");
					}
				}
			}
			function fetchinvoicedetails(){
				if(document.getElementById("vtype1").checked == true){
					var a = "customer";
				}
				else if(document.getElementById("vtype2").checked == true){
					var a = "supplier";
				}
				else{
					var a = "";
				}
				if(a.length != 0){
					var b = document.getElementById("invno").value;
					var c = document.getElementById("datepickers1").value;
					$("#tbl_row tr").remove();
					var prices = new XMLHttpRequest();
					var method = "GET";
					var url = "main_fetchinvoicesdetails.php?vtype="+a+"&vcode="+b+"&vdate="+c;
					var asynchronous = true;
					prices.open(method, url, asynchronous);
					prices.send();
					prices.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var f = this.responseText;
							if(f == "") {
								alert("There are no invoice to select \n Please check and try again ..!");
							}
							else {
								var e = f.split("&mksp;");
								for(var g = 0; g < e.length;g++){
									$("#tbl_row").append(e[g]);
								}
								
								$('.select').select2();
							}
						}
					}
				}
				else{
					//alert("Invoice date and Customer/Supplier is not appropriate \n Kindly check and try again");
				}
			}
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			document.getElementById("form_id").onkeypress = function(e) {
				var key = e.charCode || e.keyCode || 0;     
				if (key == 13) {
				//alert("No Enter!");
				e.preventDefault();
				}
			} 
		</script>
	</body>
</html>