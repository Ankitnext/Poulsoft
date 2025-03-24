<?php
	session_start(); include "newConfig.php";
	include "header_head.php";
	$sql = "SELECT * FROM `main_officetypes` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ if($row['description'] == "Shop" || $row['description'] == "shop"){ $shop_code = $row['code']; } }
?>
<html>
	<head>
	<style>
		#stds,#stdcosts,#iselec1,#iselec,#iwpac1,#iwpac,#ilimit1,#ilimit,#icogs1,#icogs,#istartvalue1,#istartvalue,#isalesac1,#isalesac,#iseries1,#iseries,#israc1,#israc,#issva1,#issva {
			visibility: hidden;
		}
	</style>
</head>
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
    <!-- Main content -->
    <section class="content">
      <!-- SELECT2 EXAMPLE -->
      <div class="box box-default">
        <?php
			/*$id = $_GET['id'];
			$sql = "SELECT * FROM `inv_itemdetails` WHERE `id` = '$id'"; $query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){
				$description = $row['description'];
				$category = $row['category'];
				$sunits = $row['sunits'];
				$cunits = $row['cunits'];
			}*/
		?>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-12">
				<form action="inv_updateofficemaster.php" method="get" role="form" onsubmit="return checkval()" name="form_name" id = "form_id" >
					<div class="col-md-12">
						<div class="form-group col-md-4"></div>
						<div class="form-group col-md-4">
							<label>Sector Description<b style="color:red;">&nbsp;*</b></label>
							<input type="text" name="idesc" id="idesc" class="form-control" placeholder="Enter description..." onkeyup="validatename(this.id)">
						</div>
						<div class="form-group col-md-4"></div>
					</div>
					<div class="col-md-12">
						<div class="form-group col-md-4"></div>
						<div class="form-group col-md-3">
							<label>Sector Type<b style="color:red;">&nbsp;*</b></label>
							<select name="stype" id="stype" class="form-control select2" style="width: 100%;" onchange="addretailinfo()">
								<option value="select">select</option>
								<?php
									$sql = "SELECT * FROM `main_officetypes` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){
								?>
										<option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
								<?php
									}
								?>
							</select>
						</div>
						<div class="form-group col-md-1" style="padding-top: 12px;"><br/>
							<a href="acc_addsectortype.php" target="_new"><i class="fa fa-plus"></i></a>
						</div>
						<div class="form-group col-md-4"></div>
					</div>
					<div class="col-md-12">
						<div class="form-group col-md-4"></div>
						<div class="form-group col-md-4">
							<label>Location<b style="color:red;">&nbsp;*</b></label>
						<input type="text" name="sloc" id="sloc" class="form-control" placeholder="Enter Location..." onkeyup="validatename(this.id)">
						</div>
						<div class="form-group col-md-4"></div>
					</div>
					<div class="col-md-12" id="shop_details" style="visibility:hidden;">
						<div class="col-md-12">
							<div class="row">
								<div class="form-group col-md-4">
									<label>Shop Manager</label>
									<input type="text" name="shop_manager" id="shop_manager" class="form-control" placeholder="Manager Name...">
								</div>
								<div class="form-group col-md-4">
									<label>Phone/Mobile</label>
									<input type="text" name="shop_mobile" id="shop_mobile" class="form-control" placeholder="Mobile No...">
								</div>
								<div class="form-group col-md-4">
									<label>State</label>
									<input type="text" name="shop_state" id="shop_state" class="form-control" placeholder="State...">
								</div>
							</div>
						</div>
						<div class="col-md-12">
							<div class="row">
								<div class="form-group col-md-6">
									<label>Address</label>
									<input type="text" name="shop_address" id="shop_address" class="form-control" placeholder="Enter Address...">
								</div>
								<div class="form-group col-md-6">
									<label>Email</label>
									<input type="text" name="shop_email" id="shop_email" class="form-control" placeholder="Enter Email...">
								</div>
							</div>
						</div>
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
    </section>
<script>
function checkval(){
	var a = document.getElementById("idesc").value;
	var b = document.getElementById("stype").value;
	var c = document.getElementById("sloc").value;
	if(a.length == 0){
		alert("Enter Description ..!");
		document.getElementById("idesc").focus();
		return false;
	}
	else if(b.match("select")){
		alert("Select Category ..!");
		document.getElementById("stype").focus();
		return false;
	}
	else if(c.length == 0){
		alert("Enter Location ..!");
		document.getElementById("sloc").focus();
		return false;
	}
	else {
		return true;
	}
}
function redirection_page(){
	window.location.href = "main_displayoffices.php";
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
function addretailinfo(){
	var a = document.getElementById("stype").value;
	<?php
		echo "if(a == '$shop_code'){";
	?>
	document.getElementById("shop_details").style.visibility = "visible";
	<?php	
		echo "} else{";
	?>
	document.getElementById("shop_details").style.visibility = "hidden";
	<?php
		echo "}";
	?>
}
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