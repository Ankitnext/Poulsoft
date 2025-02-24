<?php
//layer_add_dailyentry1.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['dailyentry1'];
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
        $sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'layer Daily Entry' AND `field_function` = 'Fetch Flocks Based On Shed Selection' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $shed_sflag = mysqli_num_rows($query);
        $sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'layer Daily Entry' AND `field_function` = 'Display Only Stock Available Feed Items' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $feed_aflag = mysqli_num_rows($query);
        $sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'layer Daily Entry' AND `field_function` = 'Display 2nd Feed Entry For Layer Birds' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $feed_2flag = mysqli_num_rows($query);
        $sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'layer Daily Entry' AND `field_function` = 'Feed Stock in Bags' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $bfstk_bags = mysqli_num_rows($query);
        $sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'layer Module' AND `field_function` = 'Maintain Feed Stock in FARM/UNIT/SHED/BATCH/FLOCK' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $bfeed_stkon = ""; while($row = mysqli_fetch_assoc($query)){ $bfeed_stkon = $row['field_value']; } if($bfeed_stkon == ""){ $bfeed_stkon = "FLOCK"; }
        
        if((int)$shed_sflag == 1){
            $sql = "SELECT * FROM `layer_sheds` WHERE `active` = '1' AND `dflag` = '0' AND `cls_flag` = '0' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); $bshed_code = $bshed_name = array();
            while($row = mysqli_fetch_assoc($query)){ $bshed_code[$row['code']] = $row['code']; $bshed_name[$row['code']] = $row['description']; }
        }
        else{
            $sql = "SELECT * FROM `layer_shed_allocation` WHERE `active` = '1' AND `dflag` = '0' AND `cls_flag` = '0' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); $bflk_code = $bflk_name = array();
            while($row = mysqli_fetch_assoc($query)){ $bflk_code[$row['code']] = $row['code']; $bflk_name[$row['code']] = $row['description']; }
        }

        //layer Feed Details
        if((int)$feed_aflag == 0){
            $sql = "SELECT * FROM `item_category` WHERE `active` = '1' AND `bfeed_flag` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); $icat_alist = $ib_flag = array();
            while($row = mysqli_fetch_assoc($query)){ $icat_alist[$row['code']] = $row['code']; $ib_flag[$row['code']] = $row['bfeed_flag']; }
            $icat_list = implode("','", $icat_alist);
            $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); $bfeed_code = $bfeed_name = array();
            while($row = mysqli_fetch_assoc($query)){
                if($ib_flag[$row['code']] == 1){ $bfeed_code[$row['code']] = $row['code']; $bfeed_name[$row['code']] = $row['description']; }
                
            }
        }
        //layer Egg Details
        $sql = "SELECT * FROM `item_category` WHERE `active` = '1' AND `begg_flag` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $icat_alist = array();
        while($row = mysqli_fetch_assoc($query)){ $icat_alist[$row['code']] = $row['code']; }
        $icat_list = implode("','", $icat_alist);
        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $begg_code = $begg_name = array();
        while($row = mysqli_fetch_assoc($query)){ $begg_code[$row['code']] = $row['code']; $begg_name[$row['code']] = $row['description']; }
        $esize = sizeof($begg_code);
