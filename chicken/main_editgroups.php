<?php
	session_start(); include "newConfig.php";
	include "header_head.php";

	$sql = "SELECT * FROM `chicken_designation` WHERE `description` LIKE '%Sales Manager%' AND  `active` = '1' ORDER BY id";
	$query = mysqli_query($conn,$sql); $desg_code = $desg_name = array();
	while($row = mysqli_fetch_assoc($query)){ $desg_code[$row['code']] = $row['code']; $desg_name[$row['code']] = $row['name']; }
    $desg_list = implode("','",$desg_code);

	$sql = "SELECT * FROM `chicken_employee` WHERE `desig_code` IN ('$desg_list') AND `active` = '1' ORDER BY id";
	$query = mysqli_query($conn,$sql); $emp_code = $emp_name = array();
	while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }

	$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'main_displaygroups.php' AND `field_function` = 'sale manager' AND `flag` = '1' ORDER BY id";
	$query = mysqli_query($conn,$sql); $em_flag = mysqli_num_rows($query);
?>
<html>
	<head>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
		  <h1>Fill all required fields</h1>
		  <ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li><a href="#">Supplier &amp; Customer</a></li>
			<li class="active">Group</li>
			<li class="active">Add</li>
		  </ol>
		</section>
		<section class="content">
			<form action="main_savegroups.php" method="get" role="form" onsubmit="return checkval()" name="form_name" id = "form_id">
				<div class="box box-warning"><br/>
					<?php
						$id = $_GET['id'];
						$sql = "SELECT * FROM `main_groups` WHERE `id` = '$id'"; $query = mysqli_query($conn,$sql);
						while($row = mysqli_fetch_assoc($query)){
							$gtype = $row['gtype'];
							$smtype = $row['sm_code'];
							$description = $row['description'];
						}
					?>
					<div class="box-body">
						<div class="col-md-12">
							<div class="form-group col-md-4"></div>
							<div class="form-group col-md-4">
								<label>Group Type<b style="color:red;">&nbsp;*</b></label>
								<select name="gtype" id="gtype" class="form-control select2" style="width: 100%;" onchange="em_set()">
									<option value="<?php echo $gtype; ?>" selected disabled ><?php if($gtype == "S"){ echo "Supplier"; } else if($gtype == "C") { echo "Customer"; } else { echo "Both"; }?></option>
								</select>
							</div>
							<div class="form-group col-md-4"></div>
						</div>
						<?php if($em_flag > 0){ ?>
						<div class="col-md-12" id="salesManagerWrapper" style="display: none;">
							<div class="form-group col-md-4"></div>
							<div class="form-group col-md-4">
								<label>Sales Manager<b style="color:red;">&nbsp;*</b></label>
								<select name="smtype" id="smtype" class="form-control select2" style="width: 100%;">
									<option value="all">All</option>
                                <?php foreach($emp_code as $ecode){ ?>
									<option value="<?php echo $ecode ?>" <?php if($smtype == $ecode){ echo "selected";} ?>><?php echo $emp_name[$ecode] ?></option>
								<?php } ?>
									
								</select>
							</div>
							<div class="form-group col-md-4"></div>
						</div>
						<?php } else {}?>
						<div class="col-md-12">
							<div class="col-md-4"></div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Description</label>
									<input type="text" name="gdesc" id="gdesc" class="form-control" placeholder="Enter description..." value="<?php echo $description; ?>" onkeyup="validatename(this.id)">
								</div>
								<div class="form-group" style="visibility:hidden;">
									<label>Id</label>
									<input type="text" name="id" id="id" class="form-control" value="<?php echo $id; ?>">
								</div>
								<br/>
								<div class="box-body" align="center">
									<button type="submit" name="submittrans" id="submittrans" value="updatepage" class="btn btn-flat btn-social btn-linkedin">
										<i class="fa fa-save"></i> Update
									</button>&ensp;&ensp;&ensp;&ensp;
									<button type="button" name="cancelled" id="cancelled" class="btn btn-flat btn-social btn-google" onclick="redirection_page()">
										<i class="fa fa-trash"></i> Cancel
									</button>
								</div>
							</div>
							<div class="col-md-4"></div>
						</div>
					</div>
				</div>
			</form>
		</section>
		<script>
			function checkval(){
				var a = document.getElementById("gtype").value;
				var b = document.getElementById("gdesc").value;
				if(a.match("select")){
					alert("Select Type ..!");
					return false;
				}
				else if(b.length == 0){
					alert("Enter Description ..!");
					return false;
				}
				else {
					return true;
				}
			}
			function redirection_page(){
				window.location.href = "main_displaygroups.php";
			}
			function em_set() {
				const em_flag = "<?php echo $em_flag ?>";
				if(em_flag > 0 ) {
					const gtype = document.getElementById("gtype").value;
					const salesManager = document.getElementById("salesManagerWrapper");

					if (gtype === "C" || gtype === "S&C") {
						salesManager.style.display = "block";
					} else {
						salesManager.style.display = "none";
					}
				}
			}
			em_set();
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