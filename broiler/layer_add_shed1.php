<?php
//layer_add_shed1.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['shed1'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $link_childid = $row['childid']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $alink = array(); $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $alink = explode(",",$row['addaccess']);
        if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $user_type = "S"; }
        else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $user_type = "A"; }
        else{ $user_type = "N"; }
    }
    if($user_type == "S"){ $acount = 1; }
    else{
        foreach($alink as $add_access_flag){
            if($add_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){
        $sql = "SELECT * FROM `layer_farms` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $bfarm_code = $bfarm_name = array();
        while($row = mysqli_fetch_assoc($query)){ $bfarm_code[$row['code']] = $row['code']; $bfarm_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `breeder_shed_type` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $bs_code = $bs_name = array();
        while($row = mysqli_fetch_assoc($query)){ $bs_code[$row['code']] = $row['code']; $bs_name[$row['code']] = $row['description'];}
        

        $sql = "SELECT * FROM `broiler_employee` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC";
        $query = mysqli_query($conn,$sql); $bemp_code = $bemp_name = array();
        while($row = mysqli_fetch_assoc($query)){ $bemp_code[$row['code']] = $row['code']; $bemp_name[$row['code']] = $row['name']; }
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
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Shed</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="layer_save_shed1.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row justify-content-center align-items-center">
                                        <table class="table1" style="width:auto;">
                                            <tbody>
                                                <tr>
                                                    <th><label for="farm_code">Farm<b style="color:red;">&nbsp;*</b></label></th>
                                                    <td colspan="3"><select name="farm_code" id="farm_code" class="form-control select2" style="width:390px;" onchange="fetch_bfarm_units();"><option value="select">-select-</option><?php foreach($bfarm_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $bfarm_name[$ucode]; ?></option><?php } ?></select></td>
                                                </tr>
                                                <tr>
                                                    <th><label for="unit_code">Unit<b style="color:red;">&nbsp;*</b></label></th>
                                                    <td colspan="3"><select name="unit_code" id="unit_code" class="form-control select2" style="width:390px;"><option value="select">-select-</option></select></td>
                                                </tr>
                                                <tr>
                                                    <th><label for="shed_code">Shed Code<b style="color:red;">&nbsp;*</b></label></th>
                                                    <td><input type="text" name="shed_code" id="shed_code" class="form-control" style="width:110px;" onkeyup="validatename(this.id);" onchange="validatename(this.id);check_scode_duplicate();" /></td>
                                                    <th><label for="description">Shed Name<b style="color:red;">&nbsp;*</b></label></th>
                                                    <td><input type="text" name="description" id="description" class="form-control" style="width:210px;" onkeyup="validatename(this.id);" onchange="validatename(this.id);check_sname_duplicate();" /></td>
                                                </tr>
                                                <tr>
                                                    <th><label for="shed_type">Shed Type</label></th>
                                                    <td><select name="bs_code" id="bs_code" class="form-control select2" style="width:300px;">
                                                        <option value="select">-select-</option>
                                                        <?php foreach($bs_code as $b_code) { ?>
                                                            <option value="<?php echo $b_code ?>"><?php echo $bs_name[$b_code]; ?></option>
                                                      <?php  } ?>
                                                    </select></td>
                                                    <th><label for="shed_sqft">Shed (Sqft)</label></th>
                                                    <td><input type="text" name="shed_sqft" id="shed_sqft" class="form-control text-right" style="width:110px;" onkeyup="validatenum(this.id);" onchange="validatenum(this.id);" /></td>
                                                </tr>
                                                <tr>
                                                    <th><label for="nof_emps">No.of Employees</label></th>
                                                    <td><input type="text" name="nof_emps" id="nof_emps" class="form-control text-right" style="width:110px;" onkeyup="validate_count(this.id);" onchange="validate_count(this.id);" /></td>
                                                    <th><label for="bird_capacity">Total Bird Capacity</label></th>
                                                    <td><input type="text" name="bird_capacity" id="bird_capacity" class="form-control text-right" style="width:110px;" onkeyup="validate_count(this.id);" onchange="validate_count(this.id);" /></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div><br/>
                                    <div class="row" style="visibility:hidden;">
                                        <div class="form-group" style="width:30px;">
                                            <label>CD</label>
                                            <input type="text" name="cdup_flag" id="cdup_flag" class="form-control" value="0" style="width:20px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:30px;">
                                            <label>ND</label>
                                            <input type="text" name="ndup_flag" id="ndup_flag" class="form-control" value="0" style="width:20px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:30px;">
                                            <label>EB</label>
                                            <input type="text" name="ebtncount" id="ebtncount" class="form-control" value="0" style="width:20px;" readonly />
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group" align="center">
                                            <button type="submit" name="submit" id="submit" class="btn btn-sm bg-purple">Submit</button>&ensp;
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
                var l = true;
                var farm_code = document.getElementById("farm_code").value;
                var unit_code = document.getElementById("unit_code").value;
                var shed_code = document.getElementById("shed_code").value;
                var description = document.getElementById("description").value;
                var cdup_flag = document.getElementById("cdup_flag").value; if(cdup_flag == ""){ cdup_flag = 0; }
                var ndup_flag = document.getElementById("ndup_flag").value; if(ndup_flag == ""){ ndup_flag = 0; }

                if(farm_code == "" || farm_code == "select"){
                    alert("Please select Farm Name");
                    document.getElementById("farm_code").focus();
                    l = false;
                }
                else if(unit_code == "" || unit_code == "select"){
                    alert("Please select Unit Name");
                    document.getElementById("unit_code").focus();
                    l = false;
                }
                else if(shed_code == ""){
                    alert("Please enter Shed Code");
                    document.getElementById("shed_code").focus();
                    l = false;
                }
                else if(description == ""){
                    alert("Please enter Shed Name");
                    document.getElementById("description").focus();
                    l = false;
                }
                else if((parseFloat(cdup_flag)) == 1){
                    alert("Shed Code already exist, please check and try again");
                    document.getElementById("shed_code").focus();
                    l = false;
                }
                else if((parseFloat(ndup_flag)) == 1){
                    alert("Shed Name already exist, please check and try again");
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
                window.location.href = 'layer_display_shed1.php?ccid='+ccid;
            }
            function fetch_bfarm_units(){
                var farm_code = document.getElementById("farm_code").value;
                removeAllOptions(document.getElementById("unit_code"));
                if(farm_code != "select"){
                    var inv_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "layer_fetch_farm_units.php?farm_code="+farm_code;
                    //window.open(url);
                    var asynchronous = true;
                    inv_items.open(method, url, asynchronous);
                    inv_items.send();
                    inv_items.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var item_list = this.responseText;
                            if(item_list.length > 0){
                                $('#unit_code').append(item_list);
                            }
                            else{
                                alert("Active Farm Units are not available \n Kindly check and try again ...!");
                            }
                        }
                    }
                }
            }
			function check_scode_duplicate(){
				var shed_code = document.getElementById("shed_code").value;
				var type = "add";
				if(shed_code != ""){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "layer_fetch_shedcode_duplicates.php?shed_code="+shed_code+"&type="+type;
                    //window.open(url);
					var asynchronous = true;
					oldqty.open(method, url, asynchronous);
					oldqty.send();
					oldqty.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var cdup_flag = this.responseText;
							if(parseInt(cdup_flag) == 0){
								document.getElementById("cdup_flag"). value = 0;
							}
							else {
								alert("Shed Code already exist.\n please check once.");
								document.getElementById("cdup_flag"). value = 1;
							}
						}
					}
				}
				else { }
			}
			function check_sname_duplicate(){
				var description = document.getElementById("description").value;
				var type = "add";
				if(description != ""){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "layer_fetch_shedname_duplicates.php?description="+description+"&type="+type;
                    //window.open(url);
					var asynchronous = true;
					oldqty.open(method, url, asynchronous);
					oldqty.send();
					oldqty.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var ndup_flag = this.responseText;
							if(parseInt(ndup_flag) == 0){
								document.getElementById("ndup_flag"). value = 0;
							}
							else {
								alert("Shed Name already exist.\n please check once.");
								document.getElementById("ndup_flag"). value = 1;
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
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
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