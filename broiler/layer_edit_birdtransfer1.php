<?php
//layer_edit_birdtransfer1.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['birdtransfer1'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
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
    }
    if($user_type == "S"){ $acount = 1; }
    else{
        foreach($elink as $edit_access_flag){
            if($edit_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){
        $sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'layer Bird Transfer' AND `field_function` = 'Check Bird Stock' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $bstk_cflag = mysqli_num_rows($query);

        $bsql = "SELECT * FROM `layer_shed_allocation` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $bquery = mysqli_query($conn,$bsql); $bflk_code = $bflk_name = array();
        while($brow = mysqli_fetch_assoc($bquery)){ $bflk_code[$brow['code']] = $brow['code']; $bflk_name[$brow['code']] = $brow['description']; }
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
        $sql = "SELECT * FROM `layer_bird_transfer` WHERE `trnum` = '$ids' AND `dflag` = '0' AND `trlink` = 'layer_display_birdtransfer1.php'";
        $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $date = date("d.m.Y",strtotime($row['date']));
            $from_flock = $row['from_flock'];
            $to_flock = $row['to_flock'];
            $lyr_bqty = round($row['lyr_bqty'],5);
            $remarks = $row['remarks'];
        }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Bird Transfer</h3></div>
                        </div>
                        <div class="pl-2 card-body">
                            <form action="layer_modify_birdtransfer1.php" method="post" role="form" onsubmit="return checkval()">
                                <div class="row row_body2">
                                    <table class="p-1 table1" style="width:auto;" align="center">
                                        <thead>
                                            <tr>
                                                <th colspan="4" style="text-align:center;">
                                                    <div class="row justify-content-center align-items-center">
                                                        <div class="form-group" style="width:120px;">
                                                            <label for="date">Date</label>
                                                            <input type="text" name="date" id="date" class="form-control datepicker" value="<?php echo $date; ?>" style="width:110px;" onchange="fetch_bird_details();" readonly />
                                                        </div>
                                                        <div class="form-group" style="width:200px;">
                                                            <label for="from_flock">From Flock</label>
                                                            <select name="from_flock" id="from_flock" class="form-control select2" style="width:190px;" onchange="fetch_bird_details();">
                                                                <option value="select">-select-</option>
                                                                <?php foreach($bflk_code as $ucode){ ?><option value="<?php echo $ucode; ?>" <?php if($ucode == $from_flock){ echo "selected"; } ?>><?php echo $bflk_name[$ucode]; ?></option><?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="4" style="text-align:center;background:#00fabe;">Available Bird Details</th>
                                            </tr>
                                            <tr>
                                                <th colspan="4" style="text-align:center;">
                                                    <div class="row justify-content-center align-items-center">
                                                        <div class="form-group" style="width:20px;visibility:hidden;">
                                                            <label for="bird_age">A</label>
                                                            <input type="text" name="bird_age" id="bird_age" class="form-control text-right" style="width:20px;" readonly />
                                                        </div>
                                                        <div class="form-group" style="width:90px;">
                                                            <label for="bird_wage">Age</label>
                                                            <input type="text" name="bird_wage" id="bird_wage" class="form-control text-right" style="width:80px;" readonly />
                                                        </div>
                                                        <div class="form-group" style="width:90px;">
                                                            <label for="lyr_qty">Quantity</label>
                                                            <input type="text" name="lyr_qty" id="lyr_qty" class="form-control text-right" style="width:80px;" readonly />
                                                        </div>
                                                        <div class="form-group" style="width:20px;visibility:hidden;">
                                                            <label for="lyr_bprc">Rate</label>
                                                            <input type="text" name="lyr_bprc" id="lyr_bprc" class="form-control text-right" style="width:20px;" readonly />
                                                        </div>
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <thead>
                                            <tr>
                                                <th style="text-align:center;"><label>To Flock<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label>Quantity</label></th>
                                                <th style="text-align:center;"><label>Remarks</label></th>
                                                <th style="visibility:hidden;"><label>Action</label></th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody">
                                            <tr>
                                                <td><select name="to_flock" id="to_flock" class="form-control select2" style="width:190px;"><option value="select">-select-</option><?php foreach($bflk_code as $ucode){ ?><option value="<?php echo $ucode; ?>" <?php if($ucode == $to_flock){ echo "selected"; } ?>><?php echo $bflk_name[$ucode]; ?></option><?php } ?></select></td>
                                                <td><input type="text" name="lyr_bqty" id="lyr_bqty" class="form-control text-right" value="<?php echo $lyr_bqty; ?>" style="width:90px;" onkeyup="validate_count(this.id);" /></td>
                                                <td><textarea name="remarks" id="remarks" class="form-control" style="padding:0;width:150px;height:28px;" onkeyup="validatename(this.id);"><?php echo $remarks; ?></textarea></td>
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
				update_ebtn_status(1);
                var l = true;
                var bstk_cflag = '<?php echo $bstk_cflag; ?>'; if(bstk_cflag == ""){ bstk_cflag = 0; }
                var date = document.getElementById("date").value;
                var from_flock = document.getElementById("from_flock").value;
                var lyr_qty = document.getElementById("lyr_qty").value; if(lyr_qty == ""){ lyr_qty = 0; }
                var tot_qty = document.getElementById("tot_qty").value; if(tot_qty == ""){ tot_qty = 0; }
                var to_flock = document.getElementById("to_flock").value;
                var lyr_bqty = document.getElementById("lyr_bqty").value; if(lyr_bqty == ""){ lyr_bqty = 0; }
                
                if(date == ""){
                    alert("Please select Date");
                    document.getElementById("date").focus();
                    l = false;
                }
                else if(from_flock == "" || from_flock == "select"){
                    alert("Please select From Flock");
                    document.getElementById("from_flock").focus();
                    l = false;
                }
                else if(parseInt(bstk_cflag) == 1 && parseFloat(tot_qty) > 0 && parseFloat(lyr_qty) <= 0){
                    alert("Bird stock not available for the selected Flock");
                    document.getElementById("lyr_qty").focus();
                    l = false;
                }
                else if(parseInt(bstk_cflag) == 1 && parseFloat(tot_qty) > parseFloat(lyr_qty)){
                    alert("Bird transfer entry must be less than equal to available bird stock. Please check and try again.");
                    document.getElementById("tot_fqty").focus();
                    l = false;
                }
                else if(parseFloat(tot_qty) == 0 ){
                    alert("Please enter bird to transfer");
                    document.getElementById("tot_qty").focus();
                    l = false;
                }
                else if(to_flock == "" || to_flock == "select"){
                    alert("Please select To Flock");
                    document.getElementById("to_flock").focus();
                    l = false;
                }
                else if(from_flock == to_flock){
                    alert("From and To Flocks are same");
                    document.getElementById("to_flock").focus();
                    l = false;
                }
                else if(parseInt(lyr_bqty) == 0 ) == 0){
                    alert("Please enter Bird quantity");
                    document.getElementById("lyr_bqty").focus();
                    l = false;
                }
                else{ }
                
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
                window.location.href = 'layer_display_birdtransfer1.php?ccid='+ccid;
            }
            function fetch_bird_details(){
                update_ebtn_status(1);
                var date = document.getElementById("date").value;
                var from_flock = document.getElementById("from_flock").value;
                document.getElementById("bird_age").value = 0;
                document.getElementById("bird_wage").value = 0;
                document.getElementById("lyr_qty").value = 0;
                document.getElementById("lyr_bprc").value = 0;
                var trnum = '<?php echo $ids; ?>';
                if(date == "" || from_flock == "" || from_flock == "select"){ update_ebtn_status(0); }
                else{
                    var oldqty = new XMLHttpRequest();
                    var method = "GET";
                    var url = "layer_fetch_avlstock_quantity.php?date="+date+"&fflock="+from_flock+"&trnum="+trnum+"&rows=0&ftype=bird_transfer&ttype=edit";
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
                            var lyr_qty = item_sdt2[3]; if(lyr_qty == ""){ lyr_qty = 0; }
                            var lyr_bprc = item_sdt2[4]; if(lyr_bprc == ""){ lyr_bprc = 0; }
                            var bird_age = item_sdt2[7]; if(bird_age == ""){ bird_age = 0; }

                            if(parseInt(err_flag) == 1){ alert(err_msg); }
                            else{
                                document.getElementById("lyr_qty").value = parseFloat(lyr_qty).toFixed(0);
                                document.getElementById("lyr_bprc").value = parseFloat(lyr_bprc).toFixed(5);
                                document.getElementById("bird_age").value = parseFloat(bird_age).toFixed(0);
                                var bird_wage = calculate_age_weeks(bird_age);
                                document.getElementById("bird_wage").value = parseFloat(bird_wage).toFixed(1);
                            }
                            update_ebtn_status(0);
                        }
                    }
                }
            }
            function calculate_age_weeks(d_age){
                var week_no = Math.floor(parseFloat(d_age) / 7);
                var age_no = parseFloat(d_age) % 7;
                if(parseInt(age_no) == 0){
                    age_no = 7; week_no = parseInt(week_no) - 1;
                }
                var age_weeks = week_no+"."+age_no;
                return parseFloat(age_weeks).toFixed(1);
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
            fetch_bird_details();
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