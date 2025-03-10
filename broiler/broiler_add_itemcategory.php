<?php
//broiler_add_itemcategory.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['itemcategory'];
$uri = explode("/",$_SERVER['REQUEST_URI']); $href = $uri[1];
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
        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Processing Plant' AND `field_function` LIKE 'Item Category: Checkbox%' AND `user_access` LIKE 'all'";
        $query = mysqli_query($conn,$sql); $pp_count = mysqli_num_rows($query); if($pp_count > 0){ while($row = mysqli_fetch_assoc($query)){ $pp_flag = $row['flag']; } } else{ $pp_flag = 0; } if($pp_flag == ""){ $pp_flag = 0; }

        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Processing Plant' AND `field_function` LIKE 'Main Item Category selection' AND `user_access` LIKE 'all'";
        $query = mysqli_query($conn,$sql); $ppmic_count = mysqli_num_rows($query); if($ppmic_count > 0){ while($row = mysqli_fetch_assoc($query)){ $ppmic_flag = $row['flag']; } } else{ $ppmic_flag = 0; } if($ppmic_flag == ""){ $ppmic_flag = 0; }

        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Feed Sale' AND `field_function` LIKE 'Item selection checkbox' AND `user_access` LIKE 'all'";
        $query = mysqli_query($conn,$sql); $fs_count = mysqli_num_rows($query); if($fs_count > 0){ while($row = mysqli_fetch_assoc($query)){ $fs_flag = $row['flag']; } } else{ $fs_flag = 0; } if($fs_flag == ""){ $fs_flag = 0; }

        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Item Category Master' AND `field_function` LIKE 'Finished Goods Checkbox' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $fgoods_cbox = mysqli_num_rows($query);

        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Item Category Master' AND `field_function` LIKE 'Raw Ingredient Checkbox' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $rawing_cbox = mysqli_num_rows($query);

        $sql = "SELECT * FROM `main_item_category` WHERE `plant_portioning` = '1' AND `active` = '1' AND `dflag` = '0' ORDER BY `plant_sort_order`,`description` ASC;";
        $query = mysqli_query($conn,$sql); $micat_code = $micat_name = array();
        while($row = mysqli_fetch_assoc($query)){ $micat_code[$row['code']] = $row['code']; $micat_name[$row['code']] = $row['description']; }
        
        $sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Item Category Master' AND `field_function` = 'Breeder Feed Category' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $bfeed_cflag = mysqli_num_rows($query);
        $sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Item Category Master' AND `field_function` = 'Breeder Egg Category' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $bec_cflag = mysqli_num_rows($query);
        $sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Item Category Master' AND `field_function` = 'Breeder Medicine/Vaccine Category' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $bmvc_cflag = mysqli_num_rows($query);

        $sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'Item Category Master' AND `field_function` = 'layer Feed Category' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $lfeed_cflag = mysqli_num_rows($query);
        $sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'Item Category Master' AND `field_function` = 'layer Egg Category' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $lec_cflag = mysqli_num_rows($query);
        $sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'Item Category Master' AND `field_function` = 'layer Medicine/Vaccine Category' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $lmvc_cflag = mysqli_num_rows($query);
