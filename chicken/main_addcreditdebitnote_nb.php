<?php
	session_start(); include "newConfig.php";
	include "header_head.php";

	$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'CrDr-Note Transaction' AND `field_function` LIKE 'Display: Reason selection' AND `flag` = '1'";
	$query = mysqli_query($conn,$sql); $rsncrdr_flag = mysqli_num_rows($query);

	if((int)$rsncrdr_flag == 1){
		$sql = "SELECT * FROM `crdr_note_reasons` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC";
		$query = mysqli_query($conn,$sql); $reason_code = $reason_name = array();
		while($row = mysqli_fetch_assoc($query)){ $reason_code[$row['code']] = $row['code']; $reason_name[$row['code']] = $row['description']; }
	}

	//check and fetch date range
    global $drng_cday; $drng_cday = 0; global $drng_furl; $drng_furl = str_replace("_add_","_display_",basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
    include "poulsoft_fetch_daterange_master.php";
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
				width: 12%;
				text-align: center;
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Add Credit / Debit Note</h1>
			<h5>Please fill mandatory (<b style="color:red;"> * </b>) fields</h5>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Transaction</a></li>
				<li class="active">Credit / Debit Display</li>
				<li class="active">Add</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
			<?php
				$today = date("Y-m-d");
				$fdate = date("d.m.Y",strtotime($today));
				$y = date("y"); $y2 = $y + 1; $m = date("m"); $d = date("d");
				$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$wcode[$row['code']] = $row['code'];
					$wdesc[$row['code']] = $row['description'];
				}
				$sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $disccode = "";
				while($row = mysqli_fetch_assoc($query)){
					$acode[$row['code']] = $row['code'];
					$adesc[$row['code']] = $row['description'];
                    if($row['description'] == 'Discount'){
                        $disccode = $row['code'];
                    }
				}
				$idisplay = ''; $ndisplay = 'style="display:none;';
			$sql = "SELECT * FROM `dataentry_daterange` WHERE `active` = '1'";
			$query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){
				$from_date = date("d.m.Y",strtotime($row['fromdate']));
				$to_date = date("d.m.Y",strtotime($row['todate']));
			}
			?>
			
				<div class="box-body" style="min-height:400px;">
					<div class="row">
						<div class="col-md-18" align="center">
							<div class="col-md-18" align="center">
								<form action="main_updatecreditdebitnote.php" method="post" role="form" onsubmit="return checkval()" name="form_name" id = "form_id" >
									<div class="form-group col-md-18" style="visibility:hidden;">
										<input type="text" style="width:auto;" class="form-control" name="incr" id="incr" value="0">
										<input type="text" style="width:auto;" class="form-control" name="incrs" id="incrs" value="0">
									</div>
									<div class="form-group col-md-18">
									<table style="width:100%;line-height:30px;" id="tab3" align="center">
										<thead>
											<tr style="line-height:30px;">
												<th><label>S/C Type<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>C/D Type<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Supplier/Customer<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Date<b style="color:red;">&nbsp;*</b></label></th>
												<th style="width:80px;"><label>Doc No</label></th>
												<th><label>Account<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Amount<b style="color:red;">&nbsp;*</b></label></th>
												<?php if((int)$rsncrdr_flag == 1){ echo '<th><label>Reason</label></th>'; } ?>
												
												<th><label>Remarks</label></th>
												<th>Action</th>
                                                <th style="visibility:hidden"><label>Sector<b style="color:red;">&nbsp;*</b></label></th>
											</tr>
										</thead>
											<tr style="line-height:30px;">
												<td><select name="vtype[]" id="vtype[0]" class="form-control select2" onchange="selectcsdetails()" style="width:100%"> <option value="select">select</option><option value="S">Supplier</option><option value="C">Customer</option></select></td>
												<td><select name="cdtype[]" id="cdtype[0]" class="form-control select2" style="width:100%"> <option value="select">select</option><option value="CN">Credit Note</option><option value="DN">Debit Note</option></select></td>
												<td><select name="pname[]" id="pname[0]" class="form-control select2" style="width:100%"> <option value="select">select</option></select></td>
												<td><input type="text" name="pdate[]" id="pdate[0]"class="form-control range_picker" value="<?php echo $fdate; ?>" readonly></td>
												<td><input type="text" name="dcno[]" id="dcno[0]" class="form-control" style="width:80px;" ></td>
                                                <td>
                                                <select name="mode[]" id="mode[0]" class="form-control select2" style="width:100%">
                                                    <option value="select">select</option> 
                                                    <?php foreach($acode as $fcode){ ?> 
                                                        <option value="<?php echo $fcode; ?>" 
                                                                <?php echo ($fcode == $disccode) ? 'selected' : ''; ?>>
                                                            <?php echo $adesc[$fcode]; ?>
                                                        </option> 
                                                    <?php } ?> 
                                                </select>
                                            </td>
												<td><input type="text" name="amount[]" id="amount[0]" class="form-control" value="0" onkeyup="granttotalamount()" onchange="validateamount(this.id);"></td>
												<?php if((int)$rsncrdr_flag == 1){ ?><td><select name="reason_code[]" id="reason_code[0]" class="form-control select2" style="width:100%"> <?php foreach($reason_code as $rcode){ ?> <option value="<?php echo $rcode; ?>"><?php echo $reason_name[$rcode]; ?></option> <?php } ?> </select></td><?php } ?>
												
												<td><textarea name="remark[]" id="remark[0]" class="form-control" style="height: 23px;"></textarea></td>
												<td style="width: 100%;"><a href="JavaScript:Void(0);" name="addval[]" id="addval[0]" onclick="rowgen(this.id)"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval[0]" onclick="rowdes()" style="visibility:hidden;"><i class="fa fa-minus" style="color:red;"></i></a></td>
												<td style="visibility:hidden;"><input type="text" name="gtamtinwords[]" id="gtamtinwords[0]" class="form-control" readonly /></td>
                                                <td style="visibility:hidden"><select name="sector[]" id="sector[0]" class="form-control select2" style="width:100%"> <?php foreach($wcode as $fcode){ ?> <option value="<?php echo $wcode[$fcode]; ?>"><?php echo $wdesc[$fcode]; ?></option> <?php } ?> </select></td>
											</tr>
									</table></div><br/><br/><br/>
									<div class="col-md-12" align="left">
										<div class="col-md-4">
											<label>Total Credit / Debit</label>
											<input type="text" name="tno" id="tno" class="form-control"style="width:auto;" >
										</div>
										<div class="col-md-4">
											<label>Credit / Debit Amount</label>
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
		</section>
		<?php include "header_foot.php"; ?>
		<script src="dist/js/adminlte.min.js"></script>
		<script src="dist/js/demo.js"></script>
		<script>
			//Date Range selection
            var s_date = '<?php echo $rng_sdate; ?>'; var e_date = '<?php echo $rng_edate; ?>';
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
				var rsncrdr_flag = '<?php echo $rsncrdr_flag; ?>';
				var html = '';
				html+= '<tr style="line-height:30px;">';
					html+= '<td><select name="vtype[]" id="vtype['+d+']" class="form-control select2" onchange="selectcsdetails()" style="width:100%"><option value="select">select</option><option value="S">Supplier</option><option value="C">Customer</option></select></td>';
					html+= '<td><select name="cdtype[]" id="cdtype['+d+']" class="form-control select2" style="width:100%"> <option value="select">select</option><option value="CN">Credit Note</option><option value="DN">Debit Note</option></select></td>';
					html+= '<td><select name="pname[]" id="pname['+d+']" class="form-control select2" style="width:100%"> <option value="select">select</option></select></td>';
					html+= '<td><input type="text" name="pdate[]" id="pdate['+d+']" class="form-control range_picker" value="<?php echo $fdate; ?>" onmouseover="crdr_displaycalendor()" readonly></td>';
					html+= '<td><input type="text" name="dcno[]" id="dcno['+d+']" class="form-control" style="width:80px;"></td>';
					html+= '<td><select name="mode[]" id="mode[0]" class="form-control select2" style="width:100%"><option value="select">select</option> <?php foreach($acode as $fcode){ ?> <option value="<?php echo $fcode; ?>" <?php echo ($fcode == $disccode) ? 'selected' : ''; ?>><?php echo $adesc[$fcode]; ?></option> <?php } ?> </select></td>';
					html+= '<td><input type="text" name="amount[]" id="amount['+d+']" class="form-control" value="0" onkeyup="granttotalamount()" onchange="validateamount(this.id);"></td>';
					if(parseInt(rsncrdr_flag) == 1){ html+= '<td><select name="reason_code[]" id="reason_code['+d+']" class="form-control select2" style="width:100%"> <?php foreach($reason_code as $rcode){ ?> <option value="<?php echo $rcode; ?>"><?php echo $reason_name[$rcode]; ?></option> <?php } ?> </select></td>'; }
					
					html+= '<td><textarea name="remark[]" id="remark['+d+']" class="form-control" style="height: 23px;"></textarea></td>';
					html+= '<td style="width: 100%;"><a href="JavaScript:Void(0);" name="addval[]" id="addval['+d+']" onclick="rowgen(this.id)"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval['+d+']" class="delete" onclick="rowdes()" style="visibility:visible;"><i class="fa fa-minus" style="color:red;"></i></a></td>';
					html+= '<td style="visibility:hidden;"><input type="text" name="gtamtinwords[]" id="gtamtinwords['+d+']" class="form-control" readonly /></td>';
                    html+= '<td style="visibility:hidden"><select name="sector[]" id="sector['+d+']" class="form-control select2" style="width:100%"> <?php foreach($wcode as $fcode){ ?> <option value="<?php echo $wcode[$fcode]; ?>"><?php echo $wdesc[$fcode]; ?></option> <?php } ?> </select></td>';
				html+= '</tr>';
				$('#tab3 tbody').append(html); var row = $('#row_cnt').val(); $('#row_cnt').val(parseInt(row) + parseInt(1)); var newtrlen = d; if(newtrlen > 0){ $('#submit').show(); } else{ $('#submit').hide(); } document.getElementById("incr").value = d;
				$('.select2').select2(); granttotalamount();
				$( ".range_picker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", minDate: s_date, maxDate: e_date, beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });

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
			function redirection_page(){ window.location.href = "main_displaycreditdebitnote.php"; }
			function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; getamountinwords(); }
			function checkval(){
				document.getElementById("submittrans").style.visibility = "hidden";
				var a = document.getElementById("incr").value;
				for (var j=0;j<=a;j++){
					var b = document.getElementById("vtype["+j+"]").value;
					var c = document.getElementById("cdtype["+j+"]").value;
					var d = document.getElementById("pname["+j+"]").value;
					//var e = document.getElementById("dcno["+j+"]").value;
					var f = document.getElementById("mode["+j+"]").value;
					var g = document.getElementById("amount["+j+"]").value;
					var h = document.getElementById("sector["+j+"]").value;
					var i = false; var l = j; l++;
					if(b.match("select")){
						alert("Please select supplier / Customer type in row : "+l);
						k = false;
					}
					else if(c.match("select")){
						alert("Please select Credit / Debit type in row : "+l);
						k = false;
					}
					else if(d.match("select")){
						alert("Please select supplier / Customer name in row : "+l);
						k = false;
					}
					/*else if(e == 0 || e == "" || e.lenght == 0){
						alert("Please enter Document No. in row : "+l);
						k = false;
					}*/
					else if(f.match("select")){
						alert("Please select CoA in row : "+l);
						k = false;
					}
					else if(g == 0 || g.lenght == 0 || g == ""){
						alert("Please enter Amount in row : "+l);
						k = false;
					}
					else if(h.match("select") || h.lenght == 0 || h == ""){
						alert("Please select sector in row : "+l);
						k = false;
					}
					else {
						k = true;
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
			function selectcsdetails(){
				var a = document.getElementById("incrs").value;
				var b = document.getElementById("vtype["+a+"]").value;
				removeAllOptions(document.getElementById("pname["+a+"]"));
				myselect = document.getElementById("pname["+a+"]"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("select"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				if(b.match("S")){
					<?php
					$sql="SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?> 
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['name']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
					<?php } ?>
				}
				else if(b.match("C")){
					<?php
					$sql="SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1'  ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?> 
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['name']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
					<?php } ?>
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
		<script src="main_numbertoamount.js"></script>
		<script src="handle_ebtn_as_tbtn.js"></script>
		<script>
			//Date Range selection
			$( ".range_picker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", minDate: s_date, maxDate: e_date, beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
		</script>
		<footer align="center"> <?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer>
	</body>
</html>