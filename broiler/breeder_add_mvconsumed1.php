<?php
//breeder_add_mvconsumed1.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['mvconsumed1'];
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
        $date = date("Y-m-d");

        //Breeder Medicine/Vaccine Details
        $sql = "SELECT * FROM `item_category` WHERE `active` = '1' AND `bmv_flag` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $icat_alist = array();
        while($row = mysqli_fetch_assoc($query)){ $icat_alist[$row['code']] = $row['code']; }
        $icat_list = implode("','", $icat_alist);
        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
        while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_cunit[$row['code']] = $row['cunits']; }

        $sql = "SELECT * FROM `breeder_shed_allocation` WHERE `active` = '1' AND `dflag` = '0' AND `cls_flag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $bflk_code = $bflk_name = array();
        while($row = mysqli_fetch_assoc($query)){ $bflk_code[$row['code']] = $row['code']; $bflk_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder MedVac Consumption' AND `field_function` = 'Check Item Stock' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $bmv_scflag = mysqli_num_rows($query);
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
                            <div class="float-left"><h3 class="card-title">Add Medicine/Vaccine Consumed</h3></div>
                        </div>
                        <div class="card-body">
                            <form action="breeder_save_mvconsumed1.php" method="post" role="form" onsubmit="return checkval()">
                                <div class="row row_body2">
                                    <table class="p-1 table1" style="width:auto;">
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
                                                <th style="text-align:left;"><label>+/-</label></th>
                                                <th style="text-align:center;visibility:hidden;">SQ</th>
                                                <th style="text-align:center;visibility:hidden;">SP</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody">
                                            <tr>
                                                <td><select name="flock_code[]" id="flock_code[0]" class="form-control select2" style="width:190px;" onchange="breeder_fetch_item_details(this.id);"><option value="select">-select-</option><?php foreach($bflk_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $bflk_name[$ucode]; ?></option><?php } ?></select></td>
                                                <td><select name="item_code[]" id="item_code[0]" class="form-control select2" style="width:190px;" onchange="update_item_details(this.id);fetch_item_uom(this.id);breeder_fetch_item_details(this.id);"><option value="select">-select-</option><?php foreach($item_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $item_code[$ucode]; ?></option><?php } ?></select></td>
                                                <td><select name="item_code1[]" id="item_code1[0]" class="form-control select2" style="width:190px;" onchange="update_item_details(this.id);fetch_item_uom(this.id);breeder_fetch_item_details(this.id);"><option value="select">-select-</option><?php foreach($item_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $item_name[$ucode]; ?></option><?php } ?></select></td>
                                                <td><input type="text" name="item_uom[]" id="item_uom[0]" class="form-control" style="width:110px;" readonly /></td>
                                                <td><input type="text" name="quantity[]" id="quantity[0]" class="form-control text-right" style="width:110px;" onkeyup="validatenum(this.id);cal_stk_qty(this.id);" onchange="validateamount(this.id);" /></td>
                                                <td><textarea name="remarks[]" id="remarks[0]" class="form-control" style="width:110px;height:28px;" onkeyup="validatename(this.id);"></textarea></td>
                                                <td id="action[0]" style="width:80px;"><a href="javascript:void(0);" id="addrow[0]" onClick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                                <td style="visibility:hidden;"><input type="text" name="avl_stk[]" id="avl_stk[0]" class="form-control text-right" value="0" style="width:30px;" readonly /></td>
                                                <td style="visibility:hidden;"><input type="text" name="avg_prc[]" id="avg_prc[0]" class="form-control text-right" value="0" style="width:30px;" readonly /></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div><br/>
                                <div class="row" style="visibility:hidden;">
                                    <div class="form-group" style="width:30px;">
                                        <label>IN<b style="color:red;">&ensp;*</b></label>
                                        <input type="text" name="incr" id="incr" class="form-control" value="0" style="width:20px;" readonly />
                                    </div>
                                    <div class="form-group" style="width:30px;">
                                        <label>EB<b style="color:red;">&ensp;*</b></label>
                                        <input type="text" name="ebtncount" id="ebtncount" class="form-control" value="0" style="width:20px;" readonly />
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
                var date = document.getElementById("date").value;
                if(date == "" || date == "01.01.1970"){
                    alert("Please select appropriate date");
                    document.getElementById("date").focus();
                    l = false;
                }
                else{
                    var incr = document.getElementById("incr").value;
                    var flock_code = item_code = item_code1 = ""; var e = quantity = avl_stk = avg_prc = 0;
                    var bmv_scflag = '<?php echo $bmv_scflag; ?>';

                    for(var d = 0;d <= incr;d++){
                        if(l == true){
                            e = d + 1;
                            flock_code = document.getElementById("flock_code["+d+"]").value;
                            item_code = document.getElementById("item_code["+d+"]").value;
                            item_code1 = document.getElementById("item_code1["+d+"]").value;
                            quantity = document.getElementById("quantity["+d+"]").value; if(quantity == ""){ quantity = 0; }
                            avl_stk = document.getElementById("avl_stk["+d+"]").value; if(avl_stk == ""){ avl_stk = 0; }
                            avg_prc = document.getElementById("avg_prc["+d+"]").value; if(avg_prc == ""){ avg_prc = 0; }
                                
                            if(flock_code == "" || flock_code == "select"){
                                alert("Please enter Flock in row: "+e);
                                document.getElementById("flock_code["+d+"]").focus();
                                l = false;
                            }
                            else if(item_code == "" || item_code == "select"){
                                alert("Please enter Item Code in row: "+e);
                                document.getElementById("item_code["+d+"]").focus();
                                l = false;
                            }
                            else if(item_code1 == "" || item_code1 == "select"){
                                alert("Please enter Item Name in row: "+e);
                                document.getElementById("item_code1["+d+"]").focus();
                                l = false;
                            }
                            else if(parseInt(quantity) == 0){
                                alert("Please enter Quantity in row: "+e);
                                document.getElementById("quantity["+d+"]").focus();
                                l = false;
                            }
                            else if(parseInt(bmv_scflag) == 1 && parseInt(quantity) > parseInt(avl_stk) || parseInt(bmv_scflag) == 1 && parseInt(avl_stk) <= 0){
                                alert("Stock not available, please check and try again in row: "+e);
                                document.getElementById("quantity["+d+"]").focus();
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
                window.location.href = 'breeder_display_mvconsumed1.php?ccid='+ccid;
            }
			function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                var slno = d + 1;
                document.getElementById("incr").value = d;
                html += '<tr id="row_no['+d+']">';
                html += '<td><select name="flock_code[]" id="flock_code['+d+']" class="form-control select2" style="width:190px;" onchange="breeder_fetch_item_details(this.id);"><option value="select">-select-</option><?php foreach($bflk_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $bflk_name[$ucode]; ?></option><?php } ?></select></td>';
                html += '<td><select name="item_code[]" id="item_code['+d+']" class="form-control select2" style="width:190px;" onchange="update_item_details(this.id);fetch_item_uom(this.id);breeder_fetch_item_details(this.id);"><option value="select">-select-</option><?php foreach($item_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $item_code[$ucode]; ?></option><?php } ?></select></td>';
                html += '<td><select name="item_code1[]" id="item_code1['+d+']" class="form-control select2" style="width:190px;" onchange="update_item_details(this.id);fetch_item_uom(this.id);breeder_fetch_item_details(this.id);"><option value="select">-select-</option><?php foreach($item_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $item_name[$ucode]; ?></option><?php } ?></select></td>';
                html += '<td><input type="text" name="item_uom[]" id="item_uom['+d+']" class="form-control" style="width:110px;" readonly /></td>';
                html += '<td><input type="text" name="quantity[]" id="quantity['+d+']" class="form-control text-right" style="width:110px;" onkeyup="validatenum(this.id);cal_stk_qty(this.id);" onchange="validateamount(this.id);" /></td>';
                html += '<td><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="width:110px;height:28px;" onkeyup="validatename(this.id);"></textarea></td>';
                html += '<td id="action['+d+']" style="padding-top: 5px;width:80px;"><br class="labelrow" style="display:none;" /><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="avl_stk[]" id="avl_stk['+d+']" class="form-control text-right" value="0" style="width:30px;" readonly /></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="avg_prc[]" id="avg_prc['+d+']" class="form-control text-right" value="0" style="width:30px;" readonly /></td>';
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
			function update_item_details(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                if(b[0] == "item_code"){
                    var icode = document.getElementById("item_code["+d+"]").value;
                    $('#item_code1\\[' + d + '\\]').select2();
                    document.getElementById("item_code1["+d+"]").value = icode;
                    $('#item_code1\\[' + d + '\\]').select2();
                }
                else if(b[0] == "item_code1"){
                    var icode = document.getElementById("item_code1["+d+"]").value;
                    $('#item_code\\[' + d + '\\]').select2();
                    document.getElementById("item_code["+d+"]").value = icode;
                    $('#item_code\\[' + d + '\\]').select2();
                }
                else{ }
            }
            function fetch_item_uom(a){
                update_ebtn_status(1);
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                if(b[0] == "item_code"){
                    var code = document.getElementById("item_code["+d+"]").value;
                }
                else if(b[0] == "item_code1"){
                    var code = document.getElementById("item_code1["+d+"]").value;
                }
                else{}
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
                document.getElementById("item_uom["+d+"]").value = uom;
                update_ebtn_status(0);
            }
			function breeder_fetch_item_details(a){
                update_ebtn_status(1);
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
				var item_code = document.getElementById("item_code["+d+"]").value;
				var flock_code = document.getElementById("flock_code["+d+"]").value;
				var type = "add";
				
                var date = document.getElementById("date").value;
                document.getElementById("avl_stk["+d+"]").value = 0;
				document.getElementById("avg_prc["+d+"]").value = 0;
                
				if(item_code != "select" && flock_code != "select"){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
                    var url = "breeder_fetch_avlstock_quantity.php?date="+date+"&flock_code="+flock_code+"&item_code="+item_code+"&rows="+d+"&itype=medvac&ftype=brd_mventry&ttype=add";
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
                                    if(parseInt(rows) == parseInt(d)){ }
                                    else{
                                        rcode = document.getElementById("item_code["+d+"]").value;
                                        rqty = document.getElementById("quantity["+d+"]").value; if(rqty == ""){ rqty = 0; }
                                        if(item_code == rcode){ item_qty = parseFloat(item_qty) - parseFloat(rqty); }
                                    }
                                }
                                document.getElementById("avl_stk["+rows+"]").value = parseFloat(item_qty).toFixed(2);
                                document.getElementById("avg_prc["+rows+"]").value = parseFloat(item_prc).toFixed(5);
                            }
                            update_ebtn_status(0);
						}
					}
				}
				else { update_ebtn_status(0); }
			}
            function cal_stk_qty(a){
                update_ebtn_status(1);
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var incr = document.getElementById("incr").value;
                //Initial Stock
                var stock = []; var e = f = g = 0;
                var icode = document.getElementById("item_code["+d+"]").value;
                var iqty = document.getElementById("quantity["+d+"]").value; if(iqty == ""){ iqty = 0; }
                var sqty = document.getElementById("avl_stk["+d+"]").value; if(sqty == ""){ sqty = 0; }
                stock[d] = parseFloat(sqty) - parseFloat(iqty);
                e = parseInt(d) + 1;
                if(parseFloat(e) <= parseFloat(incr)){
                    var rcode = ""; var rqty = 0;
                    for(var f = e;f <= incr;f++){
                        rcode = ""; rqty = 0;
                        rcode = document.getElementById("item_code["+f+"]").value;
                        rqty = document.getElementById("quantity["+f+"]").value; if(rqty == ""){ rqty = 0; }

                        if(icode == rcode){
                            g = f - 1;
                            if(parseFloat(stock[g]) >= parseFloat(rqty)){
                                document.getElementById("avl_stk["+f+"]").style.color = "black";
                            }
                            else{
                                document.getElementById("avl_stk["+f+"]").style.color = "red";
                            }
                            document.getElementById("avl_stk["+f+"]").value = parseFloat(stock[g]).toFixed(2);
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