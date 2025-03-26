<?php
//chicken_edit_generalpurchase2.php
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

    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `name` ASC";
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
    
    //Fetch Column From CoA Table
    $sql='SHOW COLUMNS FROM `acc_coa`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
    while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
    if(in_array("mobile_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa` ADD `mobile_no` VARCHAR(300) NULL DEFAULT NULL AFTER `flag`"; mysqli_query($conn,$sql); }
    if(in_array("transport_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa` ADD `transport_flag` INT(100) NOT NULL DEFAULT '0' AFTER `mobile_no`"; mysqli_query($conn,$sql); }

    //$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'chicken_display_generalpurchase2.php' AND `field_function` LIKE 'Add Voucher in Purchase screen' AND `user_access` LIKE 'all' AND `flag` = '1'";
    //$query = mysqli_query($conn,$sql); $avou_flag = mysqli_num_rows($query); $avou_flag = 1;

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
            $trnum = $_GET['trnum'];
            $sql = "SELECT * FROM `pur_purchase` WHERE `invoice` = '$trnum' AND `flag` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice` ASC";
            $query = mysqli_query($conn,$sql); $ids = "";
            while($row = mysqli_fetch_assoc($query)){ $ids = $row['link_trnum']; }

            if($ids != ""){
                $sql = "SELECT * FROM `pur_purchase` WHERE `link_trnum` = '$ids' AND `flag` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice` ASC";
                $query = mysqli_query($conn,$sql); $tcdsamt = array(); $c = 0;
                while($row = mysqli_fetch_assoc($query)){
                    $date = $row['date'];
                    $invoice[$c] = $row['invoice'];
                    $bookinvoice[$c] = $row['bookinvoice'];
                    $vendorcode[$c] = $row['vendorcode'];
                    $itemcode[$c] = $row['itemcode'];
                    $jals[$c] = round($row['jals'],2);
                    $birds[$c] = round($row['birds'],2);
                    $totalweight[$c] = round($row['totalweight'],2);
                    $emptyweight[$c] = round($row['emptyweight'],2);
                    $netweight[$c] = round($row['netweight'],2);
                    $itemprice[$c] = round($row['itemprice'],2);
                    $totalamt[$c] = round($row['totalamt'],2);
                    $warehouse = $row['warehouse'];
                    $tcdsamt[$c] = round($row['tcdsamt'],2);
                    $roundoff[$c] = round($row['roundoff'],2);
                    $finaltotal[$c] = round($row['finaltotal'],2);
                    $remarks[$c] = $row['remarks'];
                    $c++;
                } $c = $c - 1;

                $tcds_per = 0;
                $sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$date' AND `tdate` >= '$date' AND `type` = 'TDS' AND `active` = '1' AND `dflag` = '0'";
                $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $tcds_per = $row['tcds']; }
                
                
                $acc_list = implode("','",$acc_fcode);
                $sql = "SELECT * FROM `acc_vouchers` WHERE `link_trnum` = '$ids' AND `tcoa` IN ('$acc_list') AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `id` ASC";
                $query = mysqli_query($conn,$sql); $trnum2 = ""; $vadv_amt = $slno = 0; $tcoa_alist = array();
                while($row = mysqli_fetch_assoc($query)){
                    $trnum2 = $row['trnum'];
                    $billno = $row['dcno'];
                    $group_code = $row['group_code'];
                    $fcoa = $row['fcoa'];
                    $slno++;
                    //Labour & Vehicle Advance
                    if(!empty($acc_aname[$row['tcoa']]) && $acc_aname[$row['tcoa']] == "Labour Advance"){
                        $acc_aamt[$row['tcoa']] = round($row['amount'],5);
                        $acc_arm2[$row['tcoa']] = $row['remarks'];
                    }
                    else if(!empty($acc_aname[$row['tcoa']]) && $acc_aname[$row['tcoa']] == "Vehicle Advance"){
                        $acc_aamt[$row['tcoa']] = round($row['amount'],5);
                        $vadv_amt = round($row['amount'],5);
                        $acc_arm2[$row['tcoa']] = $row['remarks'];
                    }
                    else{
                        $tcoa_amt[$row['tcoa']] = round($row['amount'],5);
                        $tcoa_alist[$row['tcoa']] = $row['tcoa'];
                        $remarks2[$row['tcoa']] = $row['remarks'];
                    }

                    $from_location = $row['from_location'];
                    $to_location = $row['to_location'];
                    $from_kms = round($row['from_kms'],2);
                    $to_kms = round($row['to_kms'],2);
                    $total_kms = round($row['total_kms'],2);
                    $fcoa_amt = round($row['advance_amt'],2);
                    $balance_amt = round($row['balance_amt'],2);
                }
                
                $acc_list = implode("','",$driver_code);
                $sql = "SELECT * FROM `acc_vouchers` WHERE `link_trnum` = '$ids' AND `tcoa` IN ('$acc_list') AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `id` ASC";
                $query = mysqli_query($conn,$sql); $trnum3 = ""; $supr_flag = $sup_eamt = $dcoa_alist = array();
                while($row = mysqli_fetch_assoc($query)){
                    $trnum3 = $row['trnum'];
                    $emp_sacc = $row['tcoa'];
                    $cost_type = $row['emp_ctype'];
                    $cost_prc = round($row['emp_cvalue'],5);

                    $emps_amt[$row['tcoa']] = round($row['amount'],5);
                    $supr_flag[$row['tcoa']] = round($row['supr_flag'],5);
                    $sup_eamt[$row['tcoa']] = round($row['sup_eamt'],5);
                    $dcoa_alist[$row['tcoa']] = $row['tcoa'];
                    $remarks3[$row['tcoa']] = $row['remarks'];
                }
            }
            ?>
            <div class="card border-secondary mb-3">
                <div class="card-header">Edit Purchase</div>
                <form action="chicken_modify_generalpurchase2.php" method="post" onsubmit="return checkval();">
                    <div class="ml-5 card-body">
                        <div class="row">
                            <div class="form-group" style="width:110px;">
                                <label for="date">Date<b style="color:red;">&nbsp;*</b></label>
                                <input type="text" name="date" id="date" class="form-control pur_datepickers" value="<?php echo date("d.m.Y",strtotime($date)); ?>" style="width:100px;" onchange="fetch_tcds_per(this.id);" readonly />
                            </div>
                            <div class="form-group" style="width:190px;">
                                <label>Warehouse/Vehicle<b style="color:red;">&nbsp;*</b></label>
                                <select name="warehouse" id="warehouse" class="form-control select2" style="width:180px;">
                                    <option value="select">select</option>
                                    <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($warehouse == $scode){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option><?php } ?>
                                </select>
                            </div>
                            <!--<div class="form-group" style="width:90px;">
                                <label>Bill No</label>
                                <input type="text" name="billno" id="billno" class="form-control" style="width:80px;" onkeyup="validatename(this.id);" />
                            </div>-->
                            <div class="form-group" style="width:110px;">
                                <label>Start KMs</label>
                                <input type="text" name="from_kms" id="from_kms" class="form-control text-right" value="<?php echo $from_kms; ?>" style="width:100px;" onkeyup="validate_num(this.id);calculate_tot_kms();" />
                            </div>
                            <div class="form-group" style="width:110px;">
                                <label>End KMs</label>
                                <input type="text" name="to_kms" id="to_kms" class="form-control text-right" value="<?php echo $to_kms; ?>" style="width:100px;" onkeyup="validate_num(this.id);calculate_tot_kms();" />
                            </div>
                            <div class="form-group" style="width:110px;">
                                <label>Total KMs</label>
                                <input type="text" name="total_kms" id="total_kms" class="form-control text-right" value="<?php echo $total_kms; ?>" style="width:100px;" readonly />
                            </div>
                            <div class="form-group" style="width:110px;">
                                <label>Advance</label>
                                <input type="text" name="vadv_amt" id="vadv_amt" class="form-control text-right" value="<?php echo $vadv_amt; ?>" style="width:100px;" onkeyup="validate_num(this.id);calculate_total_amt2();" />
                            </div>
                            <div class="form-group" style="width:30px;visibility:hidden;">
                                <label for="trnum">TP</label>
                                <input type="text" name="tcds_per" id="tcds_per" class="form-control text-right" value="<?php echo $tcds_per; ?>" style="width:100px;" readonly />
                            </div>
                        </div>
                        <div class="row">
                            <table>
                                <thead>
                                    <tr>
                                        <th colspan="<?php echo $colspan; ?>" style="background-color:#d1ffe4;color:#00722f;text-align:center;">Supplier Purchase Details</th>
                                    </tr>
                                    <tr>
                                        <th>Supplier<b style="color:red;">&nbsp;*</b></th>
                                        <th>Dc.No.</th>
                                        <th>Item<b style="color:red;">&nbsp;*</b></th>
                                        <?php if((int)$jals_flag == 1){ $colspan++; echo "<th>Jals</th>"; } ?>
                                        <?php if((int)$birds_flag == 1){ $colspan++; echo "<th>Birds</th>"; } ?>
                                        <?php if((int)$tweight_flag == 1){ $colspan++; echo "<th>T. Weight</th>"; } ?>
                                        <?php if((int)$eweight_flag == 1){ $colspan++; echo "<th>E. Weight</th>"; } ?>
                                        <th>Wt<b style="color:red;">&nbsp;*</b></th>
                                        <th>Price<b style="color:red;">&nbsp;*</b></th>
                                        <th>Amount</th>
                                        <th style="width:50px;text-align:center;">TCS</th>
                                        <th>TCS Amount</th>
                                        <th>Net Amount</th>
                                        <th>Remarks</th>
                                        <th style="width:70px;"></th>
                                        <th style="width:20px;visibility:hidden;text-align:center;">RC</th>
                                        <th style="width:20px;visibility:hidden;">RA</th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                <?php $incr = $c; for($c = 0;$c <=$incr;$c++){ ?>
                                    <tr id="row_no[<?php echo $c; ?>]">
                                        <td><select name="vcode[]" id="vcode[<?php echo $c; ?>]" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($sup_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($vendorcode[$c] == $scode){ echo "selected"; } ?>><?php echo $sup_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><input type="text" name="bookinvoice[]" id="bookinvoice[<?php echo $c; ?>]" class="form-control" value="<?php echo $bookinvoice[$c]; ?>" style="width:50px;" /></td>
                                        <td><select name="icode[]" id="icode[<?php echo $c; ?>]" class="form-control select2" style="width:140px;" onchange="update_row_fields(this.id);"><option value="select">-select-</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($itemcode[$c] == $scode){ echo "selected"; } ?>><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>
                                        <?php
                                        if((int)$jals_flag == 1){ ?><td><input type="text" name="jals[]" id="jals[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $jals[$c]; ?>" style="width:90px;" onkeyup="validate_count(this.id);calculate_total_amt();" /></td><?php }
                                        if((int)$birds_flag == 1){ ?><td><input type="text" name="birds[]" id="birds[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $birds[$c]; ?>" style="width:50px;" onkeyup="validate_count(this.id);calculate_total_amt();" /></td><?php }
                                        if((int)$tweight_flag == 1){ ?><td><input type="text" name="tweight[]" id="tweight[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $totalweight[$c]; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td><?php }
                                        if((int)$eweight_flag == 1){ ?><td><input type="text" name="eweight[]" id="eweight[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $emptyweight[$c]; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td><?php }
                                        ?>
                                        <td><input type="text" name="nweight[]" id="nweight[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $netweight[$c]; ?>" style="width:60px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="price[]" id="price[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $itemprice[$c]; ?>" style="width:60px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="item_amt[]" id="item_amt[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $totalamt[$c]; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" readonly /></td>
                                        <td style="width:50px;text-align:center;"><input type="checkbox" name="tcds_chk[]" id="tcds_chk[<?php echo $c; ?>]" onchange="calculate_total_amt();" <?php if($tcdsamt[$c] > 0){ echo "checked"; } ?> /></td>
                                        <td><input type="text" name="tcds_amt[]" id="tcds_amt[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $tcdsamt[$c]; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" readonly /></td>
                                        <td><input type="text" name="finaltotal[]" id="finaltotal[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $finaltotal[$c]; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" readonly /></td>
                                        <td><textarea name="remarks[]" id="remarks[<?php echo $c; ?>]" class="form-control" style="width:150px;height:25px;"><?php echo $remarks[$c]; ?></textarea></td>
                                        <?php
                                        if($c == $incr){ echo '<td id="action['.$c.']" style="padding-top: 5px;visibility:visible;">'; }
                                        else{ echo '<td id="action['.$c.']" style="padding-top: 5px;visibility:hidden;">'; }
                                        echo '<a href="javascript:void(0);" id="addrow['.$c.']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;';
                                        if($c > 0){ echo '<a href="javascript:void(0);" id="deductrow['.$c.']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a>'; }
                                        echo '</td>';
                                        ?>
                                        <td style="width:20px;visibility:hidden;text-align:center;"><input type="checkbox" name="rndoff_chk[]" id="rndoff_chk[<?php echo $c; ?>]" onchange="calculate_total_amt();" checked /></td>
                                        <td style="width:20px;visibility:hidden;"><input type="text" name="roundoff[]" id="roundoff[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $roundoff[$c]; ?>" style="width:20px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" readonly /></td>
                                        <td style="width:20px;visibility:hidden;"><input type="text" name="trnum[]" id="trnum[<?php echo $c; ?>]" class="form-control" value="<?php echo $invoice[$c]; ?>" style="width:20px;" readonly /></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" style="text-align:right;">Total</th>
                                        <?php
                                        if((int)$jals_flag == 1){ echo '<th><input type="text" name="tot_jals" id="tot_jals" class="form-control text-right" style="width:90px;" readonly /></th>'; }
                                        if((int)$birds_flag == 1){ echo '<th><input type="text" name="tot_birds" id="tot_birds" class="form-control text-right" style="width:50px;" readonly /></th>'; }
                                        if((int)$tweight_flag == 1){ echo '<th><input type="text" name="tot_tweight" id="tot_tweight" class="form-control text-right" style="width:90px;" readonly /></th>'; }
                                        if((int)$eweight_flag == 1){ echo '<th><input type="text" name="tot_eweight" id="tot_eweight" class="form-control text-right" style="width:90px;" readonly /></th>'; }
                                        ?>
                                        <th><input type="text" name="tot_nweight" id="tot_nweight" class="form-control text-right" style="width:60px;" readonly /></th>
                                        <th></th>
                                        <th><input type="text" name="tot_item_amt" id="tot_item_amt" class="form-control text-right" style="width:90px;" readonly /></th>
                                        <th></th>
                                        <th><input type="text" name="tot_tcds_amt" id="tot_tcds_amt" class="form-control text-right" style="width:90px;" readonly /></th>
                                        <th><input type="text" name="tot_finl_amt" id="tot_finl_amt" class="form-control text-right" style="width:90px;" readonly /></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div><br/>
                        <div class="row">
                            <div class="col-md-6">
                                <table style="width:auto;">
                                    <thead>
                                        <tr>
                                            <th colspan="4" style="background-color:#d1ffe4;color:#00722f;text-align:center;">Vehicle Expense Details</th>
                                        </tr>
                                    <thead>
                                        <tr>
                                            <th colspan="4">
                                                <div class="row justify-content-center align-items-center">
                                                    <?php if($cash_mode != ""){ ?>
                                                    <div class="form-group" style="width:40px;">
                                                        <label for="group_code1">Cash</label>
                                                        <input type="radio" name="group_code" id="group_code1" class="form-control1" value="<?php echo $cash_mode; ?>" onchange="update_accounts(this.id);" <?php if($group_code == $cash_mode){ echo "checked"; } ?> />
                                                    </div>
                                                    <?php } ?>
                                                    <?php if($bank_mode != ""){ ?>
                                                    <div class="form-group" style="width:40px;">
                                                        <label for="group_code2">Bank</label>
                                                        <input type="radio" name="group_code" id="group_code2" class="form-control1" value="<?php echo $bank_mode; ?>" onchange="update_accounts(this.id);" <?php if($group_code == $bank_mode){ echo "checked"; } ?> />
                                                    </div>
                                                    <?php } ?>
                                                    <div class="form-group" style="width:190px;">
                                                        <label>CoA Account<b style="color:red;">&nbsp;*</b></label>
                                                        <select name="fcoa" id="fcoa" class="form-control select2" style="width:180px;">
                                                            <option value="select">select</option>
                                                            <?php if($group_code == $cash_mode){ foreach($cash_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($fcoa == $scode){ echo "selected"; } ?>><?php echo $cash_name[$scode]; ?></option><?php } }
                                                            else if($group_code == $bank_mode){ foreach($bank_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($fcoa == $scode){ echo "selected"; } ?>><?php echo $bank_name[$scode]; ?></option><?php } } ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group" style="width:20px;visibility:hidden;">
                                                        <label>T2</label>
                                                        <input type="text" name="trnum2" id="trnum2" class="form-control" value="<?php echo $trnum2; ?>" />
                                                    </div>
                                                </div>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Advance</th>
                                            <th><input type="text" name="fcoa_amt" id="fcoa_amt" class="form-control text-right" value="<?php echo $fcoa_amt; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt2();" onchange="validate_amount(this.id);" /></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                        <tr style="text-align:left;">
                                            <th>Expense Type</th>
                                            <th>Amount</th>
                                            <th>Remarks</th>
                                            <th style="width:70px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="row_body2">
                                        <?php $vincr = 0; foreach($acc_acode as $acode){ ?>
                                        <tr id="row_no2[<?php echo $vincr; ?>]">
                                            <td><select name="tcoa[]" id="tcoa[<?php echo $vincr; ?>]" class="form-control select2" style="width:180px;"><option value="<?php echo $acode; ?>"><?php echo $acc_aname[$acode]; ?></option></select></td>
                                            <td><input type="text" name="tcoa_amt[]" id="tcoa_amt[<?php echo $vincr; ?>]" class="form-control text-right" value="<?php echo $acc_aamt[$acode]; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt2();" onchange="validate_amount(this.id);" /></td>
                                            <th><textarea name="remarks2[]" id="remarks2[<?php echo $vincr; ?>]" class="form-control" style="width:150px;height:28px;"><?php echo $acc_arm2[$acode]; ?></textarea></th>
                                            <td></td>
                                        </tr>
                                        <?php
                                        $vincr++;
                                        }
                                        if(sizeof($tcoa_alist) > 0){
                                            $incr2 = sizeof($tcoa_alist) - 1; $c = 0;
                                            foreach($tcoa_alist as $acode){
                                            ?>
                                            <tr id="row_no2[<?php echo $vincr; ?>]">
                                                <td><select name="tcoa[]" id="tcoa[<?php echo $vincr; ?>]" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($acc_code as $ccode){ ?><option value="<?php echo $ccode; ?>" <?php if($acode == $ccode){ echo "selected"; } ?>><?php echo $acc_name[$ccode]; ?></option><?php } ?></select></td>
                                                <td><input type="text" name="tcoa_amt[]" id="tcoa_amt[<?php echo $vincr; ?>]" class="form-control text-right" value="<?php echo $tcoa_amt[$acode]; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt2();" onchange="validate_amount(this.id);" /></td>
                                                <th><textarea name="remarks2[]" id="remarks2[<?php echo $vincr; ?>]" class="form-control" style="width:150px;height:28px;"><?php echo $remarks2[$acode]; ?></textarea></th>
                                                <?php
                                                if($c == $incr2){ echo '<td id="action2['.$vincr.']" style="padding-top: 5px;visibility:visible;">'; }
                                                else{ echo '<td id="action2['.$vincr.']" style="padding-top: 5px;visibility:hidden;">'; }
                                                echo '<a href="javascript:void(0);" id="addrow2['.$vincr.']" onclick="create_row2(this.id);"><i class="fa fa-plus"></i></a>&ensp;';
                                                if($c > 0){ echo '<a href="javascript:void(0);" id="deductrow2['.$vincr.']" onclick="destroy_row2(this.id);"><i class="fa fa-minus" style="color:red;"></i></a>'; }
                                                echo '</td>';
                                                ?>
                                            </tr>
                                        <?php
                                            $vincr++; $c++;
                                            }
                                        }
                                        else{
                                        ?>
                                        <tr id="row_no2[<?php echo $vincr; ?>]">
                                            <td><select name="tcoa[]" id="tcoa[<?php echo $vincr; ?>]" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($acc_code as $acode){ ?><option value="<?php echo $acode; ?>"><?php echo $acc_name[$acode]; ?></option><?php } ?></select></td>
                                            <td><input type="text" name="tcoa_amt[]" id="tcoa_amt[<?php echo $vincr; ?>]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt2();" onchange="validate_amount(this.id);" /></td>
                                            <th><textarea name="remarks2[]" id="remarks2[<?php echo $vincr; ?>]" class="form-control" style="width:150px;height:28px;"></textarea></th>
                                            <td id="action2[<?php echo $vincr; ?>]"><a href="javascript:void(0);" id="addrow2[<?php echo $vincr; ?>]" onclick="create_row2(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                        </tr>
                                        <?php
                                        $vincr++;
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td style="text-align:right;">Total</td>
                                            <td style="visibility:visible;"><input type="text" name="tamount" id="tamount" class="form-control text-right" style="width:90px;" readonly /></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:right;">Balance</td>
                                            <td style="visibility:visible;"><input type="text" name="balance_amt" id="balance_amt" class="form-control text-right" value="<?php echo $balance_amt; ?>" style="width:90px;" readonly /></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table style="width:auto;">
                                    <thead>
                                        <tr>
                                            <th colspan="4" style="background-color:#d1ffe4;color:#00722f;text-align:center;">Labour Attendance & Advance Details</th>
                                        </tr>
                                    <thead>
                                        <tr style="text-align:left;">
                                            <th>Labour</th>
                                            <th>Supervisor</th>
                                            <th>Adv Amt</th>
                                            <th>Remarks</th>
                                            <th style="width:70px;"></th>
                                            <th style="width:25px;visibility:hidden;">EA</th>
                                        </tr>
                                    </thead>
                                    <tbody id="row_body3">
                                        <?php
                                        if(sizeof($dcoa_alist) > 0){
                                            $c = 0;
                                            $incr3 = sizeof($dcoa_alist) - 1;
                                            foreach($dcoa_alist as $acode){
                                            ?>
                                            <tr id="row_no3[<?php echo $c; ?>]">
                                                <td><select name="emp_scode[]" id="emp_scode[<?php echo $c; ?>]" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($driver_code as $ccode){ ?><option value="<?php echo $ccode; ?>" <?php if($acode == $ccode){ echo "selected"; } ?>><?php echo $driver_name[$ccode]; ?></option><?php } ?></select></td>
                                                <td style="text-align:center;"><input type="checkbox" name="supr_flag[]" id="supr_flag[<?php echo $c; ?>]" onchange="calculate_empsal_amt2();" <?php if((int)$supr_flag[$acode] == 1){ echo "checked"; } ?> /></td>
                                                <td><input type="text" name="emps_amt[]" id="emps_amt[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $emps_amt[$acode]; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_empsal_amt2();" onchange="validate_amount(this.id);" /></td>
                                                <th><textarea name="remarks3[]" id="remarks3[<?php echo $c; ?>]" class="form-control" style="width:150px;height:28px;"><?php echo $remarks3[$acode]; ?></textarea></th>
                                                <?php
                                                if($c == $incr3){ echo '<td id="action3['.$c.']" style="padding-top: 5px;visibility:visible;">'; }
                                                else{ echo '<td id="action3['.$c.']" style="padding-top: 5px;visibility:hidden;">'; }
                                                echo '<a href="javascript:void(0);" id="addrow3['.$c.']" onclick="create_row3(this.id);"><i class="fa fa-plus"></i></a>&ensp;';
                                                if($c > 0){ echo '<a href="javascript:void(0);" id="deductrow3['.$c.']" onclick="destroy_row3(this.id);"><i class="fa fa-minus" style="color:red;"></i></a>'; }
                                                echo '</td>';
                                                ?>
                                                <td style="width:25px;visibility:hidden;"><input type="text" name="emps_eamt[]" id="emps_eamt[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $sup_eamt[$acode]; ?>" style="width:20px;" onkeyup="validate_num(this.id);calculate_empsal_amt2();" onchange="validate_amount(this.id);" readonly /></td>
                                            </tr>
                                            <?php
                                            $c++;
                                            }
                                        }
                                        else{
                                        ?>
                                        <tr id="row_no3[0]">
                                            <td><select name="emp_scode[]" id="emp_scode[0]" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($driver_code as $acode){ ?><option value="<?php echo $acode; ?>"><?php echo $driver_name[$acode]; ?></option><?php } ?></select></td>
                                            <td style="text-align:center;"><input type="checkbox" name="supr_flag[]" id="supr_flag[0]" onchange="calculate_empsal_amt2();" /></td>
                                            <td><input type="text" name="emps_amt[]" id="emps_amt[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_empsal_amt2();" onchange="validate_amount(this.id);" /></td>
                                            <th><textarea name="remarks3[]" id="remarks3[0]" class="form-control" style="width:150px;height:28px;"></textarea></th>
                                            <td id="action3[0]"><a href="javascript:void(0);" id="addrow3[0]" onclick="create_row3(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                            <td style="width:25px;visibility:hidden;"><input type="text" name="emps_eamt[]" id="emps_eamt[0]" class="form-control text-right" style="width:20px;" onkeyup="validate_num(this.id);calculate_empsal_amt2();" onchange="validate_amount(this.id);" readonly /></td>
                                        </tr>
                                        <?php
                                        $incr3 = 0;
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td style="text-align:right;" colspan="2">Total</td>
                                            <td style="visibility:visible;"><input type="text" name="tot_empsal_amt" id="tot_empsal_amt" class="form-control text-right" style="width:90px;" readonly /></td>
                                            <td style="visibility:hidden;"><input type="text" name="trnum3" id="trnum3" class="form-control" value="<?php echo $trnum3; ?>" /></td>
                                            <td></td>
                                            <td style="width:25px;visibility:hidden;"><input type="text" name="tot_emps_eamt" id="tot_emps_eamt" class="form-control text-right" style="width:20px;" readonly /></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="row" style="visibility:hidden;">
                            <div class="form-group" style="width:30px;">
                                <label>ID</label>
                                <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $ids; ?>" style="width:20px;" readonly />
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>IN</label>
                                <input type="text" name="incr" id="incr" class="form-control" value="<?php echo $incr; ?>" style="width:20px;" readonly />
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>IN</label>
                                <input type="text" name="incr2" id="incr2" class="form-control" value="<?php echo $vincr - 1; ?>" style="width:20px;" readonly />
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>IN</label>
                                <input type="text" name="incr3" id="incr3" class="form-control" value="<?php echo $incr3; ?>" style="width:20px;" readonly />
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>EB</label>
                                <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0" style="width:20px;" readonly />
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>MC</label>
                                <input type="text" style="width:auto;" class="form-control" name="mnu_fcnt" id="mnu_fcnt" value="1" style="width:20px;" readonly />
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
                function return_back(){
                    window.location.href = "chicken_display_generalpurchase2.php";
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var date = document.getElementById("date").value;
                    var warehouse = document.getElementById("warehouse").value;
                    var l = true;

                    if(date == ""){
                        alert("Please select date");
                        document.getElementById("date").focus();
                        l = false;
                    }
                    else if(warehouse == "select"){
                        alert("Please select Warehouse/Vehicle");
                        document.getElementById("warehouse").focus();
                        l = false;
                    }
                    else{
                        var vcode = icode = ""; var c = nweight = price = item_amt = 0;
                        var incr = document.getElementById("incr").value;
                        for(var d = 0;d <= incr;d++){
                            if(l == true){
                                c = d + 1;
                                vcode = document.getElementById("vcode["+d+"]").value;
                                icode = document.getElementById("icode["+d+"]").value;
                                nweight = document.getElementById("nweight["+d+"]").value; if(nweight == ""){ nweight = 0; }
                                price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }
                                item_amt = document.getElementById("item_amt["+d+"]").value; if(item_amt == ""){ item_amt = 0; }

                                if(vcode == "select"){
                                    alert("Please select Supplier in row: "+c);
                                    document.getElementById("vcode["+d+"]").focus();
                                    l = false;
                                }
                                else if(icode == "select"){
                                    alert("Please select Item in row: "+c);
                                    document.getElementById("icode["+d+"]").focus();
                                    l = false;
                                }
                                else if(parseFloat(nweight) == 0){
                                    alert("Please enter net weight in row: "+c);
                                    document.getElementById("nweight["+d+"]").focus();
                                    l = false;
                                }
                                else if(parseFloat(price) == 0){
                                    alert("Please enter price in row: "+c);
                                    document.getElementById("price["+d+"]").focus();
                                    l = false;
                                }
                                else if(parseFloat(item_amt) == 0){
                                    alert("Please enter price/Weight in row: "+c);
                                    document.getElementById("item_amt["+d+"]").focus();
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
                        return false;
                    }
                }
                function fetch_tcds_per(){
                    var date = document.getElementById("date").value;
                    var tdsper = new XMLHttpRequest();
                    var method = "GET";
                    var url = "main_gettcdsvalue.php?type=TDS&cdate="+date;
                    var asynchronous = true;
                    tdsper.open(method, url, asynchronous);
                    tdsper.send();
                    tdsper.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var tds = this.responseText;
                            if(tds != ""){
                                document.getElementById("tcds_per").value = tds;
                            }
                        }
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
                    html += '<td><select name="vcode[]" id="vcode['+d+']" class="form-control select2" style="width:180px;" onchange="update_row_fields(this.id);"><option value="select">-select-</option><?php foreach($sup_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sup_name[$scode]; ?></option><?php } ?></select></td>';
                    html += '<td><input type="text" name="bookinvoice[]" id="bookinvoice['+d+']" class="form-control" style="width:50px;" /></td>';
                    html += '<td><select name="icode[]" id="icode['+d+']" class="form-control select2" style="width:140px;" onchange="update_row_fields(this.id);"><option value="select">-select-</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>';
                    if(parseInt(jals_flag) == 1){ html += '<td><input type="text" name="jals[]" id="jals['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);calculate_total_amt();" /></td>'; }
                    if(parseInt(birds_flag) == 1){ html += '<td><input type="text" name="birds[]" id="birds['+d+']" class="form-control text-right" style="width:50px;" onkeyup="validate_count(this.id);calculate_total_amt();" /></td>'; }
                    if(parseInt(tweight_flag) == 1){ html += '<td><input type="text" name="tweight[]" id="tweight['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>'; }
                    if(parseInt(eweight_flag) == 1){ html += '<td><input type="text" name="eweight[]" id="eweight['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>'; }
                    html += '<td><input type="text" name="nweight[]" id="nweight['+d+']" class="form-control text-right" style="width:60px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><input type="text" name="price[]" id="price['+d+']" class="form-control text-right" style="width:60px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><input type="text" name="item_amt[]" id="item_amt['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" readonly /></td>';
                    html += '<td style="width:50px;text-align:center;"><input type="checkbox" name="tcds_chk[]" id="tcds_chk['+d+']" onchange="calculate_total_amt();" /></td>';
                    html += '<td><input type="text" name="tcds_amt[]" id="tcds_amt['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" readonly /></td>';
                    html += '<td><input type="text" name="finaltotal[]" id="finaltotal['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" readonly /></td>';
                    html += '<td><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="width:150px;height:25px;"></textarea></td>';
                    html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    html += '<td style="width:20px;visibility:hidden;text-align:center;"><input type="checkbox" name="rndoff_chk[]" id="rndoff_chk['+d+']" onchange="calculate_total_amt();" checked /></td>';
                    html += '<td style="width:20px;visibility:hidden;"><input type="text" name="roundoff[]" id="roundoff['+d+']" class="form-control text-right" style="width:20px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" readonly /></td>';
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
                function calculate_total_amt(){
                    var jals_flag = '<?php echo $jals_flag; ?>';
                    var birds_flag = '<?php echo $birds_flag; ?>';
                    var tweight_flag = '<?php echo $tweight_flag; ?>';
                    var eweight_flag = '<?php echo $eweight_flag; ?>';

                    /*Total Calculations*/
                    var incr = document.getElementById("incr").value;
                    var jals = birds = tweight = eweight = nweight = price = item_amt = 0;
                    var tot_jals = tot_birds = tot_tweight = tot_eweight = tot_nweight = tot_item_amt = tot_tcds_amt = tot_finl_amt = bird_flag = 0;
                    for(var d = 0;d <= incr;d++){
                        jals = birds = tweight = eweight = nweight = price = item_amt = bird_flag = 0;
                        icode = iname = "";
                        icode = document.getElementById("icode["+d+"]");
                        iname = icode.options[icode.selectedIndex].text;
                        bird_flag = iname.search(/Birds/i);

                        if(parseInt(jals_flag) == 1){ jals = document.getElementById("jals["+d+"]").value; } if(jals == ""){ jals = 0; }
                        if(parseInt(birds_flag) == 1){ birds = document.getElementById("birds["+d+"]").value; } if(birds == ""){ birds = 0; }
                        if(parseInt(tweight_flag) == 1){ tweight = document.getElementById("tweight["+d+"]").value; } if(tweight == ""){ tweight = 0; }
                        if(parseInt(eweight_flag) == 1){ eweight = document.getElementById("eweight["+d+"]").value; } if(eweight == ""){ eweight = 0; }
                        if(parseInt(tweight_flag) == 1 && parseInt(eweight_flag) == 1 && parseInt(bird_flag) > 0){
                            nweight = parseFloat(tweight) - parseFloat(eweight);
                            document.getElementById("nweight["+d+"]").value = parseFloat(nweight).toFixed(2);
                        }
                        else{
                            nweight = document.getElementById("nweight["+d+"]").value; if(nweight == ""){ nweight = 0; }
                        }
                        var price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }
                        var item_amt = parseFloat(nweight) * parseFloat(price);
                        document.getElementById("item_amt["+d+"]").value = parseFloat(item_amt).toFixed(2);

                        /*TCDS Calculations*/
                        item_amt = document.getElementById("item_amt["+d+"]").value; if(item_amt == ""){ item_amt = 0; }
                        var tcds_chk = document.getElementById("tcds_chk["+d+"]");
                        var tcds_per = tcds_amt = finaltotal = famt1 = 0;
                        if(tcds_chk.checked == true){
                            tcds_per = document.getElementById("tcds_per").value; if(tcds_per == ""){ tcds_per = 0; }
                            if(parseFloat(tcds_per) > 0){
                                tcds_amt = ((parseFloat(tcds_per) / 100) * parseFloat(item_amt));
                            }
                        }
                        document.getElementById("tcds_amt["+d+"]").value = parseFloat(tcds_amt).toFixed(2);

                        tcds_amt = document.getElementById("tcds_amt["+d+"]").value; if(tcds_amt == ""){ tcds_amt = 0; }
                        famt1 = parseFloat(item_amt) + parseFloat(tcds_amt);
                        /*Round-Off Calculations*/
                        var rndoff_chk = document.getElementById("rndoff_chk["+d+"]");
                        var roundoff = 0;
                        if(rndoff_chk.checked == true){
                            if(parseFloat(famt1) > 0){
                                finaltotal = parseFloat(famt1).toFixed(0);
                                roundoff = parseFloat(finaltotal) - parseFloat(famt1);
                            }
                        }
                        else{
                            finaltotal = parseFloat(famt1).toFixed(2);
                        }
                        document.getElementById("roundoff["+d+"]").value = parseFloat(roundoff).toFixed(2);
                        document.getElementById("finaltotal["+d+"]").value = parseFloat(finaltotal).toFixed(2);
                        
                        tot_jals = parseFloat(tot_jals) + parseFloat(jals);
                        tot_birds = parseFloat(tot_birds) + parseFloat(birds);
                        tot_tweight = parseFloat(tot_tweight) + parseFloat(tweight);
                        tot_eweight = parseFloat(tot_eweight) + parseFloat(eweight);
                        tot_nweight = parseFloat(tot_nweight) + parseFloat(nweight);
                        tot_item_amt = parseFloat(tot_item_amt) + parseFloat(item_amt);
                        tot_tcds_amt = parseFloat(tot_tcds_amt) + parseFloat(tcds_amt);
                        tot_finl_amt = parseFloat(tot_finl_amt) + parseFloat(finaltotal);
                    }
                    if(parseInt(jals_flag) == 1){ document.getElementById("tot_jals").value = parseFloat(tot_jals).toFixed(0); }
                    if(parseInt(birds_flag) == 1){ document.getElementById("tot_birds").value = parseFloat(tot_birds).toFixed(0); }
                    if(parseInt(tweight_flag) == 1){ document.getElementById("tot_tweight").value = parseFloat(tot_tweight).toFixed(2); }
                    if(parseInt(eweight_flag) == 1){ document.getElementById("tot_eweight").value = parseFloat(tot_eweight).toFixed(2); }
                    document.getElementById("tot_nweight").value = parseFloat(tot_nweight).toFixed(2);
                    document.getElementById("tot_item_amt").value = parseFloat(tot_item_amt).toFixed(2);
                    document.getElementById("tot_tcds_amt").value = parseFloat(tot_tcds_amt).toFixed(2);
                    document.getElementById("tot_finl_amt").value = parseFloat(tot_finl_amt).toFixed(2);

                    var mnu_fcnt = document.getElementById("mnu_fcnt").value; if(mnu_fcnt == ""){ mnu_fcnt = 0; }
                    if(parseFloat(mnu_fcnt) == 1){ document.getElementById("mnu_fcnt").value = 0; } else{ calculate_empsal_amt2(); }
                }
                function update_row_fields(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var icode = document.getElementById("icode["+d+"]");
                    var iname = icode.options[icode.selectedIndex].text;
                    var bird_flag = iname.search(/Birds/i);

                    var jals_flag = '<?php echo $jals_flag; ?>';
                    var birds_flag = '<?php echo $birds_flag; ?>';
                    var tweight_flag = '<?php echo $tweight_flag; ?>';
                    var eweight_flag = '<?php echo $eweight_flag; ?>';

                    if(parseInt(bird_flag) > 0){
                        if(parseInt(jals_flag) == 1){ document.getElementById("jals["+d+"]").style.visibility = "visible"; }
                        if(parseInt(birds_flag) == 1){ document.getElementById("birds["+d+"]").style.visibility = "visible"; }
                        if(parseInt(tweight_flag) == 1){ document.getElementById("tweight["+d+"]").style.visibility = "visible"; }
                        if(parseInt(eweight_flag) == 1){ document.getElementById("eweight["+d+"]").style.visibility = "visible"; }
                        if(parseInt(tweight_flag) == 1 && parseInt(eweight_flag) == 1){ document.getElementById("nweight["+d+"]").readOnly = true; }
                    }
                    else{
                        if(parseInt(jals_flag) == 1){ document.getElementById("jals["+d+"]").style.visibility = "hidden"; document.getElementById("jals["+d+"]").value = ""; }
                        if(parseInt(birds_flag) == 1){ document.getElementById("birds["+d+"]").style.visibility = "hidden"; document.getElementById("birds["+d+"]").value = ""; }
                        if(parseInt(tweight_flag) == 1){ document.getElementById("tweight["+d+"]").style.visibility = "hidden"; document.getElementById("tweight["+d+"]").value = ""; }
                        if(parseInt(eweight_flag) == 1){ document.getElementById("eweight["+d+"]").style.visibility = "hidden"; document.getElementById("eweight["+d+"]").value = ""; }
                        document.getElementById("nweight["+d+"]").readOnly = false;
                    }
                    calculate_total_amt();
                }
                function update_accounts(a){
                    var coa_mode = document.getElementById(a).value;
                    removeAllOptions(document.getElementById("fcoa"));

                    if(coa_mode != ""){
                        var prices = new XMLHttpRequest();
                        var method = "GET";
                        var url = "chicken_fetch_cashrbankdetails.php?coa_mode="+coa_mode+"&type=add";
                        //window.open(url);
                        var asynchronous = true;
                        prices.open(method, url, asynchronous);
                        prices.send();
                        prices.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var fcoa = this.responseText;
                                $('#fcoa').append(fcoa);
                            }
                        }
                    }
                }
                function create_row2(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("action2["+d+"]").style.visibility = "hidden";
                    d++; var html = '';
                    document.getElementById("incr2").value = d;
                    html += '<tr id="row_no2['+d+']">';
                    html += '<td><select name="tcoa[]" id="tcoa['+d+']" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($acc_code as $acode){ ?><option value="<?php echo $acode; ?>"><?php echo $acc_name[$acode]; ?></option><?php } ?></select></td>';
                    html += '<td><input type="text" name="tcoa_amt[]" id="tcoa_amt['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt2();" onchange="validate_amount(this.id);" /></td>';
                    html += '<th><textarea name="remarks2[]" id="remarks2['+d+']" class="form-control" style="width:150px;height:28px;"></textarea></th>';
                    html += '<td id="action2['+d+']"><a href="javascript:void(0);" id="addrow2['+d+']" onclick="create_row2(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow2['+d+']" onclick="destroy_row2(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    html += '</tr>';
                    $('#row_body2').append(html);
                    $('.select2').select2();
                    document.getElementById("tcoa["+d+"]").focus();
                    calculate_total_amt2();
                }
                function destroy_row2(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("row_no2["+d+"]").remove();
                    d--;
                    document.getElementById("incr2").value = d;
                    document.getElementById("action2["+d+"]").style.visibility = "visible";
                    calculate_total_amt2();
                }
                function create_row3(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("action3["+d+"]").style.visibility = "hidden";
                    d++; var html = '';
                    document.getElementById("incr3").value = d;
                    html += '<tr id="row_no3['+d+']">';
                    html += '<td><select name="emp_scode[]" id="emp_scode['+d+']" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($driver_code as $acode){ ?><option value="<?php echo $acode; ?>"><?php echo $driver_name[$acode]; ?></option><?php } ?></select></td>';
                    html += '<td style="text-align:center;"><input type="checkbox" name="supr_flag[]" id="supr_flag['+d+']" onchange="calculate_empsal_amt2();" /></td>';
                    html += '<td><input type="text" name="emps_amt[]" id="emps_amt['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_empsal_amt2();" onchange="validate_amount(this.id);" /></td>';
                    html += '<th><textarea name="remarks3[]" id="remarks3['+d+']" class="form-control" style="width:150px;height:28px;"></textarea></th>';
                    html += '<td id="action3['+d+']"><a href="javascript:void(0);" id="addrow3['+d+']" onclick="create_row3(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow3['+d+']" onclick="destroy_row3(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    html += '<td style="width:25px;visibility:hidden;"><input type="text" name="emps_eamt[]" id="emps_eamt['+d+']" class="form-control text-right" style="width:20px;" onkeyup="validate_num(this.id);calculate_empsal_amt2();" onchange="validate_amount(this.id);" readonly /></td>';
                    html += '</tr>';
                    $('#row_body3').append(html);
                    $('.select2').select2();
                    document.getElementById("emp_scode["+d+"]").focus();
                }
                function destroy_row3(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("row_no3["+d+"]").remove();
                    d--;
                    document.getElementById("incr3").value = d;
                    document.getElementById("action3["+d+"]").style.visibility = "visible";
                    calculate_empsal_amt2();
                }
                function calculate_total_amt2(){
                    var incr2 = document.getElementById("incr2").value;
                    var fcoa_amt = vadv_amt = tamount = balance_amt = 0;
                    //Advance Vehicle Expense
                    vadv_amt = document.getElementById("vadv_amt").value; if(vadv_amt == ""){ vadv_amt = 0; }
                    var tcoa = "";
                    for(var d = 0;d <= incr2;d++){
                        tcoa = "";
                        tcoa = document.getElementById("tcoa["+d+"]");
                        if(tcoa.options[tcoa.selectedIndex].text == "Vehicle Advance"){
                            document.getElementById("tcoa_amt["+d+"]").value = parseFloat(vadv_amt).toFixed(2);
                        }
                    }

                    fcoa_amt = document.getElementById("fcoa_amt").value; if(fcoa_amt == ""){ fcoa_amt = 0; }
                    var anme = "";
                    for(var d = 0;d <= incr2;d++){
                        //tcoa = document.getElementById("tcoa["+d+"]");
                        var amount = document.getElementById("tcoa_amt["+d+"]").value; if(amount == ""){ amount = 0; }
                        //if(anme == ""){ anme = tcoa.options[tcoa.selectedIndex].text+"@"+amount; } else{ anme = anme+"\n"+tcoa.options[tcoa.selectedIndex].text+"@"+amount; }
                        tamount = parseFloat(tamount) + parseFloat(amount);
                    }
                    //alert(anme);
                    document.getElementById("tamount").value = parseFloat(tamount);
                    balance_amt = parseFloat(fcoa_amt) - parseFloat(tamount);
                    document.getElementById("balance_amt").value = parseFloat(balance_amt);
                }
                function calculate_tot_kms(){
                    var from_kms = document.getElementById("from_kms").value; if(from_kms == ""){ from_kms = 0; }
                    var to_kms = document.getElementById("to_kms").value; if(to_kms == ""){ to_kms = 0; }
                    var total_kms = parseFloat(to_kms) - parseFloat(from_kms); if(total_kms == ""){ total_kms = 0; }
                    document.getElementById("total_kms").value = parseFloat(total_kms).toFixed(2);
                }
                function calculate_empsal_amt2(){
                    var incr3 = document.getElementById("incr3").value;
                    var cost_amt = tot_empsal_amt = tot_emps_eamt = emps_eamt = 0; var supr_flag = "";
                    for(var d = 0;d <= incr3;d++){
                        cost_amt = document.getElementById("emps_amt["+d+"]").value; if(cost_amt == ""){ cost_amt = 0; }
                        supr_flag = document.getElementById("supr_flag["+d+"]");
                        emps_eamt = 0;
                        if(supr_flag.checked == true){
                            emps_eamt = 100;
                        }
                        document.getElementById("emps_eamt["+d+"]").value = emps_eamt;
                        tot_empsal_amt = parseFloat(tot_empsal_amt) + parseFloat(cost_amt);
                        tot_emps_eamt = parseFloat(tot_emps_eamt) + parseFloat(emps_eamt);
                    }
                    document.getElementById("tot_empsal_amt").value = parseFloat(tot_empsal_amt).toFixed(2);
                    document.getElementById("tot_emps_eamt").value = parseFloat(tot_emps_eamt).toFixed(2);

                    //Add Labour Expense to Labout Advance
                    var incr2 = document.getElementById("incr2").value;
                    var tcoa = "";
                    //var temp_amt = parseFloat(tot_empsal_amt) + parseFloat(tot_emps_eamt);
                    var temp_amt = parseFloat(tot_empsal_amt);
                    for(var d = 0;d <= incr2;d++){
                        tcoa = "";
                        tcoa = document.getElementById("tcoa["+d+"]");
                        if(tcoa.options[tcoa.selectedIndex].text == "Labour Advance"){
                            document.getElementById("tcoa_amt["+d+"]").value = parseFloat(temp_amt).toFixed(2);
                        }
                    }
                    calculate_total_amt2();
                }
                /*function fetch_supplier_outstanding(){
                    var vcode = document.getElementById("vcode").value;
                    if(!vcode.match("select")){
                        var inv_items = new XMLHttpRequest();
                        var method = "GET";
                        var url = "supplier_fetch_balance.php?pname="+vcode;
                        //window.open(url);
                        var asynchronous = true;
                        inv_items.open(method, url, asynchronous);
                        inv_items.send();
                        inv_items.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var dval = this.responseText;
                                document.getElementById("out_balance").value = dval;
                            }
                        }
                    }
                    else{
                        document.getElementById("out_balance").value = "";
                    }
                }*/
                function update_item_fields(){
                    var incr = document.getElementById("incr").value;
                    var idx = "";
                    for(var d = 0;d <= incr;d++){ idx = ""; idx = "finaltotal["+d+"]"; update_row_fields(idx); }
                    calculate_total_amt();
                }
                update_item_fields(); calculate_total_amt2(); calculate_empsal_amt2();
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
		    <script src="handle_ebtn_as_tbtn.js"></script>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
