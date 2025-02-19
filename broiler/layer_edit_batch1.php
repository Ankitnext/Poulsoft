<?php
//layer_edit_batch1.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['batch1'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $link_childid = $row['childid']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $elink = array(); $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $elink = explode(",",$row['editaccess']);
        if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $user_type = "S"; }
        else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $user_type = "A"; }
        else{ $user_type = "N"; }
    }
    if($user_type == "S"){ $acount = 1; }
    else{
        foreach($elink as $edit_access_flag){
            if($edit_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){
        $sql = "SELECT * FROM `layer_farms` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $bfarm_code = $bfarm_name = array();
        while($row = mysqli_fetch_assoc($query)){ $bfarm_code[$row['code']] = $row['code']; $bfarm_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `layer_breed_details` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $bbreed_code = $bbreed_name = array();
        while($row = mysqli_fetch_assoc($query)){ $bbreed_code[$row['code']] = $row['code']; $bbreed_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `main_officetypes` WHERE `description` LIKE '%hatchery%' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $off_alist = array();
        while($row = mysqli_fetch_assoc($query)){ $off_alist[$row['code']] = $row['code']; }

        $off_list = implode("','", $off_alist);
        $sql = "SELECT * FROM `inv_sectors` WHERE `type` IN ('$off_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $hsec_code = $hsec_name = array();
        while($row = mysqli_fetch_assoc($query)){ $hsec_code[$row['code']] = $row['code']; $hsec_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC";
        $query = mysqli_query($conn,$sql); $ven_code = $ven_name = array();
        while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; }

        $sql = "SELECT * FROM `layer_units` WHERE `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $bunit_code = $bunit_name = array();
        while($row = mysqli_fetch_assoc($query)){ $bunit_code[$row['code']] = $row['code']; $bunit_name[$row['code']] = $row['description']; $bunit_farm[$row['code']] = $row['farm_code']; }
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
        $sql = "SELECT * FROM `layer_batch` WHERE `id` = '$ids' AND `dflag` = '0' AND `trlink` = 'layer_display_batch1.php'";
        $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $batch_code = $row['batch_code'];
            $description = $row['description'];
            $farm_code = $row['farm_code'];
            $unit_code = $row['unit_code'];
            $bird_source = $row['bird_source'];
            $vs_code = $row['vs_code'];
            $breed_code = $row['breed_code'];
            //$bstart_date = date("d.m.Y",strtotime($row['bstart_date']));
            //$start_age = round($row['start_age'],5);
            $beps_flag = $row['beps_flag'];
        }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Batch</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="layer_modify_batch1.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row justify-content-center align-items-center">
                                        <table class="table1" style="width:auto;">
                                            <tbody>
                                                <!--<tr>
                                                    <th><label for="farm_code">Farm<b style="color:red;">&nbsp;*</b></label></th>
                                                    <td colspan="3"><select name="farm_code" id="farm_code" class="form-control select2" style="width:390px;"><option value="select">-select-</option><?php /*foreach($bfarm_code as $ucode){ ?><option value="<?php echo $ucode; ?>" <?php if($ucode == $farm_code){ echo "selected"; } ?>><?php echo $bfarm_name[$ucode]; ?></option><?php }*/ ?></select></td>
                                                </tr>
                                                <tr>
                                                    <th><label for="unit_code">Unit<b style="color:red;">&nbsp;*</b></label></th> onchange="fetch_bfarm_units();"
                                                    <td colspan="3">
                                                        <select name="unit_code" id="unit_code" class="form-control select2" style="width:390px;">
                                                            <option value="select">-select-</option>
                                                            <?php
                                                            /*foreach($bunit_code as $ucode){
                                                                if($bunit_farm[$ucode] == $farm_code){
                                                            ?>
                                                            <option value="<?php echo $ucode; ?>" <?php if($ucode == $unit_code){ echo "selected"; } ?>><?php echo $bunit_name[$ucode]; ?></option>
                                                            <?php
                                                                }
                                                            }*/
                                                            ?>
                                                        </select>
                                                    </td>
                                                </tr>-->
                                                <!-- <tr>
                                                    <th><label for="unit_code">Bird Source</label></th>
                                                    <td colspan="3">
                                                        <div class="row justify-content-center align-items-center">
                                                            <div class="form-group">
                                                                <label for="bird_source1">Own Hatchery</label>
                                                                <input type="radio" name="bird_source" id="bird_source1" value="own_hatchery" onchange="fetch_layer_vslist();" <?php if($bird_source == "own_hatchery"){ echo "checked"; } ?> />
                                                            </div>&ensp;&ensp;&ensp;&ensp;
                                                            <div class="form-group">
                                                                <label for="bird_source2">Purchase</label>
                                                                <input type="radio" name="bird_source" id="bird_source2" value="purchase" onchange="fetch_layer_vslist();" <?php if($bird_source == "purchase"){ echo "checked"; } ?> />
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr> -->
                                                <tr>
                                                    <th><label for="vs_code">Supplier<b style="color:red;">&nbsp;*</b></label></th>
                                                    <td colspan="3">
                                                        <select name="vs_code" id="vs_code" class="form-control select2" style="width:390px;">
                                                            <option value="select">-select-</option>
                                                            <?php foreach($ven_code as $vcode){ ?>
                                                                <option value="<?php echo $vcode; ?>" <?php if($vcode == $vs_code){ echo "selected"; } ?>><?php echo $ven_name[$vcode]; ?></option>
                                                            <?php }  ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><label for="batch_code">Batch Code<b style="color:red;">&nbsp;*</b></label></th>
                                                    <td><input type="text" name="batch_code" id="batch_code" class="form-control" value="<?php echo $batch_code; ?>" style="width:120px;" onkeyup="validatename(this.id);" onchange="validatename(this.id);check_scode_duplicate();" /></td>
                                                    <th><label for="description">Batch Name<b style="color:red;">&nbsp;*</b></label></th>
                                                    <td><input type="text" name="description" id="description" class="form-control" value="<?php echo $description; ?>" style="width:170px;" onkeyup="validatename(this.id);" onchange="validatename(this.id);check_sname_duplicate();" /></td>
                                                </tr>
                                                <tr>
                                                    <th><label for="breed_code">Breed<b style="color:red;">&nbsp;*</b></label></th>
                                                    <td colspan="3"><select name="breed_code" id="breed_code" class="form-control select2" style="width:390px;"><option value="select">-select-</option><?php foreach($bbreed_code as $ucode){ ?><option value="<?php echo $ucode; ?>" <?php if($ucode == $breed_code){ echo "selected"; } ?>><?php echo $bbreed_name[$ucode]; ?></option><?php } ?></select></td>
                                                </tr>
                                                <!--<tr>
                                                    <th><label for="bstart_date">Batch Start Date</label></th>
                                                    <td><input type="text" name="bstart_date" id="bstart_date" class="form-control datepicker" value="<?php //echo $bstart_date; ?>" style="width:120px;" readonly /></td>
                                                    <th><label for="start_age">Start Age</label></th>
                                                    <td><input type="text" name="start_age" id="start_age" class="form-control text-right" value="<?php //echo $start_age; ?>" style="width:110px;" onkeyup="validate_count(this.id);" onchange="validate_count(this.id);" /></td>
                                                </tr>-->
                                                <tr>
                                                    <th></th>
                                                    <td colspan="3">
                                                        <div class="row">
                                                            <div class="form-group">
                                                                &ensp;<input type="checkbox" name="beps_flag" id="beps_flag" <?php if($beps_flag == 1){ echo "checked"; } ?> />
                                                                <label for="beps_flag">Egg Production</label>
                                                            </div>
                                                        </div>
                                                    </td>
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
                /*var farm_code = document.getElementById("farm_code").value;
                var unit_code = document.getElementById("unit_code").value;*/
                var vs_code = document.getElementById("vs_code").value;
                var batch_code = document.getElementById("batch_code").value;
                var description = document.getElementById("description").value;
                var breed_code = document.getElementById("breed_code").value;
                var cdup_flag = document.getElementById("cdup_flag").value; if(cdup_flag == ""){ cdup_flag = 0; }
                var ndup_flag = document.getElementById("ndup_flag").value; if(ndup_flag == ""){ ndup_flag = 0; }

                /*if(farm_code == "" || farm_code == "select"){
                    alert("Please select Farm Name");
                    document.getElementById("farm_code").focus();
                    l = false;
                }
                else if(unit_code == "" || unit_code == "select"){
                    alert("Please select Unit Name");
                    document.getElementById("unit_code").focus();
                    l = false;
                }*/
                if(vs_code == "" || vs_code == "select"){
                    alert("Please select Supplier");
                    document.getElementById("vs_code").focus();
                    l = false;
                }
                else if(batch_code == ""){
                    alert("Please enter Batch Code");
                    document.getElementById("batch_code").focus();
                    l = false;
                }
                else if(description == ""){
                    alert("Please enter Batch Name");
                    document.getElementById("description").focus();
                    l = false;
                }
                else if(breed_code == "" || breed_code == "select"){
                    alert("Please select Breed");
                    document.getElementById("breed_code").focus();
                    l = false;
                }
                else if((parseFloat(cdup_flag)) == 1){
                    alert("Batch Code already exist, please check and try again");
                    document.getElementById("batch_code").focus();
                    l = false;
                }
                else if((parseFloat(ndup_flag)) == 1){
                    alert("Batch Name already exist, please check and try again");
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
                window.location.href = 'layer_display_batch1.php?ccid='+ccid;
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
            function fetch_layer_vslist(){
                removeAllOptions(document.getElementById("vs_code"));
                var bird_source = "";
                if(document.getElementById("bird_source1").checked == true){
                    bird_source = document.getElementById("bird_source1").value;
                }
                else if(document.getElementById("bird_source2").checked == true){
                    bird_source = document.getElementById("bird_source2").value;
                }
                else{ }

                if(bird_source == "own_hatchery"){
                    myselect = document.getElementById("vs_code");
                    theOption1 = document.createElement("OPTION");
                    theText1 = document.createTextNode("-select-");
                    theOption1.value = "select";
                    theOption1.appendChild(theText1);
                    myselect.appendChild(theOption1);
                    <?php foreach($hsec_code as $hcode){ $name = $hsec_name[$hcode]; ?> 
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $name; ?>"); theOption1.value = "<?php echo $hcode; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
					<?php } ?>
                }
                else if(bird_source == "purchase"){
                    myselect = document.getElementById("vs_code");
                    theOption1 = document.createElement("OPTION");
                    theText1 = document.createTextNode("-select-");
                    theOption1.value = "select";
                    theOption1.appendChild(theText1);
                    myselect.appendChild(theOption1);
                    <?php foreach($ven_code as $hcode){ $name = $ven_name[$hcode]; ?> 
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $name; ?>"); theOption1.value = "<?php echo $hcode; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
					<?php } ?>
                }
                else{ }
            }
			function check_scode_duplicate(){
				var batch_code = document.getElementById("batch_code").value;
                var id = '<?php echo $ids; ?>';
				var type = "edit";
				if(batch_code != ""){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "layer_fetch_batchcode_duplicates.php?batch_code="+batch_code+"&type="+type+"&id="+id;
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
								alert("Batch No. already exist.\n please check once.");
								document.getElementById("cdup_flag"). value = 1;
							}
						}
					}
				}
				else { }
			}
			function check_sname_duplicate(){
				var description = document.getElementById("description").value;
                var id = '<?php echo $ids; ?>';
				var type = "edit";
				if(description != ""){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "layer_fetch_batchname_duplicates.php?description="+description+"&type="+type+"&id="+id;
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