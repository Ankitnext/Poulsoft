<?php
	session_start(); include "newConfig.php";
	include "header_head.php";
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
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
						<div class="col-md-6">
							<form action="acc_updateschedule.php" method="post" role="form" onsubmit="return checkval()" name="form_name" id = "form_id" >
								<div class="form-group">
									<label>Description</label>
									<input type="text" name="cdesc" id="cdesc" class="form-control" placeholder="Enter description..." onkeyup="validatename(this.id)">
								</div>
								<div class="form-group">
									<label>Type</label>
									<select name="ctype" id="ctype" class="form-control select2" style="width: 100%;" onchange="setparentid()">
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
								<div class="form-group">
									<label>Parent Schedule</label>
									<select name="cptype" id="cptype" class="form-control select2" style="width: 100%;">
										<option value="select">select</option>
									</select>
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
						</div>
					</div>
				</div>
			</div>
		</section>
		<script>
			function checkval(){
				var a = document.getElementById("cdesc").value;
				var b = document.getElementById("ctype").value;
				var c = document.getElementById("cptype").value;
				if(a.length == 0){
					alert("Enter Schdule name ..!");
					return false;
				}
				else if(b.match("select")){
					alert("Select Type ..!");
					return false;
				}
				else if(c.match("select")){
					alert("Select Parent Schedule ..!");
					return false;
				}
				else {
					return true;
				}
			}
			function redirection_page(){
				window.location.href = "acc_displayschedule.php";
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
			function setparentid(){
				removeAllOptions(document.getElementById("cptype"));
				
				myselect1 = document.getElementById("cptype"); 
				theOption1=document.createElement("OPTION"); 
				theText1=document.createTextNode("Select"); 
				theOption1.value = "select"; 
				theOption1.appendChild(theText1); 
				myselect1.appendChild(theOption1);
				var stypes = document.getElementById("ctype").value;
				<?php
					$sql="SELECT * FROM `acc_category` WHERE `active` = '1' ORDER BY `description` ASC";
					$query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ echo "if(stypes == '$row[subtype]'){"; ?> 
						theOption1=document.createElement("OPTION");
						theText1=document.createTextNode("<?php echo $row['description']; ?>"); 
						theOption1.value = "<?php echo $row['code']; ?>"; 
						theOption1.appendChild(theText1); myselect1.appendChild(theOption1);	
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
		<footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer>
	</body>
</html>