<?php
//layer_add_stocktransfer1.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['stocktransfer1'];
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
        $date = date("d.m.Y");
        $sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'layer Stock Transfer' AND `field_function` = 'Feed Stock in Bags' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $bfstk_bags = mysqli_num_rows($query);
        $sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'layer Stock Transfer' AND `field_function` = 'Check Item Stock' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $bstk_cflag = mysqli_num_rows($query);
        $sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'layer Office' AND `field_function` = 'Create layer Sectors/Offices' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $bsec_sflag = mysqli_num_rows($query);
        //layer
        $sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'layer Module' AND `field_function` = 'Maintain Feed Stock in FARM/UNIT/SHED/BATCH/FLOCK' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $bfeed_scnt = mysqli_num_rows($query); $sector_code = $sector_name = array();
        if((int)$bfeed_scnt > 0){
            $bfeed_stkon = ""; while($row = mysqli_fetch_assoc($query)){ $bfeed_stkon = $row['field_value']; } if($bfeed_stkon == ""){ $bfeed_stkon = "FLOCK"; }
            if($bfeed_stkon == "FARM"){
                $bsql = "SELECT * FROM `layer_farms` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
                while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
            }
            else if($bfeed_stkon == "UNIT"){
                $bsql = "SELECT * FROM `layer_units` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
                while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
            }
            else if($bfeed_stkon == "SHED"){
                $bsql = "SELECT * FROM `layer_sheds` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
                while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
            }
            else if($bfeed_stkon == "BATCH"){
                $bsql = "SELECT * FROM `layer_batch` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
                while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
            }
            else if($bfeed_stkon == "FLOCK"){
                $bsql = "SELECT * FROM `layer_shed_allocation` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
                while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
            }
            else{ }
        }
        if((int)$bsec_sflag > 0){
            $bsql = "SELECT * FROM `inv_sectors` WHERE `brd_sflag` = '1' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
            while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
        }
        //layer Feed Details
        $sql = "SELECT * FROM `item_category` WHERE `active` = '1' AND (`bffeed_flag` = '1' OR `bmfeed_flag` = '1' OR `bmv_flag` = '1') AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $icat_alist = array();
        while($row = mysqli_fetch_assoc($query)){ $icat_alist[$row['code']] = $row['code']; }
        $icat_list = implode("','", $icat_alist);
        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $bitem_code = $bitem_name = array();
        while($row = mysqli_fetch_assoc($query)){ $bitem_code[$row['code']] = $row['code']; $bitem_name[$row['code']] = $row['description']; }
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
        /*::-webkit-scrollbar { width: 8px; height:8px; }
        .row_body2{
            width:100%;
            overflow-y: auto;
        }*/
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
                            <div class="float-left"><h3 class="card-title">Add Stock Transfer</h3></div>
                        </div>
                        <div class="pl-2 card-body">
                            <form action="layer_save_stocktransfer1.php" method="post" role="form" onsubmit="return checkval()">
                                <div class="row row_body2">
                                    <table class="p-1 table1" style="width:auto;">
                                        <thead>
                                            <tr>
                                                <th style="text-align:center;"><label>Date<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label>Dc No.</label></th>
                                                <th style="text-align:center;"><label>From Location<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label>Item<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label>Quantity<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label>Price</label></th>
                                                <th style="text-align:center;"><label>To Location<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label>Remarks</label></th>
                                                <th style="visibility:hidden;"><label>Action</label></th>
                                                <th style="visibility:hidden;"><label>SQ</label></th>
                                                <th style="visibility:hidden;"><label>SP</label></th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody">
                                            <tr>
                                                <td><input type="text" name="date[]" id="date[0]" class="form-control datepicker" value="<?php echo $date; ?>" style="width:110px;" readonly /></td>
                                                <td><input type="text" name="dcno[]" id="dcno[0]" class="form-control" style="width:60px;" onkeyup="validatename(this.id);" /></td>
                                                <td><select name="fromwarehouse[]" id="fromwarehouse[0]" class="form-control select2" style="width:190px;" onchange="fetch_stock_qty(this.id);"><option value="select">-select-</option><?php foreach($sector_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $sector_name[$ucode]; ?></option><?php } ?></select></td>
                                                <td><select name="code[]" id="code[0]" class="form-control select2" style="width:190px;" onchange="fetch_stock_qty(this.id);"><option value="select">-select-</option><?php foreach($bitem_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $bitem_name[$ucode]; ?></option><?php } ?></select></td>
                                                <td><input type="text" name="quantity[]" id="quantity[0]" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);cal_stk_qty(this.id);" onchange="validateamount(this.id);" /></td>
                                                <td><input type="text" name="price[]" id="price[0]" class="form-control text-right" style="width:90px;" readonly /></td>
                                                <td><select name="towarehouse[]" id="towarehouse[0]" class="form-control select2" style="width:190px;"><option value="select">-select-</option><?php foreach($sector_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $sector_name[$ucode]; ?></option><?php } ?></select></td>
                                                <td><textarea name="remarks[]" id="remarks[0]" class="form-control" style="padding:0;width:150px;height:28px;" onkeyup="validatename(this.id);"></textarea></td>
                                                <td id="action[0]" style="width:80px;"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                                
                                                <td style="visibility:hidden;"><input type="text" name="item_sqty[]" id="item_sqty[0]" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>
                                                <td style="visibility:hidden;"><input type="text" name="item_sprc[]" id="item_sprc[0]" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>
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
                var incr = document.getElementById("incr").value;
                var date = fromwarehouse = code = towarehouse = ""; var e = quantity = price = item_sqty = 0;
                var bstk_cflag = '<?php echo $bstk_cflag; ?>'; if(bstk_cflag == ""){ bstk_cflag = 0; }

                for(var d = 0;d <= incr;d++){
                    if(l == true){
                        e = d + 1;
                        date = document.getElementById("date["+d+"]").value;
                        fromwarehouse = document.getElementById("fromwarehouse["+d+"]").value;
                        code = document.getElementById("code["+d+"]").value;
                        quantity = document.getElementById("quantity["+d+"]").value; if(quantity == ""){ quantity = 0; }
                        price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }
                        towarehouse = document.getElementById("towarehouse["+d+"]").value;
                        item_sqty = document.getElementById("item_sqty["+d+"]").value; if(item_sqty == ""){ item_sqty = 0; }
                        
                        if(date == ""){
                            alert("Please select Date in row: "+e);
                            document.getElementById("date["+d+"]").focus();
                            l = false;
                        }
                        else if(fromwarehouse == "" || fromwarehouse == "select"){
                            alert("Please select From Location in row: "+e);
                            document.getElementById("fromwarehouse["+d+"]").focus();
                            l = false;
                        }
                        else if(code == "" || code == "select"){
                            alert("Please select item in row: "+e);
                            document.getElementById("code["+d+"]").focus();
                            l = false;
                        }
                        else if(parseFloat(quantity) == 0){
                            alert("Please enter Quantity in row: "+e);
                            document.getElementById("quantity["+d+"]").focus();
                            l = false;
                        }
                        else if(towarehouse == "" || towarehouse == "select"){
                            alert("Please select To Location in row: "+e);
                            document.getElementById("towarehouse["+d+"]").focus();
                            l = false;
                        }
                        else if(parseInt(bstk_cflag) == 1 && parseFloat(quantity) > parseFloat(item_sqty)){
                            alert("Stock not available in row: "+e);
                            document.getElementById("quantity["+d+"]").focus();
                            l = false;
                        }
                        else if(fromwarehouse == towarehouse){
                            alert("From Location and To Location are same in row: "+e);
                            document.getElementById("towarehouse["+d+"]").focus();
                            l = false;
                        }
                        else{ }
                    }
                }
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
                window.location.href = 'layer_display_stocktransfer1.php?ccid='+ccid;
            }
			function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                document.getElementById("incr").value = d;
                
                html += '<tr id="row_no['+d+']">';
                html += '<td><input type="text" name="date[]" id="date['+d+']" class="form-control datepicker" value="<?php echo $date; ?>" style="width:110px;" readonly /></td>';
                html += '<td><input type="text" name="dcno[]" id="dcno['+d+']" class="form-control" style="width:60px;" onkeyup="validatename(this.id);" /></td>';
                html += '<td><select name="fromwarehouse[]" id="fromwarehouse['+d+']" class="form-control select2" style="width:190px;" onchange="fetch_stock_qty(this.id);"><option value="select">-select-</option><?php foreach($sector_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $sector_name[$ucode]; ?></option><?php } ?></select></td>';
                html += '<td><select name="code[]" id="code['+d+']" class="form-control select2" style="width:190px;" onchange="fetch_stock_qty(this.id);"><option value="select">-select-</option><?php foreach($bitem_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $bitem_name[$ucode]; ?></option><?php } ?></select></td>';
                html += '<td><input type="text" name="quantity[]" id="quantity['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);cal_stk_qty(this.id);" onchange="validateamount(this.id);" /></td>';
                html += '<td><input type="text" name="price[]" id="price['+d+']" class="form-control text-right" style="width:90px;" readonly /></td>';
                html += '<td><select name="towarehouse[]" id="towarehouse['+d+']" class="form-control select2" style="width:190px;"><option value="select">-select-</option><?php foreach($sector_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $sector_name[$ucode]; ?></option><?php } ?></select></td>';
                html += '<td><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="padding:0;width:150px;height:28px;" onkeyup="validatename(this.id);"></textarea></td>';
                html += '<td id="action['+d+']" style="padding-top: 5px;width:80px;"><br class="labelrow" style="display:none;" /><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';

                html += '<td style="visibility:hidden;"><input type="text" name="item_sqty[]" id="item_sqty['+d+']" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="item_sprc[]" id="item_sprc['+d+']" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>';
                html += '</tr>';
                $('#tbody').append(html);
                $('.select2').select2();
                var today = '<?php echo date("d.m.Y"); ?>';
                $( ".datepicker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", maxDate: today, beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
            }
            function fetch_stock_qty(a){
                update_ebtn_status(1);
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var date = document.getElementById("date["+d+"]").value;
                var fsector = document.getElementById("fromwarehouse["+d+"]").value;
                var code = document.getElementById("code["+d+"]").value;
                document.getElementById("price["+d+"]").value = 0;
                document.getElementById("item_sqty["+d+"]").value = 0;
                document.getElementById("item_sprc["+d+"]").value = 0;

                if(date == "" || fsector == "" || fsector == "select" || code == "" || code == "select"){ update_ebtn_status(0); }
                else{
                    var oldqty = new XMLHttpRequest();
                    var method = "GET";
                    var url = "layer_fetch_avlstock_quantity.php?date="+date+"&fsector="+fsector+"&item_code="+code+"&rows="+d+"&ftype=stk_transfer&ttype=add";
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
                                var incr = document.getElementById("incr").value;
                                var rcode = ""; var rqty = 0;
                                for(var d = 0;d <= incr;d++){
                                    rcode = document.getElementById("code["+d+"]").value;
                                    rqty = document.getElementById("quantity["+d+"]").value; if(rqty == ""){ rqty = 0; }
                                    if(code == rcode){ item_qty = parseFloat(item_qty) - parseFloat(rqty); }
                                }
                                document.getElementById("item_sqty["+rows+"]").value = parseFloat(item_qty).toFixed(2);
                                document.getElementById("price["+rows+"]").value = parseFloat(item_prc).toFixed(5);
                                document.getElementById("item_sprc["+rows+"]").value = parseFloat(item_prc).toFixed(5);
                            }
                            update_ebtn_status(0);
                        }
                    }
                }
            }
            function cal_stk_qty(a){
                update_ebtn_status(1);
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var incr = document.getElementById("incr").value;
                //Initial Stock
                var stock = []; var e = f = g = 0;
                var icode = document.getElementById("code["+d+"]").value;
                var iqty = document.getElementById("quantity["+d+"]").value; if(iqty == ""){ iqty = 0; }
                var sqty = document.getElementById("item_sqty["+d+"]").value; if(sqty == ""){ sqty = 0; }
                stock[d] = parseFloat(sqty) - parseFloat(iqty);
                e = parseInt(d) + 1;
                if(parseFloat(e) <= parseFloat(incr)){
                    var rcode = ""; var rqty = 0;
                    for(var f = e;f <= incr;f++){
                        rcode = ""; rqty = 0;
                        rcode = document.getElementById("code["+f+"]").value;
                        rqty = document.getElementById("quantity["+f+"]").value; if(rqty == ""){ rqty = 0; }

                        if(icode == rcode){
                            g = f - 1;
                            if(parseFloat(stock[g]) >= parseFloat(rqty)){
                                document.getElementById("item_sqty["+f+"]").style.color = "black";
                            }
                            else{
                                document.getElementById("item_sqty["+f+"]").style.color = "red";
                            }
                            document.getElementById("item_sqty["+f+"]").value = parseFloat(stock[g]).toFixed(2);
                            stock[f] = parseFloat(stock[g]) - parseFloat(rqty);
                        }
                    }
                }
                update_ebtn_status(0);
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