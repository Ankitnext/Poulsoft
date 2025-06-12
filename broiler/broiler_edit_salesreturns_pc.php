<?php
//broiler_edit_salesreturns.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['salesreturns_pc'];
date_default_timezone_set("Asia/Kolkata");
$uri = explode("/",$_SERVER['REQUEST_URI']); $url2 = explode("?",$uri[1]); $href = $url2[0];
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $link_childid = $row['childid']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $alink = array(); $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $alink = explode(",",$row['editaccess']);
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
        foreach($alink as $edit_access_flag){
            if($edit_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){
        $sql = "SELECT * FROM `item_details` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); // `category` IN ('$bcodes') AND 
        while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
		$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; }
        
        $sql = "SELECT * FROM `tax_details` WHERE `active` = '1' ORDER BY `value` ASC"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
        while($row = mysqli_fetch_assoc($query)){ $gst_code[$row['code']] = $row['code']; $gst_name[$row['code']] = $row['gst_type']; $gst_value[$row['code']] = $row['value']; }
        
        $sql = "SELECT * FROM `inv_sectors` WHERE `dflag` = '0' ".$sector_access_filter1.""; $query = mysqli_query($conn,$sql); // `category` IN ('$bcodes') AND 
        while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }


?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
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
            $id = $_GET['trnum'];
            $sql = "SELECt * FROM `broiler_itemreturns` WHERE `trnum` = '$id'"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $key_code = $row['itemcode']."@".$row['warehouse'];
                $link_trnum = $row['link_trnum'];
                $date = $row['date'];
                $inv_date = date('d.m.Y',strtotime($row['inv_date']));
                $vcode = $row['vcode'];
                $quantity[$key_code] = $row['quantity'];
                $price[$key_code] = $row['price'];
                $gstval[$key_code] = $row['gst_per'];
                $amount[$key_code] = $row['amount'];
                $sectors = $row['warehouse'];
                $remarks[$key_code] = $row['remarks'];
                $item_status[$key_code] = $row['stk_status'];
            }
            
            $sql = "SELECT * FROM `broiler_itemreturns` WHERE `link_trnum` IN ('$link_trnum') AND `trnum` NOT IN ('$id') AND `active` = '1' AND `dflag` = '0' ORDER BY `id` ASC";
            $query = mysqli_query($conn,$sql); $rcount = mysqli_num_rows($query);
            if($rcount > 0){
                $prq[$row['itemcode']] = $prq[$row['itemcode']] + $row['quantity'];
            }
            else{ }
            if($inv_date == "" || $inv_date == "01.01.1970"){
                $sql = "SELECT * FROM `broiler_sales` WHERE `trnum` LIKE '$link_trnum' WHERE `dflag` = '0'";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $inv_date = date('d.m.Y',strtotime($row['date']));
                }
            }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Purchase Return</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_modify_salesreturns_pc.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="form-group">
                                            <label>Date<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="date" id="date" class="form-control datepicker" value="<?php echo date('d.m.Y',strtotime($date)); ?>" style="width:100px;">
                                        </div>&ensp;
                                        <div class="form-group">
                                            <label>Location<b style="color:red;">&nbsp;*</b></label>
                                            <select name="warehouse" id="warehouse" class="form-control select2" style="width:180px;">
                                                <option value="select">select</option>
                                                <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($sectors == $scode){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>&ensp;
                                        <div class="form-group">
                                            <label>Customer<b style="color:red;">&nbsp;*</b></label>
                                            <select name="vcode" id="vcode" class="form-control select2" style="width:180px;" >
                                                <option value="select">select</option>
                                                <option value="<?php echo $vcode; ?>" selected><?php echo $ven_name[$vcode]; ?></option>
                                            </select>
                                        </div>&ensp;
                                        <div class="form-group">
                                            <label>Invoice<b style="color:red;">&nbsp;*</b></label>
                                            <select name="link_trnum" id="link_trnum" class="form-control select2" style="width:180px;" onchange="fetch_invoice_details();">
                                            <option value="<?php echo $link_trnum; ?>" selected><?php echo $link_trnum; ?></option>
                                            </select>
                                        </div>
                                    </div><br/><br/>
                                    <div id="row_body">
                                    <?php
                                        $sql = "SELECT * FROM `broiler_sales` WHERE `trnum` IN ('$link_trnum') AND `active` = '1' AND `dflag` = '0' ORDER BY `id` ASC";
                                        $query = mysqli_query($conn,$sql); $lqt_count = mysqli_num_rows($query); $c = 0;
                                        if($lqt_count > 0){
                                            $inv_list .= '
                                            <div class="col-md-2"></div>
                                            <div class="col-md-8">
                                            <table>
                                            <tr class="bg-primary" style="text-align:center;">
                                                <th>Select<br/><input type="checkbox" name="check_all" id="check_all" class="form-control1" onclick="select_all_checkboxes();" ></th>
                                                <th>Inv Date</th>
                                                <th>Item</th>
                                                <th>Sold Qty</th>
                                                <th>Sold Price</th>
                                                <th>Returned Qty</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Remarks</th>
                                            </tr>
                                            ';
                                            while($row = mysqli_fetch_assoc($query)){
                                                $key_code = $row['icode']."@".$row['warehouse'];
                                                $icode = $row['icode'];
                                                $rcd_qty = $row['rcd_qty'];
                                                $crtn_qty = $quantity[$key_code];
                                                $rate = $price[$key_code];
                                                $crtn_amt = round(($crtn_qty * $rate),2);
                                                $eremarks = $remarks[$key_code];
                                                $istatus = $item_status[$key_code];
                                                $inv_list .= '
                                                    <tr>
                                                        <td style="text-align:center;">
                                                            <input type="checkbox" name="check_value[]" id="check_value['.$c.']" class="form-control1" value="'.$c.'" onchange="change_row_modify(this.id);">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="inv_date[]" id="inv_date['.$c.']" class="form-control" value="'.$inv_date.'" style="width:90px;text-align:left;" readonly >
                                                        </td>
                                                        <td>
                                                            <select name="item_code[]" id="item_code['.$c.']" class="form-control1" style="width:150px;border:1px solid #ccc;">
                                                                <option value="'.$icode.'">'.$item_name[$icode].'</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="rcd_qty[]" id="rcd_qty['.$c.']" class="form-control" value="'.$rcd_qty.'" style="width:90px;text-align:right;" readonly >
                                                        </td>
                                                        <td>
                                                            <input type="text" name="rate[]" id="rate['.$c.']" class="form-control" value="'.$rate.'" style="width:90px;text-align:right;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" readonly >
                                                        </td>
                                                        <td>
                                                            <input type="text" name="crtn_qty[]" id="crtn_qty['.$c.']" class="form-control" value="'.$crtn_qty.'" style="width:90px;text-align:right;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" readonly >
                                                        </td>
                                                        <td>
                                                            <input type="text" name="crtn_amt[]" id="crtn_amt['.$c.']" class="form-control" value="'.$crtn_amt.'" style="width:90px;text-align:right;" readonly >
                                                        </td>
                                                        <td>
                                                            <select name="stk_status[]" id="stk_status['.$c.']" class="form-control1" style="width:150px;border:1px solid #ccc;">
                                                            ';
                                                            if($istatus == "add"){
                                                                $inv_list .= '<option value="add" selected >Add To Stock</option>';
                                                                $inv_list .= '<option value="waste">Wastage</option>';
                                                            }
                                                            else if($istatus == "waste"){
                                                                $inv_list .= '<option value="add">Add To Stock</option>';
                                                                $inv_list .= '<option value="waste" selected >Wastage</option>';
                                                            }
                                                            else{
                                                                $inv_list .= '<option value="add" selected >Add To Stock</option>';
                                                                $inv_list .= '<option value="waste">Wastage</option>';
                                                            }
                                                $inv_list .= '
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <textarea name="remarks[]" id="remarks['.$c.']" class="form-control" style="width:120px;height:23px;">'.$eremarks.'</textarea>
                                                        </td>
                                                    </tr>
                                                ';
                                                $c++;
                                            }
                                            $inv_list .= '</div><div class="col-md-2"></div></table>';
                                        }
                                        echo $inv_list;
                                        $c = $c - 1;
                                    ?>
                                    </div><br/><br/>
                                    <div class="row">
                                        <div class="form-group col-md-12" style="visibility:hidden;">
                                            <label>Incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="<?php echo $c; ?>" >
                                        </div>
                                        <div class="form-group col-md-12" style="visibility:hidden;">
                                            <label>Id<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $id; ?>" >
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
        <script>
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_salesreturns_pc.php?ccid'+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden"; 
                var $row1 = chk_size = rate = crtn_qty = i = 0; var l = true;
                var warehouse = document.getElementById("warehouse").value;
                var vcode = document.getElementById("vcode").value;
                var link_trnum = document.getElementById("link_trnum").value;
                var checkboxes = document.querySelectorAll('input[name="check_value[]"]');

                for (var d = 0; d < checkboxes.length; d++){
                    $row1 = $row1 + 1;
                    if(l == true){
                        if(checkboxes[d].checked == true){
                            rate = document.getElementById("rate["+d+"]").value;
                            crtn_qty = document.getElementById("crtn_qty["+d+"]").value;
                            if(rate == "" || rate == 0){
                                alert("Enter Sales Return Rate ...!");
                                document.getElementById("rate["+d+"]").focus();
                                l = false;
                            }
                            else if(crtn_qty == "" || crtn_qty == 0){
                                alert("Enter Sales Return Quantity ...!Row"+$row1);
                                document.getElementById("crtn_qty["+d+"]").focus();
                                l = false;
                            }
                            chk_size = chk_size + 1;
                        }
                    }
                }
                if(l == true){
                    if(warehouse.match("select")){
                        alert("Select Location ...!");
                        document.getElementById("warehouse").focus();
                        l = false;
                    }
                    else if(vcode.match("select")){
                        alert("Select Customer ...!");
                        document.getElementById("vcode").focus();
                        l = false;
                    }
                    else if(link_trnum.match("select")){
                        alert("Select Transaction No ...!");
                        document.getElementById("link_trnum").focus();
                        l = false;
                    }
                    else if(chk_size == 0){
                        alert("Select Atleast one checkbox to proceed ...!");
                        l = false;
                    }
                    else{ }
                }
                if(l == true){
                    var answer = window.confirm("Are You Sure! You want to Update The Transaction.");
                    if (answer) {
                        //some code
                        return true;
                    }
                    else {
                        //some code
                        document.getElementById("submit").style.visibility = "visible";
					    document.getElementById("ebtncount").value = "0";
                        return false;
                    }
                }
                else{
                    document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
                    return false;
                }
            }
            function select_all_checkboxes(){
                var selectallbox = document.getElementById("check_all");
                var checkboxes = document.querySelectorAll('input[type="checkbox"]');
                if(selectallbox.checked == true){
                    for (var i = 0; i < checkboxes.length; i++){
                        checkboxes[i].checked = true;
                        document.getElementById("rate["+i+"]").readOnly = false;
                        document.getElementById("crtn_qty["+i+"]").readOnly = false;
                    }
                }
                else{
                    for (var i = 0; i < checkboxes.length; i++){
                        checkboxes[i].checked = false;
                        document.getElementById("rate["+i+"]").readOnly = true;
                        document.getElementById("crtn_qty["+i+"]").readOnly = true;
                    }
                }
            }
            function calculate_total_amt(a){
                var b = a.split("["); var c = b[1].split("]"); var i = c[0];
                var rate = document.getElementById("rate["+i+"]").value;
                var crtn_qty = document.getElementById("crtn_qty["+i+"]").value;
                var crtn_amt =parseFloat(rate) * parseFloat(crtn_qty);
                document.getElementById("crtn_amt["+i+"]").value = parseFloat(crtn_amt).toFixed(2);
            }
            function change_row_modify(a){
                var cbox =document.getElementById(a);
                var b = a.split("["); var c = b[1].split("]"); var i = c[0];
                if(cbox.checked == true){
                    document.getElementById("rate["+i+"]").readOnly = false;
                    document.getElementById("crtn_qty["+i+"]").readOnly = false;
                }
                else{
                    document.getElementById("rate["+i+"]").readOnly = true;
                    document.getElementById("crtn_qty["+i+"]").readOnly = true;
                }
            }
            function fetch_supplier_invoices(){
                var vcode = document.getElementById("vcode").value;
                removeAllOptions(document.getElementById("link_trnum"));
                document.getElementById("row_body").innerHTML = "";
                document.getElementById("incr").value = 0;
                var inv_items = new XMLHttpRequest();
				var method = "GET";
				var url = "broiler_fetch_saleinvoices_preturn.php?vcode="+vcode;
                //window.open(url);
				var asynchronous = true;
				inv_items.open(method, url, asynchronous);
				inv_items.send();
				inv_items.onreadystatechange = function(){
					if(this.readyState == 4 && this.status == 200){
						var item_list = this.responseText;
                        if(item_list.length > 0){
                            $('#link_trnum').append(item_list);
                        }
                        else{
                            alert("Active Sales Invoices are not available \n Kindly check and try again ...!");
                        }
                    }
                }
            }
            function fetch_invoice_details(){
                var link_trnum = document.getElementById("link_trnum").value;
                document.getElementById("row_body").innerHTML = "";
                var inv_items = new XMLHttpRequest();
				var method = "GET";
				var url = "broiler_fetch_salesinvoicedetails.php?link_trnum="+link_trnum;
                //window.open(url);
				var asynchronous = true;
				inv_items.open(method, url, asynchronous);
				inv_items.send();
				inv_items.onreadystatechange = function(){
					if(this.readyState == 4 && this.status == 200){
						var item_list = this.responseText;
                        if(item_list.length > 0){
                            var elist = item_list.split("@");
                            $('#row_body').append(elist[0]);
                            document.getElementById("incr").value = elist[1];
                        }
                        else{
                            alert("There are Items available \n Kindly check and try again ...!");
                        }
                    }
                }
            }
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
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