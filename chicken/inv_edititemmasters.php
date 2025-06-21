<?php
	session_start(); include "newConfig.php";
	include "header_head.php";

	$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Item Master' AND `field_function` LIKE 'Short-Name' AND `user_access` LIKE 'all' AND `flag` = '1'";
	$query = mysqli_query($conn,$sql); $sname_flag = mysqli_num_rows($query);
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
			$id = $_GET['id'];
			$sql = "SELECT * FROM `item_details` WHERE `id` = '$id'"; $query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){
				$description = $row['description'];
				$short_name = $row['short_name'];
				$category = $row['category'];
				$sunits = $row['sunits'];
				$cunits = $row['cunits'];
			}
		?>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-12">
				<form action="inv_updateitemmaster.php" method="get" role="form" onsubmit="return checkval()" name="form_name" id = "form_id" >
					<div class="form-group col-md-6">
						<label>Description<b style="color:red;">&nbsp;*</b></label>
						<input type="text" name="idesc" id="idesc" class="form-control" value="<?php echo $description; ?>" placeholder="Enter description..." onkeyup="validatename(this.id)">
					</div>
					<?php
					if((int)$sname_flag == 1){
					?>
					<div class="form-group col-md-6">
						<label>Short Name<b style="color:red;">&nbsp;*</b></label>
						<input type="text" name="short_name" id="short_name" class="form-control" value="<?php echo $short_name; ?>" onkeyup="validatename(this.id)">
					</div>
					<div class="form-group col-md-6">
					<?php
					}
					else{
					?>
					<div class="form-group col-md-6">
					<?php
					}
					?>
						<label>Category<b style="color:red;">&nbsp;*</b></label>
						<select name="icat" id="icat" class="form-control select2" style="width: 100%;">
							<option value="select">select</option>
							<?php
								$sql = "SELECT * FROM `item_category` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
							?>
									<option <?php if($category == $row['code']){ echo 'selected'; } ?> value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
							<?php
								}
							?>
						</select>
					</div>
					<?php
					if((int)$sname_flag == 1){
					?>
					<div class="form-group col-md-6">
						<div class="form-group col-md-12">
						<?php
					}
					else{
					?>
					<div class="form-group col-md-12">
						<div class="form-group col-md-6">
					<?php
					}
					?>
						<label>Consumption Unit</label>
						<select name="istored" id="istored" class="form-control select2" style="width: 100%;" onchange="">
							<option value="select">select</option>
							<?php
								$sql = "SELECT DISTINCT sunits as sunits FROM `item_units` ORDER BY `sunits` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
							?>
									<option <?php if($sunits == $row['sunits']){ echo 'selected'; } ?> value="<?php echo $row['sunits']; ?>"><?php echo $row['sunits']; ?></option>
							<?php
								}
							?>
						</select>
					</div>
					</div>
					<!-- <div class="form-group col-md-6">
						<label>Consumption Unit<b style="color:red;">&nbsp;*</b></label>
						<select name="icunit" id="icunit" class="form-control select2" style="width: 100%;">
							<option value="select">select</option>
							<?php
								$sql = "SELECT cunits FROM `item_units` WHERE `sunits` = '$sunits' ORDER BY `cunits` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
							?>
									<option <?php if($cunits == $row['cunits']){ echo 'selected'; } ?> value="<?php echo $row['cunits']; ?>"><?php echo $row['cunits']; ?></option>
							<?php
								}
							?>
						</select>
					</div> -->
					<div class="form-group col-md-12" style="visibility:hidden;">
						<label>id<b style="color:red;">&ensp;*</b></label>
						<input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $id; ?>" placeholder="Enter description..." onkeyup="validatename(this.id);colorchange(this.id);">
					</div>
					<div class="box-body" align="center">
						<button type="submit" name="submittrans" id="submittrans" value="updatepage" class="btn btn-flat btn-social btn-linkedin">
							<i class="fa fa-save"></i> Update
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
	var b = document.getElementById("icat").value;
	//var c = document.getElementById("ctype").value;
	var d = document.getElementById("istored").value;
	// var e = document.getElementById("icunit").value;
	if(a.length == 0){
		alert("Enter Description ..!");
				document.getElementById("idesc").focus();
		return false;
	}
	else if(b.match("select")){
		alert("Select Category ..!");
		return false;
	}
	/*else if(c.match("select")){
		alert("Select Control Type ..!");
		return false;
	}*/
	else if(d.match("select")){
		alert("Select Consumption unit ..!");
		return false;
	}
	// else if(e.match("select")){
	// 	alert("Select Consumption unit ..!");
	// 	return false;
	// }
	else {
		return true;
	}
}
function redirection_page(){
	window.location.href = "inv_displayitems.php";
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
function validatenum(x) {
	expr = /^[0-9.]*$/;
	var a = document.getElementById(x).value;
	if(a.length > 50){
		a = a.substr(0,a.length - 1);
	}
	if(!a.match(expr)){
		a = a.replace(/[^0-9.]/g, '');
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
		$sql="SELECT * FROM `acc_schedules` WHERE `flag` = '1' AND `active` = '1' ORDER BY `description` ASC";
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
function setcunits(){
	removeAllOptions(document.getElementById("icunit"));
	
	myselect1 = document.getElementById("icunit"); 
	theOption1=document.createElement("OPTION"); 
	theText1=document.createTextNode("Select"); 
	theOption1.value = "select"; 
	theOption1.appendChild(theText1); 
	myselect1.appendChild(theOption1);
	
	var stypes = document.getElementById("istored").value;
	<?php
		$sql="SELECT * FROM `item_units` ORDER BY `cunits` ASC";
		$query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ echo "if(stypes == '$row[sunits]'){"; ?> 
			theOption1=document.createElement("OPTION");
			theText1=document.createTextNode("<?php echo $row['cunits']; ?>"); 
			theOption1.value = "<?php echo $row['cunits']; ?>"; 
			theOption1.appendChild(theText1); myselect1.appendChild(theOption1);	
		<?php echo "}"; } ?>
	
}
function checkalt() {
	var idescription = document.getElementById("cdesc").value;
	<?php
		$sql="SELECT * FROM `item_details` ORDER BY `description` ASC";
		$query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ echo "if(idescription == '$row[description]'){"; ?>
		var x = confirm("THe Description already available \n Please check...!");
		
		if(x == true){
			document.getElementById("cdesc").focus = true;
		}
		else if(x == true){
			document.getElementById("cdesc").focus = true;
		}
		else {
			document.getElementById("cdesc").focus = true;
		}
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
</body>
</html>
	<?php include "header_foot.php"; ?>