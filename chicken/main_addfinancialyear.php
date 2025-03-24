<?php
	session_start(); include "newConfig.php";
	include "header_head.php";
?>
<html>
	<head>
		<?php
			$d1 = "01"; $d2 = "31";
			$m1 = "04"; $m2 = "03";
			$y1 = date("Y"); $y2 = $y1 + 1;
			
			$fdate = $d1.".".$m1.".".$y1;
			$tdate = $d2.".".$m2.".".$y2;
		?>
	</head>
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
			<form action="main_updatefinancialyear.php" method="post" role="form" name="form_name" id = "form_id" >
				<div class="box box-warning"><br/>
					<div class="box-body">
						<div class="col-md-12">
							<div class="form-group col-md-4"></div>
							<div class="form-group col-md-4">
								<label>From Date<b style="color:red;">&nbsp;*</b></label>
								<input type="text" name="fdate" id="datepickers" class="form-control" value="<?php echo $fdate; ?>">
								<label>To Date<b style="color:red;">&nbsp;*</b></label>
								<input type="text" name="tdate" id="datepickers1" class="form-control" value="<?php echo $tdate; ?>">
							</div>
							<div class="form-group col-md-4"></div>
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
				window.location.href = "main_displaydefinefinancialyear.php";
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
<?php include "header_foot.php"; ?>