?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
    <!-- Datepicker -->
    <link href="datepicker/jquery-ui.css" rel="stylesheet">
    <style>
        body{
            overflow: auto;
        }
        .form-control{
            font-size: 13px;
        }
        .f-info{ border-color: #c94eff !important; }
        /*.f-info .select2-selection{ border-color: #c94eff !important; }*/
        .m-info{ border-color: #ffb25b; }
        .p-info{ border-color: #0b9100; }
        ::-webkit-scrollbar { width: 8px; height:8px; } /*display: none;*/
        .row_body2{
            width:100%;
            overflow-y: auto;
        }
        .table1{
            transform: scale(0.8);
            transform-origin: top left;
        }
    </style>
    </head>
    <body class="m-0 hold-transition">
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Daily Entry</h3></div>
                        </div>
                        <div class="pl-2 card-body">
                            <form action="layer_save_dailyentry1.php" method="post" role="form" onsubmit="return checkval()">
                                <div class="row row_body2">
                                    <table class="p-1 table1" style="width:auto;">
                                        <thead>
                                            <?php if((int)$shed_sflag == 1){ ?>
                                            <tr>
                                                <th colspan="13">
                                                    <div class="pl-2 row">
                                                        <div class="form-group" style="width:200px;">
                                                            <label for="shed_code">Shed<b style="color:red;">&nbsp;*</b></label>
                                                            <select name="shed_code" id="shed_code" class="form-control select2" style="width:190px;" onchange="fetch_active_flock_list();">
                                                                <option value="select">-select-</option>
                                                                <?php foreach($bshed_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $bshed_name[$ucode]; ?></option><?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="form-group" style="width:200px;">
                                                            <label for="flock_code">Flock<b style="color:red;">&nbsp;*</b></label>
                                                            <select name="flock_code" id="flock_code" class="form-control select2" style="width:190px;" onchange="check_prod_flag();">
                                                                <option value="select">-select-</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group" style="width:70px;visibility:hidden;">
                                                            <label for="beps_flag">P.Flag</label>
                                                            <input type="text" name="beps_flag" id="beps_flag" class="form-control text-right" value="0" style="width:60px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" readonly />
                                                        </div>
                                                    </div>
                                                </th>
                                            </tr>
                                            <?php } else{ ?>
                                            <tr>
                                                <th colspan="13">
                                                    <div class="pl-2 row">
                                                        <div class="form-group" style="width:200px;">
                                                            <label for="flock_code">Flock<b style="color:red;">&nbsp;*</b></label>
                                                            <select name="flock_code" id="flock_code" class="form-control select2" style="width:190px;" onchange="check_prod_flag();">
                                                                <option value="select">-select-</option>
                                                                <?php foreach($bflk_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $bflk_name[$ucode]; ?></option><?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="form-group" style="width:70px;visibility:hidden;">
                                                            <label for="beps_flag">P.Flag</label>
                                                            <input type="text" name="beps_flag" id="beps_flag" class="form-control text-right" value="0" style="width:60px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" readonly />
                                                        </div>
                                                    </div>
                                                </th>
                                            </tr>
                                            <?php
                                            }
                                            $fcol_cnt = 4;
                                            ?>
                                            <tr>
                                                <th colspan="2"></th>
                                                <?php if((int)$feed_2flag == 1){ $fcol_cnt += 2; ?><th colspan="7" style="background-color:#c94eff;text-align:center;">Birds</th><?php } else{ ?><th colspan="5" style="background-color:#c94eff;text-align:center;">Birds</th><?php } ?>
                                                <th></th>
                                                <th></th>
                                                <?php if((int)$esize > 0){ ?><th colspan="<?php echo $esize + 1; ?>" style="background-color:#0b9100;text-align:center;" class="egg_list">Egg Production</th><?php } ?>
                                                <th colspan="<?php echo $fcol_cnt; ?>" style="background-color:#00d7a3;text-align:center;visibility:hidden;">Stock</th>
                                            </tr>
                                            <tr>
                                                <th style="text-align:center;"><label>Date</label></th>
                                                <th style="text-align:center;"><label>Age</label></th>

                                                <!--Layer Bird and Feed Details-->
                                                <th style="text-align:center;"><label>Mort.</label></th>
                                                <th style="text-align:center;"><label>Culls</label></th>
                                                <th style="text-align:center;"><label>B.Wt(Grams)</label></th>
                                                <th style="text-align:center;"><label>Feed</label></th>
                                                <?php if((int)$bfstk_bags == 1){ ?><th style="text-align:center;"><label>Bag's</label></th><?php } else{ ?><th style="text-align:center;"><label>Kg's</label></th><?php } ?>
                                                <?php if((int)$feed_2flag == 1){ ?>
                                                <th style="text-align:center;"><label>Feed-2</label></th>
                                                <?php if((int)$blyr_bags == 1){ ?><th style="text-align:center;"><label>Bag's</label></th><?php } else{ ?><th style="text-align:center;"><label>Kg's</label></th><?php } ?>
                                                <?php } ?>
                                                <th style="text-align:center;"><label>Remarks</label></th>
                                                
                                                <th style="text-align:center;visibility:hidden;"><label>Action</label></th>

                                                <!--Egg Production Details-->
                                                <?php
                                                foreach($begg_code as $icode){
                                                    echo '<th style="text-align:center;" class="egg_list">'.$begg_name[$icode].'</th>';
                                                }
                                                ?>
                                                <?php if((int)$esize > 0){ ?>
                                                    <th style="text-align:center;" class="egg_list">Egg Wt.</th>
                                                <?php } ?>
                                                <th style="visibility:hidden;" title="Layer Feed Stock Qty-1">FS1</th>
                                                <th style="visibility:hidden;" title="Layer Feed Stock Prc-1">FP1</th>
                                                <?php if((int)$feed_2flag == 1){ ?>
                                                <th style="visibility:hidden;" title="Layer Feed Stock Qty-2">FS2</th>
                                                <th style="visibility:hidden;" title="Layer Feed Stock Prc-2">FP2</th>
                                                <?php } ?>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody">
                                            <tr>
                                                <td><input type="text" name="date[]" id="date[0]" class="form-control" style="width:110px;" readonly /></td>
                                                <td><input type="text" name="breed_wage[]" id="breed_wage[0]" class="form-control text-right" style="width:60px;" readonly /></td>

                                                <!--layer Bird and Feed Details-->
                                                <td><input type="text" name="mort_qty[]" id="mort_qty[0]" class="form-control f-info text-right" style="width:60px;" onkeyup="validate_count(this.id);" /></td>
                                                <td><input type="text" name="cull_qty[]" id="cull_qty[0]" class="form-control f-info text-right" style="width:60px;" onkeyup="validate_count(this.id);" /></td>
                                                <td><input type="text" name="body_weight[]" id="fbody_weight[0]" class="form-control f-info text-right" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>
                                                <td><select name="feed_code1[]" id="feed_code1[0]" class="form-control select2 f-info" style="width:190px;" onchange="fetch_feedstock_qty(this.id);"><option value="select">-select-</option><?php foreach($bfeed_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $bfeed_name[$ucode]; ?></option><?php } ?></select></td>
                                                <td><input type="text" name="feed_qty1[]" id="feed_qty1[0]" class="form-control f-info text-right" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>
                                                <?php if((int)$feed_2flag == 1){ ?>
                                                <td><select name="feed_code2[]" id="feed_code2[0]" class="form-control f-info select2" style="width:190px;" onchange="fetch_feedstock_qty(this.id);"><option value="select">-select-</option><?php foreach($bfeed_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $bfeed_name[$ucode]; ?></option><?php } ?></select></td>
                                                <td><input type="text" name="feed_qty2[]" id="feed_qty2[0]" class="form-control f-info text-right" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>
                                                <?php } ?>

                                                <td><textarea name="remarks[]" id="remarks[0]" class="form-control" style="padding:0;width:150px;height:28px;" onkeyup="validatename(this.id);"></textarea></td>
                                                    
                                                <td id="action[0]" style="width:80px;"><a href="javascript:void(0);" id="addrow[0]" onClick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                                
                                                <!--Egg Production Details-->
                                                <?php
                                                foreach($begg_code as $icode){
                                                    $ikey = ""; $ikey = "egg_".$icode;
                                                ?>
                                                    <td class="egg_list"><input type="text" name="<?php echo $ikey; ?>[]" id="<?php echo $ikey; ?>[0]" class="form-control p-info text-right" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>
                                                <?php
                                                }
                                                ?>
                                                <?php if((int)$esize > 0){ ?>
                                                    <td class="egg_list"><input type="text" name="egg_weight[]" id="egg_weight[0]" class="form-control p-info text-right" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>
                                                <?php } ?>
                                                <td style="visibility:hidden;"><input type="text" name="feed_sqty1[]" id="feed_sqty1[0]" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>
                                                <td style="visibility:hidden;"><input type="text" name="feed_sprc1[]" id="feed_sprc1[0]" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>
                                                <?php if((int)$feed_2flag == 1){ ?>
                                                <td style="visibility:hidden;"><input type="text" name="feed_sqty2[]" id="feed_sqty2[0]" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>
                                                <td style="visibility:hidden;"><input type="text" name="feed_sprc2[]" id="feed_sprc2[0]" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>
                                                <?php } ?>
                                               
                                                <td style="visibility:hidden;"><input type="text" name="breed_age[]" id="breed_age[0]" class="form-control text-right" style="width:20px;" readonly /></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div><br/>
                                <div class="row" style="visibility:hidden;">
                                    <div class="form-group" style="width:30px;">
                                        <label>IN</label>
                                        <input type="text" name="incr" id="incr" class="form-control" value="0" style="padding:0;width:20px;" readonly />
                                    </div>
                                    <div class="form-group" style="width:30px;">
                                        <label>EB</label>
                                        <input type="text" name="ebtncount" id="ebtncount" class="form-control" value="0" style="padding:0;width:20px;" readonly />
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
            </section>
        </div>
        <!-- Datepicker -->
        <script src="datepicker/jquery/jquery.js"></script>
        <script src="datepicker/jquery-ui.js"></script>
        <script>
			function checkval(){
				update_ebtn_status(1);
                var l = true;
                var flock_code = document.getElementById("flock_code").value;
                if(flock_code == "" || flock_code == "select"){
                    alert("Please select Flock");
                    document.getElementById("flock_code").focus();
                    l = false;
                }
                else{
                    var incr = document.getElementById("incr").value;
                    var date = rdate = ""; var e = f = breed_age = feed_qty1 = feed_sqty1 = feed_qty2 = feed_sqty2 = mfeed_qty1 = mfeed_sqty1 = mfeed_qty2 = mfeed_sqty2 = 0;
                    var feed_aflag = '<?php echo $feed_aflag; ?>';
                    var feed_2flag = '<?php echo $feed_2flag; ?>';
                    for(var d = 0;d <= incr;d++){
                        if(l == true){
                            e = d + 1;
                            date = document.getElementById("date["+d+"]").value;
                            breed_age = document.getElementById("breed_age["+d+"]").value;
                            feed_qty1 = document.getElementById("feed_qty1["+d+"]").value; if(feed_qty1 == ""){ feed_qty1 = 0; }
                            feed_sqty1 = document.getElementById("feed_sqty1["+d+"]").value; if(feed_sqty1 == ""){ feed_sqty1 = 0; }
                            if(parseInt(feed_2flag) == 1){
                                feed_qty2 = document.getElementById("feed_qty2["+d+"]").value; if(feed_qty2 == ""){ feed_qty2 = 0; }
                                feed_sqty2 = document.getElementById("feed_sqty2["+d+"]").value; if(feed_sqty2 == ""){ feed_sqty2 = 0; }
                            }
                            if(date == ""){
                                alert("Please select Active Flock to fetch Date in row: "+e);
                                document.getElementById("date["+d+"]").focus();
                                l = false;
                            }
                            else if(breed_age == ""){
                                alert("Please select Active Flock to fetch Age in row: "+e);
                                document.getElementById("breed_age["+d+"]").focus();
                                l = false;
                            }
                            else if(parseInt(feed_aflag) == 1 && parseFloat(feed_qty1) > parseFloat(feed_sqty1)){
                                alert("Entered Birds Feed Consumed Quantity is greater than Feed Stock Available.\n Available Stock is: "+feed_sqty1+" in row: "+e);
                                document.getElementById("feed_qty1["+d+"]").focus();
                                l = false;
                            }
                            else if(parseInt(feed_2flag) == 1 && parseInt(feed_aflag) == 1 && parseFloat(feed_qty2) > parseFloat(feed_sqty2)){
                                alert("Entered Birds Feed-2 Consumed Quantity is greater than Feed Stock Available.\n Available Stock is: "+feed_sqty2+" in row: "+e);
                                document.getElementById("feed_qty2["+d+"]").focus();
                                l = false;
                            }
                            else{
                                for(var c = 0;c <= incr;c++){
                                    if(l == true){
                                        if(c == d){ }
                                        else{
                                            rdate = document.getElementById("date["+c+"]").value;
                                            if(rdate == date){
                                                alert("Same date details already available in row: "+e);
                                                document.getElementById("date["+c+"]").focus();
                                                l = false;
                                            }
                                        }
                                    }
                                } 
                            }
                        }
                    }
                }
                if(l == true){
                    var x = confirm("Are sure do you want to save this Entry?");
                    if(x == true){
                        return true;
                    }
                    else{
                        update_ebtn_status(0);
                        return false;
                    }
                }
                else{
                    update_ebtn_status(0);
                    return false;
                }
			}
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'layer_display_dailyentry1.php?ccid='+ccid;
            }
            function fetch_active_flock_list(){
                clear_data();
                removeAllOptions(document.getElementById("flock_code"));
                var shed_code = document.getElementById("shed_code").value;
                if(shed_code == "select" || shed_code == ""){ }
                else{
					var abrd_bchs = new XMLHttpRequest();
					var method = "GET";
					var url = "layer_fetch_shedwise_avlflocks.php?shed_code="+shed_code;
                    //window.open(url);
					var asynchronous = true;
					abrd_bchs.open(method, url, asynchronous);
					abrd_bchs.send();
					abrd_bchs.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var bch_list = this.responseText;
                            $('#flock_code').append(bch_list);
						}
					}
                }
            }
			function create_row(a){
                var flock_code = document.getElementById("flock_code").value;
                if(flock_code == "" || flock_code == "select"){
                    alert("Please select Flock");
                }
                else{
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("action["+d+"]").style.visibility = "hidden";
                    d++; var html = '';
                    var slno = d + 1;
                    document.getElementById("incr").value = d;
                    html += '<tr id="row_no['+d+']">';
                    html += '<td><input type="text" name="date[]" id="date['+d+']" class="form-control" style="width:110px;" readonly /></td>';
                    html += '<td><input type="text" name="breed_wage[]" id="breed_wage['+d+']" class="form-control text-right" style="width:60px;" readonly /></td>';

                    /*Layer Bird and Feed Details*/
                    var feed_2flag = '<?php echo $feed_2flag; ?>';
                    html += '<td><input type="text" name="mort_qty[]" id="mort_qty['+d+']" class="form-control f-info text-right" style="width:60px;" onkeyup="validate_count(this.id);" /></td>';
                    html += '<td><input type="text" name="cull_qty[]" id="cull_qty['+d+']" class="form-control f-info text-right" style="width:60px;" onkeyup="validate_count(this.id);" /></td>';
                    html += '<td><input type="text" name="body_weight[]" id="body_weight['+d+']" class="form-control f-info text-right" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                    html += '<td><select name="feed_code1[]" id="feed_code1['+d+']" class="form-control f-info select2" style="width:190px;" onchange="fetch_feedstock_qty(this.id);"><option value="select">-select-</option><?php foreach($bfeed_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $bfeed_name[$ucode]; ?></option><?php } ?></select></td>';
                    html += '<td><input type="text" name="feed_qty1[]" id="feed_qty1['+d+']" class="form-control f-info text-right" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                    if(parseInt(feed_2flag) == 1){
                    html += '<td><select name="feed_code2[]" id="feed_code2['+d+']" class="form-control f-info select2" style="width:190px;" onchange="fetch_feedstock_qty(this.id);"><option value="select">-select-</option><?php foreach($bfeed_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $bfeed_name[$ucode]; ?></option><?php } ?></select></td>';
                    html += '<td><input type="text" name="feed_qty2[]" id="feed_qty2['+d+']" class="form-control f-info text-right" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                    }

                    html += '<td><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="padding:0;width:150px;height:28px;" onkeyup="validatename(this.id);"></textarea></td>';
                    html += '<td id="action['+d+']" style="padding-top: 5px;width:80px;"><br class="labelrow" style="display:none;" /><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    
                    /*Egg Production Details*/
                    <?php
                    foreach($begg_code as $icode){
                        $ikey = ""; $ikey = "egg_".$icode;
                    ?>
                    html += '<td class="egg_list"><input type="text" name="<?php echo $ikey; ?>[]" id="<?php echo $ikey; ?>['+d+']" title="<?php echo $ikey; ?>['+d+']" class="form-control p-info text-right" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                    <?php
                    }
                    ?>
                    <?php if((int)$esize > 0){ ?>
                        html += '<td class="egg_list"><input type="text" name="egg_weight[]" id="egg_weight['+d+']" class="form-control p-info text-right" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                    <?php } ?>
                    html += '<td style="visibility:hidden;"><input type="text" name="feed_sqty1[]" id="feed_sqty1['+d+']" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>';
                    html += '<td style="visibility:hidden;"><input type="text" name="feed_sprc1[]" id="feed_sprc1['+d+']" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>';
                    if(parseInt(feed_2flag) == 1){
                    html += '<td style="visibility:hidden;"><input type="text" name="feed_sqty2[]" id="feed_sqty2['+d+']" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>';
                    html += '<td style="visibility:hidden;"><input type="text" name="feed_sprc2[]" id="feed_sprc2['+d+']" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>';
                    }
                    html += '<td style="visibility:hidden;"><input type="text" name="breed_age[]" id="breed_age['+d+']" class="form-control text-right" style="width:20px;" readonly /></td>';
                    html += '</tr>';
                    $('#tbody').append(html);
                    $('.select2').select2();
                    update_eggprod_fields();
                    fetch_flock_details();
                }
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
            }
            function check_prod_flag(){
                update_ebtn_status(1); clear_data();
                var flock_code = document.getElementById("flock_code").value;
                if(flock_code != "select"){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "layer_check_eggprod_status.php?flock_code="+flock_code;
                    //window.open(url);
					var asynchronous = true;
					oldqty.open(method, url, asynchronous);
					oldqty.send();
					oldqty.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var beps_flag = this.responseText;
                            document.getElementById("beps_flag").value = beps_flag;
                            update_eggprod_fields();
                            fetch_flock_details();
                            update_ebtn_status(0);
						}
					}
                }
                else{
                    update_eggprod_fields();
                    fetch_flock_details();
                    update_ebtn_status(0);
                }
            }
            function fetch_flock_details(){
                update_ebtn_status(1);
                var flock_code = document.getElementById("flock_code").value;
                var incr = document.getElementById("incr").value;
                if(flock_code != "select"){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "layer_fetch_flock_details.php?flock_code="+flock_code+"&incr="+incr;
                    //window.open(url);
					var asynchronous = true;
					oldqty.open(method, url, asynchronous);
					oldqty.send();
					oldqty.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var bbch_dt1 = this.responseText;
                            var bbch_dt2 = bbch_dt1.split("[@$&]");
                            var rows = bbch_dt2[0];
                            var date = bbch_dt2[1];
                            var breed_age = bbch_dt2[2];
                            var max_eflag = bbch_dt2[3];
                            if(parseInt(max_eflag) == 1){
                                alert("Upto date Daily Entry is available. Please check again.");
                                var a = "date["+rows+"]";
                                destroy_row(a);
                            }
                            else{
                                var breed_wage = calculate_age_weeks(breed_age);
                                document.getElementById("date["+rows+"]").value = date;
                                document.getElementById("breed_age["+rows+"]").value = breed_age;
                                document.getElementById("breed_wage["+rows+"]").value = breed_wage;
                            }
                            update_ebtn_status(0);
                            fetch_feedstock_items();
						}
                        else{
                            update_ebtn_status(0);
                            fetch_feedstock_items();
                        }
					}
                }
                else{
                    update_ebtn_status(0);
                    fetch_feedstock_items();
                }
            }
            function clear_data(){
                document.getElementById("tbody").innerHTML = "";
                document.getElementById("incr").value = 0;
                var html = ''; var d = 0;
                html += '<tr id="row_no['+d+']">';
                html += '<td><input type="text" name="date[]" id="date['+d+']" class="form-control" style="width:110px;" readonly /></td>';
                html += '<td><input type="text" name="breed_wage[]" id="breed_wage['+d+']" class="form-control text-right" style="width:60px;" readonly /></td>';

                /*Layer Bird and Feed Details*/
                var feed_2flag = '<?php echo $feed_2flag; ?>';
                html += '<td><input type="text" name="mort_qty[]" id="mort_qty['+d+']" class="form-control f-info text-right" style="width:60px;" onkeyup="validate_count(this.id);" /></td>';
                html += '<td><input type="text" name="cull_qty[]" id="cull_qty['+d+']" class="form-control f-info text-right" style="width:60px;" onkeyup="validate_count(this.id);" /></td>';
                html += '<td><input type="text" name="fbody_weight[]" id="body_weight['+d+']" class="form-control f-info text-right" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                html += '<td><select name="feed_code1[]" id="feed_code1['+d+']" class="form-control f-info select2" style="width:190px;" onchange="fetch_feedstock_qty(this.id);"><option value="select">-select-</option><?php foreach($bfeed_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $bfeed_name[$ucode]; ?></option><?php } ?></select></td>';
                html += '<td><input type="text" name="feed_qty1[]" id="feed_qty1['+d+']" class="form-control f-info text-right" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                if(parseInt(feed_2flag) == 1){
                html += '<td><select name="feed_code2[]" id="feed_code2['+d+']" class="form-control f-info select2" style="width:190px;" onchange="fetch_feedstock_qty(this.id);"><option value="select">-select-</option><?php foreach($bfeed_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $bfeed_name[$ucode]; ?></option><?php } ?></select></td>';
                html += '<td><input type="text" name="feed_qty2[]" id="feed_qty2['+d+']" class="form-control f-info text-right" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                }

                html += '<td><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="padding:0;width:150px;height:28px;" onkeyup="validatename(this.id);"></textarea></td>';

                html += '<td id="action['+d+']" style="width:80px;"><a href="javascript:void(0);" id="addrow['+d+']" onClick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>';
                
                /*Egg Production Details*/
                <?php
                foreach($begg_code as $icode){
                    $ikey = ""; $ikey = "egg_".$icode;
                ?>
                html += '<td class="egg_list"><input type="text" name="<?php echo $ikey; ?>[]" id="<?php echo $ikey; ?>['+d+']" title="<?php echo $ikey; ?>['+d+']" class="form-control p-info text-right" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                <?php
                }
                ?>
                <?php if((int)$esize > 0){ ?>
                    html += '<td class="egg_list"><input type="text" name="egg_weight[]" id="egg_weight['+d+']" class="form-control p-info text-right" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                <?php } ?>
                html += '<td style="visibility:hidden;"><input type="text" name="feed_sqty1[]" id="feed_sqty1['+d+']" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="feed_sprc1[]" id="feed_sprc1['+d+']" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>';
                if(parseInt(feed_2flag) == 1){
                html += '<td style="visibility:hidden;"><input type="text" name="feed_sqty2[]" id="feed_sqty2['+d+']" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="feed_sprc2[]" id="feed_sprc2['+d+']" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>';
                }
                html += '<td style="visibility:hidden;"><input type="text" name="breed_age[]" id="breed_age['+d+']" class="form-control text-right" style="width:20px;" readonly /></td>';
				html += '</tr>';
				$('#tbody').append(html);
				$('.select2').select2();
            }
            function update_eggprod_fields(){
                var beps_flag = document.getElementById("beps_flag").value; if(beps_flag == ""){ beps_flag = 0; }
                var egg_list = document.getElementsByClassName("egg_list");
                for(let i = 0;i < egg_list.length;i++) {
                    if(parseInt(beps_flag) == 1){
                        egg_list[i].style.width = "90px";
                        egg_list[i].style.visibility = "visible";
                    }
                    else{
                        egg_list[i].style.width = "1px";
                        egg_list[i].style.visibility = "hidden";
                    }
                }
            }
            function fetch_feedstock_items(){
                var feed_aflag = '<?php echo $feed_aflag; ?>';
                if(parseInt(feed_aflag) == 1){
                    update_ebtn_status(1);
                    var shed_sflag = '<?php echo $shed_sflag; ?>';
                    var shed_code = "";
                    if(parseInt(shed_sflag) == 1){ shed_code = document.getElementById("shed_code").value; }
                    var flock_code = document.getElementById("flock_code").value;
                    var feed_2flag = '<?php echo $feed_2flag; ?>';
                    var mfeed_2flag = '<?php echo $mfeed_2flag; ?>';
                    
                    var d = document.getElementById("incr").value;
                    var date = document.getElementById("date["+d+"]").value;
                    removeAllOptions(document.getElementById("feed_code1["+d+"]"));
                    if(parseInt(feed_2flag) == 1){ removeAllOptions(document.getElementById("feed_code2["+d+"]")); }
    
                    if(flock_code != "select" && date != ""){
                        var oldqty = new XMLHttpRequest();
                        var method = "GET";
                        var url = "layer_fetch_avlstock_items.php?date="+date+"&flock_code="+flock_code+"&rows="+d+"&itype=feed&ftype=brd_dentry&ttype=add&shed_code="+shed_code;
                        //window.open(url);
                        var asynchronous = true;
                        oldqty.open(method, url, asynchronous);
                        oldqty.send();
                        oldqty.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var bfeed_dt1 = this.responseText;
                                var bfeed_dt2 = bfeed_dt1.split("[@$&]");
                                var err_flag = bfeed_dt2[0];
                                var err_msg = bfeed_dt2[1];
                                var rows = bfeed_dt2[2];
                                var feed_opt = bfeed_dt2[3];
                                if(parseInt(err_flag) == 1){ alert(err_msg); }
                                else{ }
                                $('#feed_code1\\['+rows+'\\]').append(feed_opt);
                                if(parseInt(feed_2flag) == 1){ $('#feed_code2\\['+rows+'\\]').append(feed_opt); }
                                update_ebtn_status(0);
                            }
                        }
                    }
                }
                else{ update_ebtn_status(0); }
            }
            function fetch_feedstock_qty(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                update_ebtn_status(1);
                var shed_sflag = '<?php echo $shed_sflag; ?>';
                var shed_code = "";
                if(parseInt(shed_sflag) == 1){ shed_code = document.getElementById("shed_code").value; }
                var flock_code = document.getElementById("flock_code").value;
                var date = document.getElementById("date["+d+"]").value;
                var feeds = document.getElementById(a).value;

                var sq_id = a.replace('code', 'sqty');
                var sp_id = a.replace('code', 'sprc');
                document.getElementById(sq_id).value = 0;
                document.getElementById(sp_id).value = 0;
                var feed_2flag = '<?php echo $feed_2flag; ?>';
                var mfeed_2flag = '<?php echo $mfeed_2flag; ?>';

                var incr = document.getElementById("incr").value;
                if(feeds == "" || feeds == "select" || flock_code == "" || flock_code == "select" || date == ""){ update_ebtn_status(0); }
                else{
                    var oldqty = new XMLHttpRequest();
                    var method = "GET";
                    var url = "layer_fetch_avlstock_quantity.php?date="+date+"&flock_code="+flock_code+"&item_code="+feeds+"&rows="+d+"&itype=feed&ftype=brd_dentry&ttype=add&shed_code="+shed_code;
                    //window.open(url);
                    var asynchronous = true;
                    oldqty.open(method, url, asynchronous);
                    oldqty.send();
                    oldqty.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var item_sdt1 = this.responseText;
                            var item_sdt2 = item_sdt1.split("[@$&]");
                            var err_flag = item_sdt2[0];
                            var err_msg = item_sdt2[1];
                            var rows = item_sdt2[2];
                            var item_qty = item_sdt2[3];
                            var item_prc = item_sdt2[4];
                            if(parseInt(err_flag) == 1){ alert(err_msg); }
                            else{
                                var feed_code1 = feed_code2 = "";
                                var feed_qty1 = feed_qty2 = 0;
                                for(var d = 0;d <= incr;d++){
                                    feed_code1 = document.getElementById("feed_code1["+d+"]").value;
                                    feed_qty1 = document.getElementById("feed_qty1["+d+"]").value; if(feed_qty1 == ""){ feed_qty1 = 0; }
                                    if(feeds == feed_code1){ item_qty = parseFloat(item_qty) - parseFloat(feed_qty1); }

                                    if(parseInt(feed_2flag) == 1){
                                        feed_code2 = document.getElementById("feed_code2["+d+"]").value;
                                        feed_qty2 = document.getElementById("feed_qty2["+d+"]").value; if(feed_qty2 == ""){ feed_qty2 = 0; }
                                        if(feeds == feed_code2){ item_qty = parseFloat(item_qty) - parseFloat(feed_qty2); }
                                    }
                                }
                                document.getElementById(sq_id).value = parseFloat(item_qty).toFixed(2);
                                document.getElementById(sp_id).value = parseFloat(item_prc).toFixed(5);
                            }
                            update_ebtn_status(0);
                        }
                    }
                }
            }
            function calculate_rowwise_itemstk(){ }
            function update_ebtn_status(a){
                if(parseInt(a) == 1){
                    document.getElementById("ebtncount").value = "1";
                    document.getElementById("submit").style.visibility = "hidden";
                }
                else{
                    document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
                }
            }
            function calculate_age_weeks(d_age){
                var week_no = Math.floor(parseFloat(d_age) / 7);
                var age_no = parseFloat(d_age) % 7;
                if(parseInt(age_no) == 0){
                    age_no = 7; week_no = parseInt(week_no) - 1;
                }
                var age_weeks = week_no+"."+age_no;
                return parseFloat(age_weeks).toFixed(1);
            }
            update_eggprod_fields();
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