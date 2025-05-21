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
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Add Payments</h1>
			<h5>Please fill mandatory (<b style="color:red;"> * </b>) fields</h5>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Transaction</a></li>
				<li class="active">Payment Display</li>
				<li class="active">Add</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
			<?php
				/*Check for Table Availability*/
				$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
				$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
				if(in_array("extra_access", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.extra_access LIKE poulso6_admin_chickenmaster.extra_access;"; mysqli_query($conn,$sql1); }
				
				/*Check for Sector Display Flag*/
				$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Supplier Payment' AND `field_function` LIKE 'Sector Selection Display' AND `user_access` LIKE 'all'";
				$query = mysqli_query($conn,$sql); $fcount = mysqli_num_rows($query); $secs_dflag = 1;
				if($fcount > 0){ while($row = mysqli_fetch_assoc($query)){ $secs_dflag = $row['flag']; } }
				else{ $sql = "INSERT INTO `extra_access` (`id`, `field_name`, `field_function`, `field_value`, `user_access`, `flag`) VALUES (NULL, 'Supplier Payment', 'Sector Selection Display', NULL, 'all', '1');"; mysqli_query($conn,$sql); }
				if($secs_dflag == ""){ $secs_dflag = 1; }

				$today = date("Y-m-d");
				$fdate = date("d.m.Y",strtotime($today));
				$y = date("y"); $y2 = $y + 1; $m = date("m"); $d = date("d");
				$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
				$fpcode = array();
				while($row = mysqli_fetch_assoc($query)){
					$fpcode[$row['code']] = $row['code'];
					$fpname[$row['code']] = $row['name'];
				} $fpsize = sizeof($fpcode);
				$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$wcode[$row['code']] = $row['code'];
					$wdesc[$row['code']] = $row['description'];
				}

				$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Supplier Payment' AND `field_function` LIKE 'Display selected Modes' AND `user_access` LIKE 'all' AND `flag` = '1'";
				$query = mysqli_query($conn,$sql); $m_cnt = mysqli_num_rows($query); $mode_list = $mode_fltr = "";
				if($m_cnt > 0){
					while($row = mysqli_fetch_assoc($query)){ $m_list = $row['field_value']; }
					$m_alist = explode(",",$m_list);
					foreach($m_alist as $mcode){
						if($mode_list == ""){ $mode_list = $mcode; } else{ $mode_list = $mode_list."','".$mcode; }
					}
					$mode_fltr = " AND `description` IN ('$mode_list')";
				}
				$sql = "SELECT * FROM `acc_modes` WHERE `active` = '1'".$mode_fltr." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$acode[$row['code']] = $row['code'];
					$adesc[$row['code']] = $row['description'];
				}
				$sql = "SELECT * FROM `acc_coa` WHERE `ctype` LIKE '%Cash%' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$cash_code[$row['code']] = $row['code'];
					$cash_name[$row['code']] = $row['description'];
				}

				$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'pur_displaypayments.php' AND `field_function` LIKE 'Display Supplier Balance' AND `user_access` LIKE 'all' AND `flag` = '1'";
				$query = mysqli_query($conn,$sql); $bal_flag = mysqli_num_rows($query);


				$idisplay = ''; $ndisplay = 'style="display:none;';
			?>
			
				<div class="box-body" style="min-height:400px;">
					<div class="row">
						<div class="col-md-18" align="center">
							<div class="col-md-18" align="center">
								<form action="pur_updatepayments.php" method="post" role="form" onsubmit="return checkval()" name="form_name" id = "form_id" >
									<div class="form-group col-md-18" style="visibility:hidden;">
										<input type="text" style="width:auto;" class="form-control" name="incr" id="incr" value="0">
										<input type="text" style="width:auto;" class="form-control" name="incrs" id="incrs" value="0">
									</div>
									<table style="width:125%;line-height:30px;" id="tab3" align="center">
										<thead>
											<tr style="line-height:30px;">
												<th><label>Date<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Supplier<b style="color:red;">&nbsp;*</b></label></th>
												<?php if((int)$bal_flag == 1){ echo '<th style="width: 150px;padding-right:10px;"><label>Balance</label></th>'; } ?>
												<th><label>Mode<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Cash/Bank<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Amount<b style="color:red;">&nbsp;*</b></label></th>
												<th style="width:80px;"><label>Doc No</label></th>
												<?php if((int)$secs_dflag == 1){ ?><th><label>Sector<b style="color:red;">&nbsp;*</b></label></th><?php } ?>
												<!--<th style="visibility:visible;" id="thcdetails" style="width:80px;"><label>Cheque No.<b style="color:red;">&nbsp;*</b></label></th>
												<th style="visibility:visible;" id="thcdetails" ><label>Cheque Date<b style="color:red;">&nbsp;*</b></label></th>-->
												<th><label>Remarks</label></th>
												<th>Action</th>
											</tr>
										</thead>
											<tr style="line-height:30px;">
												<td><input type="text"  name="pdate[]" id="pdate[0]"class="form-control pay_datepickers" value="<?php echo $fdate; ?>" readonly></td>
												<td><select name="pname[]" id="pname[0]" class="form-control select2" onchange="fetchbalance(this.id);"> <option value="select">select</option> <?php foreach($fpcode as $fcode){ ?> <option value="<?php echo $fpcode[$fcode]; ?>"><?php echo $fpname[$fcode]; ?></option> <?php } ?> </select></td>
												<?php if($bal_flag == 1){ echo '<td  style= "width: 80px;padding-right:10px;"><input type="text" style="width: 80px;" name="balc[]" id="balc[0]" class="form-control" /></td>'; } ?>
												<td><select name="mode[]" id="mode[0]" class="form-control select2" onchange="updatecode(this.id)"> <option value="select">select</option> <?php foreach($acode as $fcode){ ?> <option value="<?php echo $acode[$fcode]; ?>" ><?php echo $adesc[$fcode]; ?></option> <?php } ?> </select></td>
												<td><select name="code[]" id="code[0]" class="form-control select2" > <option value="select">select</option></select></td>
												<td><input type="text" name="amount[]" id="amount[0]" class="form-control" value="" onkeyup="granttotalamount();getamountinwords();"></td>
												<td><input type="text" name="dcno[]" id="dcno[0]" class="form-control" style="width:80px;" ></td>
												<?php if((int)$secs_dflag == 1){ ?><td><select name="sector[]" id="sector[0]" class="form-control select2" >  <?php foreach($wcode as $fcode){ ?> <option value="<?php echo $wcode[$fcode]; ?>"><?php echo $wdesc[$fcode]; ?></option> <?php } ?> </select></td><?php } ?>
												<!--<td><input type="text" name="cno[]" id="cno[0]" class="form-control" style="width:80px;visibility:hidden;"></td>
												<td><input type="text" name="cdate[]" id="cdate[0]" class="form-control pay_datepickers" style="visibility:hidden;" value="<?php //echo $fdate; ?>" readonly></td>-->
												<td><textarea name="remark[]" id="remark[0]" class="form-control" style="height: 23px;"></textarea></td>
												<td style="width: 100%;"><a href="JavaScript:Void(0);" name="addval[]" id="addval[0]" onclick="rowgen()"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval[0]" onclick="rowdes()" style="visibility:hidden;"><i class="fa fa-minus" style="color:red;"></i></a></td>
												<td style="visibility:hidden;"><input type="text" name="gtamtinwords[]" id="gtamtinwords[0]" class="form-control" readonly /></td>
											</tr>
									</table><br/><br/><br/>
									<div class="col-md-12" align="left">
										<div class="col-md-4">
											<label>Total Payments</label>
											<input type="text" name="tno" id="tno" class="form-control"style="width:auto;" >
										</div>
										<div class="col-md-4">
											<label>Payment Amount</label>
											<input type="text" name="gtamt" id="gtamt" class="form-control" style="width:auto;" readonly>
										</div>
									</div>
										<br/>
										<div class="col-md-12" align="left">
											<div class="col-md-12" style="width:auto;visibility:hidden;">
												<label>Amount in words</label>
												
											</div>
										</div><br/><br/><br/>
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
			function getamountinwords() {
				var a = document.getElementById("incrs").value;
				var b = document.getElementById("amount["+a+"]").value;
				var c = convertNumberToWords(b);
				document.getElementById("gtamtinwords["+a+"]").value = c;
			}
			function rowgen(){
				var c = document.getElementById("incr").value;
				document.getElementById("addval["+c+"]").style.visibility = "hidden";
				document.getElementById("rmval["+c+"]").style.visibility = "hidden";
				c++;

				var bal_flag = '<?php echo $bal_flag; ?>';
				var secs_dflag = '<?php echo $secs_dflag; ?>';
				var html = '';
				html+= '<tr style="line-height:30px;">';
					html+= '<td><input type="text" name="pdate[]" id="pdate['+c+']" class="form-control pay_datepickers" value="<?php echo $fdate; ?>" onmouseover="displaycalendor()" readonly></td>';
					html+= '<td><select name="pname[]" id="pname['+c+']" class="form-control select2" onchange="fetchbalance(this.id);"> <option value="select">select</option> <?php foreach($fpcode as $fcode){ ?> <option value="<?php echo $fpcode[$fcode]; ?>"><?php echo $fpname[$fcode]; ?></option> <?php } ?> </select></td>';
					if(parseInt(bal_flag) > 0){ html+= '<td style="width: 100px;padding-right:10px;"><input type="text" name="balc[]" id="balc['+c+']" style="width: 100px;" class="form-control"></td>'; }
					html+= '<td><select name="mode[]" id="mode['+c+']" class="form-control select2" onchange="updatecode(this.id)"> <option value="select">select</option> <?php foreach($acode as $fcode){ ?> <option value="<?php echo $acode[$fcode]; ?>"><?php echo $adesc[$fcode]; ?></option> <?php } ?> </select></td>';
					html+= '<td><select name="code[]" id="code['+c+']" class="form-control select2"> <option value="select">select</option></select></td>';
					html+= '<td><input type="text" name="amount[]" id="amount['+c+']" class="form-control" value="" onkeyup="granttotalamount();getamountinwords();"></td>';
					html+= '<td><input type="text" name="dcno[]" id="dcno['+c+']" class="form-control" style="width:80px;"></td>';
					if(parseInt(secs_dflag) == 1){ html+= '<td><select name="sector[]" id="sector['+c+']" class="form-control select2">  <?php foreach($wcode as $fcode){ ?> <option value="<?php echo $wcode[$fcode]; ?>"><?php echo $wdesc[$fcode]; ?></option> <?php } ?> </select></td>'; }
					//html+= '<td><input type="text" name="cno[]" id="cno['+c+']" class="form-control" style="width:80px;visibility:hidden;" ></td>';
					//html+= '<td><input type="text" name="cdate[]" id="cdate['+c+']" class="form-control pay_datepickers" value="<?php echo $fdate; ?>" style="visibility:hidden;" onmouseover="displaycalendor()" readonly></td>';
					html+= '<td><textarea name="remark[]" id="remark['+c+']" class="form-control" style="height: 23px;"></textarea></td>';
					html+= '<td style="width: 100%;"><a href="JavaScript:Void(0);" name="addval[]" id="addval['+c+']" onclick="rowgen()"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval['+c+']" class="delete" onclick="rowdes()" style="visibility:visible;"><i class="fa fa-minus" style="color:red;"></i></a></td>';
					html+= '<td style="visibility:hidden;"><input type="text" name="gtamtinwords[]" id="gtamtinwords['+c+']" class="form-control" readonly /></td>';
				html+= '</tr>';
				$('#tab3 tbody').append(html); var row = $('#row_cnt').val(); $('#row_cnt').val(parseInt(row) + parseInt(1)); var newtrlen = c; if(newtrlen > 0){ $('#submit').show(); } else{ $('#submit').hide(); } document.getElementById("incr").value = c;
				$('.select2').select2(); granttotalamount();
			}
			$(document).on('click','tr',function(){	var index = $('tr').index(this); var newIndex = parseInt(index) - parseInt(1); document.getElementById("incrs").value = newIndex; });
			$(document).on('click','.delete',function(){ var index = $('.delete').index(this); var newIndex = parseInt(index) + parseInt(1); $('#tab3 > tbody > tr:eq('+newIndex+')').remove(); var row = $('#row_cnt').val(); var trlen = $('#tab3 > tbody > tr').length; var minusIndex = parseInt(trlen) - parseInt(1); if(trlen > 1){ $('.add:eq('+minusIndex+')').removeClass('disabledbutton'); $('#row_cnt').val(trlen); }else{ $('.add:eq(0)').removeClass('disabledbutton'); $('#row_cnt').val(1); } var a = document.getElementById("incr").value; a--; document.getElementById("incr").value = a; if(a > 0){ document.getElementById("rmval["+a+"]").style.visibility = "visible"; } else { document.getElementById("rmval["+a+"]").style.visibility = "hidden"; } document.getElementById("addval["+a+"]").style.visibility = "visible"; granttotalamount(); });
			
			function granttotalamount(){
				var s = document.getElementById("incr").value;
				var k = l = 0;
				for(var j=0;j<=s;j++){
					k = document.getElementById("amount["+j+"]").value;
					l = parseFloat(l) + parseFloat(k);
				}
				s++;
				document.getElementById("tno").value = s;
				document.getElementById("gtamt").value = l;
			}
			function fetchbalance(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var bal_flag = '<?php echo $bal_flag; ?>';
                    if(parseFloat(bal_flag) > 0){
                        var e = document.getElementById("pname["+d+"]").value;
                        var f = e.split("@");
                        var g = f[0];
                        if(!e.match("select")){
                            var prices = new XMLHttpRequest();
                            var method = "GET";
                            var url = "chicken_supplier_balances.php?vendors="+g+"&row_cnt="+d;
                            var asynchronous = true;
                            prices.open(method, url, asynchronous);
                            prices.send();
                            prices.onreadystatechange = function(){
                                if(this.readyState == 4 && this.status == 200){
                                    var res = this.responseText;
                                    var info = res.split("[@$&]");
                                    var rows = info[0];
                                    var balance = info[1];
                                    //alert(res);  
                                    if(balance == null || balance == "") {
                                        document.getElementById("balc["+rows+"]").value = "0.00";
                                    }
                                    else {
                                        document.getElementById("balc["+rows+"]").value = balance;
                                    }
                                }
                            }
                        }
                        else { }
                    }
                }
			function redirection_page(){ window.location.href = "pur_displaypayments.php"; }
			function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function checkval(){
				document.getElementById("submittrans").style.visibility = "hidden";
				var a = document.getElementById("incr").value;
				var secs_dflag = '<?php echo $secs_dflag; ?>';
				var k = true;
				var sector = "";
				for (var j=0;j<=a;j++){
					if(k == true){
						var b = document.getElementById("pname["+j+"]").value;
						var c = document.getElementById("mode["+j+"]").value;
						var d = document.getElementById("code["+j+"]").value;
						var e = document.getElementById("amount["+j+"]").value;
						//var f = document.getElementById("dcno["+j+"]").value;
						if(parseInt(secs_dflag) == 1){ sector = document.getElementById("sector["+j+"]").value; } else{ sector = ""; }
						
						//var h = document.getElementById("cno["+j+"]").value;
						var l = j; l++;
						if(b.match("select")){
							alert("Please select supplier name in row : "+l);
							k = false;
						}
						else if(c.match("select")){
							alert("Please select mode of payment in row : "+l);
							k = false;
						}
						else if(d.match("select")){
							alert("Please select Paying method in row : "+l);
							k = false;
						}
						else if(e == 0 || e == "" || e.lenght == 0){
							alert("Please enter amount in row : "+l);
							k = false;
						}
						/*else if(f.lenght == 0 || f == ""){
							alert("Please enter Document No. in row : "+l);
							k = false;
						}*/
						else if(parseInt(secs_dflag) == 1 && sector == "select"){
							alert("Please select sector in row : "+l);
							k = false;
						}
						/*else if(c.match("MOD-002")){
							if(h.length == 0){
								alert("Please cheque No. in row : "+l);
								k = false;
							}
							else {
								k = true;
							}
						}*/
						else {
							k = true;
						}
					}
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
			function updatecode(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
				//var a = document.getElementById("incrs").value;
				var b = document.getElementById("mode["+d+"]").value;
				removeAllOptions(document.getElementById("code["+d+"]"));
				
				myselect = document.getElementById("code["+d+"]"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("select"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				if(b.match("MOD-001")){
					//document.getElementById("cno["+d+"]").style.visibility = "hidden";
					//document.getElementById("cdate["+d+"]").style.visibility = "hidden";
					<?php
					$sql="SELECT * FROM `acc_coa` WHERE `ctype` LIKE '%Cash%' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?> 
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['description']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
					<?php } ?>
				}
				else {
					<?php
					$sql="SELECT * FROM `acc_coa` WHERE `ctype` LIKE '%Bank%' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?> 
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['description']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
					<?php } ?>
				}
				//theOption1=document.createElement("OPTION"); theText1=document.createTextNode("Other"); theOption1.value = "Other"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
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
		<script src="main_numbertoamount.js"></script>
		<footer align="center"> <?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer>
	</body>
</html>