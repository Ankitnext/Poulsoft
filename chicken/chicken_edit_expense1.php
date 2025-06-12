<?php
//chicken_edit_expense1.php
include "newConfig.php";
include "chicken_generate_trnum_details.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'";
    $query = mysqli_query($conn,$sql); $ppzflag = $ifwt = $ifbw = $ifjbw = $ifjbwen = $jals_flag = $birds_flag = $tweight_flag = $eweight_flag = 0;
    while($row = mysqli_fetch_assoc($query)){ $ppzflag = $row['ppzflag']; $ifwt = $row['wt']; $ifbw = $row['bw']; $ifjbw = $row['jbw']; $ifjbwen = $row['jbwen']; }
    if($ifjbwen == 1 || $ifjbw == 1){ $jals_flag = 1; } if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ $birds_flag = 1; } if($ifjbwen == 1){ $tweight_flag = $eweight_flag = 1; } if($ppzflag == ""){ $ppzflag = 0; }

    $sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
    while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `sort_order` DESC,`name` DESC";
    $query = mysqli_query($conn,$sql); $sup_code = $sup_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; }
    
    $sql = "SELECT * FROM `acc_coa` WHERE `vouexp_flag` = '1' AND `active` = '1' AND `vehexp_aflag` = '0' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $acc_code = $acc_fcode = $acc_name = array();
    while($row = mysqli_fetch_assoc($query)){ $acc_code[$row['code']] = $row['code']; $acc_fcode[$row['code']] = $row['code']; $acc_name[$row['code']] = $row['description']; }
    
    $sql = "SELECT * FROM `acc_coa` WHERE `vouexp_flag` = '1' AND `active` = '1' AND `vehexp_aflag` = '1' ORDER BY `vehexp_sorder`,`description` ASC";
    $query = mysqli_query($conn,$sql); $acc_acode = $acc_aname = array();
    while($row = mysqli_fetch_assoc($query)){ $acc_acode[$row['code']] = $row['code']; $acc_fcode[$row['code']] = $row['code']; $acc_aname[$row['code']] = $row['description']; }
    
    $sql = "SELECT * FROM `acc_modes` WHERE `description` IN ('Cash','Bank') AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $cash_mode = $bank_mode = "";
    while($row = mysqli_fetch_assoc($query)){ if($row['description'] == "Cash"){ $cash_mode = $row['code']; } else if($row['description'] == "Bank"){ $bank_mode = $row['code']; } }

    $sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash') AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $cash_code = $cash_name = array();
    while($row = mysqli_fetch_assoc($query)){ $cash_code[$row['code']] = $row['code']; $cash_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Bank') AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $bank_code = $bank_name = array();
    while($row = mysqli_fetch_assoc($query)){ $bank_code[$row['code']] = $row['code']; $bank_name[$row['code']] = $row['description']; }
    
    $sql = "SELECT * FROM `acc_coa` WHERE `driver_flag` ='1' AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $driver_code = $driver_name = array();
    while($row = mysqli_fetch_assoc($query)){ $driver_code[$row['code']] = $row['code']; $driver_name[$row['code']] = $row['description']; }
    
    $sql = "SELECT * FROM `acc_coa` WHERE `description` LIKE 'Salary Benefits and Wages' AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $schedule_alist = array();
    while($row = mysqli_fetch_assoc($query)){ $schedule_alist[$row['code']] = $row['code']; }
    
    $schedule_list = implode("','",$schedule_alist);
    $sql = "SELECT * FROM `acc_coa` WHERE `schedules` IN ('$schedule_list') AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $emp_scode = $emp_sname = array();
    while($row = mysqli_fetch_assoc($query)){ $emp_scode[$row['code']] = $row['code']; $emp_sname[$row['code']] = $row['description']; }
    
     $wcode = $wdesc = $acode = $adesc = $icode = $idesc = array();
    $sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $acode[$row['code']] = $row['code'];
        $adesc[$row['code']] = $row['description'];
        if($row['description'] == "Cash In Hand"){ $cash_code = $row['code']; }
    }
    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $wcode[$row['code']] = $row['code'];
        $wdesc[$row['code']] = $row['description'];
    }
    //Fetch Column From CoA Table
    $sql='SHOW COLUMNS FROM `acc_coa`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
    while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
    if(in_array("mobile_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa` ADD `mobile_no` VARCHAR(300) NULL DEFAULT NULL AFTER `flag`"; mysqli_query($conn,$sql); }
    if(in_array("transport_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa` ADD `transport_flag` INT(100) NOT NULL DEFAULT '0' AFTER `mobile_no`"; mysqli_query($conn,$sql); }

    //$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'chicken_display_expense1.php' AND `field_function` LIKE 'Add Voucher in Purchase screen' AND `user_access` LIKE 'all' AND `flag` = '1'";
    //$query = mysqli_query($conn,$sql); $avou_flag = mysqli_num_rows($query); $avou_flag = 1;

     //check and fetch date range
    global $drng_cday; $drng_cday = 0; global $drng_furl; $drng_furl = str_replace("_add_","_display_",basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
    include "poulsoft_fetch_daterange_master.php";

    $colspan = 13;
?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
            <style>
                body{
                    overflow: auto;
                }
                /*table,tr,th,td {
                    border: 1px solid black;
                    border-collapse: collapse;
                }*/
                label{
                    font-weight:bold;
                }
            </style>
        </head>
        <body>
            <?php
            $ids = $_GET['trnum'];
            
            if($ids != ""){
                $sql = "SELECT * FROM `acc_vouchers` WHERE `trnum` = '$ids' AND `flag` = '0' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql); $tcdsamt = array(); $c = 0;
                while($row = mysqli_fetch_assoc($query)){
                    $date = $row['date'];
                    $invoice = $row['trnum'];
                    $dcno = $row['dcno'];
                    $warehouse = $row['warehouse'];
                    $fcoa = $row['fcoa'];
                    $tcoa = $row['tcoa'];
                    $amount = round($row['amount'],2);
                    $remarks = $row['remarks'];
                    $c++;
                } $c = $c - 1;

                
            }
            ?>
            <div class="card border-secondary mb-3">
                <div class="card-header">Edit Expense</div>
                <form action="chicken_modify_expense1.php" method="post" onsubmit="return checkval();">
                    <div class="ml-5 card-body">
                        <div class="row">
                            <table>
                                <thead>
                                    <tr>
                                        <th colspan="<?php echo $colspan; ?>" style="background-color:#d1ffe4;color:#00722f;text-align:center;">Supplier Purchase Details</th>
                                    </tr>
                                    <tr>
                                        <th>Date<b style="color:red;">&nbsp;*</b></th>
                                        <th>DC No..</th>
                                        <th>From COA<b style="color:red;">&nbsp;*</b></th>
                                        <th>To COA<b style="color:red;">&nbsp;*</b></th>
                                        <th>Amount<b style="color:red;">&nbsp;*</b></th>
                                        <th>Sector<b style="color:red;">&nbsp;*</b></th>
                                        <th>Remarks</th>
                                        <th style="width:70px;"></th>
                                        <th style="width:20px;visibility:hidden;text-align:center;">RC</th>
                                        <th style="width:20px;visibility:hidden;">RA</th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                <?php  ?>
                                    <tr id="">
                                        <td><input type="text" name="pdate" id="pdate" class="form-control range_picker" value="<?php echo date("d.m.Y",strtotime($date)); ?>" style="width:100px;" onchange="" readonly /></td>
                                        <td><input type="text" name="dcno" id="dcno" class="form-control" value="<?php echo $dcno ?>" style="width:80px;" /></td>
                                        <td><select name="fcoa" id="fcoa" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($acode as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($scode == $fcoa){ echo "selected";} ?>><?php echo $adesc[$scode]; ?></option><?php } ?></select></td>
                                        <td><select name="tcoa" id="tcoa" class="form-control select2" style="width:180px;" onchange=""><option value="select">-select-</option><?php foreach($acode as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($scode == $tcoa){ echo "selected";} ?>><?php echo $adesc[$scode]; ?></option><?php } ?></select></td>
                                        <td><input type="text" name="amount" id="amount" class="form-control text-right" style="width:60px;" value="<?php echo $amount ?>" onkeyup="validate_num(this.id);" onchange="validate_amount(this.id);" /></td>
                                        <td><select name="sector" id="sector" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($wcode as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($scode == $warehouse){ echo "selected";} ?>><?php echo $wdesc[$scode]; ?></option><?php } ?></select></td>
                                        <td><textarea name="remarks" id="remarks" class="form-control" style="width:150px;height:25px;"><?php echo $remarks ?></textarea></td>
                                       
                                        <td style="width:20px;visibility:hidden;"><input type="text" name="trnum" id="trnum" class="form-control" value="<?php echo $invoice; ?>" style="width:20px;" readonly /></td>
                                    </tr>
                                    <?php  ?>
                                </tbody>
                            </table>
                        </div><br/>
                        
                        <div class="row" style="visibility:hidden;">
                            <div class="form-group" style="width:30px;">
                                <label>ID</label>
                                <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $ids; ?>" style="width:20px;" readonly />
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>EB</label>
                                <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0" style="width:20px;" readonly />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group" align="center">
                                <button type="submit" name="submit" id="submit" class="btn btn-sm text-white bg-success">Update</button>&ensp;
                                <button type="button" name="cancel" id="cancel" class="btn btn-sm text-white bg-danger" onclick="return_back()">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <script>
                 //Date Range selection
                var s_date = '<?php echo $rng_sdate; ?>'; var e_date = '<?php echo $rng_edate; ?>';
                function return_back(){
                    window.location.href = "chicken_display_expense1.php";
                }
                 
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var pname = document.getElementById("pname").value;
                    var l = true;

                    if(pname == ""){
                        alert("Please select date");
                        document.getElementById("pname").focus();
                        l = false;
                    }
                    else{
                        var dcno = icode = fsector = tsector = date = ""; var c = jalqty = price = birdqty = qty = 0;
                        var incr = document.getElementById("incr").value;
                        for(var d = 0;d <= incr;d++){
                            if(l == true){
                                c = d + 1;
                                date = document.getElementById("date["+d+"]").value;
                                dcno = document.getElementById("dcno["+d+"]").value;
                                fcoa = document.getElementById("fcoa["+d+"]").value;
                                fsector = document.getElementById("fsector["+d+"]").value;
                                tsector = document.getElementById("tsector["+d+"]").value;
                                jalqty = document.getElementById("jalqty["+d+"]").value; if(nweight == ""){ nweight = 0; }
                                birdqty = document.getElementById("birdqty["+d+"]").value; if(birdqty == ""){ birdqty = 0; }
                                price = document.getElementById("qty["+d+"]").value; if(price == ""){ price = 0; }
                                qty = document.getElementById("qty["+d+"]").value; if(qty == ""){ qty = 0; }

                                if(date == ""){
                                    alert("Please Enter date in row: "+c);
                                    document.getElementById("date["+d+"]").focus();
                                    l = false;
                                }
                                else if(dcno == ""){
                                    alert("Please Enter Dc No in row: "+c);
                                    document.getElementById("dcno["+d+"]").focus();
                                    l = false;
                                }
                                else if(fcoa == "select"){
                                    alert("Please select From COA in row: "+c);
                                    document.getElementById("fcoa["+d+"]").focus();
                                    l = false;
                                }
                                else if(tcoa == "select"){
                                    alert("Please select To COA in row: "+c);
                                    document.getElementById("tcoa["+d+"]").focus();
                                    l = false;
                                }
                                else if(parseFloat(amount) == 0){
                                    alert("Please enter Amount in row: "+c);
                                    document.getElementById("amount["+d+"]").focus();
                                    l = false;
                                }
                                else if(sector == "select"){
                                    alert("Please select Warehouse in row: "+c);
                                    document.getElementById("sector["+d+"]").focus();
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
                        event.preventDefault();
                        return false;
                    }
                }
                
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
		    <script src="handle_ebtn_as_tbtn.js"></script>
            <script>
                //Date Range selection
                $( ".range_picker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", minDate: s_date, maxDate: e_date, beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
            </script>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