?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
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
                            <div class="float-left"><h3 class="card-title">Add Item Category</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_save_itemcategory.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Description<b style="color:red;">&ensp;*</b></label>
                                                <input type="text" name="cdesc" id="cdesc" class="form-control" placeholder="Enter description..." onkeyup="validatename(this.id);colorchange(this.id);" onchange="check_duplicate();">
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label>Prefix<b style="color:red;">&ensp;*</b></label>
                                                <input type="text" name="prefix" id="prefix" class="form-control" placeholder="Enter description..." onkeyup="validateprefix(this.id);colorchange(this.id);" onchange="check_duplicate2();">
                                            </div>
                                        </div>
                                        <?php
                                        if((int)$ppmic_flag == 1){
                                        ?>
                                        <div class="col-md-2">
                                            <div class="form-group" style="width:160px;">
                                                <label>Main Category</label>
                                                <select name="main_category" id="main_category" class="form-control select2" style="width:150px;">
                                                    <option value="select">-select-</option>
                                                    <?php foreach($micat_code as $mcode){ ?><option value="<?php echo $mcode; ?>"><?php echo $micat_name[$mcode]; ?></option><?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2"></div>
                                        <?php
                                        }
                                        else{
                                        ?>
                                        <div class="col-md-4"></div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                    <?php if($pp_flag > 0){ ?>
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-6">
                                            <label style="color:green;">Processing Plant Access</label>
                                            <div class="row">
                                                <?php if($pp_flag == 1 || $pp_flag == 3){ ?><div class="form-group" style="width: 200px;"><label><input type="checkbox" name="bird_plant" id="bird_plant" />&nbsp;Bird Processing</label></div><?php } ?>
                                                <?php if($pp_flag == 2 || $pp_flag == 3){ ?><div class="form-group" style="width: 200px;"><label><input type="checkbox" name="chicken_plant" id="chicken_plant" />&nbsp;Chicken Processing</label></div><?php } ?>
                                                <?php if($pp_flag == 4 || $pp_flag == 3){ ?><div class="form-group" style="width: 200px;"><label><input type="checkbox" name="plant_portioning" id="plant_portioning" />&nbsp;Plant Portioning</label></div><?php } ?>
                                            </div>
                                            
                                        </div>
                                        <div class="col-md-2"></div>
                                    </div>
                                    <?php } ?>
                                    <?php if($fs_flag > 0){ ?>
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-6">
                                            <label style="color:green;">Feed Sale</label>
                                            <div class="row">
                                                <div class="form-group" style="width: 200px;"><label><input type="checkbox" name="feedsale_flag" id="feedsale_flag" />&nbsp;Feed Sale</label></div>
                                            </div>
                                        </div>
                                        <div class="col-md-2"></div>
                                    </div>
                                    <?php } ?>
                                    <div class="row justify-content-center align-items-center">
                                        <?php if($bfeed_cflag > 0){ ?>
                                        <div class="form-group">
                                            <div class="form-group" style="width: 200px;"><label><input type="checkbox" name="bffeed_flag" id="bffeed_flag" />&nbsp;Breeder Female Feed</label></div>
                                        </div>
                                        <div class="form-group">
                                            <div class="form-group" style="width: 200px;"><label><input type="checkbox" name="bmfeed_flag" id="bmfeed_flag" />&nbsp;Breeder Male Feed</label></div>
                                        </div>
                                        <?php } ?>
                                        <?php if($bec_cflag > 0){ ?>
                                        <div class="form-group">
                                            <div class="form-group" style="width: 200px;"><label><input type="checkbox" name="begg_flag" id="begg_flag" />&nbsp;Breeder Egg</label></div>
                                        </div>
                                        <?php } ?>
                                        <?php if($bmvc_cflag > 0){ ?>
                                        <div class="form-group">
                                            <div class="form-group" style="width: 200px;"><label><input type="checkbox" name="bmv_flag" id="bmv_flag" />&nbsp;Breeder Med/Vac</label></div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <div class="row justify-content-center align-items-center">
                                        <?php if($lfeed_cflag > 0){ ?>
                                        <div class="form-group">
                                            <div class="form-group" style="width: 200px;"><label><input type="checkbox" name="lfeed_flag" id="lfeed_flag" />&nbsp;layer Feed</label></div>
                                        </div>
                                        <?php } ?>
                                        <?php if($lec_cflag > 0){ ?>
                                        <div class="form-group">
                                            <div class="form-group" style="width: 200px;"><label><input type="checkbox" name="legg_flag" id="legg_flag" />&nbsp;layer Egg</label></div>
                                        </div>
                                        <?php } ?>
                                        <?php if($lmvc_cflag > 0){ ?>
                                        <div class="form-group">
                                            <div class="form-group" style="width: 200px;"><label><input type="checkbox" name="lmv_flag" id="lmv_flag" />&nbsp;layer Med/Vac</label></div>
                                        </div>
                                        <?php } ?>
                                        <?php if($rawing_cbox > 0){ ?>
                                        <div class="form-group">
                                            <div class="form-group" style="width: 200px;"><label><input type="checkbox" name="rawing_flag" id="rawing_flag" />&nbsp;Raw Ingredient</label></div>
                                        </div>
                                        <?php } ?>
                                        <?php if($fgoods_cbox > 0){ ?>
                                        <div class="form-group">
                                            <div class="form-group" style="width: 200px;"><label><input type="checkbox" name="fgoods_flag" id="fgoods_flag" />&nbsp;Finished Goods</label></div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-3">
                                            <label>
                                                <input type="checkbox" name="newaccounts" id="newaccounts" value="new_acc" onclick="showDetails(this.id,this.value)">Create &amp; attach new accounts <!--Fill Credit / Debit Details-->
                                            </label>
                                        </div>
                                        <div class="col-md-3">
                                            <label>
                                                <input type="checkbox" name="newaccounts" id="preaccounts" value="pre_acc" onclick="showDetails(this.id,this.value)">Pre-defined Accounts <!--Fill Credit / Debit Details-->
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="itemaccounts" style="display:none;">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Item A/c<b style="color:red;">&nbsp;*</b></label>
                                                    <select name="iac" id="iac" class="form-control select2" style="width: 100%;">
                                                        <option value="select">select</option>
                                                        <?php
                                                            $sql = "SELECT * FROM `acc_coa` WHERE `description` LIKE 'STOCK -%' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                                                            while($row = mysqli_fetch_assoc($query)){
                                                        ?>
                                                                <option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
                                                        <?php
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group" id="icogs1">
                                                    <label>COGS A/c<b style="color:red;">&nbsp;*</b></label>
                                                    <select name="icogs" id="icogs" class="form-control select2" style="width: 100%;">
                                                        <option value="select">select</option>
                                                        <?php
                                                            $sql = "SELECT * FROM `acc_coa` WHERE `description` LIKE 'COGS -%' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                                                            while($row = mysqli_fetch_assoc($query)){
                                                        ?>
                                                                <option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
                                                        <?php
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group" id="isalesac1">
                                                    <label>Sales A/c<b style="color:red;">&nbsp;*</b></label>
                                                    <select name="isalesac" id="isalesac" class="form-control select2" style="width: 100%;">
                                                        <option value="select">select</option>
                                                        <?php
                                                            $sql = "SELECT * FROM `acc_coa` WHERE `description` LIKE 'Sales -%' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                                                            while($row = mysqli_fetch_assoc($query)){
                                                        ?>
                                                                <option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
                                                        <?php
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group" id="israc1">
                                                    <label>Sales Return A/c<b style="color:red;">&nbsp;*</b></label>
                                                    <select name="israc" id="israc" class="form-control select2" style="width: 100%;">
                                                        <option value="select">select</option>
                                                        <?php
                                                            $sql = "SELECT * FROM `acc_coa` WHERE `description` LIKE 'Sales Return -%' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                                                            while($row = mysqli_fetch_assoc($query)){
                                                        ?>
                                                                <option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
                                                        <?php
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group" id="iwpac1">
                                                    <label>Work in Progress<b style="color:red;">&nbsp;*</b></label>
                                                    <select name="iwpac" id="iwpac" class="form-control select2" style="width: 100%;">
                                                        <option value="select">select</option>
                                                        <?php
                                                            $sql = "SELECT * FROM `acc_coa` WHERE `description` LIKE 'WIP -%' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                                                            while($row = mysqli_fetch_assoc($query)){
                                                        ?>
                                                                <option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
                                                        <?php
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="form-group col-md-1" style="visibility:hidden;">
                                                <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                                <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
                                            </div>
                                            <div class="form-group col-md-1" style="visibility:hidden;">
                                                <label>D-Flag<b style="color:red;">&nbsp;*</b></label>
                                                <input type="text" style="width:auto;" class="form-control" name="dupflag" id="dupflag" value="0">
                                            </div>
                                            <div class="form-group col-md-1" style="visibility:hidden;">
                                                <label>D-Flag<b style="color:red;">&nbsp;*</b></label>
                                                <input type="text" style="width:auto;" class="form-control" name="dupflag2" id="dupflag2" value="0">
                                            </div>
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
        <script>
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_itemcategory.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var a = document.getElementById("cdesc").value;
                var b = document.getElementById("prefix").value;
                var c = document.getElementById("iac").value;
                var d = document.getElementById("icogs").value;
                var e = document.getElementById("isalesac").value;
                var f = document.getElementById("israc").value;
                var g = document.getElementById("newaccounts");
                var h = document.getElementById("preaccounts");
                var dupflag = document.getElementById("dupflag").value;
                var dupflag2 = document.getElementById("dupflag2").value;
                var l = true;
                if(parseFloat(dupflag) > 0){
                    alert("Item Category name already Exist \n Kindly check and try again ..!");
                    document.getElementById("cdesc").focus();
                    l = false;
                }
                else if(parseFloat(dupflag2) > 0){
                    alert("Prefix Code already exist, kindly change and click on save button ..!");
                    document.getElementById("prefix").focus();
                    document.getElementById("prefix").style.border = "1px solid red";
                    l = false;
                }
                else if(a.length == 0){
                    alert("Enter Description ..!");
                    document.getElementById("cdesc").focus();
                    document.getElementById("cdesc").style.border = "1px solid red";
                    l = false;
                }
                else if(b.length == 0){
                    alert("Enter Prefix ..!");
                    document.getElementById("prefix").focus();
                    document.getElementById("prefix").style.border = "1px solid red";
                    l = false;
                }
                else if(g.checked == false && h.checked == false){
                    alert("Please select Accounts ..!");
                    l = false;
                }
                else if(g.checked == false && h.checked == true){
                    if(c.match("select")){
                        alert("Select Item account ..!");
                        document.getElementById("iac").focus();
                        document.getElementById("iac").style.border = "1px solid red";
                        l = false;
                    }
                    else if(d.match("select")){
                        alert("Select COGS account ..!");
                        document.getElementById("icogs").focus();
                        document.getElementById("icogs").style.border = "1px solid red";
                        l = false;
                    }
                    else if(e.match("select")){
                        alert("Select Sales account ..!");
                        document.getElementById("isalesac").focus();
                        document.getElementById("isalesac").style.border = "1px solid red";
                        l = false;
                    }
                    else if(f.match("select")){
                        alert("Select Sales Return account ..!");
                        document.getElementById("israc").focus();
                        document.getElementById("cdesc").style.border = "1px solid red";
                        l = false;
                    }
                }
                else { }
                if(l == true){
                    return true;
                }
                else{
                    document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
                    return false;
                }
            }
            function colorchange(x){
                document.getElementById(x).style.border = "1px solid green";
            }
            function redirection_page(){
                window.location.href = "inv_displayitemcategory.php";
            }
            function validatename(x) {
                expr = /^[a-zA-Z0-9 (.&)_*?-]*$/;
                var a = document.getElementById(x).value;
                if(a.length > 50){
                    a = a.substr(0,a.length - 1);
                }
                if(!a.match(expr)){
                    a = a.replace(/[^a-zA-Z0-9 (.&)_*?-]/g, '');
                }
                document.getElementById(x).value = a;
            }
            function validateprefix(x) {
                expr = /^[a-zA-Z]*$/;
                var a = document.getElementById(x).value;
                if(a.length > 5){
                    a = a.substr(0,a.length - 1);
                }
                if(!a.match(expr)){
                    a = a.replace(/[^a-zA-Z]/g, '');
                }
                a = a.toUpperCase();
                document.getElementById(x).value = a;
            }
            function showDetails(a,b) {
                var checkBox = document.getElementById(a);
                if(b.match("new_acc")){
                    var a = document.getElementById("preaccounts");
                    var b = document.getElementById("itemaccounts");
                    if (checkBox.checked == true){
                        a.checked = false;
                        b.style.display = "none";
                    }
                    else { }
                }
                else if(b.match("pre_acc")){
                    var a = document.getElementById("newaccounts");
                    var b = document.getElementById("itemaccounts");
                    if (checkBox.checked == true){
                        a.checked = false;
                        b.style.display = "block";
                    }
                    else {
                    }
                }
                else {}
                
            }
			function check_duplicate(){
				var b = document.getElementById("cdesc").value;
				var c = "add";
				if(!b.length == 0){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "broiler_fetch_icategory_duplicates.php?cname="+b+"&type="+c;
                    //window.open(url);
					var asynchronous = true;
					oldqty.open(method, url, asynchronous);
					oldqty.send();
					oldqty.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var dup_count = this.responseText;
							if(parseFloat(dup_count) > 0){
								alert("Item Category Details are available with the same name.\n Kindly change the name");
								document.getElementById("dupflag"). value = 1;
							}
							else {
								document.getElementById("dupflag"). value = 0;
							}
						}
					}
				}
				else { }
			}
			function check_duplicate2(){
				var b = document.getElementById("prefix").value;
				var c = "add";
				if(!b.length == 0){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "broiler_fetch_icategoryprefix_duplicates.php?cname="+b+"&type="+c;
                    //window.open(url);
					var asynchronous = true;
					oldqty.open(method, url, asynchronous);
					oldqty.send();
					oldqty.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var dup_count = this.responseText;
							if(parseFloat(dup_count) > 0){
								alert("Item Category Prefix Details are available with the same name.\n Kindly change the name");
								document.getElementById("dupflag2"). value = 1;
							}
							else {
								document.getElementById("dupflag2"). value = 0;
							}
						}
					}
				}
				else { }
			}
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
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