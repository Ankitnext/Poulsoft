<?php
//broiler_copy_feedformula.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['feedformula'];
$uri = explode("/",$_SERVER['REQUEST_URI']); $url2 = explode("?",$uri[1]); $href = "broiler_add_feedformula.php"; //$url2[0];
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
        $sql = "SELECT * FROM `item_details` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_unit[$row['code']] = $row['cunits']; }

        $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
        while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
        
        $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%premix%'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
        
        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat') AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $feed_code[$row['code']] = $row['code']; $feed_name[$row['code']] = $row['description']; }
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
    </style>
    </head>
    <body class="m-0 hold-transition">
        <?php
        $id = $_GET['id'];
        $sql = "SELECT * FROM `broiler_feed_formula` WHERE `id` = '$id' GROUP BY `code` ORDER BY `id` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $code = $row['code'];
            $description = $row['description'];
            $formula_item_code = $row['formula_item_code'];
            $date = $row['date'];
            $mill_code = $row['mill_code'];
            $total_qty = $row['total_qty'];
        }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Copy Feed Formula</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_save_feedformula.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>Formula Creation Date<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="date" id="date" class="form-control datepicker" value="<?php echo date('d.m.Y',strtotime($date)); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Feed Mill<b style="color:red;">&nbsp;*</b></label>
                                                <select name="mill_code" id="mill_code" class="form-control select2">
                                                    <option value="all" <?php if($mill_code == "all"){ echo "selected"; } ?>>-All-</option>
                                                    <?php
                                                    $feedmill_type_code = "";
                                                    $sql = "SELECT * FROM `main_officetypes` WHERE `description` LIKE '%Feedmill%' AND `active` = '1' AND `dflag` = '0' OR `description` LIKE '%mill%' AND `active` = '1' AND `dflag` = '0' OR `description` LIKE '%feed mill%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                                                    while($row = mysqli_fetch_assoc($query)){
                                                        if($feedmill_type_code == ""){ $feedmill_type_code = $row['code']; } else{ $feedmill_type_code = $feedmill_type_code."','".$row['code']; }
                                                    }
                                                    $sql = "SELECT * FROM `inv_sectors` WHERE `type` IN ('$feedmill_type_code') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                                                    while($row = mysqli_fetch_assoc($query)){
                                                    ?>
                                                    <option value="<?php echo $row['code']; ?>" <?php if($mill_code == $row['code']){ echo "selected"; } ?>><?php echo $row['description']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Feed Name<b style="color:red;">&nbsp;*</b></label>
                                                <select name="formula_item_code" id="formula_item_code" class="form-control select2">
                                                    <option value="select">select</option>
                                                    <?php
                                                    foreach($feed_code as $icode){
                                                    ?>
                                                    <option value="<?php echo $icode; ?>" <?php if($formula_item_code == $icode){ echo "selected"; } ?>><?php echo $feed_name[$icode]; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Formula Name<b style="color:red;">&nbsp;*</b></label>
							                    <input type="text" name="description" id="description" class="form-control" value="<?php echo $description; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                    <?php
                                    $sql = "SELECT * FROM `broiler_feed_formula` WHERE `code` = '$code' ORDER BY `id` ASC"; $query = mysqli_query($conn,$sql); $c = 0; $isize = mysqli_num_rows($query);
                                    while($row = mysqli_fetch_assoc($query)){
                                    ?>
                                    <div class="row" id="row_no[<?php echo $c; ?>]">
                                        <div class="col-md-2"></div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <?php if($c == 0){ echo '<label>Item<b style="color:red;">&nbsp;*</b></label>'; } ?>
                                                <select name="item_code[]" id="item_code[<?php echo $c; ?>]" class="form-control select2" style="width: 100%;" onchange="fetch_item_unit(this.id)">
                                                    <option value="select">select</option>
                                                    <?php
                                                    foreach($item_code as $icode){
                                                    ?>
                                                    <option value="<?php echo $icode; ?>" <?php if($row['item_code'] == $icode){ echo "selected"; } ?>><?php echo $item_name[$icode]; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <?php if($c == 0){ echo '<label>Unit<b style="color:red;">&nbsp;*</b></label>'; } ?>
							                    <input type="text" name="unit_code[]" id="unit_code[<?php echo $c; ?>]" class="form-control" value="<?php echo $row['unit_code']; ?>" readonly >
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <?php if($c == 0){ echo '<label>Quantity<b style="color:red;">&nbsp;*</b></label>'; } ?>
							                    <input type="text" name="item_qty[]" id="item_qty[<?php echo $c; ?>]" class="form-control" value="<?php echo round($row['item_qty'],3); ?>" onkeyup="validatenum(this.id);calculatetotal();" onchange="validateamount(this.id)" >
                                            </div>
                                        </div>
                                        <?php
                                            if($c == 0 && $c == $isize - 1){
                                        ?>
                                                <div class="col-md-1" id="action[<?php echo $c; ?>]"><div class="form-group"><a href="javascript:void(0);" id="addrow[<?php echo $c; ?>]" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a></div></div>
                                        <?php
                                            }
                                            else if($c == $isize - 1){
                                        ?>
                                        <div class="col-md-2" id="action[<?php echo $c; ?>]"><div class="form-group"><a href="javascript:void(0);" id="addrow[<?php echo $c; ?>]" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow[<?php echo $c; ?>]" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></div></div>
                                        <?php
                                            }
                                            else{ }
                                        ?>
                                        <div class="col-md-2"></div>
                                    </div>
                                    <?php
                                    $c++;
                                    }
                                    ?>
                                    <div class="col-md-12" id="row_body">

                                    </div>
                                    <div class="col-md-12" style="visibility:hidden;">
                                        <div class="form-group">
							                <input type="text" name="incr" id="incr" class="form-control" value="<?php echo $c; ?>">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Total Quantity<b style="color:red;">&nbsp;*</b></label>
							                    <input type="text" name="total_qty" id="total_qty" class="form-control" value="<?php echo $total_qty; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                    <div class="row" style="visibility:hidden;">
                                        <div class="form-group" style="width:20px;">
                                            <label>ID</label>
							                <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $id; ?>">
                                        </div>
                                        <div class="form-group" style="width:20px;">
                                            <label>EC</label>
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
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_feedformula.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var mill_code = document.getElementById("mill_code").value;
                var formula_item_code = document.getElementById("formula_item_code").value;
                var description = document.getElementById("description").value;
                var incr = document.getElementById("incr").value;
                var item_code = item_qty = 0;
                var l = true;
                if(mill_code.match("select")){
                    alert("Please select Feed Mill");
                    document.getElementById("mill_code").focus();
                    l = false;
                }
                else if(formula_item_code.match("select")){
                    alert("Please select Feed Item");
                    document.getElementById("formula_item_code").focus();
                    l = false;
                }
                else if(description.length == 0){
                    alert("Please enter Feed Formula Name Charges");
                    document.getElementById("description").focus();
                    l = false;
                }
                else{
                    for(var i = 0;i <= incr;i++){
                        if(l == true){
                            item_code = document.getElementById("item_code["+i+"]").value;
                            item_qty = document.getElementById("item_qty["+i+"]").value;
                            if(item_code.match("select")){
                                alert("Please select Feed Item");
                                document.getElementById("item_code["+i+"]").focus();
                                l = false;
                            }
                            else if(item_qty.length == 0){
                                alert("Please enter Quantity");
                                document.getElementById("item_qty["+i+"]").focus();
                                l = false;
                            }
                            else{ }
                        }
                    }
                }
                if(l == true){
                    return true;
                }
                else{
                    document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
                    return false;
                }
            }
            function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                document.getElementById("incr").value = d;
                
                html+= '<div class="row" id="row_no['+d+']">';
                html+= '<div class="col-md-2"></div>';
                html+= '<div class="col-md-2"><div class="form-group"><select name="item_code[]" id="item_code['+d+']" class="form-control select2" style="width: 100%;" onchange="fetch_item_unit(this.id)"><option value="select">select</option><?php foreach($item_code as $icode){ ?><option value="<?php echo $icode; ?>"><?php echo $item_name[$icode]; ?></option><?php } ?></select></div></div>';
                html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="unit_code[]" id="unit_code['+d+']" class="form-control" readonly ></div></div>';
                html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="item_qty[]" id="item_qty['+d+']" class="form-control" onkeyup="validatenum(this.id);calculatetotal();" onchange="validateamount(this.id)" ></div></div>';
                html+= '<div class="col-md-2" id="action['+d+']"><div class="form-group" style="padding-top: 12px;"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></div></div>';
                html+= '<div class="col-md-2"></div>';
                html+= '</div>';
                $('#row_body').append(html); $('.select2').select2();
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
            }
            function fetch_item_unit(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var e = document.getElementById(a).value;
                <?php
                foreach($item_code as $icode){
                    echo "if(e == '$icode'){";
                ?>
                document.getElementById("unit_code["+d+"]").value = '<?php echo $item_unit[$icode]; ?>';
                <?php
                echo "}";
                }
                ?>
            }
            function calculatetotal(){
                var a = parseInt(document.getElementById("incr").value);
                var c = d = 0;
                for(var b = 0;b <= a;b++){
                    d = document.getElementById("item_qty["+b+"]").value; if(d.length == 0 || d == ""){ d = 0; }
                    c = parseFloat(c) + parseFloat(d);
                document.getElementById("total_qty").value = parseFloat(c).toFixed(3);
                }
            }
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
            function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(3); document.getElementById(x).value = b; }
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