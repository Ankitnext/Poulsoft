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
			<li><a href="#">Masters</a></li>
			<li class="active">Financial Year</li>
			<li class="active">Add</li>
		  </ol>
		</section>
		<section class="content">
			<form action="main_updatedataentrydaterange.php" method="post" role="form" name="form_name" id = "form_id">
				<div class="box box-warning"><br/>
					<div class="box-body">
						<div class="col-md-12">
							<div class="form-group col-md-3"></div>
							<div class="form-group col-md-6">
								<label>Entry start Days<b style="color:red;">&nbsp;*</b></label>
								<input type="text" name="days" id="days" class="form-control" value="1" onkeyup="validatenum(this.id)">
								<label>Type<b style="color:red;">&nbsp;*</b></label>
								<select name="ctype" id="ctype" class="form-control select2">
									<option value="all">All</option>
									<option value="Purchase">Purchase</option>
									<option value="Sales">Sales</option>
									<option value="Payments">Payments</option>
									<option value="Receipt">Receipt</option>
									<option value="Vouchers">Vouchers</option>
									<option value="CrDr Note">CrDr Note</option>
									<option value="Stock Transfer">Stock Transfer</option>
									<option value="Closing Stock">Closing Stock</option>
								</select>
							</div>
							<div class="form-group col-md-3"></div>
						</div>
						<div class="col-md-12">
							<div class="col-md-4"></div>
							<div class="col-md-4">
								<br/>
								<div class="box-body" align="center">
									<button type="submit" name="submittrans" id="submittrans" value="addpage" class="btn btn-flat btn-social btn-linkedin">
										<i class="fa fa-save"></i> Save
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
			function redirection_page(){
				window.location.href = "main_displaydatantrydaterange.php";
			}
			function validatenum(x) {
				expr = /^[0-9]*$/;
				var a = document.getElementById(x).value;
				if(a.length > 10){
					a = a.substr(0,a.length - 1);
				}
				if(!a.match(expr)){
					a = a.replace(/[^0-9]/g, '');
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