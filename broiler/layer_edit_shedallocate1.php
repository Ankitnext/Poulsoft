<?php
//layer_edit_shedallocate1.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['shedallocate1'];
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
        $date = date("d.m.Y");
        $sql = "SELECT * FROM `layer_farms` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $bfarm_code = $bfarm_name = array();
        while($row = mysqli_fetch_assoc($query)){ $bfarm_code[$row['code']] = $row['code']; $bfarm_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `layer_units` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $bunit_code = $bunit_name = array();
        while($row = mysqli_fetch_assoc($query)){ $bunit_code[$row['code']] = $row['code']; $bunit_name[$row['code']] = $row['description']; $bunit_farm[$row['code']] = $row['farm_code']; }

        $sql = "SELECT * FROM `layer_sheds` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $bshed_code = $bshed_name = $bshed_farm = $bshed_unit = array();
        while($row = mysqli_fetch_assoc($query)){ $bshed_code[$row['code']] = $row['code']; $bshed_name[$row['code']] = $row['description']; $bshed_farm[$row['code']] = $row['farm_code']; $bshed_unit[$row['code']] = $row['unit_code']; }

        $sql = "SELECT * FROM `layer_batch` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $bbatch_code = $bbatch_name = $bbatch_farm = $bbatch_unit = array();
        while($row = mysqli_fetch_assoc($query)){ $bbatch_code[$row['code']] = $row['code']; $bbatch_name[$row['code']] = $row['description']; $bbatch_farm[$row['code']] = $row['farm_code']; $bbatch_unit[$row['code']] = $row['unit_code']; }
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
        $sql = "SELECT * FROM `layer_shed_allocation` WHERE `id` = '$ids' AND `dflag` = '0' AND `trlink` = 'layer_display_shedallocate1.php'";
        $query = mysqli_query($conn,$sql); $f_cnt = 0;
        while($row = mysqli_fetch_assoc($query)){
            $code = $row['code'];
            $description = $row['description'];
            $farm_code = $row['farm_code'];
            $unit_code = $row['unit_code'];
            $farm_code = $row['farm_code'];
            $shed_code = $row['shed_code'];
            $batch_code = $row['batch_code'];
            $start_date = date("d.m.Y",strtotime($row['start_date']));
            $start_age = round($row['start_age'],5);
            $age_weeks = round($row['age_weeks'],5);
            $opn_fbirds = round($row['opn_fbirds'],5);
            $opn_frate = round($row['opn_frate'],5);
            $opn_mbirds = round($row['opn_mbirds'],5);
            $opn_mrate = round($row['opn_mrate'],5);
        }
        if($code != ""){
            $sql = "SELECT * FROM `account_summary` WHERE `flock_code` = '$code' AND `dflag` = '0'";
            $query = mysqli_query($conn,$sql); $f_cnt = mysqli_num_rows($query);
        }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Flock</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="layer_modify_shedallocate1.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row justify-content-center align-items-center">
                                        <table class="table1" style="width:auto;">
                                            <tbody>
                                                <tr>
                                                    <th><label for="farm_code">Farm<b style="color:red;">&nbsp;*</b></label></th>
                                                    <td colspan="3">
                                                        <select name="farm_code" id="farm_code" class="form-control select2" style="width:390px;" onchange="fetch_bfarm_units();">
                                                            <?php
                                                            if((int)$f_cnt > 0){
                                                            ?>
                                                            <option value="<?php echo $farm_code; ?>" selected><?php echo $bfarm_name[$farm_code]; ?></option>
                                                            <?php
                                                            }
                                                            else{
                                                                echo '<option value="select">-select-</option>';
                                                                foreach($bfarm_code as $ucode){
                                                                ?>
                                                                <option value="<?php echo $ucode; ?>" <?php if($ucode == $farm_code){ echo "selected"; } ?>><?php echo $bfarm_name[$ucode]; ?></option>
                                                                <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><label for="unit_code">Unit<b style="color:red;">&nbsp;*</b></label></th>
                                                    <td colspan="3">
                                                        <select name="unit_code" id="unit_code" class="form-control select2" style="width:390px;" onchange="fetch_bfarm_sheds();"><!--fetch_bfarm_batches();-->
                                                            <?php
                                                            if((int)$f_cnt > 0){
                                                            ?>
                                                            <option value="<?php echo $unit_code; ?>" selected><?php echo $bunit_name[$unit_code]; ?></option>
                                                            <?php
                                                            }
                                                            else{
                                                                echo '<option value="select">-select-</option>';
                                                                foreach($bunit_code as $ucode){
                                                                    if($bunit_farm[$ucode] == $farm_code){
                                                                ?>
                                                                <option value="<?php echo $ucode; ?>" <?php if($ucode == $unit_code){ echo "selected"; } ?>><?php echo $bunit_name[$ucode]; ?></option>
                                                                <?php
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><label for="shed_code">Shed/House No.<b style="color:red;">&nbsp;*</b></label></th>
                                                    <td colspan="3">
                                                        <select name="shed_code" id="shed_code" class="form-control select2" style="width:390px;" onchange="check_shedallocate_duplicate();">
                                                            <?php
                                                            if((int)$f_cnt > 0){
                                                            ?>
                                                            <option value="<?php echo $shed_code; ?>" selected><?php echo $bshed_name[$shed_code]; ?></option>
                                                            <?php
                                                            }
                                                            else{
                                                                echo '<option value="select">-select-</option>';
                                                                foreach($bshed_code as $ucode){
                                                                    if($bshed_farm[$ucode] == $farm_code && $bshed_unit[$ucode] == $unit_code){
                                                                ?>
                                                                <option value="<?php echo $ucode; ?>" <?php if($ucode == $shed_code){ echo "selected"; } ?>><?php echo $bshed_name[$ucode]; ?></option>
                                                                <?php
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><label for="batch_code">Batch No.<b style="color:red;">&nbsp;*</b></label></th>
                                                    <td colspan="3">
                                                        <select name="batch_code" id="batch_code" class="form-control select2" style="width:390px;" onchange="check_shedallocate_duplicate();fetch_existing_batch();">
                                                            <?php
                                                            if((int)$f_cnt > 0){
                                                            ?>
                                                            <option value="<?php echo $batch_code; ?>" selected><?php echo $bbatch_name[$batch_code]; ?></option>
                                                            <?php
                                                            }
                                                            else{
                                                                echo '<option value="select">-select-</option>';
                                                                foreach($bbatch_code as $ucode){
                                                                    //if($bbatch_farm[$ucode] == $farm_code && $bbatch_unit[$ucode] == $unit_code){
                                                                ?>
                                                                <option value="<?php echo $ucode; ?>" <?php if($ucode == $batch_code){ echo "selected"; } ?>><?php echo $bbatch_name[$ucode]; ?></option>
                                                                <?php
                                                                    //}
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><label for="description">Flock Name<b style="color:red;">&nbsp;*</b></label></th>
                                                    <td><input type="text" name="description" id="description" class="form-control" value="<?php echo $description; ?>" style="width:160px;" /></td>
                                                    <th><label for="start_date">Start Date<b style="color:red;">&nbsp;*</b></label></th>
                                                    <td><input type="text" name="start_date" id="start_date" <?php if((int)$f_cnt > 0){ echo 'class="form-control"'; } else{ echo 'class="form-control datepicker"'; } ?> value="<?php echo $start_date; ?>" style="width:125px;" onchange="fetch_existing_batch();" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <th><label for="start_age">Age in Days<b style="color:red;">&nbsp;*</b></label></th>
                                                    <td><input type="text" name="start_age" id="start_age" class="form-control text-right" value="<?php echo $start_age; ?>" style="width:125px;" onkeyup="validate_count(this.id);calculate_age_weeks();" onchange="validate_count(this.id);" <?php if((int)$f_cnt > 0){ echo 'readonly'; } ?> /></td>
                                                    <th><label for="age_weeks">Age in Weeks</label></th>
                                                    <td><input type="text" name="age_weeks" id="age_weeks" class="form-control text-right" value="<?php echo $age_weeks; ?>" style="width:125px;" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <th><label for="opn_fbirds">Female Opening</label></th>
                                                    <td><input type="text" name="opn_fbirds" id="opn_fbirds" class="form-control text-right" value="<?php echo $opn_fbirds; ?>" style="width:125px;" onkeyup="validate_count(this.id);" onchange="validate_count(this.id);" /></td>
                                                    <th><label for="opn_mbirds">Male Opening</label></th>
                                                    <td><input type="text" name="opn_mbirds" id="opn_mbirds" class="form-control text-right" value="<?php echo $opn_mbirds; ?>" style="width:125px;" onkeyup="validate_count(this.id);" onchange="validate_count(this.id);" /></td>
                                                </tr>
                                                <tr>
                                                    <th><label for="opn_frate">Rate</label></th>
                                                    <td><input type="text" name="opn_frate" id="opn_frate" class="form-control text-right" value="<?php echo $opn_frate; ?>" style="width:125px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>
                                                    <th><label for="opn_mrate">Rate</label></th>
                                                    <td><input type="text" name="opn_mrate" id="opn_mrate" class="form-control text-right" value="<?php echo $opn_mrate; ?>" style="width:125px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>
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
                                            <label>DF</label>
                                            <input type="text" name="dup_flag" id="dup_flag" class="form-control" value="0" style="width:20px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:30px;">
                                            <label>EB</label>
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
                var l = true;
                var farm_code = document.getElementById("farm_code").value;
                var unit_code = document.getElementById("unit_code").value;
                var shed_code = document.getElementById("shed_code").value;
                var batch_code = document.getElementById("batch_code").value;
                var description = document.getElementById("description").value;
                var dup_flag = document.getElementById("dup_flag").value; if(dup_flag == ""){ dup_flag = 0; }

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
                else if(shed_code == "" || shed_code == "select"){
                    alert("Please select Shed/House No.");
                    document.getElementById("shed_code").focus();
                    l = false;
                }
                else if(batch_code == "" || batch_code == "select"){
                    alert("Please select Batch No.");
                    document.getElementById("batch_code").focus();
                    l = false;
                }
                else if(description == ""){
                    alert("Please enter Flock Name");
                    document.getElementById("description").focus();
                    l = false;
                }
                else if((parseFloat(dup_flag)) == 1){
                    alert("Same Batch and shed combination already exist, please check and try again");
                    document.getElementById("batch_code").focus();
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
                window.location.href = 'layer_display_shedallocate1.php?ccid='+ccid;
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
            function fetch_bfarm_sheds(){
                var farm_code = document.getElementById("farm_code").value;
                var unit_code = document.getElementById("unit_code").value;
                removeAllOptions(document.getElementById("shed_code"));
                if(farm_code != "select" && unit_code != "select"){
                    var inv_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "layer_fetch_farm_sheds.php?farm_code="+farm_code+"&unit_code="+unit_code;
                    //window.open(url);
                    var asynchronous = true;
                    inv_items.open(method, url, asynchronous);
                    inv_items.send();
                    inv_items.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var item_list = this.responseText;
                            if(item_list.length > 0){
                                $('#shed_code').append(item_list);
                            }
                            else{
                                alert("Active Farm Sheds are not available \n Kindly check and try again ...!");
                            }
                        }
                    }
                }
            }
            function fetch_bfarm_batches(){
                var farm_code = document.getElementById("farm_code").value;
                var unit_code = document.getElementById("unit_code").value;
                removeAllOptions(document.getElementById("batch_code"));
                if(farm_code != "select" && unit_code != "select"){
                    var inv_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "layer_fetch_farm_batches.php?farm_code="+farm_code+"&unit_code="+unit_code;
                    //window.open(url);
                    var asynchronous = true;
                    inv_items.open(method, url, asynchronous);
                    inv_items.send();
                    inv_items.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var item_list = this.responseText;
                            if(item_list.length > 0){
                                $('#batch_code').append(item_list);
                            }
                            else{
                                alert("Active Farm Batches are not available \n Kindly check and try again ...!");
                            }
                        }
                    }
                }
            }
			function check_shedallocate_duplicate(){
                var farm_code = document.getElementById("farm_code").value;
                var unit_code = document.getElementById("unit_code").value;
				var shed_code = document.getElementById("shed_code").value;
				var batch_code = document.getElementById("batch_code").value;
				var type = "add";
				if(farm_code == "select" || unit_code == "" || unit_code == "select" || shed_code == "" || shed_code == "select" || batch_code == "" || batch_code == "select"){ }
                else{
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "layer_fetch_shedallocate_duplicates.php?farm_code="+farm_code+"&unit_code="+unit_code+"&shed_code="+shed_code+"&batch_code="+batch_code+"&type="+type;
                    //window.open(url);
					var asynchronous = true;
					oldqty.open(method, url, asynchronous);
					oldqty.send();
					oldqty.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var dup_flag = this.responseText;
							if(parseInt(dup_flag) == 0){
								document.getElementById("dup_flag"). value = 0;
							}
							else {
								alert("Shed Name already exist.\n please check once.");
								document.getElementById("dup_flag"). value = 1;
							}
						}
					}
				}
			}
			function fetch_existing_batch(){
                var bflk_aflag = '<?php echo $bflk_aflag; ?>';
                if(parseInt(bflk_aflag) == 1){
                    var batch_code = document.getElementById("batch_code").value;
                    var date = document.getElementById("start_date").value;
                    document.getElementById("start_age").readOnly = false;
                    var type = "edit"; var ids = '<?php echo $ids; ?>';
                    if(date == "" || batch_code == "" || batch_code == "select"){ }
                    else{
                        var oldqty = new XMLHttpRequest();
                        var method = "GET";
                        var url = "layer_fetch_exist_batches.php?batch_code="+batch_code+"&date="+date+"&type="+type+"&id="+ids;
                        //window.open(url);
                        var asynchronous = true;
                        oldqty.open(method, url, asynchronous);
                        oldqty.send();
                        oldqty.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var flk_dt1 = this.responseText;
                                var flk_dt2 = flk_dt1.split("@$&");
                                var c_cnt = flk_dt2[0];
                                var sdate = flk_dt2[1];
                                var sdage = flk_dt2[2];
                                if(parseInt(c_cnt) > 0){
                                    document.getElementById("start_age").readOnly = true;
                                    document.getElementById("start_age").value = sdage;
                                }
                                calculate_age_weeks();
                            }
                        }
                    }
                }
			}
            function calculate_age_weeks(){
                var start_age = document.getElementById("start_age").value; if(start_age == ""){ start_age = 0; }
                var age_weeks = 0;
                if(parseFloat(start_age) > 0){
                    //var age_weeks = parseFloat(start_age) / 7;
                    var week_no = Math.floor(parseFloat(start_age) / 7);
                    var age_no = parseFloat(start_age) % 7;
                    if(parseInt(age_no) == 0){
                        age_no = 7; week_no = parseInt(week_no) - 1;
                    }
                    age_weeks = week_no+"."+age_no;
                }
                document.getElementById("age_weeks").value = parseFloat(age_weeks).toFixed(1);
            }
            fetch_existing_batch();
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