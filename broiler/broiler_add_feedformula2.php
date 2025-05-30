<?php
//broiler_add_feedformula2.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['feedformula2'];
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
        $sql = "SELECT * FROM `item_details` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_unit[$row['code']] = $row['cunits']; }

        $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
        while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
        
        $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%premix%'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
        
        $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Finishing Material%'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
		
		$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Feed%'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
        
        $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Finished Goods%'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
        
        $sql = "SELECT * FROM `item_category` WHERE (`description` LIKE '%Raw Materials%' OR `description` LIKE '%Poultry%' OR `description` LIKE '%Aqua%' OR `description` LIKE '%Vitamins Premix%' OR `description` LIKE '%Dairy%' OR `description` LIKE '%Vitamins%' OR `description` LIKE '%Raw Ingredients%' OR `description` LIKE '%Finished Products%')"; $query = mysqli_query($conn,$sql);
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
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Feed Formula</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_save_feedformula2.php" method="post" role="form" onSubmit="return checkval()">
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>Formula Creation Date<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="date" id="date" class="form-control datepicker" value="<?php echo date('d.m.Y'); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Feed Mill<b style="color:red;">&nbsp;*</b></label>
                                                <select name="mill_code" id="mill_code" class="form-control select2">
                                                    <option value="all">-All-</option>
                                                    <?php
                                                    $feedmill_type_code = "";
                                                    $sql = "SELECT * FROM `main_officetypes` WHERE `description` LIKE '%Feedmill%' AND `active` = '1' AND `dflag` = '0' OR `description` LIKE '%mill%' AND `active` = '1' AND `dflag` = '0' OR `description` LIKE '%feed mill%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                                                    while($row = mysqli_fetch_assoc($query)){
                                                        if($feedmill_type_code == ""){ $feedmill_type_code = $row['code']; } else{ $feedmill_type_code = $feedmill_type_code."','".$row['code']; }
                                                    }
                                                    $sql = "SELECT * FROM `inv_sectors` WHERE `type` IN ('$feedmill_type_code') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                                                    while($row = mysqli_fetch_assoc($query)){
                                                    ?>
                                                    <option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
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
                                                    foreach($feed_code as $fcode){
                                                    ?>
                                                    <option value="<?php echo $fcode; ?>"><?php echo $feed_name[$fcode]; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Formula Name<b style="color:red;">&nbsp;*</b></label>
							                    <input type="text" name="description" id="description" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                    <div class="row" id="row_no[0]">
                                        <div class="col-md-2"></div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Item<b style="color:red;">&nbsp;*</b></label>
                                                <select name="item_code[]" id="item_code[0]" class="form-control select2" style="width: 100%;" onchange="fetch_item_unit(this.id);fetch_item_avg_price(this.id);">
                                                    <option value="select">select</option>
                                                    <?php
                                                    foreach($item_code as $icode){
                                                    ?>
                                                    <option value="<?php echo $icode; ?>"><?php echo $item_name[$icode]; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label>Unit<b style="color:red;">&nbsp;*</b></label>
							                    <input type="text" name="unit_code[]" id="unit_code[0]" class="form-control" readonly >
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label>Quantity<b style="color:red;">&nbsp;*</b></label>
							                    <input type="text" name="item_qty[]" id="item_qty[0]" class="form-control" onKeyUp="validatenum(this.id);calculatetotal(); val_amt();" onchange="validateamount(this.id)" >
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label>Rate<b style="color:red;">&nbsp;*</b></label>
							                    <input type="text" name="rate[]" id="rate[0]" class="form-control" onKeyUp="validatenum(this.id);calculatetotal_rate(); val_amt();" onchange="validateamount(this.id)" >
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label>Amount<b style="color:red;">&nbsp;*</b></label>
							                    <input type="text" name="amount[]" id="amount[0]" class="form-control" onKeyUp="validatenum(this.id);calculatetotal_amt();" onchange="validateamount(this.id)" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-2" id="action[0]">
                                            <div class="form-group" style="padding-top: 12px;"><br/>
                                                <a href="javascript:void(0);" id="addrow[0]" onClick="create_row(this.id)"><i class="fa fa-plus"></i></a>
                                            </div>
                                        </div>
                                        <div class="col-md-2"></div>
                                    </div>
                                    <div class="col-md-12" id="row_body">

                                    </div>
                                    <div class="row" style="visibility:hidden;">
                                        <div class="form-group" style="width:20px;">
                                            <label>IN</label>
							                <input type="text" name="incr" id="incr" class="form-control" value="0" readonly />
                                        </div>
                                        <div class="form-group" style="width:20px;">
                                            <label>EC</label>
                                            <input type="text" name="ebtncount" id="ebtncount" class="form-control" value="0" style="width:20px;" readonly />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-2">
                                        <div class="form-group">
                                                <label>Total Quantity<b style="color:red;">&nbsp;*</b></label>
							                    <input type="text" name="total_qty" id="total_qty" class="form-control"  readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Total Rate<b style="color:red;">&nbsp;*</b></label>
							                    <input type="text" name="total_rate" id="total_rate" class="form-control"  readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                        <div class="form-group">
                                                <label>Total Amount<b style="color:red;">&nbsp;*</b></label>
							                    <input type="text" name="total_amt" id="total_amt" class="form-control" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group" align="center">
                                            <button type="submit" name="submit" id="submit" class="btn btn-sm bg-purple">Submit</button>&ensp;
                                            <button type="button" name="cancel" id="cancel" class="btn btn-sm bg-danger" onClick="return_back()">Cancel</button>
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
                window.location.href = 'broiler_display_feedformula2.php?ccid='+ccid;
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
                html+= '<div class="col-md-2"><div class="form-group"><select name="item_code[]" id="item_code['+d+']" class="form-control select2" style="width: 100%;" onchange="fetch_item_unit(this.id);fetch_item_avg_price(this.id);"><option value="select">select</option><?php foreach($item_code as $icode){ ?><option value="<?php echo $icode; ?>"><?php echo $item_name[$icode]; ?></option><?php } ?></select></div></div>';
                html+= '<div class="col-md-1"><div class="form-group"><input type="text" name="unit_code[]" id="unit_code['+d+']" class="form-control" readonly ></div></div>';
                html+= '<div class="col-md-1"><div class="form-group"><input type="text" name="item_qty[]" id="item_qty['+d+']" class="form-control" onkeyup="validatenum(this.id);calculatetotal();val_amt()" onchange="validateamount(this.id)" ></div></div>';
                html+= '<div class="col-md-1"><div class="form-group"><input type="text" name="rate[]" id="rate['+d+']" class="form-control" onkeyup="validatenum(this.id);calculatetotal_rate();val_amt()" onchange="validateamount(this.id)" ></div></div>';
                html+= '<div class="col-md-1"><div class="form-group"><input type="text" name="amount[]" id="amount['+d+']" class="form-control" onkeyup="validatenum(this.id);calculatetotal_amt();" onchange="validateamount(this.id)" readonly></div></div>';
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
            function val_amt(){
                var a = document.getElementById("incr").value;
                var c = 0;
                for(var b = 0;b <= a;b++){
                    d = document.getElementById("item_qty["+b+"]").value; if(d == ""){ d = 0; }
                    r = document.getElementById("rate["+b+"]").value; if(r == ""){ r = 0; }
                    c = parseFloat(d) * parseFloat(r);
                    document.getElementById("amount["+b+"]").value = parseFloat(c).toFixed(2);
                } 
                calculatetotal_amt();
            }
            // function val_amt2(){
            //         d = document.getElementById("total_qty").value;
            //         r = document.getElementById("total_rate").value;
            //         if(d.length == 0 || d == 0 || d == "NaN" || d == "0.00"){ d = 0; }
            //         if(r.length == 0 || r == 0 || r == "NaN" || r == "0.00"){ r = 0; }
            //         c = parseFloat(d) * parseFloat(r);
            //          document.getElementById("total_amt").value = c;
            // }
            function calculatetotal(){
                var a = document.getElementById("incr").value;
                var c = f = 0;
                for(var b = 0;b <= a;b++){
                    d = document.getElementById("item_qty["+b+"]").value;
                    e = document.getElementById("rate["+b+"]").value;
                    if(d.length == 0 || d == 0 || d == "NaN" || d == "0.00"){ d = 0; }
                    if(e.length == 0 || e == 0 || e == "NaN" || e == "0.00"){ e = 0; }
                    c = c + parseFloat(d);
                    f = f + parseFloat(e);
                }
                document.getElementById("total_qty").value = c.toFixed(3);
                document.getElementById("total_rate").value = f.toFixed(3);
                 //calculatetotal_amt();
            }
            function calculatetotal_rate(){
                var a = document.getElementById("incr").value;
                var c = 0;
                for(var b = 0;b <= a;b++){
                    d = document.getElementById("rate["+b+"]").value;
                    if(d.length == 0 || d == 0 || d == "NaN" || d == "0.00"){ d = 0; }
                    c = c + parseFloat(d);
                }
                document.getElementById("total_rate").value = c.toFixed(3);
                // calculatetotal_amt();
            }
            function calculatetotal_amt(){
                var a = document.getElementById("incr").value;
                var c = 0;
                for(var b = 0;b <= a;b++){
                    d = document.getElementById("amount["+b+"]").value;
                    if(d.length == 0 || d == 0 || d == "NaN" || d == "0.00"){ d = 0; }
                    c = c + parseFloat(d);
                }
                document.getElementById("total_amt").value = c.toFixed(3);
            }
            function fetch_item_avg_price(a){
                var mill_code = document.getElementById("mill_code").value;
                var date = document.getElementById("date").value;
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var items = document.getElementById(a).value;
                var fetch_items = new XMLHttpRequest();
				var method = "GET";
				var url = "broiler_fetch_itemstockmaster_lsfi.php?sector="+mill_code+"&item_code="+items+"&date="+date+"&row_count="+d+"&trtype=FeedProduction";
                //window.open(url);
				var asynchronous = true;
				fetch_items.open(method, url, asynchronous);
				fetch_items.send();
				fetch_items.onreadystatechange = function(){
					if(this.readyState == 4 && this.status == 200){
						var item_price = this.responseText;
                        var item_details = item_price.split("@");
                        var avg_prc = item_details[1]; if(avg_prc == ""){ avg_prc = 0; }
                        if(parseFloat(item_details[0]) <= 0 || parseFloat(item_details[1]) <= 0){
                            document.getElementById("rate["+item_details[3]+"]").value = parseFloat(avg_prc).toFixed(2);
                        }
                        else{
                            document.getElementById("rate["+item_details[3]+"]").value = parseFloat(avg_prc).toFixed(2);
                        }
                        val_amt();
                    }
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