<?php
//layer_edit_mvconsumed1.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['mvconsumed1'];
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
        //layer Medicine/Vaccine Details
        $sql = "SELECT * FROM `item_category` WHERE `active` = '1' AND `bmv_flag` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $icat_alist = array();
        while($row = mysqli_fetch_assoc($query)){ $icat_alist[$row['code']] = $row['code']; }
        $icat_list = implode("','", $icat_alist);
        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
        while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_cunit[$row['code']] = $row['cunits']; }

        $sql = "SELECT * FROM `layer_shed_allocation` WHERE `active` = '1' AND `dflag` = '0' AND `cls_flag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $bflk_code = $bflk_name = array();
        while($row = mysqli_fetch_assoc($query)){ $bflk_code[$row['code']] = $row['code']; $bflk_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'layer MedVac Consumption' AND `field_function` = 'Check Item Stock' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $bmv_scflag = mysqli_num_rows($query);
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
        $ids = $_GET['trnum'];
        $sql = "SELECT * FROM `layer_medicine_consumed` WHERE `trnum` = '$ids' AND `dflag` = '0' AND `trlink` = 'layer_display_mvconsumed1.php'";
        $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $date = $row['date'];
            $flock_code = $row['flock_code'];
            $items = $row['item_code'];
            $quantity = round($row['quantity'],5); if($quantity == ""){ $quantity = 0; }
            $remarks = $row['remarks'];
        }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Medicine/Vaccine Consumed</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="layer_modify_mvconsumed1.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row justify-content-center align-items-center">
                                        <table class="table1" style="width:auto;">
                                            <thead>
                                                <tr>
                                                    <th colspan="7">
                                                        <div class="row">
                                                            <div class="form-group">
                                                                <label for="date">Date<b style="color:red;">&nbsp;*</b></label>
                                                                <input type="text" name="date" id="date" class="form-control datepicker" value="<?php echo date('d.m.Y',strtotime($date)); ?>" style="width:110px;" readonly />
                                                            </div>
                                                        </div>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th style="text-align:center;"><label>Flock<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th style="text-align:center;"><label>Item Code<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th style="text-align:center;"><label>Item name<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th style="text-align:center;"><label>Item Unit<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th style="text-align:center;"><label>Quantity<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th style="text-align:center;"><label>Remarks<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th style="text-align:center;visibility:hidden;">SQ</th>
                                                    <th style="text-align:center;visibility:hidden;">SP</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody">
                                                <tr>
                                                    <td><select name="flock_code" id="flock_code" class="form-control select2" style="width:190px;" onchange="layer_fetch_item_details();"><option value="select">-select-</option><?php foreach($bflk_code as $ucode){ ?><option value="<?php echo $ucode; ?>" <?php if($flock_code == $ucode){ echo "selected"; } ?>><?php echo $bflk_name[$ucode]; ?></option><?php } ?></select></td>
                                                    <td><select name="item_code" id="item_code" class="form-control select2" style="width:190px;" onchange="update_item_details(this.id);fetch_item_uom();layer_fetch_item_details();"><option value="select">-select-</option><?php foreach($item_code as $ucode){ ?><option value="<?php echo $ucode; ?>" <?php if($items == $ucode){ echo "selected"; } ?>><?php echo $item_code[$ucode]; ?></option><?php } ?></select></td>
                                                    <td><select name="item_code1" id="item_code1" class="form-control select2" style="width:190px;" onchange="update_item_details(this.id);fetch_item_uom();layer_fetch_item_details();"><option value="select">-select-</option><?php foreach($item_code as $ucode){ ?><option value="<?php echo $ucode; ?>" <?php if($items == $ucode){ echo "selected"; } ?>><?php echo $item_name[$ucode]; ?></option><?php } ?></select></td>
                                                    <td><input type="text" name="item_uom" id="item_uom" class="form-control" value="<?php echo $item_cunit[$items]; ?>" style="width:110px;" readonly /></td>
                                                    <td><input type="text" name="quantity" id="quantity" class="form-control text-right" value="<?php echo $quantity; ?>" style="width:110px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>
                                                    <td><textarea name="remarks" id="remarks" class="form-control" style="width:110px;height:28px;" onkeyup="validatename(this.id);"><?php echo $remarks; ?></textarea></td>
                                                    <td style="visibility:hidden;"><input type="text" name="avl_stk" id="avl_stk" class="form-control text-right" value="0" style="width:30px;" readonly /></td>
                                                    <td style="visibility:hidden;"><input type="text" name="avg_prc" id="avg_prc" class="form-control text-right" value="0" style="width:30px;" readonly /></td>
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
				update_ebtn_status(1);
                var l = true;
                var date = document.getElementById("date").value;
                var flock_code = document.getElementById("flock_code").value;
                var item_code = document.getElementById("item_code").value;
                var item_code1 = document.getElementById("item_code1").value;
                var quantity = document.getElementById("quantity").value; if(quantity == ""){ quantity = 0; }
                var avl_stk = document.getElementById("avl_stk").value; if(avl_stk == ""){ avl_stk = 0; }
                var avg_prc = document.getElementById("avg_prc").value; if(avg_prc == ""){ avg_prc = 0; }
                var bmv_scflag = '<?php echo $bmv_scflag; ?>';
                if(date == "" || date == "01.01.1970"){
                    alert("Please select appropriate date");
                    document.getElementById("date").focus();
                    l = false;
                }
                else if(flock_code == "" || flock_code == "select"){
                    alert("Please enter Flock");
                    document.getElementById("flock_code").focus();
                    l = false;
                }
                else if(item_code == "" || item_code == "select"){
                    alert("Please enter Item Code");
                    document.getElementById("item_code").focus();
                    l = false;
                }
                else if(item_code1 == "" || item_code1 == "select"){
                    alert("Please enter Item Name");
                    document.getElementById("item_code1").focus();
                    l = false;
                }
                else if(parseInt(quantity) == 0){
                    alert("Please enter Quantity");
                    document.getElementById("quantity").focus();
                    l = false;
                }
                else if(parseInt(bmv_scflag) == 1 && parseInt(quantity) > parseInt(avl_stk) || parseInt(bmv_scflag) == 1 && parseInt(avl_stk) <= 0){
                    alert("Stock not available, please check and try again");
                    document.getElementById("quantity").focus();
                    l = false;
                }
                else{ }

                if(l == true){
                    return true;
                }
                else{
                    update_ebtn_status(0);
                    return false;
                }
			}
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'layer_display_mvconsumed1.php?ccid='+ccid;
            }
			function update_item_details(a){
                update_ebtn_status(1);
                if(a == "item_code"){
                    var icode = document.getElementById("item_code").value;
                    $('#item_code1').select2();
                    document.getElementById("item_code1").value = icode;
                    $('#item_code1').select2();
                }
                else if(a == "item_code1"){
                    var icode = document.getElementById("item_code1").value;
                    $('#item_code').select2();
                    document.getElementById("item_code").value = icode;
                    $('#item_code').select2();
                }
                else{ }
                update_ebtn_status(0);
            }
            function fetch_item_uom(){
                update_ebtn_status(1);
                var code = document.getElementById("item_code").value;
                var uom = "";
                if(code != "" && code != "select"){
                    <?php
                    foreach($item_code as $icode){
                        $uom = $item_cunit[$icode];
                        echo "if(code == '$icode'){";
                    ?>
                    uom = '<?php echo $uom; ?>';
                    <?php
                        echo "}";
                    }
                    ?>
                }
                document.getElementById("item_uom").value = uom;
                update_ebtn_status(0);
            }
			function layer_fetch_item_details(){
                update_ebtn_status(1);
				var item_code = document.getElementById("item_code").value;
				var flock_code = document.getElementById("flock_code").value;
				var type = "edit";
				var trnum = '<?php echo $ids; ?>';
                var date = document.getElementById("date").value;
                document.getElementById("avl_stk").value = 0;
				document.getElementById("avg_prc").value = 0;
                
				if(item_code != "select" && flock_code != "select"){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
                    var url = "layer_fetch_avlstock_quantity.php?date="+date+"&flock_code="+flock_code+"&item_code="+item_code+"&rows=0&itype=medvac&ftype=brd_mventry&ttype=edit&trnum="+trnum;
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
                                document.getElementById("avl_stk").value = parseFloat(item_qty).toFixed(2);
                                document.getElementById("avg_prc").value = parseFloat(item_prc).toFixed(5);
                            }
                            update_ebtn_status(0);
						}
					}
				}
				else { update_ebtn_status(0); }
			}
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
            layer_fetch_item_details();
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