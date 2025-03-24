<?php
//breeder_add_eggconvert1.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['eggconvert1'];
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
        $sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Egg Conversion' AND `field_function` = 'Check Egg Stock' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $bstk_cflag = mysqli_num_rows($query);

        $bsql = "SELECT * FROM `breeder_shed_allocation` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
        while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
        $bsql = "SELECT * FROM `inv_sectors` WHERE `brd_sflag` = '1' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
        while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }

        //Breeder Egg Details
        $sql = "SELECT * FROM `item_category` WHERE `active` = '1' AND `begg_flag` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
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
                            <div class="float-left"><h3 class="card-title">Add Egg Conversion</h3></div>
                        </div>
                        <div class="pl-2 card-body">
                            <form action="breeder_save_eggconvert1.php" method="post" role="form" onsubmit="return checkval()">
                                <div class="row row_body2">
                                    <table class="p-1 table1" style="width:auto;" align="center">
                                        <thead>
                                            <tr>
                                                <th colspan="4" style="text-align:center;">
                                                    <div class="row justify-content-center align-items-center">
                                                        <div class="form-group" style="width:120px;">
                                                            <label for="date">Date</label>
                                                            <input type="text" name="date" id="date" class="form-control datepicker" value="<?php echo $date; ?>" style="width:110px;" onchange="clear_data();" readonly />
                                                        </div>
                                                        <div class="form-group" style="width:200px;">
                                                            <label for="from_loc">Sector/Flock</label>
                                                            <select name="from_loc" id="from_loc" class="form-control select2" style="width:190px;" onchange="clear_data();">
                                                                <option value="select">-select-</option>
                                                                <?php foreach($sector_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $sector_name[$ucode]; ?></option><?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <thead>
                                            <tr>
                                                <th style="text-align:center;"><label>From Egg<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label>To Egg</label></th>
                                                <th style="text-align:center;"><label>Quantity</label></th>
                                                <th style="text-align:center;"><label>Disposed Eggs</label></th>
                                                <th style="text-align:center;"><label>Remarks</label></th>
                                                <th style="visibility:hidden;"><label>Action</label></th>
                                                <th style="visibility:hidden;"><label>SQ</label></th>
                                                <th style="visibility:hidden;"><label>SP</label></th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody">
                                            <tr>
                                                <td><select name="from_item[]" id="from_item[0]" class="form-control select2" style="width:190px;" onchange="fetch_bird_details(this.id);"><option value="select">-select-</option><?php foreach($bitem_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $bitem_name[$ucode]; ?></option><?php } ?></select></td>
                                                <td><select name="to_item[]" id="to_item[0]" class="form-control select2" style="width:190px;"><option value="select">-select-</option><?php foreach($bitem_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $bitem_name[$ucode]; ?></option><?php } ?></select></td>
                                                <td><input type="text" name="to_qty[]" id="to_qty[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);cal_tot_qty();" /></td>
                                                <td><input type="text" name="disposed_qty[]" id="disposed_qty[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);cal_tot_qty();" /></td>
                                                <td><textarea name="remarks[]" id="remarks[0]" class="form-control" style="padding:0;width:150px;height:28px;" onkeyup="validatename(this.id);"></textarea></td>
                                                <td id="action[0]" style="width:80px;"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id);" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                                <td style="visibility:hidden;"><input type="text" name="stk_qty[]" id="stk_qty[0]" class="form-control text-right" style="width:20px;" readonly /></td>
                                                <td style="visibility:hidden;"><input type="text" name="stk_prc[]" id="stk_prc[0]" class="form-control text-right" style="width:20px;" readonly /></td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th style="text-align:right;" colspan="2">Total</th>
                                                <th><input type="text" name="tot_cqty" id="tot_cqty" class="form-control text-right" style="width:90px;" readonly /></th>
                                                <th><input type="text" name="tot_dqty" id="tot_dqty" class="form-control text-right" style="width:90px;" readonly /></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
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
                var bstk_cflag = '<?php echo $bstk_cflag; ?>'; if(bstk_cflag == ""){ bstk_cflag = 0; }
                var date = document.getElementById("date").value;
                var from_loc = document.getElementById("from_loc").value;
                
                if(date == ""){
                    alert("Please select Date");
                    document.getElementById("date").focus();
                    l = false;
                }
                else if(from_loc == "" || from_loc == "select"){
                    alert("Please select From Sector/Flock");
                    document.getElementById("from_loc").focus();
                    l = false;
                }
                else{
                    var incr = document.getElementById("incr").value;
                    var from_item = to_item = ""; var to_qty = disposed_qty = 0;
                    for(var d = 0;d <= incr;d++){
                        if(l == true){
                            e = d + 1;
                            from_item = document.getElementById("from_item["+d+"]").value;
                            to_item = document.getElementById("to_item["+d+"]").value;
                            to_qty = document.getElementById("to_qty["+d+"]").value; if(to_qty == ""){ to_qty = 0; }
                            disposed_qty = document.getElementById("disposed_qty["+d+"]").value; if(disposed_qty == ""){ disposed_qty = 0; }
                            stk_qty = document.getElementById("stk_qty["+d+"]").value; if(stk_qty == ""){ stk_qty = 0; }
                            tot_fqty = parseInt(to_qty) + parseInt(disposed_qty);

                            if(from_item == "" || from_item == "select"){
                                alert("Please select From Egg in row: "+e);
                                document.getElementById("from_item["+d+"]").focus();
                                l = false;
                            }
                            else if(to_item == "" || to_item == "select"){
                                alert("Please select To Egg in row: "+e);
                                document.getElementById("to_item["+d+"]").focus();
                                l = false;
                            }
                            else if(from_item == to_item){
                                alert("From and To Items are same in row: "+e);
                                document.getElementById("to_item["+d+"]").focus();
                                l = false;
                            }
                            else if(parseInt(to_qty) == 0 && parseFloat(disposed_qty) == 0){
                                alert("Please enter Quantity in row: "+e);
                                document.getElementById("to_qty["+d+"]").focus();
                                l = false;
                            }
                            else if(parseInt(bstk_cflag) == 1 && parseFloat(tot_fqty) > parseFloat(stk_qty)){
                                alert("Entered Stock is less than Available Egg Stock. Please check and try again.");
                                document.getElementById("to_qty["+d+"]").focus();
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
                    update_ebtn_status(0);
                    return false;
                }
			}
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'breeder_display_eggconvert1.php?ccid='+ccid;
            }
			function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                document.getElementById("incr").value = d;
                
                html += '<tr id="row_no['+d+']">';
                html += '<td><select name="from_item[]" id="from_item['+d+']" class="form-control select2" style="width:190px;" onchange="fetch_bird_details(this.id);"><option value="select">-select-</option><?php foreach($bitem_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $bitem_name[$ucode]; ?></option><?php } ?></select></td>';
                html += '<td><select name="to_item[]" id="to_item['+d+']" class="form-control select2" style="width:190px;"><option value="select">-select-</option><?php foreach($bitem_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $bitem_name[$ucode]; ?></option><?php } ?></select></td>';
                html += '<td><input type="text" name="to_qty[]" id="to_qty['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);cal_tot_qty();" /></td>';
                html += '<td><input type="text" name="disposed_qty[]" id="disposed_qty['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);cal_tot_qty();" /></td>';
                html += '<td><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="padding:0;width:150px;height:28px;" onkeyup="validatename(this.id);"></textarea></td>';
                html += '<td id="action['+d+']" style="padding-top: 5px;width:80px;"><br class="labelrow" style="display:none;" /><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="stk_qty[]" id="stk_qty['+d+']" class="form-control text-right" style="width:20px;" readonly /></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="stk_prc[]" id="stk_prc['+d+']" class="form-control text-right" style="width:20px;" readonly /></td>';
                html += '</tr>';
                $('#tbody').append(html);
                $('.select2').select2();
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
            }
            function fetch_bird_details(a){
                update_ebtn_status(1);
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var date = document.getElementById("date").value;
                var from_loc = document.getElementById("from_loc").value;
                var from_item = document.getElementById("from_item["+d+"]").value;
                document.getElementById("stk_qty["+d+"]").value = 0;
                document.getElementById("stk_prc["+d+"]").value = 0;

                if(date == "" || from_loc == "" || from_loc == "select"){ update_ebtn_status(0); }
                else{
                    var oldqty = new XMLHttpRequest();
                    var method = "GET";
                    var url = "breeder_fetch_avlstock_quantity.php?date="+date+"&fsector="+from_loc+"&item_code="+from_item+"&rows="+d+"&ftype=egg_stock&ttype=add";
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
                            var stk_qty = item_sdt2[3]; if(stk_qty == ""){ stk_qty = 0; }
                            var stk_prc = item_sdt2[4]; if(stk_prc == ""){ stk_prc = 0; }

                            if(parseInt(err_flag) == 1){ alert(err_msg); }
                            else{
                                document.getElementById("stk_qty["+rows+"]").value = parseFloat(stk_qty).toFixed(0);
                                document.getElementById("stk_prc["+rows+"]").value = parseFloat(stk_prc).toFixed(5);
                            }
                            update_ebtn_status(0);
                        }
                    }
                }
            }
            function clear_data(){
                document.getElementById("tbody").innerHTML = "";
                document.getElementById("incr").value = 0;
                var html = ''; var d = 0;

                html += '<tr id="row_no['+d+']">';
                html += '<td><select name="from_item[]" id="from_item['+d+']" class="form-control select2" style="width:190px;" onchange="fetch_bird_details(this.id);"><option value="select">-select-</option><?php foreach($bitem_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $bitem_name[$ucode]; ?></option><?php } ?></select></td>';
                html += '<td><select name="to_item[]" id="to_item['+d+']" class="form-control select2" style="width:190px;"><option value="select">-select-</option><?php foreach($bitem_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $bitem_name[$ucode]; ?></option><?php } ?></select></td>';
                html += '<td><input type="text" name="to_qty[]" id="to_qty['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);cal_tot_qty();" /></td>';
                html += '<td><input type="text" name="disposed_qty[]" id="disposed_qty['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);cal_tot_qty();" /></td>';
                html += '<td><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="padding:0;width:150px;height:28px;" onkeyup="validatename(this.id);"></textarea></td>';
                html += '<td id="action['+d+']" style="width:80px;"><a href="javascript:void(0);" id="addrow['+d+']" onClick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="stk_qty[]" id="stk_qty['+d+']" class="form-control text-right" style="width:20px;" readonly /></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="stk_prc[]" id="stk_prc['+d+']" class="form-control text-right" style="width:20px;" readonly /></td>';
                html += '</tr>';
                $('#tbody').append(html);
                $('.select2').select2();
            }
            function cal_tot_qty(){
                var incr = document.getElementById("incr").value;
                var tot_cqty = tot_dqty = to_qty = disposed_qty = 0;
                for(var d = 0;d <= incr;d++){
                    to_qty = disposed_qty = 0;
                    to_qty = document.getElementById("to_qty["+d+"]").value; if(to_qty == ""){ to_qty = 0; }
                    tot_cqty = parseFloat(tot_cqty) + parseFloat(to_qty);
                    disposed_qty = document.getElementById("disposed_qty["+d+"]").value; if(disposed_qty == ""){ disposed_qty = 0; }
                    tot_dqty = parseFloat(tot_dqty) + parseFloat(disposed_qty);
                }
                document.getElementById("tot_cqty").value = parseFloat(tot_cqty).toFixed(0);
                document.getElementById("tot_dqty").value = parseFloat(tot_dqty).toFixed(0);
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