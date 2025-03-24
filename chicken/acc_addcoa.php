<?php
	session_start(); include "newConfig.php";
	include "header_head.php";
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;

	/*Check for Table Availability*/
	$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
	$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
	if(in_array("extra_access", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.extra_access LIKE poulso6_admin_broiler_broilermaster.extra_access;"; mysqli_query($conn,$sql1); }
	
	$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
			
	$sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'CoA Master' AND `field_function` LIKE 'Voucher Expense Display Flag' AND `flag` = '1'"; $query = mysqli_query($conn,$sql);
	$vouexpd_flag = mysqli_num_rows($query); if($vouexpd_flag > 0){ } else { $vouexpd_flag = 0; }
	$sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'CoA Master' AND `field_function` LIKE 'Display Driver Flag' AND `flag` = '1'"; $query = mysqli_query($conn,$sql);
	$driverd_flag = mysqli_num_rows($query); if($driverd_flag > 0){ } else { $driverd_flag = 0; }
	$sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'CoA Master' AND `field_function` LIKE 'Purchase: Supplier Add-On Fields Flag' AND `flag` = '1'"; $query = mysqli_query($conn,$sql);
	$dspaf_flag = mysqli_num_rows($query); if($dspaf_flag > 0){ } else { $dspaf_flag = 0; }
?>
<html>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Fill all required fields</h1>
				<ol class="breadcrumb">
					<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
					<li><a href="#">CoA</a></li>
					<li class="active">Create CoA</li>
					<li class="active">Add</li>
				</ol>
		</section>
    <!-- Main content -->
    <section class="content">
      <!-- SELECT2 EXAMPLE -->
      <div class="box box-default">
        
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-12">
				<form action="acc_updatecoa.php" method="get" role="form" onsubmit="return checkval()" name="form_name" id = "form_id" >
					<div class="form-group col-md-6">
						<label>Description<b style="color:red;">&nbsp;*</b></label>
						<input type="text" name="cdesc" id="cdesc" class="form-control" placeholder="Enter description..." onkeyup="validatename(this.id)">
					</div>
					<div class="form-group col-md-6">
						<label>Mobile No.</label>
						<input type="text" name="mobile_no" id="mobile_no" class="form-control" onkeyup="checkmobileno(this.id);">
					</div>
					<div class="form-group col-md-6">
						<label>Type<b style="color:red;">&nbsp;*</b></label>
						<select name="type" id="type" class="form-control select2" style="width: 100%;" onchange="setparentid()">
							<option value="select">select</option>
							<?php
								$sql = "SELECT * FROM `acc_types` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
							?>
									<option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
							<?php
								}
							?>
						</select>
					</div>
					<div class="form-group col-md-6">
						<label>Control Type</label>
						<select name="ctype" id="ctype" class="form-control select2" style="width: 100%;">
							<option value="select">select</option>
						</select>
					</div>
					<div class="form-group col-md-5">
						<label>Schedule<b style="color:red;">&nbsp;*</b></label>
						<select name="stype" id="stype" class="form-control select2" style="width: 100%;">
							<option value="select">select</option>
						</select>
					</div>
					<div class="form-group col-md-1" style="padding-top: 12px;"><br/>
						<a href="acc_addschedule.php" target="_new"><i class="fa fa-plus"></i></a>
					</div>
					<div class="form-group col-md-6">
						<label>Category<b style="color:red;">&nbsp;*</b></label>
						<select name="cptype" id="cptype" class="form-control select2" style="width: 100%;">
							<option value="select">select</option>
							<?php
								$sql = "SELECT * FROM `acc_category` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
							?>
									<option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
							<?php
								}
							?>
						</select>
					</div>
                    <div class="row col-md-12 justify-content-center align-items-center">
                        <div class="form-group col-md-2" style="width:110px;">
                            <label>Opening Balance</label>
                        </div>
                        <div class="form-group col-md-2" style="width:110px;">
                            <label for="odate">Date</label>
                            <input type="text" name="odate" id="odate" class="form-control datepicker" value="<?php echo date("d.m.Y"); ?>" style="width:100px;" readonly />
                        </div>
                        <div class="form-group col-md-2" style="width:100px;">
                        	<label for="otype">Type</label>
                            <select name="otype" id="otype" class="form-control select2" style="width:90px;">
                                <option value="select">-select</option>
                                <option value="CR">-Cr</option>
                                <option value="DR">-Dr</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2" style="width:160px;">
                            <label for="oamount">Amount</label>
                            <input type="text" name="oamount" id="oamount" class="form-control" style="width:150px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" />
                        </div>
                        <div class="form-group col-md-2" style="width:210px;">
                            <label for="osector">Cost Center</label>
                            <select name="osector" id="osector" class="form-control select2" style="width:200px;">
                                <option value="select">-select</option>
                                <?php foreach($sector_code as $wcode){ ?><option value="<?php echo $wcode; ?>"><?php echo $sector_name[$wcode]; ?></option><?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-md-2" style="width:110px;">
                            <label for="oremarks">Remarks</label>
                            <textarea name="oremarks" id="oremarks" class="form-control" style="width:100px;height:28px;"></textarea>
                        </div>
                    </div>
					<div class="form-group">
						<label>
							<input type="radio" name="checbox" id="checbox" value="BS" class="minimal" checked> Balance Statement
						</label>&ensp;&ensp;&ensp;&ensp;
						<label>
							<input type="radio" name="checbox" id="checbox" value="IS" class="minimal"> Income Statement
						</label>&ensp;&ensp;&ensp;&ensp;
						<label>
							<input type="checkbox" name="transport_flag" id="transport_flag" class="minimal"> Transporter
						</label>
                        <?php if($vouexpd_flag > 0){ ?>
							<label><input type="checkbox" name="vouexp_flag" id="vouexp_flag" class="minimal"> Voucher Expense&ensp;&ensp;</label>
						<?php } ?>
                        <?php if($driverd_flag > 0){ ?>
							<label><input type="checkbox" name="driver_flag" id="driver_flag" class="minimal"> Driver&ensp;&ensp;</label>
						<?php } ?>
                        <?php if($dspaf_flag > 0){ ?>
							<label><input type="checkbox" name="spaof_flag" id="spaof_flag" class="minimal"> Supplier Add-ons&ensp;&ensp;</label>
						<?php } ?>
					</div>
					<div class="box-body" align="center">
						<button type="submit" name="submittrans" id="submittrans" value="addpage" class="btn btn-flat btn-social btn-linkedin">
							<i class="fa fa-save"></i> Save
						</button>&ensp;&ensp;&ensp;&ensp;
						<button type="button" name="cancelled" id="cancelled" class="btn btn-flat btn-social btn-google" onclick="redirection_page()">
							<i class="fa fa-trash"></i> Cancel
						</button>
					</div>
				</form>
              <!-- /.form-group -->
            </div>
		</div>
		</div>
		</div>
		<link rel="stylesheet" href="css/datepickers/datepickers.css">
		<script src="js/datepicker/datepickerjquery.js"></script>
		<script src="js/datepicker/datepickerjqueryui.js"></script>
    </section>
	<script>
		var today = '<?php echo date("d.m.Y"); ?>';
		$('.datepicker').datepicker({ dateFormat:'dd.mm.yy',changeMonth:true,changeYear:true,maxDate: today});
	</script>
<script>
function checkval(){
	var a = document.getElementById("cdesc").value;
	var b = document.getElementById("type").value;
	//var c = document.getElementById("ctype").value;
	var d = document.getElementById("stype").value;
	var e = document.getElementById("cptype").value;
	if(a.length == 0){
		alert("Enter CoA Description ..!");
		return false;
	}
	else if(b.match("select")){
		alert("Select Type ..!");
		return false;
	}
	/*else if(c.match("select")){
		alert("Select Control Type ..!");
		return false;
	}*/
	else if(d.match("select")){
		alert("Select Schedule ..!");
		return false;
	}
	else if(e.match("select")){
		alert("Select Category ..!");
		return false;
	}
	else {
		return true;
	}
}
function redirection_page(){
	window.location.href = "acc_displaycoa.php";
}
function validatename(x) {
	expr = /^[a-zA-Z0-9 (.&)_-]*$/;
	var a = document.getElementById(x).value;
	if(a.length > 50){
		a = a.substr(0,a.length - 1);
	}
	if(!a.match(expr)){
		a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, '');
	}
	document.getElementById(x).value = a;
}
function checkmobileno(x) {
                expr = /^[0-9,]*$/;
                var a = document.getElementById(x).value;
                if(a.length > 10){
                    a = a.substr(0,a.length - 1);
                }
                if(!a.match(expr)){
                    a = a.replace(/[^0-9,]/g, '');
                }
                document.getElementById(x).value = a;
            }
