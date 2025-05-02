<?php
	session_start(); include "newConfig.php";
	include "header_head.php";
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;

	$sql = "SELECT * FROM `master_itemfields` WHERE `flag` = '1' AND (`receipt_sms` = '1' || `receipt_wapp` = '1')"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
    if($ccount > 0){ 
        $sql = "SELECT * FROM `master_loadingscreen` WHERE `project` LIKE 'CTS' AND `type` LIKE 'WAPP-MSG' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conns,$sql);
		while($row = mysqli_fetch_assoc($query)){ $loading_title = $row['title']; $loading_stitle = $row['sub_title']; }
    }
	else{
		$sql = "SELECT * FROM `master_loadingscreen` WHERE `project` LIKE 'CTS' AND `type` LIKE 'SAVE-DATA' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conns,$sql);
		while($row = mysqli_fetch_assoc($query)){ $loading_title = $row['title']; $loading_stitle = $row['sub_title']; }
	}
?>
<html>
	<head>
		<!--<link rel="stylesheet" type="text/css" href="loading_screen.css">-->
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
			<h1>Add Receipts</h1>
			<h5>Please fill mandatory (<b style="color:red;"> * </b>) fields</h5>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Transaction</a></li>
				<li class="active">Receipt Display</li>
				<li class="active">Add</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
			<?php
				$today = date("Y-m-d");
				$fdate = date("d.m.Y",strtotime($today));
				$y = date("y"); $y2 = $y + 1; $m = date("m"); $d = date("d");
				$fpcode = $fpname = $wcode = $wdesc = $acode = $adesc = array();
				$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$fpcode[$row['code']] = $row['code'];
					$fpname[$row['code']] = $row['name'];
				} $fpsize = sizeof($fpcode);
				$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$wcode[$row['code']] = $row['code'];
					$wdesc[$row['code']] = $row['description'];
				}
				$sql = "SELECT * FROM `acc_modes` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$acode[$row['code']] = $row['code'];
					$adesc[$row['code']] = $row['description'];
				}
				$idisplay = ''; $ndisplay = 'style="display:none;';
			?>
			
				<div class="box-body" style="min-height:400px;">
					<div class="row">
						<div class="col-md-18" align="center">
							<div class="col-md-18" align="center">
								<form action="cus_updatereceipts.php" method="post" role="form" onsubmit="return checkval()">
									<div class="form-group col-md-18" style="visibility:hidden;">
										<input type="text" style="width:auto;" class="form-control" name="incr" id="incr" value="0">
										<input type="text" style="width:auto;" class="form-control" name="incrs" id="incrs" value="0">
										<input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
									</div>
									<table style="width:125%;line-height:30px;" id="tab3" align="center">
										<thead>
											<tr style="line-height:30px;">
												<th><label>Date<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Customer<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Mode<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Code<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Amount<b style="color:red;">&nbsp;*</b></label></th>
												<th style="width:80px;"><label>Doc No</label></th>
												<th><label>Sector<b style="color:red;">&nbsp;*</b></label></th>
												<!--<th style="visibility:visible;" id="thcdetails" style="width:80px;"><label>Cheque No.<b style="color:red;">&nbsp;*</b></label></th>
												<th style="visibility:visible;" id="thcdetails" ><label>Cheque Date<b style="color:red;">&nbsp;*</b></label></th>-->
												<th><label>Remarks</label></th>
												<th>Action</th>
											</tr>
										</thead>
											<tr style="line-height:30px;">
												<td><input type="text"  name="pdate[]" id="pdate[0]"class="form-control rct_datepickers" value="<?php echo $fdate; ?>" readonly></td>
												<td><select name="pname[]" id="pname[0]" class="form-control select2" style="width:200px;"> <option value="select">select</option> <?php foreach($fpcode as $fcode){ ?> <option value="<?php echo $fpcode[$fcode]; ?>"><?php echo $fpname[$fcode]; ?></option> <?php } ?> </select></td>
												<td><select name="mode[]" id="mode[0]" class="form-control select2" onchange="updatecode(this.id);"> <option value="select">select</option> <?php foreach($acode as $fcode){ ?> <option value="<?php echo $acode[$fcode]; ?>"><?php echo $adesc[$fcode]; ?></option> <?php } ?> </select></td>
												<td><select name="code[]" id="code[0]" class="form-control select2" > <option value="select">select</option></select></td>
												<td><input type="text" name="amount[]" id="amount[0]" class="form-control" value="" onkeyup="granttotalamount();getamountinwords();"></td>
												<td><input type="text" name="dcno[]" id="dcno[0]" class="form-control" style="width:80px;" onkeyup="validate_docno(this.id);"></td>
												<td><select name="sector[]" id="sector[0]" class="form-control select2"  style="width:170px;">  <?php foreach($wcode as $fcode){ ?> <option value="<?php echo $wcode[$fcode]; ?>"><?php echo $wdesc[$fcode]; ?></option> <?php } ?> </select></td>
												<!--<td><input type="text" name="cno[]" id="cno[0]" class="form-control" style="width:80px;visibility:hidden;"></td>
												<td><input type="text" name="cdate[]" id="cdate[0]" class="form-control rct_datepickers" style="visibility:hidden;" value="<?php //echo $fdate; ?>" readonly></td>-->
												<td><textarea name="remark[]" id="remark[0]" class="form-control" style="height: 23px;"></textarea></td>
												<td style="width: 100%;"><a href="JavaScript:Void(0);" name="addval[]" id="addval[0]" onclick="rowgen(this.id)"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval[0]" onclick="rowdes()" style="visibility:hidden;"><i class="fa fa-minus" style="color:red;"></i></a></td>
												<td style="visibility:hidden;"><input type="text" name="gtamtinwords[]" id="gtamtinwords[0]" class="form-control" style="width:80px;" readonly /></td>
											</tr>
									</table><br/><br/><br/>
									<div class="col-md-12" align="left">
										<div class="col-md-4">
											<label>Total Receipts</label>
											<input type="text" name="tno" id="tno" class="form-control"style="width:auto;" >
										</div>
										<div class="col-md-4">
											<label>Receipt Amount</label>
											<input type="text" name="gtamt" id="gtamt" class="form-control" style="width:auto;" readonly>
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
			<!--<div class="ring"><?php //echo $loading_title; ?><span></span></div>
			<div class="ring_status" id = "disp_val"></div>-->
		</section>
		<?php include "header_foot.php"; ?>
		<script src="dist/js/adminlte.min.js"></script>
		<script src="dist/js/demo.js"></script>
		<script>
			function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submittrans").style.visibility = "hidden";
				var a = document.getElementById("incr").value;
				var k = true; 
				for (var j=0;j<=a;j++){
					if(k == true){
						var b = document.getElementById("pname["+j+"]").value;
						var c = document.getElementById("mode["+j+"]").value;
						var d = document.getElementById("code["+j+"]").value;
						var e = document.getElementById("amount["+j+"]").value;
						//var f = document.getElementById("dcno["+j+"]").value;
						var g = document.getElementById("sector["+j+"]").value;
						//var h = document.getElementById("cno["+j+"]").value;
						var l = j; l++;
						if(b.match("select")){
							alert("Please select Customer name in row : "+l);
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
						else if(g.match("select") || g == "" || g.lenght == 0){
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
					/*document.getElementsByClassName("ring")[0].style.display = "block";
					document.getElementsByClassName("ring_status")[0].style.display = "block";
					document.getElementById("disp_val").innerHTML = '<?php //echo $loading_stitle; ?>';*/
					return true;
				}
				else{
					/*document.getElementsByClassName("ring")[0].style.display = "none";
					document.getElementsByClassName("ring_status")[0].style.display = "none";
					document.getElementById("disp_val").innerHTML = "";*/

					document.getElementById("ebtncount").value = "0"; document.getElementById("submittrans").style.visibility = "visible";
					return false;
				}
			}
			function getamountinwords() {
				var a = document.getElementById("incrs").value;
				var b = document.getElementById("amount["+a+"]").value;
				var c = convertNumberToWords(b);
				document.getElementById("gtamtinwords["+a+"]").value = c;
			}
			function rowgen(a){
				var b = a.split("["); var c = b[1].split("]"); var d = c[0];
				//var c = document.getElementById("incr").value;
				document.getElementById("addval["+d+"]").style.visibility = "hidden";
				document.getElementById("rmval["+d+"]").style.visibility = "hidden";
				d++;
				var html = '';
				html+= '<tr style="line-height:30px;">';
					html+= '<td><input type="text" name="pdate[]" id="pdate['+d+']" class="form-control rct_datepickers" value="<?php echo $fdate; ?>" onmouseover="rct_displaycalendor()" readonly></td>';
					html+= '<td><select name="pname[]" id="pname['+d+']" class="form-control select2" style="width:200px;"> <option value="select">select</option> <?php foreach($fpcode as $fcode){ ?> <option value="<?php echo $fpcode[$fcode]; ?>"><?php echo $fpname[$fcode]; ?></option> <?php } ?> </select></td>';
					html+= '<td><select name="mode[]" id="mode['+d+']" class="form-control select2" onchange="updatecode(this.id);"> <option value="select">select</option> <?php foreach($acode as $fcode){ ?> <option value="<?php echo $acode[$fcode]; ?>"><?php echo $adesc[$fcode]; ?></option> <?php } ?> </select></td>';
					html+= '<td><select name="code[]" id="code['+d+']" class="form-control select2"> <option value="select">select</option></select></td>';
					html+= '<td><input type="text" name="amount[]" id="amount['+d+']" class="form-control" value="" onkeyup="granttotalamount();getamountinwords();"></td>';
					html+= '<td><input type="text" name="dcno[]" id="dcno['+d+']" class="form-control" style="width:80px;" onkeyup="validate_docno(this.id);"></td>';
					html+= '<td><select name="sector[]" id="sector['+d+']" class="form-control select2" style="width:170px;">  <?php foreach($wcode as $fcode){ ?> <option value="<?php echo $wcode[$fcode]; ?>"><?php echo $wdesc[$fcode]; ?></option> <?php } ?> </select></td>';
					html+= '<td><textarea name="remark[]" id="remark['+d+']" class="form-control" style="height: 23px;"></textarea></td>';
					html+= '<td style="width: 100%;"><a href="JavaScript:Void(0);" name="addval[]" id="addval['+d+']" onclick="rowgen(this.id)"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval['+d+']" class="delete" onclick="rowdes()" style="visibility:visible;"><i class="fa fa-minus" style="color:red;"></i></a></td>';
					html+= '<td style="visibility:hidden;"><input type="text" name="gtamtinwords[]" id="gtamtinwords['+d+']" class="form-control" style="width:80px;" readonly /></td>';
				html+= '</tr>';
				$('#tab3 tbody').append(html);
				document.getElementById("incr").value = d;
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
			function updatecode(a){
				var b = a.split("["); var c = b[1].split("]"); var d = c[0];
				var mode = document.getElementById("mode["+d+"]").value;
				removeAllOptions(document.getElementById("code["+d+"]"));
				
				myselect = document.getElementById("code["+d+"]"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("select"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				if(mode.match("MOD-001")){
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
			}
			function redirection_page(){ window.location.href = "cus_displayreceipts.php"; }
			document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submittrans').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatecount(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
		</script>
		<script src="main_numbertoamount.js"></script>
		<footer align="center"> <?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer>
	</body>
</html>