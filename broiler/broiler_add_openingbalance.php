<?php
//broiler_add_openingbalance.php
include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['openingbalance'];
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
        $sql = "SELECT * FROM `item_details` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
		while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_cunit[$row['code']] = $row['cunits']; }

        $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

        //Breeder
        $sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Module' AND `field_function` = 'Maintain Feed Stock in FARM/UNIT/SHED/BATCH/FLOCK' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $bfeed_scnt = mysqli_num_rows($query);
        if((int)$bfeed_scnt > 0){
            $bfeed_stkon = ""; while($row = mysqli_fetch_assoc($query)){ $bfeed_stkon = $row['field_value']; } if($bfeed_stkon == ""){ $bfeed_stkon = "FLOCK"; }
            if($bfeed_stkon == "FARM"){
                $bsql = "SELECT * FROM `breeder_farms` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
                while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
            }
            else if($bfeed_stkon == "UNIT"){
                $bsql = "SELECT * FROM `breeder_units` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
                while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
            }
            else if($bfeed_stkon == "SHED"){
                $bsql = "SELECT * FROM `breeder_sheds` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
                while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
            }
            else if($bfeed_stkon == "BATCH"){
                $bsql = "SELECT * FROM `breeder_batch` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
                while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
            }
            else if($bfeed_stkon == "FLOCK"){
                $bsql = "SELECT * FROM `breeder_shed_allocation` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
                while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
            }
            else{ }
        }
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
            padding-left: 1px;
            padding-right: 1px;
            margin-right: 10px;
            height: 25px;
        }
    </style>
    </head>
    <body class="m-0 p-0 hold-transition">
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Openings</h3></div>
                        </div>
                        <div class="p-0 pt-5 card-body" align="center">
                            <div class="col-md-12">
                                <form action="broiler_save_openingbalance.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="form-group">
                                            <label>Date</label>
							                <input type="text" name="date" id="date" class="form-control datepicker" value="<?php echo date("d.m.Y"); ?>" style="width:100px;" />
                                        </div>
                                        <div class="form-group" style="width:170px;">
                                            <label>Type<b style="color:red;">&nbsp;*</b></label>
							                <select name="type" id="type" class="form-control select2" style="width:100px;">
                                                <option value="select">select</option>
                                                <option value="Item">Item</option>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:170px;">
                                            <label>Sector<b style="color:red;">&nbsp;*</b></label>
							                <select name="sector_code" id="sector_code" class="form-control select2" style="width:160px;">
                                                <option value="select">select</option>
                                                <?php
                                                foreach($sector_code as $scode){
                                                ?>
                                                <option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group" style="width:170px;">
                                            <label>Name/Description<b style="color:red;">&nbsp;*</b></label>
							                <select name="type_code[]" id="type_code[0]" class="form-control select2" style="width:160px;" onchange="fetch_itemuom(this.id);">
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
                                        <div class="form-group">
                                            <label>UOM<b style="color:red;">&nbsp;*</b></label>
							                <td><input type="text" name="uom[]" id="uom[0]" class="form-control" style="width:80px;" readonly /></td>
                                        </div>
                                        <div class="form-group">
                                            <label>Quantity</label>
							                <input type="text" name="quantity[]" id="quantity[0]" class="form-control" style="width:120px;" onkeyup="validatenum(this.id);calculate_total_amount(this.id);" onchange="validateamount(this.id)" />
                                        </div>
                                        <div class="form-group">
                                            <label>Rate</label>
							                <input type="text" name="rate[]" id="rate[0]" class="form-control" style="width:120px;" onkeyup="validatenum(this.id);calculate_total_amount(this.id);" onchange="validateamount(this.id)" />
                                        </div>
                                        <div class="form-group">
                                            <label>Amount</label>
							                <input type="text" name="amount[]" id="amount[0]" class="form-control" style="width:120px;" readonly />
                                        </div>
                                       
                                        <div class="form-group">
                                            <label>Remarks</label>
							                <textarea name="remarks[]" id="remarks[0]" class="form-control" style="width:120px;height:25px;"></textarea>
                                        </div>
                                        <div class="form-group" id="action[0]" style="padding-top: 12px;"><br/>
                                            <a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>
                                        </div>
                                    </div>
                                    <div class="p-0 col-md-12" id="row_body">

                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6" style="visibility:hidden;">
                                            <label>Incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="0">
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
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_openingbalance.php?ccid='+ccid;
            }
            function checkval(){
                var type = type_code = sectors = ""; var c = quantity = price = 0; var l = true;
                var a = document.getElementById("incr").value;
                sectors = document.getElementById("sector_code").value;
                type = document.getElementById("type").value;
                if(type.match("select")){
                    alert("Please select Type");
                    document.getElementById("type").focus();
                    l = false;
                }else if(sectors.match("select")){
                    alert("Please select Sector");
                    document.getElementById("sector_code").focus();
                    l = false;
                }
                else{
                    for(var b = 0;b <= a;b++){
                        c = b + 1;
                        type_code = document.getElementById("type_code["+b+"]").value;
                        quantity = document.getElementById("quantity["+b+"]").value;
                        price = document.getElementById("rate["+b+"]").value;
                       // sectors = document.getElementById("sector_code["+b+"]").value;
                        if(l == true){
                            if(type_code.match("select")){
                                alert("Kindly select Name/Description in row: "+c);
                                document.getElementById("type_code["+b+"]").focus();
                                l = false;
                            }
                            else if(quantity.length == 0 || quantity == 0 || quantity == "" || quantity == "0.00" || quantity == "0" || quantity == 0.00){
                                alert("Kindly enter Quantity in row: "+c);
                                document.getElementById("quantity["+b+"]").focus();
                                l = false;
                            }
                            else if(price.length == 0 || price == 0 || price == "" || price == "0.00" || price == "0" || price == 0.00){
                                alert("Kindly enter Rate in row: "+c);
                                document.getElementById("rate["+b+"]").focus();
                                l = false;
                            }
                            // else if(sectors.match("select")){
                            //     alert("Kindly select Sector in row: "+c);
                            //     document.getElementById("sector_code["+b+"]").focus();
                            //     l = false;
                            // }
                            else{ }
                        }
                    }
                }
                if(l == true){
                    return true;
                    document.getElementById("submit").disabled = "true";
                }
                else{
                    return false;
                }
            }
            function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                document.getElementById("incr").value = d;
                
                html += '<div class="row" id="row_no['+d+']">';
                html += '<div class="form-group" style="width:170px;"><label class="labelrow" style="display:none;">Name/Description</label><select name="type_code[]" id="type_code['+d+']" class="form-control select2" style="width:160px;" onchange="fetch_itemuom(this.id);"><option value="select">select</option><?php foreach($item_code as $icode){ ?><option value="<?php echo $icode; ?>"><?php echo $item_name[$icode]; ?></option><?php } ?></select></div>';

                 html += '<div class="form-group"><label class="labelrow" style="display:none;">UOM<b style="color:red;">&nbsp;*</b></label><input type="text" name="uom[]" id="uom['+d+']" class="form-control" style="width:80px;" readonly/></div>';

                html += '<div class="form-group"><label class="labelrow" style="display:none;">Quantity<b style="color:red;">&nbsp;*</b></label><input type="text" name="quantity[]" id="quantity['+d+']" class="form-control" style="width:120px;" onkeyup="validatenum(this.id);calculate_total_amount(this.id);" onchange="validateamount(this.id)" /></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Rate<b style="color:red;">&nbsp;*</b></label><input type="text" name="rate[]" id="rate['+d+']" class="form-control" style="width:120px;" onkeyup="validatenum(this.id);calculate_total_amount(this.id);" onchange="validateamount(this.id)" /></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Amount<b style="color:red;">&nbsp;*</b></label><input type="text" name="amount[]" id="amount['+d+']" class="form-control" style="width:120px;" readonly /></div>';
               
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Remarks</label><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="width:120px;height:25px;"></textarea></div>';
                html += '<div class="form-group" id="action['+d+']" style="padding-top: 5px;"><br class="labelrow" style="display:none;" /><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></div>';
                html += '</div>';
                html += '<hr class="labelrow" style="display:none;" />';
                $('#row_body').append(html); $('.select2').select2(); fetch_farm_details();
                $( ".datepicker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
            }
            function calculate_total_amount(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var qty = document.getElementById("quantity["+d+"]").value;
                var rate = document.getElementById("rate["+d+"]").value;
                if(qty.length == 0 || qty == "" || qty == 0 || qty == 0.00){ qty = 0; }
                if(rate.length == 0 || rate == "" || rate == 0 || rate == 0.00){ rate = 0; }

                var amount = parseFloat(qty) * parseFloat(rate);
                document.getElementById("amount["+d+"]").value = amount.toFixed(2);
            }
            function fetch_itemuom(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var code = document.getElementById("type_code["+d+"]").value;
                var uom = "";
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
                document.getElementById("uom["+d+"]").value = uom;
            }
            setInterval(function(){
                // window.screen.availHeight window.screen.availWidth
                if(window.screen.availWidth <= 400){
                    const collection = document.getElementsByClassName("labelrow");
                    for (let i = 0; i < collection.length; i++) { collection[i].style.display = "inline"; }
                }
                else{
                    const collection = document.getElementsByClassName("labelrow");
                    for (let i = 0; i < collection.length; i++) { collection[i].style.display = "none"; }
                }
            }, 1000);
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
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