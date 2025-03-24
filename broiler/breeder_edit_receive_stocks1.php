<?php
//breeder_edit_receive_stocks1.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['receive_stocks1'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
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
    }
    if($user_type == "S"){ $acount = 1; }
    else{
        foreach($alink as $edit_access_flag){
            if($edit_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){
        $sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Stock Transfer' AND `field_function` = 'Feed Stock in Bags' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $bfstk_bags = mysqli_num_rows($query);
        $sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Stock Transfer' AND `field_function` = 'Check Item Stock' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $bstk_cflag = mysqli_num_rows($query);
        $sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Office' AND `field_function` = 'Create Breeder Sectors/Offices' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $bsec_sflag = mysqli_num_rows($query);
        //Breeder
        $sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Module' AND `field_function` = 'Maintain Feed Stock in FARM/UNIT/SHED/BATCH/FLOCK' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $bfeed_scnt = mysqli_num_rows($query); $sector_code = $sector_name = array();
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
        if((int)$bsec_sflag > 0){
            $bsql = "SELECT * FROM `inv_sectors` WHERE `brd_sflag` = '1' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
            while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
        }
        //Breeder Feed Details
        $sql = "SELECT * FROM `item_category` WHERE `active` = '1' AND (`bffeed_flag` = '1' OR `bmfeed_flag` = '1' OR `bmv_flag` = '1') AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $icat_alist = array();
        while($row = mysqli_fetch_assoc($query)){ $icat_alist[$row['code']] = $row['code']; }
        $icat_list = implode("','", $icat_alist);
        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $bitem_code = $bitem_name = array();
        while($row = mysqli_fetch_assoc($query)){ $bitem_code[$row['code']] = $row['code']; $bitem_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `item_stocktransfers` WHERE `quantity` = '0' AND `active` = '0' AND `dflag` = '0' GROUP BY `trnum` ORDER BY `trnum` ASC";
        $query = mysqli_query($conn,$sql); $tr_alist = array(); while($row = mysqli_fetch_assoc($query)){ $tr_alist[$row['code']] = $row['trnum']; }

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
        <?php
        $ids = $_GET['trnum'];
        $sql = "SELECT * FROM `item_stocktransfers` WHERE `trnum` = '$ids' AND `dflag` = '0' AND `trlink` = 'breeder_display_receive_stocks1.php'";
        $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $date = date("d.m.Y",strtotime($row['date']));
            $trnm = $row['trnum'];
            $dcno = $row['dcno'];
            $fromwarehouse = $row['fromwarehouse'];
            $code = $row['code'];
            $sent_qty = round($row['sent_qty'],5);
            $rcv_qty = round($row['quantity'],5);
            $short_qty = round($row['short_qty'],5);
            $excess_qty = round($row['excess_qty'],5);
            $towarehouse = $row['towarehouse'];
            $amount = $row['amount'];
            $remarks = $row['remarks'];
        }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Stock Transfer</h3></div>
                        </div>
                       
                        <div class="pl-2 card-body">
                            <form action="breeder_modify_receive_stocks1.php" method="post" role="form" onsubmit="return checkval()">
                                <div class="row">
                                    <div class="form-group" style="width:240px;">
                                        <label>Transactions<b style="color:red;">&nbsp;*</b></label>
                                        <input type="text" name="link_trnum" id="link_trnum" class="form-control datepicker" value="<?php echo $trnm; ?>" style="width:110px;" readonly />
                                    </div>
                                </div>
                                <div class="row row_body2">
                                    <table class="p-1 table1" style="width:auto;">
                                        <thead>
                                            <tr>
                                                <th style="text-align:center;"><label>Date<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label>Dc No.</label></th>
                                                <th style="text-align:center;"><label>From Location<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label>Item<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label>Sent Qty<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label>Receive Qty</label></th>
                                                <th style="text-align:center;"><label>Shortage<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label>Amount<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label>To Location<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label>Remarks</label></th>
                                                <th style="visibility:hidden;"><label>SQ</label></th>
                                                <th style="visibility:hidden;"><label>SP</label></th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody">
                                            <tr>
                                                <td><input type="text" name="date" id="date" class="form-control " value="<?php echo $date; ?>" style="width:110px;" readonly /></td>
                                                <td><input type="text" name="dcno" id="dcno" class="form-control" value="<?php echo $dcno; ?>" style="width:60px;" onkeyup="validatename(this.id);" /></td>
                                                <td><select name="fromwarehouse" id="fromwarehouse" class="form-control select2" style="width:190px;" onchange="fetch_stock_qty();"><option value="select">-select-</option><?php foreach($sector_code as $ucode){ ?><option value="<?php echo $ucode; ?>" <?php if($ucode == $fromwarehouse){ echo "selected"; } ?>><?php echo $sector_name[$ucode]; ?></option><?php } ?></select></td>
                                                <td><select name="code" id="code" class="form-control select2" style="width:190px;" onchange="fetch_stock_qty();"><option value="select">-select-</option><?php foreach($bitem_code as $ucode){ ?><option value="<?php echo $ucode; ?>" <?php if($ucode == $code){ echo "selected"; } ?>><?php echo $bitem_name[$ucode]; ?></option><?php } ?></select></td>
                                                <td><input type="text" name="sent_qty" id="sent_qty" class="form-control text-right" value="<?php echo $sent_qty; ?>" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" readonly/></td>
                                                <td><input type="text" name="rcv_qty" id="rcv_qty" class="form-control text-right" value="<?php echo $rcv_qty; ?>" style="width:90px;" onkeyup="calculate_sort_qty(); validatenum();" /></td>
                                                <td><input type="text" name="srt_qty" id="srt_qty" class="form-control text-right" value="<?php if($rcv_qty < $sent_qty){ echo $short_qty;} else { echo $excess_qty;} ?>" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" readonly/></td>
                                                <td><input type="text" name="amount" id="amount" class="form-control text-right" value="<?php echo $amount; ?>" style="width:90px;" onkeyup="validatenum(); " onchange="validateamount(); " readonly/></td>
                                                <td><select name="towarehouse" id="towarehouse" class="form-control select2" style="width:190px;"><option value="select">-select-</option><?php foreach($sector_code as $ucode){ ?><option value="<?php echo $ucode; ?>" <?php if($ucode == $towarehouse){ echo "selected"; } ?>><?php echo $sector_name[$ucode]; ?></option><?php } ?></select></td>
                                                <td><textarea name="remarks" id="remarks" class="form-control" style="padding:0;width:150px;height:28px;" onkeyup="validatename(this.id);"><?php echo $remarks; ?></textarea></td>
                                                
                                                <td style="visibility:hidden;"><input type="text" name="item_sqty" id="item_sqty" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>
                                                <td style="visibility:hidden;"><input type="text" name="item_sprc" id="item_sprc" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div><br/>
                                <div class="row" style="visibility:hidden;">
                                    <div class="form-group" style="width:30px;">
                                        <label>ID</label>
                                        <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $ids; ?>" style="padding:0;width:20px;" readonly />
                                    </div>
                                    <div class="form-group" style="width:30px;">
                                        <label>EB</label>
                                        <input type="text" name="ebtncount" id="ebtncount" class="form-control" value="0" style="padding:0;width:20px;" readonly />
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
            </section>
        </div>
        <!-- Datepicker -->
        <script src="datepicker/jquery/jquery.js"></script>
        <script src="datepicker/jquery-ui.js"></script>
        <script>
			function checkval(){
                var l = true;
                date = document.getElementById("date").value;
                fromwarehouse = document.getElementById("fromwarehouse").value;
                code = document.getElementById("code").value;
                sent_qty = document.getElementById("sent_qty").value; if(sent_qty == ""){ sent_qty = 0; }
                towarehouse = document.getElementById("towarehouse").value;
                item_sqty = document.getElementById("item_sqty").value; if(item_sqty == ""){ item_sqty = 0; }
                
                if(date == ""){
                    alert("Please select Date : ");
                    document.getElementById("date").focus();
                    l = false;
                }
                else if(fromwarehouse == "" || fromwarehouse == "select"){
                    alert("Please select From Location : ");
                    document.getElementById("fromwarehouse").focus();
                    l = false;
                }
                else if(code == "" || code == "select"){
                    alert("Please select item : ");
                    document.getElementById("code").focus();
                    l = false;
                }
                else if(parseFloat(sent_qty) == 0){
                    alert("Please enter sent_qty : ");
                    document.getElementById("sent_qty").focus();
                    l = false;
                }
                else if(towarehouse == "" || towarehouse == "select"){
                    alert("Please select To Location : ");
                    document.getElementById("towarehouse").focus();
                    l = false;
                }
                else if(parseInt(bstk_cflag) == 1 && parseFloat(sent_qty) > parseFloat(item_sqty)){
                    alert("Stock not available : ");
                    document.getElementById("sent_qty").focus();
                    l = false;
                }
                else if(fromwarehouse == towarehouse){
                    alert("From Location and To Location are same : ");
                    document.getElementById("towarehouse").focus();
                    l = false;
                }
                else{ }
            }
          
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'breeder_display_receive_stocks1.php?ccid='+ccid;
            }
              
            function calculate_sort_qty() {
                let sentQty = parseFloat(document.getElementById('sent_qty').value);  if(sentQty == "") { sentQty = 0; }
                let rcvQty = parseFloat(document.getElementById('rcv_qty').value);  if(rcvQty == "") { rcvQty = 0; }
                let srtQty = sentQty - rcvQty;
                document.getElementById('srt_qty').value = srtQty.toFixed(2);
            }
            function fetch_stock_qty(){
                update_ebtn_status(1);
                var date = document.getElementById("date").value;
                var fsector = document.getElementById("fromwarehouse").value;
                var code = document.getElementById("code").value;
                document.getElementById("item_sqty").value = 0;
                document.getElementById("item_sprc").value = 0;
                var trnum = '<?php echo $ids; ?>';
                if(date == "" || fsector == "" || fsector == "select" || code == "" || code == "select"){ update_ebtn_status(0); }
                else{
                    var oldqty = new XMLHttpRequest();
                    var method = "GET";
                    var url = "breeder_fetch_avlstock_quantity.php?date="+date+"&fsector="+fsector+"&item_code="+code+"&rows=0&ftype=stk_transfer&ttype=edit&trnum="+trnum;
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
                                document.getElementById("item_sqty").value = parseFloat(item_qty).toFixed(2);
                                document.getElementById("item_sprc").value = parseFloat(item_prc).toFixed(5);
                            }
                            update_ebtn_status(0);
                        }
                    }
                }
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
            fetch_stock_qty();
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