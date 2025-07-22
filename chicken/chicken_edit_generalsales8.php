<?php
//chicken_edit_generalsales8.php
include "newConfig.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $fdate = date("Y-m-d"); $tcds_per = 0;
    $sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$fdate' AND `tdate` >= '$fdate' AND `type` = 'TCS' AND `active` = '1' AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $tcds_per = $row['tcds']; }
    
    $sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'";
    $query = mysqli_query($conn,$sql); $jals_flag = $birds_flag = $tweight_flag = $eweight_flag = 0;
    while($row = mysqli_fetch_assoc($query)){ $jals_flag = $row['jals_flag']; $birds_flag = $row['birds_flag']; $tweight_flag = $row['tweight_flag']; $eweight_flag = $row['eweight_flag']; }
    if($jals_flag == ""){ $jals_flag = 0; } if($birds_flag == ""){ $birds_flag = 0; } if($tweight_flag == ""){ $tweight_flag = 0; } if($eweight_flag == ""){ $eweight_flag = 0; }

    $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'chicken_display_generalsales8.php' AND `field_function` LIKE 'Show Item Short Name and Item Name Together' AND `user_access` LIKE 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $disn_flag = mysqli_num_rows($query);

	$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Item Master' AND `field_function` LIKE 'Short-Name' AND `user_access` LIKE 'all' AND `flag` = '1'";
	$query = mysqli_query($conn,$sql); $sname_flag = mysqli_num_rows($query);

    $sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
    while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; if((int)$disn_flag > 0 && (int)$sname_flag > 0){ $item_name[$row['code']] = $row['short_name'].". ".$row['description']; } else{ $item_name[$row['code']] = $row['description']; } }

    //Sector Access
    $emp_code = $_SESSION['userid'];
    $sql = "SELECT * FROM `main_access` WHERE `empcode` = '$emp_code' AND `active` = '1' ORDER BY `id` ASC";
    $query = mysqli_query($conn,$sql); $loc_access = $sec_list = $sec_fltr = ""; $sec_aflag = 0;
    while($row = mysqli_fetch_assoc($query)){ $loc_access = $row['loc_access']; }
    if($loc_access != "all"){
        $lval1 = explode(",",$loc_access);
        foreach($lval1 as $lval2){ if($lval2 == "all"){ $sec_aflag = 1; } if($sec_list == ""){ $sec_list = $lval2; } else{ $sec_list = $sec_list."','".$lval2; } }
    }
    else{ $sec_aflag = 1; }
    if((int)$sec_aflag == 0){ $sec_fltr = " AND `code` IN ('$sec_list')"; }
    
    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'".$sec_fltr." ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
    while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }
    
    $sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' AND `transport_flag` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $tport_code = $tport_name = array();
    while($row = mysqli_fetch_assoc($query)){ $tport_code[$row['code']] = $row['code']; $tport_name[$row['code']] = $row['description']; }

     $sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%C%' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $grp_code = $grp_name = array();
    while($row = mysqli_fetch_assoc($query)){ $grp_code[$row['code']] = $row['code']; $grp_name[$row['code']] = $row['description']; }

    $grp_list = implode("','",$grp_code);
    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `groupcode` IN ('$grp_list') AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        // $cus_code[$row['code']] = $row['code'];
        // $cus_name[$row['code']] = $row['name'];
        $cus_group[$row['code']] = $row['groupcode'];
    }
    $sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'chicken_display_generalsales8.php' AND `field_function` = 'Group Check' AND `flag` = '1' ORDER BY `id` ASC";
    $query = mysqli_query($conn,$sql); $gr_flag = mysqli_num_rows($query);
    // while($row = mysqli_fetch_assoc($query)){ $grp_code[$row['code']] = $row['code']; $grp_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash') AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $cash_code = $cash_name = array();
    while($row = mysqli_fetch_assoc($query)){ $cash_code[$row['code']] = $row['code']; $cash_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Bank') AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $bank_code = $bank_name = array();
    while($row = mysqli_fetch_assoc($query)){ $bank_code[$row['code']] = $row['code']; $bank_name[$row['code']] = $row['description']; }

?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
        </head>
        <body>
            <?php
            $ids = $_GET['trnum'];
            $sql = "SELECT * FROM `customer_sales` WHERE `invoice` = '$ids' AND `flag` = '0' AND `tdflag` = '0' AND `pdflag` = '0' AND `trlink` = 'chicken_display_generalsales8.php'";
            $query = mysqli_query($conn,$sql); $c = 0;
            while($row = mysqli_fetch_assoc($query)){
                $date = $row['date'];
                $trnum = $row['invoice'];
                $vcode = $row['customercode'];
                $jali_no = $row['jali_no'];
                $bookinvoice = $row['bookinvoice'];
                $itemcode[$c] = $row['itemcode'];
                $jals[$c] = round($row['jals'],5);
                $birds[$c] = round($row['birds'],5);
                $totalweight[$c] = round($row['totalweight'],5);
                $emptyweight[$c] = round($row['emptyweight'],5);
                $netweight[$c] = round($row['netweight'],5);
                $warehouse = $row['warehouse'];
                $price[$c] = round($row['itemprice'],5);
                $amount[$c] = round($row['totalamt'],5);

                $tcdsper = round($row['tcdsper'],2);
                $tcds_type1 = $row['tcds_type1'];
                $tcds_type2 = $row['tcds_type2'];
                $tcdsamt = round($row['tcdsamt'],2);
                $tports_code = $row['transporter_code'];
                $freight_amount = round($row['freight_amount'],2);
                $dressing_charge = round($row['dressing_charge'],2);
                $roundoff_type1 = $row['roundoff_type1'];
                $roundoff_type2 = $row['roundoff_type2'];
                $roundoff = round($row['roundoff'],2);
                $finaltotal = round($row['finaltotal'],2);
                $driver = $row['drivercode'];
                $vehicle = $row['vehiclecode'];
                $remarks = $row['remarks'];
                $remarks2[$c] = $row['remarks2'];
                $c++;
            } $c = $c - 1;

            //Fetch Account Modes
            $sql = "SELECT * FROM `acc_modes` WHERE `description` IN ('Cash','Bank') AND `active` = '1' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); $cash_mode = $bank_mode = "";
            while($row = mysqli_fetch_assoc($query)){ if($row['description'] == "Cash"){ $cash_mode = $row['code']; } else if($row['description'] == "Bank"){ $bank_mode = $row['code']; } }
            
            $sql = "SELECT * FROM `customer_receipts` WHERE `link_trnum` = '$ids' AND `mode` = '$cash_mode' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`trnum`,`id` ASC";
            $query = mysqli_query($conn,$sql); $cash_ramt = 0; $cash_trno = $cash_rcode = "";
            while($row = mysqli_fetch_assoc($query)){ $cash_trno = $row['trnum']; $cash_rcode = $row['method']; $cash_ramt = round($row['amount'],5); }

            $sql = "SELECT * FROM `customer_receipts` WHERE `link_trnum` = '$ids' AND `mode` = '$bank_mode' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`trnum`,`id` ASC";
            $query = mysqli_query($conn,$sql); $bank_ramt = 0; $bank_trno = $bank_rcode = "";
            while($row = mysqli_fetch_assoc($query)){ $bank_trno = $row['trnum']; $bank_rcode = $row['method']; $bank_ramt = round($row['amount'],5); }

            ?>
            <div class="card border-secondary mb-3">
                <div class="card-header">Edit Sales</div>
                <form action="chicken_modify_generalsales8.php" method="post" onsubmit="return checkval();">
                    <div class="card-body">
                        <div class="row justify-content-center align-items-center">
                            <div class="form-group" style="width:110px;">
                                <label for="date">Date<b style="color:red;">&nbsp;*</b></label>
                                <input type="text" name="date" id="date" class="form-control sale_datepickers" value="<?php echo date("d.m.Y",strtotime($date)); ?>" style="width:100px;" onchange="fetch_tcds_per(this.id);" readonly />
                            </div>
                            <div class="form-group" style="width:130px;">
                                <label for="trnum">Invoice</label>
                                <input type="text" name="trnum" id="trnum" class="form-control" value="<?php echo $trnum; ?>" style="width:120px;" readonly />
                            </div>
                            <div class="form-group" style="width:290px;">
                                <label for="vcode">Customer<b style="color:red;">&nbsp;*</b></label>
                                <select name="vcode" id="vcode" class="form-control select2" style="width:280px;" onchange="fetch_customer_outstanding();">
                                    <option value="select">-select-</option>
                                    <?php foreach($cus_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($vcode == $scode){ echo "selected"; } ?>><?php echo $cus_name[$scode]; ?></option><?php } ?>
                                </select>
                            </div>
                            <div class="form-group" style="width:290px;">
                                <label for="warehouse">Warehouse<b style="color:red;">&nbsp;*</b></label>
                                <select name="warehouse" id="warehouse" class="form-control select2" style="width:280px;" onchange="fetch_customer_outstanding();">
                                    <option value="select">-select-</option>
                                    <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($warehouse == $scode){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option><?php } ?>
                                </select>
                            </div>
                            <!-- <?php //$groups = $_POST['groups'] ?? $_GET['groups'] ?? 'SCG-0017'; ?> -->
                            <?php if($gr_flag > 0){ ?>
                            <div class="form-group" style="width:290px;">
                                <label for="groups">Groups<b style="color:red;">&nbsp;*</b></label>
                                <select name="groups" id="groups" class="form-control select2" style="width:280px;" onchange="fetch_customer_groups(this.id);">
                                    <option value="select">-select-</option>
                                    <?php foreach($grp_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($cus_group[$vcode] == $scode){ echo "selected"; } ?>><?php echo $grp_name[$scode]; ?></option><?php } ?>
                                </select>
                            </div> <?php } ?>
                            <div class="form-group" style="width:110px;">
                                <label for="bookinvoice">Book Invoice</label>
                                <input type="text" name="bookinvoice" id="bookinvoice" class="form-control" value="<?php echo $bookinvoice; ?>" style="width:100px;" />
                            </div>
                            <div class="form-group" style="width:110px;">
                                <label for="vehicle">Vehicle</label>
                                <input type="text" name="vehicle" id="vehicle" class="form-control" value="<?php echo $vehicle; ?>" style="width:100px;" />
                            </div>
                            <div class="form-group" style="width:110px;">
                                <label for="driver">Driver</label>
                                <input type="text" name="driver" id="driver" class="form-control" value="<?php echo $driver; ?>" style="width:100px;" />
                            </div>
                            <div class="form-group" style="width:140px;visibility:hidden;">
                                <label for="out_balance">Balance</label>
                                <input type="text" name="out_balance" id="out_balance" class="form-control text-right" style="width:130px;" readonly />
                            </div>
                            <div class="form-group" style="width:130px;visibility:hidden;">
                                <label for="jali_no">JALI No.</label>
                                <input type="text" name="jali_no" id="jali_no" class="form-control" value="<?php echo $jali_no; ?>" style="width:120px;" />
                            </div>
                        </div>
                        <div class="row">
                            <table align="center">
                                <thead>
                                    <tr>
                                        <th>Item<b style="color:red;">&nbsp;*</b></th>
                                        <?php
                                        if((int)$jals_flag == 1){ echo '<th>Jals</th>'; }
                                        if((int)$birds_flag == 1){ echo '<th>Birds</th>'; }
                                        if((int)$tweight_flag == 1){ echo '<th>T. Weight</th>'; }
                                        if((int)$eweight_flag == 1){ echo '<th>E. Weight</th>'; }
                                        ?>
                                        <th>N. Weight<b style="color:red;">&nbsp;*</b></th>
                                        <th>Price<b style="color:red;">&nbsp;*</b></th>
                                        <th>Amount</th>
                                        <?php if($gr_flag > 0){?>
                                            <th>Remarks2</th>
                                        <?php } ?>
                                        <th style="width:70px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                <?php $incr = $c; for($c = 0;$c <= $incr;$c++){  ?>
                                    <tr style="margin:5px 0px 5px 0px;" id="row_no[<?php echo $c; ?>]">
                                        <td><select name="itemcode[]" id="itemcode[<?php echo $c; ?>]" class="form-control select2" data-row="<?php echo $c; ?>" data-col="0" style="width:180px;" onchange="update_row_fields(this.id);fetch_latest_customer_paperrate(this.id);"><option value="select">-select-</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($itemcode[$c] == $scode){ echo "selected"; } ?>><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>
                                        <?php
                                        $colIndex = 1;
                                        if((int)$jals_flag == 1){ echo '<td><input type="text" name="jals[]" id="jals['.$c.']" class="form-control text-right" data-row="'.$c.'" data-col="'.$colIndex++.'" value="'.$jals[$c].'" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" /></td>'; }
                                        if((int)$birds_flag == 1){ echo '<td><input type="text" name="birds[]" id="birds['.$c.']" class="form-control text-right" data-row="'.$c.'" data-col="'.$colIndex++.'" value="'.$birds[$c].'" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" /></td>'; }
                                        if((int)$tweight_flag == 1){ echo '<td><input type="text" name="tweight[]" id="tweight['.$c.']" class="form-control text-right" data-row="'.$c.'" data-col="'.$colIndex++.'" value="'.$totalweight[$c].'" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" /></td>'; }
                                        if((int)$eweight_flag == 1){ echo '<td><input type="text" name="eweight[]" id="eweight['.$c.']" class="form-control text-right" data-row="'.$c.'" data-col="'.$colIndex++.'" value="'.$emptyweight[$c].'" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" /></td>'; }
                                        ?>
                                        <td><input type="text" name="nweight[]" id="nweight[<?php echo $c; ?>]" class="form-control text-right" data-row="<?php echo $c; ?>" data-col="<?php echo $colIndex++; ?>" value="<?php echo $netweight[$c]; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="price[]" id="price[<?php echo $c; ?>]" class="form-control text-right" data-row="<?php echo $c; ?>" data-col="<?php echo $colIndex++; ?>" value="<?php echo $price[$c]; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="amount[]" id="amount[<?php echo $c; ?>]" class="form-control text-right" data-row="<?php echo $c; ?>" data-col="<?php echo $colIndex++; ?>" value="<?php echo $amount[$c]; ?>" style="width:90px;" readonly /></td>
                                        <?php if($gr_flag > 0){?>
                                        <td><textarea name="remarks2[]" id="remarks2[<?php echo $c; ?>]" class="form-control" data-row="<?php echo $c; ?>" data-col="<?php echo $colIndex++; ?>" style="height:25px;width:100px;"></textarea></td>
                                        <?php } ?>
                                        <?php
                                        if($c == $incr){ echo '<td id="action['.$c.']" style="padding-top: 5px;visibility:visible;">'; }
                                        else{ echo '<td id="action['.$c.']" style="padding-top: 5px;visibility:hidden;">'; }
                                        echo '<a href="javascript:void(0);" id="addrow['.$c.']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;';
                                        if($c > 0){ echo '<a href="javascript:void(0);" id="deductrow['.$c.']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a>'; }
                                        echo '</td>';
                                        ?>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th style="text-align:right;">Total</th>
                                        <?php
                                        $colspan = 3;
                                        if((int)$jals_flag == 1){ $colspan++; echo '<th><input type="text" name="tot_jals" id="tot_jals" class="form-control text-right" style="width:90px;" readonly /></th>'; }
                                        if((int)$birds_flag == 1){ $colspan++; echo '<th><input type="text" name="tot_birds" id="tot_birds" class="form-control text-right" style="width:90px;" readonly /></th>'; }
                                        if((int)$tweight_flag == 1){ $colspan++; echo '<th><input type="text" name="tot_tweight" id="tot_tweight" class="form-control text-right" style="width:90px;" readonly /></th>'; }
                                        if((int)$eweight_flag == 1){ $colspan++; echo '<th><input type="text" name="tot_eweight" id="tot_eweight" class="form-control text-right" style="width:90px;" readonly /></th>'; }
                                        ?>
                                        <th><input type="text" name="tot_nweight" id="tot_nweight" class="form-control text-right" style="width:90px;" readonly /></th>
                                        <th></th>
                                        <th><input type="text" name="tot_amount" id="tot_amount" class="form-control text-right" style="width:90px;" readonly /></th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="<?php echo $colspan - 1; ?>"></th>
                                        <th>Net Amount</th>
                                        <th><input type="text" name="finaltotal" id="finaltotal" class="form-control text-right" value="<?php echo $finaltotal;?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" readonly /></th>
                                    </tr>
                                    <tr style="visibility:hidden;">
                                        <th colspan="<?php echo $colspan; ?>" style="padding-top:0px;text-align:right;">
                                            <table align="right">
                                                <tr>
                                                    <th>
                                                        <label>Freight: Transporter</label>
                                                    </th>
                                                    <th>
                                                        <select name="transporter_code" id="transporter_code" style="width: 230px;"  class="form-control select2">
                                                            <?php foreach($tport_code as $tcode){ ?><option value="<?php echo $tcode;?>" <?php if($tports_code == $tcode){ echo "selected"; } ?>><?php echo $tport_name[$tcode];?></option><?php } ?>
                                                        </select>
                                                    </th>
                                                </tr>
                                            </table>
                                        </th>
                                        <th><input type="text" name="freight_amt" id="freight_amt" class="form-control text-right" value="<?php echo $freight_amount; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" /></th>
                                    </tr>
                                    <tr style="visibility:hidden;">
                                        <th colspan="<?php echo $colspan; ?>" style="padding-top:0px;text-align:right;">
                                            <table align="right">
                                                <tr>
                                                    <th style="visibility:hidden;"><input type="text" name="cash_trno" id="cash_trno" class="form-control" value="<?php echo $cash_trno; ?>" style="width:110px;" readonly /></th>
                                                    <th>
                                                        <label>Cash: </label>
                                                    </th>
                                                    <th>
                                                        <select name="cash_rcode" id="cash_rcode" style="width: 230px;"  class="form-control select2">
                                                            <option value="select">-select-</option>
                                                            <?php foreach($cash_code as $tcode){ ?><option value="<?php echo $tcode;?>" <?php if($tcode == $cash_rcode){ echo "selected"; } ?>><?php echo $cash_name[$tcode];?></option><?php } ?>
                                                        </select>
                                                    </th>
                                                </tr>
                                            </table>
                                        </th>
                                        <th><input type="text" name="cash_ramt" id="cash_ramt" class="form-control text-right" value="<?php echo $cash_ramt; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" /></th>
                                    </tr>
                                    <tr style="visibility:hidden;">
                                        <th colspan="<?php echo $colspan; ?>" style="padding-top:0px;text-align:right;">
                                            <table align="right">
                                                <tr>
                                                    <th style="visibility:hidden;"><input type="text" name="bank_trno" id="bank_trno" class="form-control" value="<?php echo $bank_trno; ?>" style="width:110px;" readonly /></th>
                                                    <th>
                                                        <label>Bank: </label>
                                                    </th>
                                                    <th>
                                                        <select name="bank_rcode" id="bank_rcode" style="width: 230px;"  class="form-control select2">
                                                            <option value="select">-select-</option>
                                                            <?php foreach($bank_code as $tcode){ ?><option value="<?php echo $tcode;?>" <?php if($tcode == $bank_rcode){ echo "selected"; } ?>><?php echo $bank_name[$tcode];?></option><?php } ?>
                                                        </select>
                                                    </th>
                                                </tr>
                                            </table>
                                        </th>
                                        <th><input type="text" name="bank_ramt" id="bank_ramt" class="form-control text-right" value="<?php echo $bank_ramt; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" /></th>
                                    </tr>
                                    <tr style="visibility:hidden;">
                                        <th colspan="<?php echo $colspan; ?>" style="padding-top:0px;text-align:right;">
                                            <label>Dressing Charges</label>
                                        </th>
                                        <th><input type="text" name="dressing_charge" id="dressing_charge" class="form-control text-right" value="<?php echo $dressing_charge; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" /></th>
                                    </tr>
                                    <tr style="visibility:hidden;">
                                        <th colspan="<?php echo $colspan; ?>" style="padding-top:0px;text-align:right;">
                                            <table align="right">
                                                <tr>
                                                    <th>
                                                        <div class="m-0 p-0 form-group">
                                                            <label for="">TCS</label>
                                                            <input type="checkbox" name="tcds_chk" id="tcds_chk" <?php if((float)$tcdsamt > 0){ echo "checked"; } ?> onchange="calculate_tcds_amt()" />
                                                            <input type="text" name="tcds_per" id="tcds_per" value="<?php echo $tcds_per; ?>" style="width:10px;visibility:hidden;" readonly />
                                                        </div>
                                                    </th>
                                                    <th>
                                                        <div class="m-0 p-0 form-group">
                                                            <label for="">Type-1</label>
                                                            <select name="tcds_type1" id="tcds_type1" class="form-control select2" style="width:90px;" onchange="calculate_tcds_amt()">
                                                                <option value="auto" <?php if((float)$tcds_type1 == "auto"){ echo "selected"; } ?>>Auto</option>
                                                                <option value="manual" <?php if((float)$tcds_type1 == "manual"){ echo "selected"; } ?>>Manual</option>
                                                            </select>
                                                        </div>
                                                    </th>
                                                    <th>
                                                        <div class="m-0 p-0 form-group">
                                                            <label for="">Type-2</label>
                                                            <select name="tcds_type2" id="tcds_type2" class="form-control select2" style="width:90px;" onchange="calculate_tcds_amt()">
                                                                <option value="add" <?php if((float)$tcds_type2 == "add"){ echo "selected"; } ?>>Add</option>
                                                                <option value="deduct" <?php if((float)$tcds_type2 == "deduct"){ echo "selected"; } ?>>Deduct</option>
                                                            </select>
                                                        </div>
                                                    </th>
                                                </tr>
                                            </table>
                                        </th>
                                        <th><div class="m-0 p-0 form-group"><input type="text" name="tcds_amt" id="tcds_amt" class="form-control text-right" value="<?php echo $tcdsamt; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" readonly /></div></th>
                                    </tr>
                                    <tr style="visibility:hidden;">
                                        <th colspan="<?php echo $colspan; ?>" style="padding-top:0px;text-align:right;">
                                            <table align="right">
                                                <tr>
                                                    <th>
                                                        <label for="">Round-Off</label>
                                                    </th>
                                                    <th>
                                                        <label for="">Type-1</label>
                                                        <select name="roundoff_type1" id="roundoff_type1" class="form-control select2" style="width:90px;" onchange="calculate_final_totalamt()">
                                                            <option value="auto" <?php if($roundoff_type1 == "auto"){ echo "selected"; } ?>>Auto</option>
                                                            <option value="manual" <?php if($roundoff_type1 == "manual"){ echo "selected"; } ?>>Manual</option>
                                                        </select>
                                                    </th>
                                                    <th>
                                                        <label for="">Type-2</label>
                                                        <select name="roundoff_type2" id="roundoff_type2" class="form-control select2" style="width:90px;" onchange="calculate_final_totalamt()">
                                                            <option value="add" <?php if($roundoff_type2 == "add"){ echo "selected"; } ?>>Add</option>
                                                            <option value="deduct" <?php if($roundoff_type2 == "deduct"){ echo "selected"; } ?>>Deduct</option>
                                                        </select>
                                                    </th>
                                                </tr>
                                            </table>
                                        </th>
                                        <th><input type="text" name="roundoff_amt" id="roundoff_amt" class="form-control text-right" value="<?php echo $roundoff;?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" readonly /></th>
                                   </tr>
                                </tfoot>
                            </table>
                        </div><br/>
                        <div class="row" style="margin-bottom:3px;">
                            <div class="col-md-4 form-group"></div>
                            <div class="col-md-4 form-group">
                                <label>Remarks</label>
                                <textarea name="remarks" id="remarks" class="form-control" style="height:75px;"><?php echo $remarks;?></textarea>
                            </div>
                            <div class="col-md-4 form-group"></div>
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
            <?php include "header_foot1.php"; ?>
            <script>
                function return_back(){
                    window.location.href = "chicken_display_generalsales8.php";
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1";
                    document.getElementById("submit").style.visibility = "hidden";
                    document.getElementById("sub_pt").style.visibility = "hidden";
                    var date = document.getElementById("date").value;
                    var vcode = document.getElementById("vcode").value;
                    var warehouse = document.getElementById("warehouse["+d+"]").value;
                    var cash_rcode = document.getElementById("cash_rcode").value;
                    var cash_ramt = document.getElementById("cash_ramt").value; if(cash_ramt == ""){ cash_ramt = 0; }
                    var bank_rcode = document.getElementById("bank_rcode").value;
                    var bank_ramt = document.getElementById("bank_ramt").value; if(bank_ramt == ""){ bank_ramt = 0; }
                    var l = true;

                    if(date == ""){
                        alert("Please select date");
                        document.getElementById("date").focus();
                        l = false;
                    }
                    else if(vcode == "select"){
                        alert("Please select Customer");
                        document.getElementById("vcode").focus();
                        l = false;
                    }
                    else if(warehouse == "select"){
                        alert("Please select Warehouse");
                        document.getElementById("warehouse").focus();
                        l = false;
                    }
                    else if(parseFloat(cash_ramt) > 0 && cash_rcode == "" || parseFloat(cash_ramt) > 0 && cash_rcode == "select"){
                        alert("Please select Cash Code");
                        document.getElementById("cash_rcode").focus();
                        l = false;
                    }
                    else if(parseFloat(bank_ramt) > 0 && bank_rcode == "" || parseFloat(bank_ramt) > 0 && bank_rcode == "select"){
                        alert("Please select Bank Code");
                        document.getElementById("bank_rcode").focus();
                        l = false;
                    }
                    else{
                        var itemcode = ""; var c = nweight = price = amount = 0;
                        var incr = document.getElementById("incr").value;
                        for(var d = 0;d <= incr;d++){
                            if(l == true){
                                c = d + 1;
                                itemcode = document.getElementById("itemcode["+d+"]").value;
                                nweight = document.getElementById("nweight["+d+"]").value; if(nweight == ""){ nweight = 0; }
                                price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }
                                amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }
                                

                                if(itemcode == "select"){
                                    alert("Please select Item in row: "+c);
                                    document.getElementById("itemcode["+d+"]").focus();
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
                                else if(parseFloat(amount) == 0){
                                    alert("Please enter price/Weight in row: "+c);
                                    document.getElementById("amount["+d+"]").focus();
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
                        document.getElementById("sub_pt").style.visibility = "visible";
                        document.getElementById("ebtncount").value = "0";
                        return false;
                    }
                }
                function calculate_tcds_amt(){
                    var tcds_chk = document.getElementById("tcds_chk");
                    var tcds_amt = 0;
                    if(tcds_chk.checked == true){
                        var tcds_per = document.getElementById("tcds_per").value;
                        var tcds_type1 = document.getElementById("tcds_type1").value;
                        if(tcds_type1 == "auto"){
                            document.getElementById("tcds_amt").readOnly = true;
                            var tot_amount = document.getElementById("tot_amount").value; if(tot_amount == ""){ tot_amount = 0; }
                            tcds_amt = ((parseFloat(tcds_per) / 100) * parseFloat(tot_amount));
                            document.getElementById("tcds_amt").value = parseFloat(tcds_amt).toFixed(2);
                            calculate_final_totalamt();
                        }
                        else{
                            document.getElementById("tcds_amt").readOnly = false;
                            calculate_final_totalamt();
                        }
                    }
                    else{
                        document.getElementById("tcds_amt").value = 0;
                        calculate_final_totalamt();
                    }
                }
                function fetch_tcds_per(){
                    var date = document.getElementById("date").value;
                    var tcds_fch = new XMLHttpRequest();
                    var method = "GET";
                    var url = "main_gettcdsvalue.php?type=TCS&cdate="+date;
                    var asynchronous = true;
                    tcds_fch.open(method, url, asynchronous);
                    tcds_fch.send();
                    tcds_fch.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var tcs = this.responseText;
                            if(tcs != ""){
                                document.getElementById("tcds_per").value = tcs;
                            }
                        }
                    }
                }
                function create_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("action["+d+"]").style.visibility = "hidden";
                    d++; var html = ''; var colIndex = 0;
                    document.getElementById("incr").value = d;

                    var jals_flag = '<?php echo $jals_flag; ?>';
                    var birds_flag = '<?php echo $birds_flag; ?>';
                    var tweight_flag = '<?php echo $tweight_flag; ?>';
                    var eweight_flag = '<?php echo $eweight_flag; ?>';

                    html += '<tr id="row_no['+d+']">';
                    html += '<td><select name="itemcode[]" id="itemcode['+d+']" class="form-control select2" data-row="'+d+'" data-col="'+(colIndex++)+'" style="width:180px;" onchange="update_row_fields(this.id);fetch_latest_customer_paperrate(this.id);"><option value="select">-select-</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>';
                    if(parseInt(jals_flag) == 1){ html += '<td><input type="text" name="jals[]" id="jals['+d+']" class="form-control text-right" data-row="'+d+'" data-col="'+(colIndex++)+'" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" /></td>'; }
                    if(parseInt(birds_flag) == 1){ html += '<td><input type="text" name="birds[]" id="birds['+d+']" class="form-control text-right" data-row="'+d+'" data-col="'+(colIndex++)+'" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" /></td>'; }
                    if(parseInt(tweight_flag) == 1){ html += '<td><input type="text" name="tweight[]" id="tweight['+d+']" class="form-control text-right" data-row="'+d+'" data-col="'+(colIndex++)+'" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" /></td>'; }
                    if(parseInt(eweight_flag) == 1){ html += '<td><input type="text" name="eweight[]" id="eweight['+d+']" class="form-control text-right" data-row="'+d+'" data-col="'+(colIndex++)+'" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" /></td>'; }

                    html += '<td><input type="text" name="nweight[]" id="nweight['+d+']" class="form-control text-right" data-row="'+d+'" data-col="'+(colIndex++)+'" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><input type="text" name="price[]" id="price['+d+']" class="form-control text-right" data-row="'+d+'" data-col="'+(colIndex++)+'" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><input type="text" name="amount[]" id="amount['+d+']" class="form-control text-right" data-row="'+d+'" data-col="'+(colIndex++)+'" style="width:90px;" onkeyup="validate_num(this.id);" onchange="validate_amount(this.id);" readonly /></td>';
                    if(gr_flag > 0){ 
                        html += '<td><textarea name="remarks2[]" id="remarks2['+d+']" class="form-control" data-row="'+d+'" data-col="'+(colIndex++)+'" style="height:25px;width:100px;"></textarea></td>';
                    }
                    html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
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
                    calculate_final_totalamt();
                }
                function calculate_final_totalamt(){
                    var incr = document.getElementById("incr").value;
                    var jals_flag = '<?php echo $jals_flag; ?>';
                    var birds_flag = '<?php echo $birds_flag; ?>';
                    var tweight_flag = '<?php echo $tweight_flag; ?>';
                    var eweight_flag = '<?php echo $eweight_flag; ?>';

                    var jals = birds = tweight = eweight = nweight = price = amount = bird_flag = 0;
                    var tot_jals = tot_birds = tot_tweight = tot_eweight = tot_nweight = tot_amount = bird_flag = 0;
                    var icode = iname = "";
                    for(var d = 0;d <= incr;d++){
                        jals = birds = tweight = eweight = nweight = price = amount = bird_flag = 0;
                        icode = document.getElementById("itemcode["+d+"]");
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
                       // var price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }
                       // var amount = parseFloat(nweight) * parseFloat(price);
                       // document.getElementById("amount["+d+"]").value = parseFloat(amount).toFixed(2);
                        var amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }

                        tot_jals = parseFloat(tot_jals) + parseFloat(jals);
                        tot_birds = parseFloat(tot_birds) + parseFloat(birds);
                        tot_tweight = parseFloat(tot_tweight) + parseFloat(tweight);
                        tot_eweight = parseFloat(tot_eweight) + parseFloat(eweight);
                        tot_nweight = parseFloat(tot_nweight) + parseFloat(nweight);
                        tot_amount = parseFloat(tot_amount) + parseFloat(amount);
                    }
                    if(parseInt(jals_flag) == 1){ document.getElementById("tot_jals").value = parseFloat(tot_jals).toFixed(0); }
                    if(parseInt(birds_flag) == 1){ document.getElementById("tot_birds").value = parseFloat(tot_birds).toFixed(0); }
                    if(parseInt(tweight_flag) == 1){ document.getElementById("tot_tweight").value = parseFloat(tot_tweight).toFixed(2); }
                    if(parseInt(eweight_flag) == 1){ document.getElementById("tot_eweight").value = parseFloat(tot_eweight).toFixed(2); }
                    document.getElementById("tot_nweight").value = parseFloat(tot_nweight).toFixed(2);
                    document.getElementById("tot_amount").value = parseFloat(tot_amount).toFixed(2);

                    /*calculate TCDS to Billing Amount*/
                    var tcds_chk = document.getElementById("tcds_chk");
                    var tcds_amt = net_amt = 0; var tcds_type2 = "";
                    if(tcds_chk.checked == true){
                        var tcds_per = document.getElementById("tcds_per").value;
                        var tcds_type1 = document.getElementById("tcds_type1").value;
                        if(tcds_type1 == "auto"){
                            document.getElementById("tcds_amt").readOnly = true;
                            var tot_amount = document.getElementById("tot_amount").value; if(tot_amount == ""){ tot_amount = 0; }
                            tcds_amt = ((parseFloat(tcds_per) / 100) * parseFloat(tot_amount));
                            document.getElementById("tcds_amt").value = parseFloat(tcds_amt).toFixed(2);
                        }
                        else{ document.getElementById("tcds_amt").readOnly = false; }

                        tcds_type2 = document.getElementById("tcds_type2").value;
                        tcds_amt = document.getElementById("tcds_amt").value;
                        if(tcds_type2 == "add"){ net_amt = parseFloat(tot_amount) + parseFloat(tcds_amt); } else{ net_amt = parseFloat(tot_amount) - parseFloat(tcds_amt); }
                    }
                    else{ document.getElementById("tcds_amt").value = 0; net_amt = parseFloat(tot_amount); }
                    
                    /*Freiht to Total Invoice*/
                    var freight_amt = document.getElementById("freight_amt").value; if(freight_amt == ""){ freight_amt = 0; }
                    net_amt = parseFloat(net_amt) + parseFloat(freight_amt);

                    /*Dressing Charges to Total Invoice*/
                    var dressing_charge = document.getElementById("dressing_charge").value; if(dressing_charge == ""){ dressing_charge = 0; }
                    net_amt = parseFloat(net_amt) + parseFloat(dressing_charge);

                    /*Round-Off Calculations*/
                    var rf_type1 = document.getElementById("roundoff_type1").value;
                    if(rf_type1 == "auto"){
                        document.getElementById("roundoff_amt").readOnly = true;
                        var t_amt = parseFloat(net_amt).toFixed(0);
                        var roundoff_amt = parseFloat(t_amt) - parseFloat(net_amt);
                        if(roundoff_amt > 0){
                            $('#roundoff_type2').select2();
                            document.getElementById("roundoff_type2").value = "add";
                            $('#roundoff_type2').select2();
                        }
                        else{
                            $('#roundoff_type2').select2();
                            document.getElementById("roundoff_type2").value = "deduct";
                            $('#roundoff_type2').select2();
                        }
                        document.getElementById("roundoff_amt").value = parseFloat(roundoff_amt).toFixed(2);
                        net_amt = parseFloat(net_amt) + parseFloat(roundoff_amt);
                    }
                    else{
                        document.getElementById("roundoff_amt").readOnly = false;
                        var roundoff_amt = document.getElementById("roundoff_amt").value; if(roundoff_amt == ""){ roundoff_amt = 0; }
                        var rf_type2 = document.getElementById("roundoff_type2").value;
                        if(rf_type2 == "add"){
                            net_amt = parseFloat(net_amt) + parseFloat(roundoff_amt);
                        }
                        else{
                            net_amt = parseFloat(net_amt) - parseFloat(roundoff_amt);
                        }
                    }
                    document.getElementById("finaltotal").value = parseFloat(net_amt).toFixed(2);
                }
                function update_row_fields(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var icode = document.getElementById("itemcode["+d+"]");
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
                    calculate_final_totalamt();
                }
                function fetch_customer_outstanding(){
                    var vcode = document.getElementById("vcode").value;
                    if(!vcode.match("select")){
                        var inv_items = new XMLHttpRequest();
                        var method = "GET";
                        var url = "cus_fetchoutstandingbal.php?cuscode="+vcode;
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
                }
                // Call this on your <select id="groups"> change
                function fetch_customer_groups() {
                    var groups = document.getElementById('groups').value;
                    var xhr    = new XMLHttpRequest();
                    var url    = 'fetch_customer_groups.php?groups=' + encodeURIComponent(groups);

                    xhr.open('GET', url, true);
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                        var cust = document.getElementById('vcode');
                        // replace your <select> options
                        cust.innerHTML = xhr.responseText;
                        // refresh Select2 if you’re using it
                        $(cust).trigger('change.select2');
                        }
                    };
                    xhr.send();
                }
                function fetch_latest_customer_paperrate(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var date = document.getElementById("date").value;
                    var vcode = document.getElementById("vcode").value;
                    var itemcode = document.getElementById("itemcode["+d+"]").value;
                    document.getElementById("price["+d+"]").value = "";

                    if(date == ""){
                        alert("Please select Date");
                        document.getElementById("date").focus();
                    }
                    else if(vcode == "select"){
                        alert("Please select Customer");
                        document.getElementById("vcode").focus();
                    }
                    else if(itemcode == "select"){
                        alert("Please select Item");
                        document.getElementById("itemcode["+d+"]").focus();
                    }
                    else{
                        var inv_items = new XMLHttpRequest();
                        var method = "GET";
                        var url = "chicken_fetch_latest_cuspaperrate.php?date="+date+"&vcode="+vcode+"&icode="+itemcode+"&row_cnt="+d;
                        //window.open(url);
                        var asynchronous = true;
                        inv_items.open(method, url, asynchronous);
                        inv_items.send();
                        inv_items.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var cus_dt1 = this.responseText;
                                var cus_dt2 = cus_dt1.split("@");
                                var price = cus_dt2[0];
                                var rows = cus_dt2[1];
                                if(price != ""){
                                    document.getElementById("price["+rows+"]").value = parseFloat(price).toFixed(2);
                                }
                            }
                        }
                    }
                }
                calculate_final_totalamt();
            </script>
		    <script src="chick_validate_basicfields.js"></script>
		    <script src="handle_ebtn_as_tbtn.js"></script>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
