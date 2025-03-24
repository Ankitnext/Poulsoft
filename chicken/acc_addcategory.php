<?php
	session_start(); include "newConfig.php";
	include "header_head.php";
?>
<html>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Fill all required fields</h1>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">CoA</a></li>
				<li class="active">Schedule</li>
				<li class="active">Add</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
							<form action="acc_updatecategory.php" method="post" role="form" onsubmit="return checkval()" name="form_name" id = "form_id" >
								<div class="form-group">
									<label>Type</label>
									<select name="ctype" id="ctype" class="form-control select2" style="width: 100%;">
										<option value="select">select</option>
										<?php
											$sql = "SELECT * FROM `acc_types` WHERE `flag` = '1' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
											while($row = mysqli_fetch_assoc($query)){
										?>
												<option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
										<?php
											}
										?>
									</select>
								</div>
								<div class="form-group">
									<label>Description</label>
									<input type="text" name="cdesc" id="cdesc" class="form-control" placeholder="Enter description..." onkeyup="validatename(this.id)">
								</div>
								<br/>
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
		</section>
		<?php include "header_foot.php"; ?>
		<script>
			function checkval(){
				var a = document.getElementById("ctype").value;
				var b = document.getElementById("cdesc").value;
				if(a.match("select")){
					alert("Select Category type ..!");
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
				window.location.href = "acc_displaycategory.php";
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