function setparentid(){
	removeAllOptions(document.getElementById("stype"));
	removeAllOptions(document.getElementById("ctype"));
	
	myselect1 = document.getElementById("stype"); 
	theOption1=document.createElement("OPTION"); 
	theText1=document.createTextNode("Select"); 
	theOption1.value = "select"; 
	theOption1.appendChild(theText1); 
	myselect1.appendChild(theOption1);
	
	myselect2 = document.getElementById("ctype"); 
	theOption2=document.createElement("OPTION"); 
	theText2=document.createTextNode("Select"); 
	theOption2.value = "select"; 
	theOption2.appendChild(theText2); 
	myselect2.appendChild(theOption2);
	
	
	var stypes = document.getElementById("type").value;
	<?php
		$sql="SELECT * FROM `acc_schedules` WHERE `active` = '1' ORDER BY `description` ASC";
		$query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ echo "if(stypes == '$row[subtype]'){"; ?> 
			theOption1=document.createElement("OPTION");
			theText1=document.createTextNode("<?php echo $row['description']; ?>"); 
			theOption1.value = "<?php echo $row['code']; ?>"; 
			theOption1.appendChild(theText1); myselect1.appendChild(theOption1);	
		<?php echo "}"; } ?>
		
		<?php
		$sql="SELECT * FROM `acc_controltype` ORDER BY `controltype` ASC";
		$query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ echo "if(stypes == '$row[type]'){"; ?> 
			theOption2=document.createElement("OPTION");
			theText2=document.createTextNode("<?php echo $row['controltype']; ?>"); 
			theOption2.value = "<?php echo $row['controltype']; ?>"; 
			theOption2.appendChild(theText2); myselect2.appendChild(theOption2);	
		<?php echo "}"; } ?>
		
		
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
<?php include "header_foot.php"; ?>
</body>
</html>