<?php
//broiler_add_inventoryadjustment.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['inventoryadjustment'];
date_default_timezone_set("Asia/Kolkata");
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
        $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code'];
        $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; 
    }
    if($branch_access_code == "all"){ $branch_access_filter1 = ""; }
    else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
    if($line_access_code == "all"){ $line_access_filter1 = ""; }
    else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
    if($farm_access_code == "all"){ $farm_access_filter1 = ""; }
    else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }
    if($sector_access_code == "all"){ $sector_access_filter1 = ""; }
    else{ $sector_access_list = implode("','", explode(",",$sector_access_code)); $sector_access_filter1 = " AND `code` IN ('$sector_access_list')"; }

    if($user_type == "S"){ $acount = 1; }
    else{
        foreach($alink as $add_access_flag){
            if($add_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){
        $today = date("Y-m-d"); $fdate = date("d.m.Y",strtotime($today)); $y = date("y"); $y2 = $y + 1; $m = date("m"); $d = date("d");
		
		$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
		
		$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
		
		$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_unit[$row['code']] = $row['cunits']; }
				
		$sql = "SELECT * FROM `acc_coa` WHERE `visible_flag` = '1' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $coa_code[$row['code']] = $row['code']; $coa_name[$row['code']] = $row['description']; }

        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Stock Adjustment' AND `field_function` LIKE 'Stock Check'"; $query = mysqli_query($conn,$sql); $stockcheck_flag = 0; $sccount = mysqli_num_rows($query);
        if($sccount > 0){ while($row = mysqli_fetch_assoc($query)){ $stockcheck_flag = $row['flag']; } } else{ $stockcheck_flag = 0; } if($stockcheck_flag == "" || $stockcheck_flag == 0){ $stockcheck_flag = 0; }

        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Stock Adjustment' AND `field_function` LIKE 'Auto Avg Price'"; $query = mysqli_query($conn,$sql); $autoavgprice_flag = 0; $aapcount = mysqli_num_rows($query);
        if($aapcount > 0){ while($row = mysqli_fetch_assoc($query)){ $autoavgprice_flag = $row['flag']; } } else{ $autoavgprice_flag = 0; } if($autoavgprice_flag == "" || $autoavgprice_flag == 0){ $autoavgprice_flag = 0; }
        //echo $autoavgprice_flag;
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
            padding-left: 2px;
            padding-right: 0px;
        }
        .form-group{
            margin: 0 3px;
        }
    </style>
    </head>
    <body class="m-0 hold-transition">
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Inventory Adjustment</h3></div>
                        </div>
                        <div class="m-0 p-2 card-body">
                            <div class="col-md-18">
                                <form action="broiler_save_inventoryadjustment.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="form-group">
                                            <label>Date<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="date" id="date" class="form-control datepicker" value="<?php echo date('d.m.Y'); ?>" style="width:80px;" readonly >
                                        </div>
                                        <div class="form-group">
                                            <label>Bill No.</label>
                                            <input type="text" name="billno" id="billno" class="form-control" style="width:60px;" >
                                        </div>
                                        <div class="form-group">
                                            <label>Location/Warehouse<b style="color:red;">&nbsp;*</b></label>
                                            <select name="sector" id="sector" class="form-control select2" style="width:160px;">
                                                <option value="select">select</option>
                                                <?php foreach($sector_code as $whouse_code){ ?><option value="<?php echo $whouse_code; ?>"><?php echo $sector_name[$whouse_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Chart of Account<b style="color:red;">&nbsp;*</b></label>
                                            <select name="coa_code" id="coa_code" class="form-control select2" style="width:160px;">
                                                <option value="select">select</option>
                                                <?php foreach($coa_code as $ccode){ ?><option value="<?php echo $ccode; ?>"><?php echo $coa_name[$ccode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                    </div><br/>
                                    <table>
                                        <thead>
                                            <tr style="text-align:center;">
                                                <th>Item<b style="color:red;">&nbsp;*</b></th>
                                                <th>Unit</th>
                                                <th>Add/Deduct<b style="color:red;">&nbsp;*</b></th>
                                                <th>Quantity<b style="color:red;">&nbsp;*</b></th>
                                                <th>Rate<b style="color:red;">&nbsp;*</b></th>
                                                <th>Amount</th>
                                                <th>Remarks</th>
                                                <th></th>
                                                <th style="visibility:visible;">Stock</th>
                                                <th style="visibility:hidden;"></th>
                                                <th style="visibility:hidden;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="row_body">
                                            <tr>
                                                <td><select name="icode[]" id="icode[0]" class="form-control select2" style="width:180px;" onchange="fetch_stock_master(this.id);fetch_item_unit(this.id);"><option value="select">select</option><?php foreach($item_code as $prod_code){ ?><option value="<?php echo $prod_code; ?>"><?php echo $item_name[$prod_code]; ?></option><?php } ?></select></td>
                                                <td><input type="text" name="iunit[]" id="iunit[0]" class="form-control" style="width:90px;" readonly ></td>
                                                <td><select name="a_type[]" id="a_type[0]" class="form-control select2" style="width:180px;"><option value="select">select</option><option value="add">Add</option><option value="deduct">Deduct</option></select></td>
                                                <td><input type="text" name="quantity[]" id="quantity[0]" class="form-control" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></td>
                                                <td><input type="text" name="rate[]" id="rate[0]" class="form-control" placeholder="0.00" style="width:90px;" onkeyup="calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></td>
                                                <td><input type="text" name="amount[]" id="amount[0]" class="form-control" placeholder="0.00" style="width:90px;" readonly ></td>
                                                <td><textarea name="remarks[]" id="remarks[0]" class="form-control" style="padding:0;width:120px;height:23px;"></textarea></td>
                                                <td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                                <td style="visibility:visible;"><input type="text" name="available_stock[]" id="available_stock[0]" class="form-control" placeholder="0.00" style="width:90px;" readonly ></td>
                                                <td style="visibility:hidden;"><input type="text" name="avg_price[]" id="avg_price[0]" class="form-control" placeholder="0.00" style="width:50px;" readonly ></td>
                                                <td style="visibility:hidden;"><input type="text" name="avg_amount[]" id="avg_amount[0]" class="form-control" placeholder="0.00" style="width:50px;" readonly ></td>
                                            </tr>
                                        </tbody>
                                    </table><br/>
                                    <div class="row">
                                        <div class="form-group col-md-1" style="visibility:hidden;">
                                            <label>incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="0">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
                                            <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
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
                window.location.href = 'broiler_display_inventoryadjustment.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var incrs = document.getElementById("incr").value; var qty = price = total_amt = c = d = stock = 0; var icode = ""; var a_type = "";
                var l = true;
                //Re-calculate Item Amount
                for(d = 0;d <= incrs;d++){
                    qty = document.getElementById("quantity["+d+"]").value;
                    price = document.getElementById("rate["+d+"]").value;
                    if(qty == "" || qty.length == 0 || qty == "0.00" || qty == "0"){ qty = 0; }
                    if(price == "" || price.length == 0 || price == "0.00" || price == "0"){ price = 0; }
                    total_amt = parseFloat(qty) * parseFloat(price);
                    document.getElementById("amount["+d+"]").value = total_amt.toFixed(2);
                }

                var date = document.getElementById("date").value;
                var coa_code = document.getElementById("coa_code").value;
                var sector = document.getElementById("sector").value;
                if(date == ""){
                    alert("Kindly enter/select appropriate date");
                    document.getElementById("date").focus();
                    l = false;
                }
                else if(sector.match("select")){
                    alert("Kindly select appropriate Location");
                    document.getElementById("sector").focus();
                    l = false;
                }
                else if(coa_code.match("select")){
                    alert("Kindly select appropriate Chart of Account");
                    document.getElementById("coa_code").focus();
                    l = false;
                }
                else{
                    //Stock Check
                    var stockcheck_flag = '<?php echo $stockcheck_flag; ?>';
                    if(stockcheck_flag == 1){
                        for(d = 0;d <= incrs;d++){
                            a_type = document.getElementById("a_type["+d+"]").value;
                            if(l == true && a_type.match("deduct")){
                                c = d + 1;
                                qty = document.getElementById("quantity["+d+"]").value;
                                stock = document.getElementById("available_stock["+d+"]").value;
                                if(parseFloat(qty) > parseFloat(stock)){
                                    alert("Stock not Available in row: "+c);
                                    document.getElementById("quantity["+d+"]").focus();
                                    l = false;
                                }
                            }
                        }
                    }
                    else{ }

                    //Check Item Details
                    for(d = 0;d <= incrs;d++){
                        if(l == true){
                            c = d + 1;
                            icode = document.getElementById("icode["+d+"]").value;
                            qty = document.getElementById("quantity["+d+"]").value;
                            price = document.getElementById("rate["+d+"]").value;
                            a_type = document.getElementById("a_type["+d+"]").value;
                            if(icode.match("select")){
                                alert("Kindly select appropriate Item in row: "+c);
                                document.getElementById("icode["+d+"]").focus();
                                l = false;
                            }
                            else if(a_type.match("select")){
                                alert("Kindly select Add/Deduct Type in row: "+c);
                                document.getElementById("a_type["+d+"]").focus();
                                l = false;
                            }
                            else if(qty == "" || qty == "0.00" || qty == 0){
                                alert("Kindly enter Quantity in row: "+c);
                                document.getElementById("quantity["+d+"]").focus();
                                l = false;
                            } 
                            else if(price == "" || price == "0.00" || price == 0){
                                alert("Kindly enter Rate in row: "+c);
                                document.getElementById("rate["+d+"]").focus();
                                l = false;
                            }
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
                html += '<tr id="row_no['+d+']">';
                html += '<td><select name="icode[]" id="icode['+d+']" class="form-control select2" style="width:180px;" onchange="fetch_stock_master(this.id);fetch_item_unit(this.id);"><option value="select">select</option><?php foreach($item_code as $prod_code){ ?><option value="<?php echo $prod_code; ?>"><?php echo $item_name[$prod_code]; ?></option><?php } ?></select></td>';
                html += '<td><input type="text" name="iunit[]" id="iunit['+d+']" class="form-control" style="width:90px;" readonly ></td>';
                html += '<td><select name="a_type[]" id="a_type['+d+']" class="form-control select2" style="width:180px;"><option value="select">select</option><option value="add">Add</option><option value="deduct">Deduct</option></select></td>';
                html += '<td><input type="text" name="quantity[]" id="quantity['+d+']" class="form-control" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></td>';
                html += '<td><input type="text" name="rate[]" id="rate['+d+']" class="form-control" placeholder="0.00" style="width:90px;" onkeyup="calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></td>';
                html += '<td><input type="text" name="amount[]" id="amount['+d+']" class="form-control" placeholder="0.00" style="width:90px;" readonly ></td>';
                html += '<td><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="width:120px;height:23px;"></textarea></td>';
                html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                html += '<td style="visibility:visible;"><input type="text" name="available_stock[]" id="available_stock['+d+']" class="form-control" placeholder="0.00" style="width:90px;" readonly ></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="avg_price[]" id="avg_price['+d+']" class="form-control" placeholder="0.00" style="width:50px;" readonly ></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="avg_amount[]" id="avg_amount['+d+']" class="form-control" placeholder="0.00" style="width:50px;" readonly ></td>';
                html += '</tr>';
                $('#row_body').append(html);
                $('.select2').select2();
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
            }
            function calculate_total_amt(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var qty = document.getElementById("quantity["+d+"]").value;
                var price = document.getElementById("rate["+d+"]").value;
                var avg_price = document.getElementById("avg_price["+d+"]").value;
                if(qty == "" || qty.length == 0 || qty == "0.00" || qty == "0"){ qty = 0; }
                if(price == "" || price.length == 0 || price == "0.00" || price == "0"){ price = 0; }
                if(avg_price == "" || avg_price.length == 0 || avg_price == "0.00" || avg_price == "0"){ avg_price = 0; }
                var total_amt = parseFloat(qty) * parseFloat(price);
                var avg_amount = parseFloat(qty) * parseFloat(avg_price);
                document.getElementById("amount["+d+"]").value = total_amt.toFixed(2);
                document.getElementById("avg_amount["+d+"]").value = avg_amount.toFixed(2);
            }
            function fetch_stock_master(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var date = document.getElementById("date").value;
                var sector = document.getElementById("sector").value;
                var item_code = document.getElementById(a).value;
                var fetch_items = new XMLHttpRequest();
				var method = "GET";
				var url = "broiler_fetch_itemstockmaster_lsfi.php?sector="+sector+"&item_code="+item_code+"&date="+date+"&trtype=stkadjustment&etype=stkadjustment";
                //window.open(url);
				var asynchronous = true;
				fetch_items.open(method, url, asynchronous);
				fetch_items.send();
				fetch_items.onreadystatechange = function(){
					if(this.readyState == 4 && this.status == 200){
						var item_price = this.responseText;
                        if(item_price.length > 0){
                            var item_details = item_price.split("@");
                            document.getElementById("available_stock["+d+"]").value = item_details[0];
                            var autoavgprice_flag = '<?php echo $autoavgprice_flag; ?>';
                            if(autoavgprice_flag == 1){
                                document.getElementById("rate["+d+"]").value = item_details[1];
                            }
                            document.getElementById("avg_price["+d+"]").value = item_details[1];
                        }
                        else{
                            alert("Item Stock not available, Kindly check before saving ...!");
                            document.getElementById("available_stock["+d+"]").value = 0;
                            document.getElementById("rate["+d+"]").value = 0;
                            document.getElementById("avg_price["+d+"]").value = 0;
                        }
                    }
                }
            }
            function fetch_item_unit(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var icode = document.getElementById("icode["+d+"]").value;
                var iunit = "";
                if(!icode.match("select")){
                <?php
                foreach($item_code as $ic){
                    $act_iunit = $item_unit[$ic];
                    echo "if(icode == '$ic'){";
                ?>
                    iunit = '<?php echo $act_iunit; ?>';
                <?php
                    echo "}";
                }
                ?>
                    document.getElementById("iunit["+d+"]").value = iunit;
                }
                else{

                }
            }
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
            setInterval(function(){ if(window.screen.availWidth <= 400){ const collection = document.getElementsByClassName("labelrow"); for (let i = 0; i < collection.length; i++) { collection[i].style.display = "inline"; } } else{ const collection = document.getElementsByClassName("labelrow"); for (let i = 0; i < collection.length; i++) { collection[i].style.display = "none"; } } }, 1000);
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