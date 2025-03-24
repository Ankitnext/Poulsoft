<?php
	session_start(); include "newConfig.php";
	include "header_head.php";
?>
<html>
	<head>
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
		<section class="content">
			<div class="box box-default">
			<?php
				$id = $_GET['id'];
				$sql = "SELECT * FROM `item_details` WHERE `id` = '$id'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$description = $row['description'];
					$category = $row['category'];
					$sunits = $row['sunits'];
					$cunits = $row['cunits'];
				}
			?>
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
							<form action="FetchItemDetails.php" method="get" role="form" onsubmit="return checkval();" name="form_name" id = "form_id" >
								<div class="col-md-12">
									<div class="form-group col-md-4"></div>
									<div class="form-group col-md-3">
										<label>Item Category<b style="color:red;">&nbsp;*</b></label>
										<select name="icats[]" id="icats[]" style="width: 100%;" class="select" multiple>
											<option value="select">select</option>
											<?php
												$sql = "SELECT * FROM `item_category` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
											?>
													<option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
											<?php
												}
											?>
										</select>
									</div>
									<div class="form-group col-md-4"></div>
								</div>
								<div class="box-body" align="center">
									<button type="submit" name="submittrans" id="submittrans" value="addpage" class="btn btn-flat btn-social btn-linkedin">
										<i class="fa fa-save"></i> Submit
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
				var a = document.getElementById("icats").value;
				if(a == ""){
					alert("Select atleast one category ..!");
					document.getElementById("icats").focus();
					return false;
				}
				else {
					return true;
				}
			}
			function redirection_page(){
				window.location.href = "main_displayprices.php";
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
