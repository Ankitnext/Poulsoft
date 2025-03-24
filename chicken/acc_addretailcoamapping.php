<?php
//acc_addretailcoamapping.php
	session_start(); include "newConfig.php";
	include "header_head.php";
	$cid = $_SESSION['rcoamap'];
	$dbname = $_SESSION['dbase'];
	$sql = "SELECT * FROM `item_category` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $cat_code[$row['code']] = $row['code']; $cat_name[$row['code']] = $row['description']; $cat_rflag[$row['code']] = $row['rflag']; }
	
	$sql = "SELECT * FROM `log_useraccess` WHERE `dblist` = '$dbname' AND `flag` = '1'";  $query = mysqli_query($conns,$sql);
	while($row = mysqli_fetch_assoc($query)){ $user_code[$row['empcode']] = $row['empcode']; $user_name[$row['empcode']] = $row['username']; }
	$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` NOT IN (SELECT empcode FROM `main_useraccount` WHERE `acc_type` = 'C' AND `active` = '1' AND `dflag` = '0')";  $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $cemp_code[$row['empcode']] = $row['empcode']; $cemp_name[$row['empcode']] = $user_name[$row['empcode']]; }
	
	$sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash') AND `rflag` = '0' AND `active` = '1'";  $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $cash_code[$row['code']] = $row['code']; $cash_name[$row['code']] = $row['description']; }
	$sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Bank') AND `active` = '1'";  $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $bank_code[$row['code']] = $row['code']; $bank_name[$row['code']] = $row['description']; }
	$sql = "SELECT * FROM `upi_types` WHERE `active` = '1' ORDER BY `sorder` ASC";  $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $upi_code[$row['code']] = $row['code']; $upi_name[$row['code']] = $row['description']; }
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
			<h1>Fill all required fields</h1>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i>Home</a></li>
				<li><a href="#">CoA</a></li>
				<li class="active">Retail-CoA</li>
				<li class="active">Add</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
				<div class="box-body">
					<div class="col-md-18">
						<form action="acc_updateretailcoamapping.php" method="post" onsubmit="return checkval()" name="form_name" id = "form_id" >
							<div class="row">
								<div class="form-group col-md-2" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
									<label>incr<b style="color:red;">&nbsp;*</b></label>
									<input type="text" style="width:auto;" class="form-control" name="incr" id="incr" value="0">
								</div>
							</div>
							<div class="row">
								<div class="col-md-18">
									<table class="table1" style="width:100%;">
										<thead>
											<tr>
												<th style="width: 160px;text-align:center;"><label>Cash/Bank<b style="color:red;">&nbsp;*</b></label></th>
												<th style="text-align:center;"><label>User<b style="color:red;">&nbsp;*</b></label></th>
												<th style="text-align:center;"><label>Shop<b style="color:red;">&nbsp;*</b></label></th>
												<th style="text-align:center;"><label>Cach/Bank CoA<b style="color:red;">&nbsp;*</b></label></th>
												<th style="text-align:center;"><label>Type<b style="color:red;">&nbsp;*</b></label></th>
												<th style="text-align:center;"><label>Access Type<b style="color:red;">&nbsp;*</b></label></th>
												<th style="text-align:center;"><label>Access No.<b style="color:red;">&nbsp;*</b></label></th>
												<th style="text-align:center;"><label>+/-</label></th>
											</tr>
										</thead>
										<tbody id="tbody">
											<tr>
												<td><div class="form-group col-md-12"><input type="radio" name="cbtype0" id="ctype_0" value="C" onchange="fetchcoacodes(this.id);fetchuserdetails(this.id);" checked />&nbsp;Cash&ensp;<input type="radio" name="cbtype0" id="btype_0" value="B" onchange="fetchcoacodes(this.id);fetchuserdetails(this.id);" />&nbsp;Bank</div></td>
												<td><div class="form-group col-md-12"><select name="users0" id="users0" class="form-control select" style="width:150px;" onchange="fetchusersector(this.id);"><option value="select">select</option><?php foreach($cemp_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $cemp_name[$ucode]; ?></option><?php } ?></select></div></td>
												<td><div class="form-group col-md-12"><select name="shops0" id="shops0" class="form-control select" style="width:150px;"><option value="select">select</option></select></div></td>
												<td><div class="form-group col-md-6"><select name="coas0" id="coas0" class="form-control select" style="width:150px;"><option value="select">select</option><?php foreach($cash_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $cash_name[$scode]; ?></option><?php } ?></select></div></td>
												<td id="typevis0" style="visibility:hidden;"><div class="form-group col-md-6"><select name="types0" id="types0" class="form-control select" style="width:150px;" onchange="findtypedetails(this.id)"><option value="select">select</option><option value="upi">UPI</option><option value="other">Other</option></select></div></td>
												<td id="atypevis0" style="visibility:hidden;"><div class="form-group col-md-6"><select name="atypes0[]" id="atypes0" class="form-control select2" multiple style="width:150px;"><option value="select">select</option><?php foreach($upi_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $upi_name[$ucode]; ?></option><?php } ?></div></td>
												<td id="cbunovis0" style="visibility:hidden;"><div class="form-group col-md-12"><input type="text" name="cbuno0" id="cbuno0" class="form-control" style="width:150px;" /></div></td>
												<td><div class="form-group col-md-12"><a href="JavaScript:Void(0);" name="addval[]" id="addval[0]" onclick="rowgen()"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval[0]" onclick="rowdes()" style="visibility:hidden;"><i class="fa fa-minus" style="color:red;"></i></a></div></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<div class="row">
								<div class="col-md-4"></div>
								<div class="col-md-4">
									<div class="box-body" align="center">
										<button type="submit" name="submittrans" id="submittrans" value="addpage" class="btn btn-flat btn-social btn-linkedin">
											<i class="fa fa-save"></i> Save
										</button>&ensp;&ensp;&ensp;&ensp;
										<button type="button" name="cancelled" id="cancelled" class="btn btn-flat btn-social btn-google" onclick="redirection_page()">
											<i class="fa fa-trash"></i> Cancel
										</button>
									</div>
								</div>
								<div class="col-md-3"></div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</section>
		<script>
			function rowgen(){
				var a = document.getElementById("incr").value;
				document.getElementById("addval["+a+"]").style.visibility = "hidden";
				document.getElementById("rmval["+a+"]").style.visibility = "hidden";
				a++;
				document.getElementById("incr").value = a;
				html = '';
				html+= '<tr id="tr_'+a+'">';
				html+= '<td><div class="form-group col-md-12"><input type="radio" name="cbtype'+a+'" id="ctype_'+a+'" value="C" onchange="fetchcoacodes(this.id);fetchuserdetails(this.id);" checked />&nbsp;Cash&ensp;<input type="radio" name="cbtype'+a+'" id="btype_'+a+'" value="B" onchange="fetchcoacodes(this.id);fetchuserdetails(this.id);" />&nbsp;Bank</div></td>';
				html+= '<td><div class="form-group col-md-12"><select name="users'+a+'" id="users'+a+'" class="form-control select" style="width:150px;" onchange="fetchusersector(this.id);"><option value="select">select</option><?php foreach($cemp_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $cemp_name[$ucode]; ?></option><?php } ?></select></div></td>';
				html+= '<td><div class="form-group col-md-12"><select name="shops'+a+'" id="shops'+a+'" class="form-control select" style="width:150px;"><option value="select">select</option></select></div></td>';
				html+= '<td><div class="form-group col-md-6"><select name="coas'+a+'" id="coas'+a+'" class="form-control select" style="width:150px;"><option value="select">select</option><?php foreach($cash_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $cash_name[$scode]; ?></option><?php } ?></select></div></td>';
				html+= '<td id="typevis'+a+'" style="visibility:hidden;"><div class="form-group col-md-6"><select name="types'+a+'" id="types'+a+'" class="form-control select" style="width:150px;" onchange="findtypedetails(this.id)"><option value="select">select</option><option value="upi">UPI</option><option value="other">Other</option></select></div></td>';
				html+= '<td id="atypevis'+a+'" style="visibility:hidden;"><div class="form-group col-md-6"><select name="atypes'+a+'[]" id="atypes'+a+'" class="form-control select" multiple style="width:150px;"><option value="select">select</option><?php foreach($upi_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $upi_name[$ucode]; ?></option><?php } ?></div></td>';
				html+= '<td id="cbunovis'+a+'" style="visibility:hidden;"><div class="form-group col-md-12"><input type="text" name="cbuno'+a+'" id="cbuno'+a+'" class="form-control" style="width:150px;" /></div></td>';
				html+= '<td style="width: 60px;"><div class="form-group col-md-12"><a href="JavaScript:Void(0); "name="addval[]" id="addval['+a+']" onclick="rowgen()"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval['+a+']" class="delete" onclick="rowdes()" title="'+a+'"><i class="fa fa-minus" style="color:red;"></i></a></div></td>';
				html+= '</tr>';
				$('#tbody').append(html);
				$('.select').select2();
			}
			function rowdes(){
				var a = document.getElementById("incr").value;
				document.getElementById('tr_'+a).remove();
				a--;
				if(a > 0){
					document.getElementById("addval["+a+"]").style.visibility = "visible";
					document.getElementById("rmval["+a+"]").style.visibility = "visible";
				}
				else{
					document.getElementById("addval["+a+"]").style.visibility = "visible";
				}
				document.getElementById("incr").value = a;
			}
			function checkval(){
				var a = document.getElementById("incr").value;
				var b = c = d = e = f = g = h = i = j = k = m = ""; var l = true;
				for(var i = 0;i <= a;i++){
					if(l == true){
						b = "ctype_"+i;
						c = "btype_"+i;
						d = document.getElementById(b);
						e = document.getElementById(c);
						
						if(d.checked == true){
							f = g = h = "";
							f = document.getElementById("users"+i).value;
							g = document.getElementById("shops"+i).value;
							h = document.getElementById("coas"+i).value;
							if(f.match("select")){
								alert("Select User in row:- "+i);
								document.getElementById('users'+i).focus();
								l = false;
							}
							else if(g.match("select")){
								alert("Select Shop in row:- "+i);
								document.getElementById('shops'+i).focus();
								l = false;
							}
							else if(h.match("select")){
								alert("Select Cash/Bank in row:- "+i);
								document.getElementById('coas'+i).focus();
								l = false;
							}
							else{
								l = true;
							}
						}
						else if(e.checked == true){
							f = g = h = j = "";
							f = document.getElementById("users"+i).value;
							g = document.getElementById("shops"+i).value;
							h = document.getElementById("coas"+i).value;
							j = document.getElementById("types"+i).value;
							if(f.match("select")){
								alert("Select User in row:- "+i);
								document.getElementById('users'+i).focus();
								l = false;
							}
							else if(g.match("select")){
								alert("Select Shop in row:- "+i);
								document.getElementById('shops'+i).focus();
								l = false;
							}
							else if(h.match("select")){
								alert("Select Cash/Bank in row:- "+i);
								document.getElementById('coas'+i).focus();
								l = false;
							}
							else if(j.match("select")){
								alert("Select Type in row:- "+i);
								document.getElementById('types'+i).focus();
								l = false;
							}
							else if(j.match("upi")){
								for (var option of document.getElementById('atypes'+i).options){
									if (option.selected) {
										k+= option.value;
									}
								}
								m = document.getElementById("cbuno"+i).value;
								if(k.length == 0){
									alert("Select Access Type in row:- "+i);
									document.getElementById('atypes'+i).focus();
									l = false;
								}
								else if(m == "" || m.length == 0){
									alert("Select Access No. in row:- "+i);
									document.getElementById('cbuno'+i).focus();
									l = false;
								}
								else{
									l = true;
								}
							}
							else{
								l = true;
							}
						}
						else{
							
						}
					}
					else{
						l = false;
					}
				}
				if(l == true){
					return true;
				}
				else if(l == false){
					return false;
				}
				else{
					return false;
				}
			}
			function redirection_page(){
				var a = '<?php echo $cid; ?>';
				window.location.href = "acc_displayretailcoamapping.php?cid="+a;
			}
			function fetchcoacodes(a){
				var b = a.split("_");
				var c = document.getElementById(a).value;
				var d = "coas"+b[1]; var e = "typevis"+b[1];
				removeAllOptions(document.getElementById(d));
				myselect = document.getElementById(d); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("select"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				if(c.match("C")){
					<?php
					foreach($cash_code as $ccode){
					?>
					theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $cash_name[$ccode]; ?>"); theOption1.value = "<?php echo $ccode; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
					<?php
						}
					?>
					document.getElementById(e).style.visibility = "hidden";
				}
				else{
					<?php
					foreach($bank_code as $ccode){
					?>
					theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $bank_name[$ccode]; ?>"); theOption1.value = "<?php echo $ccode; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
					<?php
						}
					?>
					document.getElementById(e).style.visibility = "visible";
				}
			}
			function fetchuserdetails(a){
				var b = a.split("_");
				var c = document.getElementById(a).value;
				var f = "users"+b[1]; var g = "";
				removeAllOptions(document.getElementById(f));
				myselect = document.getElementById(f); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("select"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				if(c.match("C")){
				<?php
					$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` NOT IN (SELECT empcode FROM `main_useraccount` WHERE `acc_type` = 'C' AND `active` = '1' AND `dflag` = '0')";  $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){
						$ccoacode = $row['cashcode'];
						echo "if('$ccoacode' == g){";
				?>
					theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $user_name[$row['empcode']]; ?>"); theOption1.value = "<?php echo $row['empcode']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				<?php
						echo "}";
					}
				?>
				}
				else if(c.match("B")){
				<?php
					$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` NOT IN (SELECT empcode FROM `main_useraccount` WHERE `acc_type` = 'B' AND `active` = '1' AND `dflag` = '0')";  $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){
						$bcoacode = $row['bankcode'];
						echo "if('$bcoacode' == g){";
				?>
					theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $user_name[$row['empcode']]; ?>"); theOption1.value = "<?php echo $row['empcode']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				<?php
						echo "}";
					}
				?>
				}
				else{
					
				}
			}
			function fetchusersector(a){
				var b = a.split("users");
				var c = document.getElementById(a).value;
				var d = "shops"+b[1];
				removeAllOptions(document.getElementById(d));
				myselect = document.getElementById(d); //theOption1=document.createElement("OPTION"); theText1=document.createTextNode("select"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				if(!c.match("select")){
					var prices = new XMLHttpRequest();
					var method = "GET";
					var url = "cus_fetchsector.php?usrcode="+c;
					var asynchronous = true;
					//window.open(url);
					prices.open(method, url, asynchronous);
					prices.send();
					prices.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var f = this.responseText;
							if(f == ""){
								alert("Kindly allocate a shop to the user");
							}
							else{
								var g = f.split("@");
								theOption1=document.createElement("OPTION"); theText1=document.createTextNode(g[1]); theOption1.value = g[0]; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
							}
						}
					}
				}
				else { }
			}
			function findtypedetails(a){
				var b = a.split("types");
				var c = document.getElementById(a).value;
				var f = "atypevis"+b[1]; var g = "cbunovis"+b[1];
				if(c.match("upi")){
					document.getElementById(f).style.visibility = "visible";
					document.getElementById(g).style.visibility = "visible";
				}
				else if(c.match("other")){
					document.getElementById(f).style.visibility = "hidden";
					document.getElementById(g).style.visibility = "hidden";
				}
				else{
					document.getElementById(f).style.visibility = "hidden";
					document.getElementById(g).style.visibility = "hidden";
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
	</body>
</html>
<?php include "header_foot.php"; ?>