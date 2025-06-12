<?php
//chicken_add_stocktransfer1.php
include "newConfig.php";
include "chicken_generate_trnum_details.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $date = date("Y-m-d");
    //Generate Transaction No.
    $incr = 0; $prefix = $trnum = "";
    $trno_dt1 = generate_transaction_details($date,"stocktransfer1","STI","display",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2];

    $tcds_per = 0;
    $sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$date' AND `tdate` >= '$date' AND `type` = 'TDS' AND `active` = '1' AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $tcds_per = $row['tcds']; }
    
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
    $query = mysqli_query($conn,$sql); $acc_code = $acc_name = array();
    while($row = mysqli_fetch_assoc($query)){ $acc_code[$row['code']] = $row['code']; $acc_name[$row['code']] = $row['description']; }
    
    $sql = "SELECT * FROM `acc_coa` WHERE `vouexp_flag` = '1' AND `active` = '1' AND `vehexp_aflag` = '1' ORDER BY `vehexp_sorder`,`description` ASC";
    $query = mysqli_query($conn,$sql); $acc_acode = $acc_aname = array();
    while($row = mysqli_fetch_assoc($query)){ $acc_acode[$row['code']] = $row['code']; $acc_aname[$row['code']] = $row['description']; }
    
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
    
    //Fetch Column From CoA Table
    $sql='SHOW COLUMNS FROM `acc_coa`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
    while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
    if(in_array("mobile_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa` ADD `mobile_no` VARCHAR(300) NULL DEFAULT NULL AFTER `flag`"; mysqli_query($conn,$sql); }
    if(in_array("transport_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa` ADD `transport_flag` INT(100) NOT NULL DEFAULT '0' AFTER `mobile_no`"; mysqli_query($conn,$sql); }

    //$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'chicken_display_stocktransfer1.php' AND `field_function` LIKE 'Add Voucher in Purchase screen' AND `user_access` LIKE 'all' AND `flag` = '1'";
    //$query = mysqli_query($conn,$sql); $avou_flag = mysqli_num_rows($query); $avou_flag = 1;

    $today = date("Y-m-d");
    $fdate = date("d.m.Y",strtotime($today));
    $y = date("y"); $y2 = $y + 1; $m = date("m"); $d = date("d");
    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $wcode[$row['code']] = $row['code'];
        $wdesc[$row['code']] = $row['description'];
    }
     //check and fetch date range
    global $drng_cday; $drng_cday = 0; global $drng_furl; $drng_furl = str_replace("_add_","_display_",basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
    include "poulsoft_fetch_daterange_master.php";

    $colspan = 13;
?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
            <style>
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
            <div class="card border-secondary mb-3">
                <div class="card-header">Add Stock Transfer</div>
                <form action="chicken_save_stocktransfer1.php" method="post" onsubmit="return checkval();">
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
                                        <th>From Warehouse<b style="color:red;">&nbsp;*</b></th>
                                        <th>Item<b style="color:red;">&nbsp;*</b></th>
                                        <?php if((int)$jals_flag == 1){ $colspan++; echo "<th>Jals</th>"; } ?>
                                        <?php if((int)$birds_flag == 1){ $colspan++; echo "<th>Birds</th>"; } ?>
                                        <th>Quantity<b style="color:red;">&nbsp;*</b></th>
                                        <th>Price<b style="color:red;">&nbsp;*</b></th>
                                        <th>To Warehouse<b style="color:red;">&nbsp;*</b></th>
                                        <th>Remarks</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                    <tr style="margin:5px 0px 5px 0px;">
                                        <td><input type="text" name="pdate[]" id="pdate[0]" class="form-control range_picker" value="<?php echo date("d.m.Y",strtotime($date)); ?>" style="width:100px;" onchange="" readonly /></td>
                                        <td><input type="text" name="dcno[]" id="dcno[0]" class="form-control" style="width:80px;" /></td>
                                        <td><select name="fsector[]" id="fsector[0]" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><select name="icode[]" id="icode[0]" class="form-control select2" style="width:180px;" onchange=""><option value="select">-select-</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>
                                        <?php
                                        if((int)$jals_flag == 1){ echo '<td><input type="text" name="jalqty[]" id="jalqty[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);" /></td>'; }
                                        if((int)$birds_flag == 1){ echo '<td><input type="text" name="birdqty[]" id="birdqty[0]" class="form-control text-right" style="width:50px;" onkeyup="validate_count(this.id);" /></td>'; }
                                        ?>
                                        <td><input type="text" name="qty[]" id="qty[0]" class="form-control text-right" style="width:60px;" onkeyup="validate_num(this.id);" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="price[]" id="price[0]" class="form-control text-right" style="width:60px;" onkeyup="validate_num(this.id);" onchange="validate_amount(this.id);" /></td>
                                        <td><select name="tsector[]" id="tsector[0]" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><textarea name="remarks[]" id="remarks[0]" class="form-control" style="width:150px;height:25px;"></textarea></td>
                                        
                                        <td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                        <td style="width:20px;visibility:hidden;text-align:center;"><input type="checkbox" name="rndoff_chk[]" id="rndoff_chk[0]" onchange="" checked /></td>
                                        <td style="width:20px;visibility:hidden;"><input type="text" name="roundoff[]" id="roundoff[0]" class="form-control text-right" style="width:20px;" onkeyup="validate_num(this.id);" onchange="validate_amount(this.id);" readonly /></td>
                                    </tr>
                                </tbody>
                                
                            </table>
                        </div><br/>
                        
                        <div class="row" style="visibility:hidden;">
                            <div class="form-group" style="width:30px;">
                                <label>IN</label>
                                <input type="text" name="incr" id="incr" class="form-control" value="0" style="width:20px;" readonly />
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>IN</label>
                                <input type="text" name="incr2" id="incr2" class="form-control" value="<?php echo $vincr; ?>" style="width:20px;" readonly />
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>IN</label>
                                <input type="text" name="incr3" id="incr3" class="form-control" value="0" style="width:20px;" readonly />
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>EB</label>
                                <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0" style="width:20px;" readonly />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group" align="center">
                                <button type="submit" name="submit" id="submit" class="btn btn-sm text-white bg-success">Submit</button>&ensp;
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
                    window.location.href = "chicken_display_stocktransfer1.php";
                }
                
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var date = document.getElementById("date").value;
                    var warehouse = document.getElementById("warehouse").value;
                    var l = true;

                   
                        var dcno = icode = fsector = tsector = date = ""; var c = jalqty = price = birdqty = qty = 0;
                        var incr = document.getElementById("incr").value;
                        for(var d = 0;d <= incr;d++){
                            if(l == true){
                                c = d + 1;
                                date = document.getElementById("date["+d+"]").value;
                                dcno = document.getElementById("dcno["+d+"]").value;
                                icode = document.getElementById("icode["+d+"]").value;
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
                                else if(fsector == "select"){
                                    alert("Please select From Warehouse in row: "+c);
                                    document.getElementById("fsector["+d+"]").focus();
                                    l = false;
                                }
                                else if(icode == "select"){
                                    alert("Please select Item in row: "+c);
                                    document.getElementById("icode["+d+"]").focus();
                                    l = false;
                                }
                                else if(parseFloat(jalqty) == 0){
                                    alert("Please enter JALS in row: "+c);
                                    document.getElementById("jalqty["+d+"]").focus();
                                    l = false;
                                }
                                else if(parseFloat(jalqty) == 0){
                                    alert("Please enter JALS in row: "+c);
                                    document.getElementById("jalqty["+d+"]").focus();
                                    l = false;
                                }
                                else if(parseFloat(qty) == 0){
                                    alert("Please enter Quantity in row: "+c);
                                    document.getElementById("qty["+d+"]").focus();
                                    l = false;
                                }
                                else if(parseFloat(price) == 0){
                                    alert("Please enter price in row: "+c);
                                    document.getElementById("price["+d+"]").focus();
                                    l = false;
                                }
                                else if(tsector == "select"){
                                    alert("Please select To Warehouse in row: "+c);
                                    document.getElementById("tsector["+d+"]").focus();
                                    l = false;
                                }
                                else{ }
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
               
                function create_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("action["+d+"]").style.visibility = "hidden";
                    d++; var html = '';
                    document.getElementById("incr").value = d;

                    var jals_flag = '<?php echo $jals_flag; ?>';
                    var birds_flag = '<?php echo $birds_flag; ?>';
                    var tweight_flag = '<?php echo $tweight_flag; ?>';
                    var eweight_flag = '<?php echo $eweight_flag; ?>';

                    html += '<tr id="row_no['+d+']">';
                    html += '<td><input type="text" name="pdate[]" id="pdate['+d+']" class="form-control range_picker" value="<?php echo date("d.m.Y",strtotime($date)); ?>" style="width:100px;" onchange="" readonly /></td>';
                    html += '<td><input type="text" name="dcno[]" id="dcno['+d+']" class="form-control" style="width:80px;" /></td>';
                    html += '<td><select name="fsector[]" id="fsector['+d+']" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?></select></td>';
                    html += '<td><select name="icode[]" id="icode['+d+']" class="form-control select2" style="width:180px;" onchange=""><option value="select">-select-</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>';
                    if(parseInt(jals_flag) == 1){ html += '<td><input type="text" name="jalqty[]" id="jalqty['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);" /></td>'; }
                    if(parseInt(birds_flag) == 1){ html += '<td><input type="text" name="birdqty[]" id="birdqty['+d+']" class="form-control text-right" style="width:50px;" onkeyup="validate_count(this.id);" /></td>'; }
                    html += '<td><input type="text" name="qty[]" id="qty['+d+']" class="form-control text-right" style="width:60px;" onkeyup="validate_num(this.id);" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><input type="text" name="price[]" id="price['+d+']" class="form-control text-right" style="width:60px;" onkeyup="validate_num(this.id);" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><select name="tsector[]" id="tsector['+d+']" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?></select></td>';
                    html += '<td><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="width:150px;height:25px;"></textarea></td>';
                    html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    html += '<td style="width:20px;visibility:hidden;text-align:center;"><input type="checkbox" name="rndoff_chk[]" id="rndoff_chk['+d+']" onchange="" checked /></td>';
                    html += '<td style="width:20px;visibility:hidden;"><input type="text" name="roundoff[]" id="roundoff['+d+']" class="form-control text-right" style="width:20px;" onkeyup="validate_num(this.id);" onchange="validate_amount(this.id);" readonly /></td>';
                    html += '</tr>';

                    $('#row_body').append(html);
                    $('.select2').select2();
                    document.getElementById("vcode["+d+"]").focus();
                }
                function destroy_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("row_no["+d+"]").remove();
                    d--;
                    document.getElementById("incr").value = d;
                    document.getElementById("action["+d+"]").style.visibility = "visible";
                    calculate_final_total_amount();
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
