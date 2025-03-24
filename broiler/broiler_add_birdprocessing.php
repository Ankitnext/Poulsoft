<?php
//broiler_add_birdprocessing.php
include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['birdprocessing'];
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
        $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $bird_processing = $row['bird_processing']; } $incr = $bird_processing + 1;
        if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
        $batch_no = "BCH-".$incr;
        $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Bird%' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; }
				
        $sql = "SELECT * FROM `item_details` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
		
        $pcats = $pitem_code = array();
        $sql = "SELECT * FROM `item_category` WHERE `active` = '1' AND `dflag` = '0' AND `bird_plant` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $pcats[$row['code']] = $row['code']; } $icat_list = implode("','", $pcats);
        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $pitem_code[$row['code']] = $row['code']; }
		
        $pcats = $pmitem_code = array();
        $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Packing Material%' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $pcats[$row['code']] = $row['code']; } $icat_list = implode("','", $pcats);
        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $pmitem_code[$row['code']] = $row['code']; }
				
		$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
        
        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Processing Plant' AND `field_function` LIKE 'Transaction: selection to display Transactions in Bird Processing screens. 1. Bird Received. 2. Purchase. 3. both'";
        $query = mysqli_query($conn,$sql); $trans_flag = 0; $count = mysqli_num_rows($query);
        if($count > 0){ while($row = mysqli_fetch_assoc($query)){ $trans_flag = $row['flag']; } } else{ $trans_flag = 0; } if($trans_flag == "" || $trans_flag == 0){ $trans_flag = 0; }
        
        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Processing Plant' AND `field_function` LIKE 'Bird Processing: Batch Auto Creation' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $bch_flag = 0; $bch_flag = mysqli_num_rows($query); if($bch_flag == "" || $bch_flag == 0){ $bch_flag = 0; }
        
        if(isset($_REQUEST['link_trnum']) == true && $_REQUEST['link_trnum'] != "select"){ $link_trnum = $_REQUEST['link_trnum']; $trans_type = $_REQUEST['trans_type']; } else{ $link_trnum = "select"; $trans_type = ""; }
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
            padding-left: 1px;
            padding-right: 1px;
            margin-right: 10px;
            height: 25px;
        }
    </style>
    </head>
    <body class="m-0 p-0 hold-transition">
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Bird Processing</h3></div>
                        </div>
                        <div class="p-0 pt-5 card-body">
                            <?php if($trans_flag > 0){ ?>
                            <div class="col-md-12">
                                <form action="broiler_add_birdprocessing.php" method="post" role="form" onsubmit="return checkval1()">
                                    <div class="row">
                                        <?php if($trans_flag == 1 || $trans_flag == 3){ ?>
                                        <div class="form-group" style="width:120px;text-align:center;">
                                            <label>Bird Received<b style="color:red;">&nbsp;*</b></label>
							                <input type="radio" name="trans_type" id="trans_type1" class="form-control" value="BR" style="transform: scale(.5);text-align:center;" <?php if($trans_type == "BR" || $trans_type == "" && $trans_flag == 1 || $trans_type == "" && $trans_flag == 3){ echo "checked"; } ?> onclick="fetch_transactions(this.id);" />
                                        </div>
                                        <?php } ?>
                                        <?php if($trans_flag == 2 || $trans_flag == 3){ ?>
                                        <div class="form-group" style="width:120px;text-align:center;">
                                            <label>Purchases<b style="color:red;">&nbsp;*</b></label>
							                <input type="radio" name="trans_type" id="trans_type2" class="form-control" value="PUR" style="transform: scale(.5);text-align:center;" <?php if($trans_type == "" && $trans_flag == 2 || $trans_type == "PUR"){ echo "checked"; } ?> onclick="fetch_transactions(this.id);" />
                                        </div>
                                        <?php } ?>
                                        <div class="form-group" style="width:240px;">
                                            <label>Transactions<b style="color:red;">&nbsp;*</b></label>
							                <select name="link_trnum" id="link_trnum" class="form-control select2" style="width:230px;" onchange="this.form.submit();">
                                                <option value="select">select</option>
                                                <?php
                                                if($trans_type == "BR" || $trans_type == "" && $trans_flag == 1 || $trans_type == "" && $trans_flag == 3){
                                                    $sql = "SELECT * FROM `broiler_bird_receivedin` WHERE `item_code` = '$bird_code' AND `active` = '1' AND `dflag` = '0' AND `processed_flag` = '0' GROUP BY `trnum` ORDER BY `trnum` ASC";
                                                }
                                                else if($trans_type == "" && $trans_flag == 2 || $trans_type == "PUR"){
                                                    $sec_list = ""; $sec_list = implode("','", $sector_code);
                                                    $sql = "SELECT * FROM `broiler_purchases` WHERE `icode` = '$bird_code' AND `active` = '1' AND `dflag` = '0' AND `processed_flag` = '0' GROUP BY `trnum` ORDER BY `trnum` ASC";
                                                }
                                                    $query = mysqli_query($conn,$sql);
                                                    while($row = mysqli_fetch_assoc($query)){
                                                ?>
                                                <option value="<?php echo $row['trnum']; ?>" <?php if($link_trnum == $row['trnum']){echo "selected"; } ?>><?php echo $row['trnum']; ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <?php } ?>
                            <?php
                            if(isset($_REQUEST['link_trnum']) == true && $_REQUEST['link_trnum'] != "select"){
                            ?>
                            <div class="col-md-12">
                                <form action="broiler_save_birdprocessing.php" method="post" role="form" onsubmit="return checkval()">
                                    <?php
                                        if($_REQUEST['trans_type'] == "BR"){
                                            $sql = "SELECT * FROM `broiler_bird_receivedin` WHERE `trnum` = '$link_trnum' AND `active` = '1' AND `dflag` = '0' AND `processed_flag` = '0' GROUP BY `trnum` ORDER BY `trnum` ASC";
                                            $query = mysqli_query($conn,$sql);
                                            while($row = mysqli_fetch_assoc($query)){
                                                $date = $row['date'];
                                                $bs_no = $row['bs_no'];
                                                $dcno = $row['dcno'];
                                                $towarehouse = $row['towarehouse'];
                                                $rcvd_birds = $row['tot_rcvd_birds'];
                                                $rcvd_weight = $row['tot_rcvd_weight'];
                                                $avg_amount = $row['tot_avg_amount'];
                                            }
                                        }
                                        else if($_REQUEST['trans_type'] == "PUR"){
                                            $sql = "SELECT * FROM `broiler_purchases` WHERE `trnum` = '$link_trnum' AND `active` = '1' AND `dflag` = '0' AND `processed_flag` = '0' GROUP BY `trnum` ORDER BY `trnum` ASC";
                                            $query = mysqli_query($conn,$sql);
                                            while($row = mysqli_fetch_assoc($query)){
                                                $date = $row['date'];
                                                $bs_no = "";
                                                $dcno = $row['billno'];
                                                $towarehouse = $row['warehouse'];
                                                $rcvd_birds += (float)$row['birds'];
                                                $rcvd_weight += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                                                $avg_amount = (float)$row['item_tamt'];
                                            }
                                        }
                                        else{ }
                                    ?>
                                    <div class="row" style="text-align:center;" align="center">
                                        <div class="form-group">
                                            <label>Date<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="date" id="date" class="form-control datepicker" style="width:100px;" value="<?php echo date('d.m.Y'); ?>" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label>Received Date<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="rcvd_date" id="rcvd_date" class="form-control" style="width:100px;" value="<?php echo date('d.m.Y',strtotime($date)); ?>" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label>Processing No.</label>
							                <input type="text" name="dcno" id="dcno" class="form-control" value="<?php echo $dcno; ?>" style="width:110px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label>Bird Receiving No.</label>
							                <input type="text" name="bs_no" id="bs_no" class="form-control" value="<?php echo $bs_no; ?>" style="width:110px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:170px;">
                                            <label>Processing Unit<b style="color:red;">&nbsp;*</b></label>
							                <select name="warehouse" id="warehouse" class="form-control select2" style="width:160px;">
                                                <option value="<?php echo $towarehouse; ?>" selected ><?php echo $sector_name[$towarehouse]; ?></option>
                                            </select>
                                        </div>
                                        <div class="form-group" <?php if($bch_flag > 0){ } else{ echo 'style="visibility:hidden;"'; } ?>>
                                            <label>Batch No.</label>
							                <input type="text" name="batch_no" id="batch_no" class="form-control" value="<?php echo $batch_no; ?>" style="width:110px;" readonly />
                                        </div>
                                        <div class="form-group" style="visibility:hidden;">
                                            <label>Link No.</label>
							                <input type="text" name="l_trnum" id="l_trnum" class="form-control" value="<?php echo $link_trnum; ?>" style="width:110px;" readonly />
                                        </div>
                                        <div class="form-group" style="visibility:hidden;">
                                            <label>Type</label>
							                <input type="text" name="trtype" id="trtype" class="form-control" value="<?php echo $trans_type; ?>" style="width:110px;" readonly />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group" style="width:170px;">
                                            <label>Item<b style="color:red;">&nbsp;*</b></label>
							                <select name="item_code" id="item_code" class="form-control select2" style="width:160px;">
                                                <option value="select">select</option>
                                                <option value="<?php echo $bird_code; ?>" selected ><?php echo $item_name[$bird_code]; ?></option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Rcvd Birds</label>
							                <input type="text" name="rcvd_birds" id="rcvd_birds" class="form-control" value="<?php echo $rcvd_birds; ?>" placeholder="0" style="width:110px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label>Rcvd Weight</label>
							                <input type="text" name="rcvd_weight" id="rcvd_weight" class="form-control" value="<?php echo $rcvd_weight; ?>" placeholder="0" style="width:110px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label>Mort Birds</label>
							                <input type="text" name="mort_birds" id="mort_birds" class="form-control" placeholder="0.00" style="width:110px;" onkeyup="validatenum(this.id);calculate_netdetails();" />
                                        </div>
                                        <div class="form-group">
                                            <label>Mort Weight</label>
							                <input type="text" name="mort_weight" id="mort_weight" class="form-control" placeholder="0.00" style="width:110px;" onkeyup="validatenum(this.id);calculate_netdetails();" />
                                        </div>
                                        <div class="form-group">
                                            <label>Net Birds</label>
							                <input type="text" name="net_birds" id="net_birds" class="form-control" value="<?php echo $rcvd_birds; ?>" placeholder="0.00" style="width:110px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label>Net Weight</label>
							                <input type="text" name="net_weight" id="net_weight" class="form-control" value="<?php echo $rcvd_weight; ?>" placeholder="0.00" style="width:110px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label>Rcvd Amt</label>
							                <input type="text" name="avg_amount" id="avg_amount" class="form-control" value="<?php echo $avg_amount; ?>" style="width:110px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label>Avg. Price</label>
							                <input type="text" name="avg_price" id="avg_price" class="form-control" style="width:110px;" readonly />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12" align="center"><h5 style="color:red;">Out Products</h5></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2"></div>
                                        <div class="col-md-8">
                                        <table class="table1">
                                            <thead>
                                                <tr>
                                                    <th>Items</th>
                                                    <th>Packets</th>
                                                    <th>Weight</th>
                                                    <th>Yield (%)</th>
                                                    <th>Avg. Price</th>
                                                    <th>Avg. Amount</th>
                                                    <th style="width:60px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="row_body">
                                                <tr style="padding: 5px;line-height: 1.6;">
                                                    <td><select name="p_item[]" id="p_item[0]" class="form-control select2" style="width:160px;"><option value="select">-select-</option><?php foreach($pitem_code as $icode){ ?><option value="<?php echo $icode; ?>" ><?php echo $item_name[$icode]; ?></option><?php } ?></select></td>
                                                    <td><input type="text" name="p_packet[]" id="p_packet[0]" class="form-control" onkeyup="validatenum(this.id);calculate_total_qty();" onchange="validateamount(this.id);" /></td>
                                                    <td><input type="text" name="p_weight[]" id="p_weight[0]" class="form-control" onkeyup="validatenum(this.id);calculate_total_qty();" onchange="validateamount(this.id);" /></td>
                                                    <td><input type="text" name="p_yield[]" id="p_yield[0]" class="form-control" readonly /></td>
                                                    <td><input type="text" name="p_avgprc[]" id="p_avgprc[0]" class="form-control" readonly /></td>
                                                    <td><input type="text" name="p_avgamt[]" id="p_avgamt[0]" class="form-control" readonly /></td>
                                                    <td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onClick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="7"><br/></th>
                                                </tr>
                                                <tr>
                                                    <th>Total</th>
                                                    <th><input type="text" name="tp_packet" id="tp_packet" class="form-control" readonly /></th>
                                                    <th><input type="text" name="tp_weight" id="tp_weight" class="form-control" readonly /></th>
                                                    <th><input type="text" name="tp_yield" id="tp_yield" class="form-control" readonly /></th>
                                                    <th><input type="text" name="tp_avgprc" id="tp_avgprc" class="form-control" readonly /></th>
                                                    <th><input type="text" name="tp_avgamt" id="tp_avgamt" class="form-control" readonly /></th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                <tr>
                                                    <th colspan="7"><br/></th>
                                                </tr>
                                                    <th>Wastage</th>
                                                    <th><input type="text" name="twp_packet" id="twp_packet" class="form-control" readonly /></th>
                                                    <th><input type="text" name="twp_weight" id="twp_weight" class="form-control" readonly /></th>
                                                    <th><input type="text" name="twp_yield" id="twp_yield" class="form-control" readonly /></th>
                                                    <th><input type="text" name="twp_avgprc" id="twp_avgprc" class="form-control" readonly /></th>
                                                    <th><input type="text" name="twp_avgamt" id="twp_avgamt" class="form-control" readonly /></th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        </div>
                                        <div class="col-md-2"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12" align="center"><h5 style="color:red;">Other Packing Materials</h5></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2"></div>
                                        <div class="col-md-8">
                                        <table class="table1">
                                            <thead>
                                                <tr>
                                                    <th>Items</th>
                                                    <th>Stock</th>
                                                    <th>Quantity Used</th>
                                                    <th>Avg. Price</th>
                                                    <th>Avg. Amount</th>
                                                    <th style="width:60px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="row_body2">
                                                <tr style="padding: 5px;line-height: 1.6;">
                                                    <td><select name="packing_items[]" id="packing_items[0]" class="form-control select2" style="width:160px;" onchange="fetch_stock_master(this.id);"><option value="select">-select-</option><?php foreach($pmitem_code as $icode){ ?><option value="<?php echo $icode; ?>" ><?php echo $item_name[$icode]; ?></option><?php } ?></select></td>
                                                    <td><input type="text" name="packing_stock[]" id="packing_stock[0]" class="form-control" readonly /></td>
                                                    <td><input type="text" name="packing_qty[]" id="packing_qty[0]" class="form-control" onkeyup="validatenum(this.id);calculate_total_qty2();" onchange="validateamount(this.id);" /></td>
                                                    <td><input type="text" name="packing_avgprc[]" id="packing_avgprc[0]" class="form-control" readonly /></td>
                                                    <td><input type="text" name="packing_avgamt[]" id="packing_avgamt[0]" class="form-control" readonly /></td>
                                                    <td id="action2[0]"><a href="javascript:void(0);" id="addrow2[0]" onClick="create_row2(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Total</th>
                                                    <th><input type="text" name="tpk_stock" id="tpk_stock" class="form-control" readonly /></th>
                                                    <th><input type="text" name="tpk_qty" id="tpk_qty" class="form-control" readonly /></th>
                                                    <th><input type="text" name="tpk_prc" id="tpk_prc" class="form-control" readonly /></th>
                                                    <th><input type="text" name="tpk_amt" id="tpk_amt" class="form-control" readonly /></th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        </div>
                                        <div class="col-md-2"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Remarks</label>
                                                <textarea name="remarks" id="remarks" class="form-control" style="width:100%;"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-1" style="visibility:hidden;">
                                            <label>Incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="0">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;">
                                            <label>Incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="incr2" id="incr2" class="form-control" value="0">
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
                            <?php
                            }
                            ?>
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
                window.location.href = 'broiler_display_birdprocessing.php?ccid='+ccid;
            }
            function checkval1(){
                var link_trnum = document.getElementById("link_trnum").value;
                if(link_trnum == "select"){
                    return false;
                }
                else{
                    return true;
                }
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var incr = document.getElementById("incr").value;
                var l = true; var p_weight = tot_pqty = b = 0;
                var link_trnum = document.getElementById("link_trnum").value;
                var warehouse = document.getElementById("warehouse").value;
                //var birds = document.getElementById("net_birds").value;
                var weight = document.getElementById("net_weight").value;
                
                if(link_trnum.match("select")){
                    alert("Kindly select Transaction No. to fetch Details");
                    document.getElementById("link_trnum").focus();
                    l = false;
                }
                else if(warehouse.match("select")){
                    alert("Kindly select Sector");
                    document.getElementById("warehouse").focus();
                    l = false;
                }
                /*else if(birds == "" || parseFloat(birds) == 0){
                    alert("Kindly enter Birds");
                    document.getElementById("net_birds").focus();
                    l = false;
                }*/
                else if(weight == "" || parseFloat(weight) == 0){
                    alert("Kindly enter Weight");
                    document.getElementById("net_weight").focus();
                    l = false;
                }
                else{
                    for(b = 0;b <= incr;b++){
                        p_weight = document.getElementById("p_weight["+b+"]").value; if(p_weight == ""){ p_weight = 0; }
                        if(parseFloat(p_weight) != 0){
                            tot_pqty = parseFloat(tot_pqty) + parseFloat(p_weight);
                        }
                    }
                    if(parseFloat(tot_pqty) == 0){
                        alert("Kindly enter Produced Quantity");
                        document.getElementById("p_weight[0]").focus();
                        l = false;
                    }
                }
                if(l == true){
                    //Stock Check
                    var stockcheck_flag = '<?php echo $stockcheck_flag; ?>';
                    if(stockcheck_flag == 1){
                        for(b = 0;b <= incr;b++){
                            if(l == true){
                                var stock = document.getElementById("packing_stock["+b+"]").value; if(stock == ""){ stock = 0; }
                                var qty = document.getElementById("packing_qty["+b+"]").value; if(qty == ""){ qty = 0; }
                                if(parseFloat(qty) > parseFloat(stock)){
                                    alert("Stock not Available");
                                    document.getElementById("packing_qty["+b+"]").focus();
                                    l = false;
                                }
                            }
                        }
                    }
                    else{ }
                }
                if(l == true){
                    var answer = window.confirm("Are You Sure! You want to Save The Transaction.");
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
            function calculate_netdetails(){
                var rcvd_birds = document.getElementById("rcvd_birds").value; if(rcvd_birds == ""){ rcvd_birds = 0; }
                var rcvd_weight = document.getElementById("rcvd_weight").value; if(rcvd_weight == ""){ rcvd_weight = 0; }
                var mort_birds = document.getElementById("mort_birds").value; if(mort_birds == ""){ mort_birds = 0; }
                var mort_weight = document.getElementById("mort_weight").value; if(mort_weight == ""){ mort_weight = 0; }
                var net_birds = net_weight = 0;

                net_birds = parseFloat(rcvd_birds) - parseFloat(mort_birds);
                net_weight = parseFloat(rcvd_weight) - parseFloat(mort_weight);
                document.getElementById("net_birds").value = parseFloat(net_birds).toFixed(0);
                document.getElementById("net_weight").value = parseFloat(net_weight).toFixed(3);
            }
            function calculate_total_qty(){
                var weight = document.getElementById("net_weight").value; if(weight == ""){ weight = 0; }
                var avg_amount = document.getElementById("avg_amount").value; if(avg_amount == ""){ avg_amount = 0; }
                var avg_price = parseFloat(avg_amount) / parseFloat(weight);
                document.getElementById("avg_price").value = parseFloat(avg_price).toFixed(5);
                var incr = document.getElementById("incr").value;

                var p_packet = tp_packet = p_weight = tp_weight = twp_weight = p_yield = tp_yield = twp_yield = 0;
                for(var d = 0;d <= incr;d++){
                    p_packet = document.getElementById("p_packet["+d+"]").value; if(p_packet == ""){ p_packet = 0; }
                    tp_packet = parseFloat(tp_packet) + parseFloat(p_packet);

                    p_weight = document.getElementById("p_weight["+d+"]").value; if(p_weight == ""){ p_weight = 0; }
                    tp_weight = parseFloat(tp_weight) + parseFloat(p_weight);

                    if(parseFloat(weight) != 0){ p_yield = ((parseFloat(p_weight) / parseFloat(weight)) * 100); } else{ p_yield = 0; }
                    document.getElementById("p_yield["+d+"]").value = parseFloat(p_yield).toFixed(2);
                }
                document.getElementById("tp_packet").value = parseFloat(tp_packet).toFixed(2);
                document.getElementById("tp_weight").value = parseFloat(tp_weight).toFixed(2);
                twp_weight = parseFloat(weight) - parseFloat(tp_weight);
                document.getElementById("twp_weight").value = parseFloat(twp_weight).toFixed(2);
                if(parseFloat(weight) != 0){ tp_yield = ((parseFloat(tp_weight) / parseFloat(weight)) * 100); } else{ tp_yield = 0; }
                document.getElementById("tp_yield").value = parseFloat(tp_yield).toFixed(2);
                if(parseFloat(weight) != 0){ twp_yield = ((parseFloat(twp_weight) / parseFloat(weight)) * 100); } else{ twp_yield = 0; }
                document.getElementById("twp_yield").value = parseFloat(twp_yield).toFixed(2);

                //Calculate Avg Price and Amount for Produced Items
                var tpk_amt = document.getElementById("tpk_amt").value; if(tpk_amt == ""){ tpk_amt = 0; }
                avg_amount = parseFloat(avg_amount) + parseFloat(tpk_amt);
                if(parseFloat(tp_weight) != 0){ var p_avgprc = parseFloat(avg_amount) / parseFloat(tp_weight); } else{ var p_avgprc = 0; }
                var p_avgamt = tp_avgprc = tp_avgamt = 0;
                for(var d = 0;d <= incr;d++){
                    p_weight = document.getElementById("p_weight["+d+"]").value; if(p_weight == ""){ p_weight = 0; }
                    p_avgamt = parseFloat(p_avgprc) * parseFloat(p_weight);

                    document.getElementById("p_avgprc["+d+"]").value = parseFloat(p_avgprc).toFixed(2);
                    document.getElementById("p_avgamt["+d+"]").value = parseFloat(p_avgamt).toFixed(2);
                    tp_avgamt = parseFloat(tp_avgamt) + parseFloat(p_avgamt);
                }
                if(parseFloat(tp_weight) != 0){ tp_avgprc = parseFloat(tp_avgamt) / parseFloat(tp_weight); } else{ tp_avgprc = 0; }
                document.getElementById("tp_avgprc").value = parseFloat(tp_avgprc).toFixed(2);
                document.getElementById("tp_avgamt").value = parseFloat(tp_avgamt).toFixed(2);
            }
            function calculate_total_qty2(){
                var incr = document.getElementById("incr2").value;
                var packing_qty = tpk_qty = packing_avgprc = packing_avgamt = tpk_amt = tpk_prc = 0;
                for(var d = 0;d <= incr;d++){
                    packing_qty = document.getElementById("packing_qty["+d+"]").value; if(packing_qty == ""){ packing_qty = 0; }
                    tpk_qty = parseFloat(tpk_qty) + parseFloat(packing_qty);
                    packing_avgprc = document.getElementById("packing_avgprc["+d+"]").value; if(packing_avgprc == ""){ packing_avgprc = 0; }
                    packing_avgamt = parseFloat(packing_avgprc) * parseFloat(packing_qty);
                    document.getElementById("packing_avgamt["+d+"]").value = parseFloat(packing_avgamt).toFixed(2);
                    tpk_amt = parseFloat(tpk_amt) + parseFloat(packing_avgamt);
                }
                if(parseFloat(tpk_qty) != 0){ tpk_prc = parseFloat(tpk_amt) / parseFloat(tpk_qty); }
                document.getElementById("tpk_qty").value = parseFloat(tpk_qty).toFixed(2);
                document.getElementById("tpk_prc").value = parseFloat(tpk_prc).toFixed(2);
                document.getElementById("tpk_amt").value = parseFloat(tpk_amt).toFixed(2);
                calculate_total_qty();
            }
            function fetch_stock_master(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var date = document.getElementById("date").value;
                var sector = document.getElementById("warehouse").value;
                var item_code = document.getElementById("packing_items["+d+"]").value;
                if(sector != "select"){
                    var prices = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_fetch_itemstockmaster_lsfi.php?sector="+sector+"&item_code="+item_code+"&date="+date+"&row_count="+d;
                    var asynchronous = true;
                    //window.open(url);
                    prices.open(method, url, asynchronous);
                    prices.send();
                    prices.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var item_price = this.responseText;
                            if(item_price.length > 0){
                                var item_details = item_price.split("@");
                                document.getElementById("packing_stock["+item_details[3]+"]").value = item_details[0];
                                document.getElementById("packing_avgprc["+item_details[3]+"]").value = item_details[1];
                            }
                            else{
                                alert("Item Stock not available, Kindly check before saving ...!");
                                document.getElementById("packing_stock["+item_details[3]+"]").value = 0;
                                document.getElementById("packing_avgprc["+item_details[3]+"]").value = 0;
                            }
                        }
                    }
                }
                else{
                    document.getElementById("packing_stock["+item_details[3]+"]").value = 0;
                    document.getElementById("packing_avgprc["+item_details[3]+"]").value = 0;
                }
            }
            function fetch_transactions(a){
                var trans_type = document.getElementById(a).value;
                removeAllOptions(document.getElementById("link_trnum"));
                myselect = document.getElementById("link_trnum"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("select"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				
                if(trans_type == "BR"){
                    <?php
                    $sql = "SELECT * FROM `broiler_bird_receivedin` WHERE `item_code` = '$bird_code' AND `active` = '1' AND `dflag` = '0' AND `processed_flag` = '0' GROUP BY `trnum` ORDER BY `trnum` ASC";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ ?> 
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['trnum']; ?>"); theOption1.value = "<?php echo $row['trnum']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
					<?php } ?>
                }
                else if(trans_type == "PUR"){
                    <?php
                    $sql = "SELECT * FROM `broiler_purchases` WHERE `icode` = '$bird_code' AND `active` = '1' AND `dflag` = '0' AND `processed_flag` = '0' GROUP BY `trnum` ORDER BY `trnum` ASC";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ ?> 
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['trnum']; ?>"); theOption1.value = "<?php echo $row['trnum']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
					<?php } ?>
                }
                else{ }
            }
            function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                document.getElementById("incr").value = d;
                html += '<tr id="row_no['+d+']">';
                html += '<td><select name="p_item[]" id="p_item['+d+']" class="form-control select2" style="width:160px;"><option value="select">-select-</option><?php foreach($pitem_code as $icode){ ?><option value="<?php echo $icode; ?>" ><?php echo $item_name[$icode]; ?></option><?php } ?></select></td>';
                html += '<td><input type="text" name="p_packet[]" id="p_packet['+d+']" class="form-control" onkeyup="validatenum(this.id);calculate_total_qty();" onchange="validateamount(this.id);" /></td>';
                html += '<td><input type="text" name="p_weight[]" id="p_weight['+d+']" class="form-control" onkeyup="validatenum(this.id);calculate_total_qty();" onchange="validateamount(this.id);" /></td>';
                html += '<td><input type="text" name="p_yield[]" id="p_yield['+d+']" class="form-control" readonly /></td>';
                html += '<td><input type="text" name="p_avgprc[]" id="p_avgprc['+d+']" class="form-control" readonly /></td>';
                html += '<td><input type="text" name="p_avgamt[]" id="p_avgamt['+d+']" class="form-control" readonly /></td>';
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
            function create_row2(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action2["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                document.getElementById("incr2").value = d;
                html += '<tr id="row_no2['+d+']">';
                html += '<td><select name="packing_items[]" id="packing_items['+d+']" class="form-control select2" style="width:160px;" onchange="fetch_stock_master(this.id);"><option value="select">-select-</option><?php foreach($pmitem_code as $icode){ ?><option value="<?php echo $icode; ?>" ><?php echo $item_name[$icode]; ?></option><?php } ?></select></td>';
                html += '<td><input type="text" name="packing_stock[]" id="packing_stock['+d+']" class="form-control" readonly /></td>';
                html += '<td><input type="text" name="packing_qty[]" id="packing_qty['+d+']" class="form-control" onkeyup="validatenum(this.id);calculate_total_qty2();" onchange="validateamount(this.id);" /></td>';
                html += '<td><input type="text" name="packing_avgprc[]" id="packing_avgprc['+d+']" class="form-control" readonly /></td>';
                html += '<td><input type="text" name="packing_avgamt[]" id="packing_avgamt['+d+']" class="form-control" readonly /></td>';
                html += '<td id="action2['+d+']"><a href="javascript:void(0);" id="addrow2['+d+']" onclick="create_row2(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow2['+d+']" onclick="destroy_row2(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                html += '</tr>';
                $('#row_body2').append(html);
                $('.select2').select2();
            }
            function destroy_row2(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no2["+d+"]").remove();
                d--;
                document.getElementById("incr2").value = d;
                document.getElementById("action2["+d+"]").style.visibility = "visible";
                calculate_final_totalamt();
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