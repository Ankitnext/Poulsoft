<?php
//broiler_edit_inventoryreceived.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['inventoryreceived'];
date_default_timezone_set("Asia/Kolkata");
$uri = explode("/",$_SERVER['REQUEST_URI']); $url2 = explode("?",$uri[1]); $href = $url2[0];
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
        foreach($elink as $edit_access_flag){
            if($edit_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){
        $today = date("Y-m-d"); $fdate = date("d.m.Y",strtotime($today)); $y = date("y"); $y2 = $y + 1; $m = date("m"); $d = date("d");
		
		$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'  ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
		
		$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
		
		$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_unit[$row['code']] = $row['cunits']; }
				
		$sql = "SELECT * FROM `acc_coa` WHERE `visible_flag` = '1' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $coa_code[$row['code']] = $row['code']; $coa_name[$row['code']] = $row['description']; }

        //Fetch Feed Details and Feed in Bags Flag
        $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Feed%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $item_cat = "";
        while($row = mysqli_fetch_assoc($query)){ if($item_cat == ""){ $item_cat = $row['code']; } else{ $item_cat = $item_cat."','".$row['code']; } }
        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $item_feed_code[$row['code']] = $row['code']; $item_feed_name[$row['code']] = $row['description']; }
        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Stock Received' AND `field_function` LIKE 'Bags' AND `flag` = '1'"; $query = mysqli_query($conn,$sql); $bag_access_flag = mysqli_num_rows($query);

        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Stock Received' AND `field_function` LIKE 'Stock Check'"; $query = mysqli_query($conn,$sql); $stockcheck_flag = 0; $sccount = mysqli_num_rows($query);
        if($sccount > 0){ while($row = mysqli_fetch_assoc($query)){ $stockcheck_flag = $row['flag']; } } else{ $stockcheck_flag = 0; } if($stockcheck_flag == "" || $stockcheck_flag == 0){ $stockcheck_flag = 0; }

        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Stock Received' AND `field_function` LIKE 'Auto Avg Price'"; $query = mysqli_query($conn,$sql); $autoavgprice_flag = 0; $aapcount = mysqli_num_rows($query);
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
        <?php
        $trnum = $_GET['trnum'];
        $sql = "SELECT * FROM `broiler_inv_intermediate_received` WHERE `trnum` = '$trnum'"; $query = mysqli_query($conn,$sql); $i = 0;
        while($row = mysqli_fetch_assoc($query)){
            $date = $row['date'];
            $doc_no = $row['doc_no'];
            $coacodes = $row['coa_code'];
            $icode = $row['item_code'];
            $quantity = round($row['quantity'],3);
            $rate = round($row['rate'],3);
            $amount = round($row['amount'],3);
            $avg_price = round($row['avg_price'],3);
            $avg_amount = round($row['avg_amount'],3);

            //Check and calculate Feed Bags
            $feed_item =  $row['item_code'];
            if(!empty($item_feed_name[$feed_item]) && !empty($row['quantity']) && $bag_access_flag > 0){
                $bsql = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$icode' AND `active` = '1' AND `dflag` = '0'";
                $bquery = mysqli_query($conn,$bsql); $bcount = $ibag_flag1 = mysqli_num_rows($bquery);
              
                if($bcount > 0){
                    if($ibag_flag1 > 0 && $bag_access_flag > 0){
                        while($brow = mysqli_fetch_assoc($bquery)){
                            if($brow['code'] != "all"){
                                $quantity = $row['quantity'] / $brow['bag_size'];
                                $rate = $rate * $brow['bag_size'];
                                $avg_price = $avg_price * $brow['bag_size'];
                            }
                            else{
                                $quantity = $row['quantity'] / $brow['bag_size'];
                                $rate = $rate * $brow['bag_size'];
                                $avg_price = $avg_price * $brow['bag_size'];
                            }
                        }
                    }
                    else{
                        $quantity = $row['quantity'];
                        $rate = $row['amount'] / $row['quantity'];
                        $avg_price = $row['avg_amount'] / $row['quantity'];
                    }
                }
                else{
                    $bsql = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE 'all' AND `active` = '1' AND `dflag` = '0'";
                    $bquery = mysqli_query($conn,$bsql); $ibag_flag1 = mysqli_num_rows($bquery);

                    if($ibag_flag1 > 0 && $bag_access_flag > 0){
                        while($brow = mysqli_fetch_assoc($bquery)){
                            if($brow['code'] != "all"){
                                $quantity = $row['quantity'] / $brow['bag_size'];
                                $rate = $rate * $brow['bag_size'];
                                $avg_price = $avg_price * $brow['bag_size'];
                            }
                            else{
                                $quantity = $row['quantity'] / $brow['bag_size'];
                                $rate = $rate * $brow['bag_size'];
                                $avg_price = $avg_price * $brow['bag_size'];
                            }
                        }
                    }
                    else{
                        $quantity = $row['quantity'];
                        $rate = $row['amount'] / $row['quantity'];
                        $avg_price = $row['avg_amount'] / $row['quantity'];
                    }
                }
            }
            else{
                $quantity = $row['quantity'];
                $rate = $row['amount'] / $row['quantity'];
                $avg_price = $row['avg_amount'] / $row['quantity'];
            }

            $remarks = $row['remarks'];
            $sectors = $row['sector'];
            $i++;
        }
        $i = $i - 1;
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Stock Received</h3></div>
                        </div>
                        <div class="m-0 p-2 card-body">
                            <div class="col-md-18">
                                <form action="broiler_modify_inventoryreceived.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="form-group">
                                            <label>Date<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="date" id="date" class="form-control datepicker" value="<?php echo date('d.m.Y',strtotime($date)); ?>" style="width:80px;" readonly >
                                        </div>
                                        <div class="form-group">
                                            <label>Location<b style="color:red;">&nbsp;*</b></label>
                                            <select name="sector" id="sector" class="form-control select2" style="width:160px;" onchange="fetch_stock_master();">
                                                <option value="select">select</option>
                                                <?php foreach($sector_code as $whouse_code){ ?><option value="<?php echo $whouse_code; ?>" <?php if($sectors == $whouse_code){ echo "selected"; } ?>><?php echo $sector_name[$whouse_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Chart of Account<b style="color:red;">&nbsp;*</b></label>
                                            <select name="coa_code" id="coa_code" class="form-control select2" style="width:160px;">
                                                <option value="select">select</option>
                                                <?php foreach($coa_code as $ccode){ ?><option value="<?php echo $ccode; ?>" <?php if($coacodes == $ccode){ echo "selected"; } ?>><?php echo $coa_name[$ccode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                    </div><br/>
                                    <table>
                                        <thead>
                                            <tr style="text-align:center;">
                                                <th>Item<b style="color:red;">&nbsp;*</b></th>
                                                <th>Unit</th>
                                                <th>Quantity<b style="color:red;">&nbsp;*</b></th>
                                                <th>Rate<b style="color:red;">&nbsp;*</b></th>
                                                <th>Amount</th>
                                                <th>Remarks</th>
                                                <th style="visibility:hidden;"></th>
                                                <th style="visibility:hidden;"></th>
                                                <th style="visibility:hidden;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="row_body">
                                            <tr>
                                                <td><select name="icode" id="icode" class="form-control select2" style="width:180px;" onchange="fetch_stock_master();fetch_item_unit();"><option value="select">select</option><?php foreach($item_code as $prod_code){ ?><option value="<?php echo $prod_code; ?>" <?php if($icode == $prod_code){ echo "selected"; } ?>><?php echo $item_name[$prod_code]; ?></option><?php } ?></select></td>
                                                <td><input type="text" name="iunit" id="iunit" class="form-control" value="<?php echo $item_unit[$icode]; ?>" style="width:90px;" readonly ></td>
                                                <td><input type="text" name="quantity" id="quantity" class="form-control" value="<?php echo $quantity; ?>" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt();" onchange="validateamount(this.id);" ></td>
                                                <td><input type="text" name="rate" id="rate" class="form-control" value="<?php echo $rate; ?>" placeholder="0.00" style="width:90px;" onkeyup="calculate_total_amt();" onchange="validateamount(this.id);" ></td>
                                                <td><input type="text" name="amount" id="amount" class="form-control" value="<?php echo $amount; ?>" placeholder="0.00" style="width:90px;" readonly ></td>
                                                <td><textarea name="remarks" id="remarks" class="form-control" style="padding:0;width:120px;height:23px;"><?php echo $remarks; ?></textarea></td>
                                                <td style="visibility:hidden;"><input type="text" name="available_stock" id="available_stock" class="form-control" placeholder="0.00" style="width:90px;" readonly ></td>
                                                <td style="visibility:hidden;"><input type="text" name="avg_price" id="avg_price" class="form-control" placeholder="0.00" style="width:50px;" readonly ></td>
                                                <td style="visibility:hidden;"><input type="text" name="avg_amount" id="avg_amount" class="form-control" placeholder="0.00" style="width:50px;" readonly ></td>
                                            </tr>
                                        </tbody>
                                    </table><br/>
                                    <div class="row">
                                        <div class="form-group col-md-1" style="visibility:hidden;">
                                            <label>id<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $trnum; ?>">
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
                window.location.href = 'broiler_display_inventoryreceived.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var qty = price = total_amt = c = d = stock = 0; var icode = "";
                var l = true;
                //Re-calculate Item Amount
                qty = document.getElementById("quantity").value;
                price = document.getElementById("rate").value;
                if(qty == "" || qty.length == 0 || qty == "0.00" || qty == "0"){ qty = 0; }
                if(price == "" || price.length == 0 || price == "0.00" || price == "0"){ price = 0; }
                total_amt = parseFloat(qty) * parseFloat(price);
                document.getElementById("amount").value = total_amt.toFixed(2);

                var date = document.getElementById("date").value;
                var coa_code = document.getElementById("coa_code").value;
                var sector = document.getElementById("sector").value;
                
                icode = document.getElementById("icode").value;

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
                else if(icode.match("select")){
                    alert("Kindly select appropriate Item");
                    document.getElementById("icode").focus();
                    l = false;
                }
                else if(qty == "" || qty == "0.00" || qty == 0){
                    alert("Kindly enter Quantity");
                    document.getElementById("quantity").focus();
                    l = false;
                } 
                else if(price == "" || price == "0.00" || price == 0){
                    alert("Kindly enter Rate ");
                    document.getElementById("rate").focus();
                    l = false;
                }
                else{ }
                
                if(l == true){
                    return true;
                }
                else{
                    document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
                    return false;
                }
            }
            function fetch_item_unit(){
                var icode = document.getElementById("icode").value;
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
                    document.getElementById("iunit").value = iunit;
                }
                else{

                }
            }
            function calculate_total_amt(){
                var qty = document.getElementById("quantity").value;
                var price = document.getElementById("rate").value;
                var avg_price = document.getElementById("avg_price").value;
                if(qty == "" || qty.length == 0 || qty == "0.00" || qty == "0"){ qty = 0; }
                if(price == "" || price.length == 0 || price == "0.00" || price == "0"){ price = 0; }
                if(avg_price == "" || avg_price.length == 0 || avg_price == "0.00" || avg_price == "0"){ avg_price = 0; }
                var total_amt = parseFloat(qty) * parseFloat(price);
                var avg_amount = parseFloat(qty) * parseFloat(avg_price);
                document.getElementById("amount").value = total_amt.toFixed(2);
                document.getElementById("avg_amount").value = avg_amount.toFixed(2);
            }
            function fetch_stock_master(){
                var date = document.getElementById("date").value;
                var sector = document.getElementById("sector").value;
                var item_code = document.getElementById("icode").value;
                var idvalue = document.getElementById("idvalue").value;

                if(sector != "select" && item_code != "select"){
                    var fetch_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_fetch_itemstockmaster_lsfi.php?sector="+sector+"&item_code="+item_code+"&date="+date+"&id="+idvalue+"&trtype=stkreceived&etype=stkreceived";
                    //window.open(url);
                    var asynchronous = true;
                    fetch_items.open(method, url, asynchronous);
                    fetch_items.send();
                    fetch_items.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var item_price = this.responseText;
                            if(item_price.length > 0){
                                var item_details = item_price.split("@");
                                document.getElementById("available_stock").value = item_details[0];
                                var autoavgprice_flag = '<?php echo $autoavgprice_flag; ?>';
                                if(autoavgprice_flag == 1){ document.getElementById("rate").value = item_details[1]; }
                                document.getElementById("avg_price").value = item_details[1];
                                calculate_total_amt();
                            }
                            else{
                                document.getElementById("available_stock").value = 0;
                                document.getElementById("rate").value = 0;
                                document.getElementById("avg_price").value = 0;
                                calculate_total_amt();
                            }
                        }
                    }
                }
                else{
                    document.getElementById("available_stock").value = 0;
                    document.getElementById("rate").value = 0;
                    document.getElementById("avg_price").value = 0;
                    calculate_total_amt();
                }
            }
            function fetch_stock_master2(){
                var date = document.getElementById("date").value;
                var sector = document.getElementById("sector").value;
                var item_code = document.getElementById("icode").value;
                var idvalue = document.getElementById("idvalue").value;

                if(sector != "select" && item_code != "select"){
                    var fetch_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_fetch_itemstockmaster_lsfi.php?sector="+sector+"&item_code="+item_code+"&date="+date+"&id="+idvalue;
                    //window.open(url);
                    var asynchronous = true;
                    fetch_items.open(method, url, asynchronous);
                    fetch_items.send();
                    fetch_items.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var item_price = this.responseText;
                            if(item_price.length > 0){
                                var item_details = item_price.split("@");
                                document.getElementById("available_stock").value = item_details[0];
                                document.getElementById("avg_price").value = item_details[1];
                                calculate_total_amt();
                            }
                            else{
                                document.getElementById("available_stock").value = 0;
                                document.getElementById("avg_price").value = 0;
                                calculate_total_amt();
                            }
                        }
                    }
                }
                else{
                    document.getElementById("available_stock").value = 0;
                    document.getElementById("avg_price").value = 0;
                    calculate_total_amt();
                }
            }
            fetch_stock_master2(); fetch_item_unit();
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