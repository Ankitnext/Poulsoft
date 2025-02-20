<?php
//breeder_edit_farm1.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['farm1'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $link_childid = $row['childid']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $alink = array(); $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $alink = explode(",",$row['editaccess']);
        if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $user_type = "S"; }
        else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $user_type = "A"; }
        else{ $user_type = "N"; }
    }
    if($user_type == "S"){ $acount = 1; }
    else{
        foreach($alink as $edit_access_flag){
            if($edit_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){
        $sql = "SELECT * FROM `item_units` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `cunits` ASC";
        $query = mysqli_query($conn,$sql); $item_cunit = array();
        while($row = mysqli_fetch_assoc($query)){ $item_cunit[$row['cunits']] = $row['cunits']; }
        
        $sql = "SELECT * FROM `item_grades` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC";
        $query = mysqli_query($conn,$sql); $grade_code = $grade_name = array();
        while($row = mysqli_fetch_assoc($query)){ $grade_code[$row['code']] = $row['code']; $grade_name[$row['code']] = $row['description']; }
        
?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
    <!-- Datepicker -->
    <link href="datepicker/jquery-ui.css" rel="stylesheet">
    <style>
        body{
            overflow: hidden;
        }
        .form-control{
            font-size: 13px;
        }
    </style>
    </head>
    <body class="m-0 hold-transition">
        <?php
        $ids = $_GET['id'];
        $sql = "SELECT * FROM `breeder_farms` WHERE `id` = '$ids' AND `dflag` = '0' AND `trlink` = 'breeder_display_farm1.php'";
        $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $code = $row['code'];
            $farm_code = $row['farm_code'];
            $description = $row['description'];
            $farm_capacity = round($row['farm_capacity'],5);
        }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Farms</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="breeder_modify_farm1.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row justify-content-center align-items-center">
                                        <table class="table1" style="width:auto;">
                                            <thead>
                                                <tr>
                                                    <th style="text-align:center;visibility:hidden;"><label>SC<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th style="text-align:center;"><label>Farm Code<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th style="text-align:center;"><label>Farm Name<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th style="text-align:center;"><label>Farm Capacity</label></th>
                                                    <th style="visibility:hidden;"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody">
                                                <tr>
                                                    <td style="text-align:center;visibility:hidden;"><input type="text" name="code" id="code" class="form-control" value="<?php echo $code; ?>" style="width:20px;" readonly /></td>
                                                    <td><input type="text" name="farm_code" id="farm_code" class="form-control" value="<?php echo $farm_code; ?>" style="width:210px;" onkeyup="validatename(this.id); check_duplicate1();" /></td>
                                                    <td><input type="text" name="description" id="description" class="form-control" value="<?php echo $description; ?>" style="width:210px;" onkeyup="validatename(this.id);check_duplicate();" /></td>
                                                    <td><input type="text" name="farm_capacity" id="farm_capacity" class="form-control text-right" value="<?php echo $farm_capacity; ?>" style="width:100px;" onkeyup="validatenum(this.id);" onchange="validatenum(this.id);" /></td>
                                                   <td style="visibility:hidden;"><input type="text" name="dupflag" id="dupflag" class="form-control text-right" value="0" style="width:20px;" readonly /></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div><br/>
                                    <div class="row" style="visibility:hidden;">
                                        <div class="form-group" style="width:30px;">
                                            <label>ID<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $ids; ?>" style="width:20px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:30px;">
                                            <label>EB<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="ebtncount" id="ebtncount" class="form-control" value="0" style="width:20px;" readonly />
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group" align="center">
                                            <button type="submit" name="submit" id="submit" class="btn btn-sm bg-purple">Update</button>&ensp;
                                            <button type="button" name="cancel" id="cancel" class="btn btn-sm bg-danger" onclick="return_back()">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- Datepicker -->
        <script src="datepicker/jquery/jquery.js"></script>
        <script src="datepicker/jquery-ui.js"></script>
        <script>
			function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var farm_code = description = dupflag = iname = ""; var e = 0;
                var l = true;
                farm_code = document.getElementById("farm_code").value;
                description = document.getElementById("description").value;
                dupflag = document.getElementById("dupflag").value;
                if(farm_code == ""){
                    alert("Please enter Farm Code");
                    document.getElementById("farm_code").focus();
                    l = false;
                }
                else if(description == ""){
                    alert("Please enter Farm Name");
                    document.getElementById("description").focus();
                    l = false;
                }
                else if(parseInt(dupflag) == 1){
                    alert("Farm Name already exist.\n Kindly check");
                    document.getElementById("description").focus();
                    l = false;
                }
                else{ }
                if(l == true){
                    return true;
                }
                else{
                    document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
                    return false;
                }
			}
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'breeder_display_farm1.php?ccid='+ccid;
            }
			function check_duplicate(){
				var description = document.getElementById("description").value;
                var id = '<?php echo $ids; ?>';
				var type = "edit";
				if(description != ""){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "breeder_fetch_farm1_duplicates.php?description="+description+"&id="+id+"&type="+type;
                    //window.open(url);
					var asynchronous = true;
					oldqty.open(method, url, asynchronous);
					oldqty.send();
					oldqty.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var dup_dt = this.responseText;
                            var dup_info = dup_dt.split("@");
                            
							if(parseInt(dup_info[0]) == 0){
								document.getElementById("dupflag"). value = 0;
							}
							else {
								alert("Farm Name already exist.\n Kindly change the name");
								document.getElementById("dupflag"). value = 1;
							}
						}
					}
				}
				else { }
			}
            function check_duplicate1(){
				var farm_code = document.getElementById("farm_code").value;
                var id = '<?php echo $ids; ?>';
				var type = "edit";
				if(farm_code != ""){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "breeder_fetch_farm1_duplicates2.php?farm_code="+farm_code+"&id="+id+"&type="+type;
                    //window.open(url);
					var asynchronous = true;
					oldqty.open(method, url, asynchronous);
					oldqty.send();
					oldqty.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var dup_dt = this.responseText;
                            var dup_info = dup_dt.split("@");
                            
							if(parseInt(dup_info[0]) == 0){
								document.getElementById("dupflag"). value = 0;
							}
							else {
								alert("Farm Code already exist.\n Kindly change the Farm Code");
								document.getElementById("dupflag"). value = 1;
							}
						}
					}
				}
				else { }
			}
            
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validate_count(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
		</script>
        <?php include "header_foot.php"; ?>
    </body>
</html>

<?php
    }
    else{
        echo "You don't have access to this page \n Kindly contact your admin for more information"; 
    }
}
else{
    echo "You don't have access to this page \n Kindly contact your admin for more information";
}
?>