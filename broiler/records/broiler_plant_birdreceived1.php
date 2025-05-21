<?php
//broiler_plant_birdreceived1.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    include "header_head.php";
    $form_path = "broiler_plant_birdreceived1.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    include "header_head.php";
    $form_path = "broiler_plant_birdreceived1.php?db=$db&userid=".$user_code;
}
include "decimal_adjustments.php";

$file_name = "Plant Bird Received Report";
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

/*Check for Table Availability*/
$database_name = $dbname; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("plant_bird_received_main_details", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.plant_bird_received_main_details LIKE poulso6_admin_broiler_broilermaster.plant_bird_received_main_details;"; mysqli_query($conn,$sql1); }
if(in_array("plant_bird_received_link_details", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.plant_bird_received_link_details LIKE poulso6_admin_broiler_broilermaster.plant_bird_received_link_details;"; mysqli_query($conn,$sql1); }
if(in_array("plant_receivein_types", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.plant_receivein_types LIKE poulso6_admin_broiler_broilermaster.plant_receivein_types;"; mysqli_query($conn,$sql1); }

$sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $farm_code = $farm_name = array();
while($row = mysqli_fetch_assoc($query)){ $farm_code[$row['code']] = $row['code']; $farm_name[$row['code']] = $row['description']; }

$office_code = array();
$sql = "SELECT * FROM `main_officetypes` WHERE `description` LIKE '%Processing Plant%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $office_code[$row['code']] = $row['code']; }
$office_list = implode("','", $office_code);
$sql = "SELECT * FROM `inv_sectors` WHERE `type` IN ('$office_list') AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; $farm_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_name = array();
while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farm_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `plant_receivein_types` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $rcvd_type = array();
while($row = mysqli_fetch_assoc($query)){ $rcvd_type[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `sms_master` WHERE `sms_type` = 'BB-birdreceiving' AND  `msg_type` IN ('WAPP') AND `active` = '1'";
$query = mysqli_query($conn,$sql); $mob_list = "";
while($row = mysqli_fetch_assoc($query)){ $mob_list = $row['numers']; }

$fdate = $tdate = date("Y-m-d"); $plants = "all"; $excel_type = $vis_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $plants = $_POST['plants'];
    $mob_list = $_POST['mob_list'];
    $vis_type = $_POST['vis_type'];
    $excel_type = $_POST['export'];
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
        <?php if($excel_type == "print"){ include "headerstyle_wprint.php"; } else{ include "headerstyle_woprint.php"; } ?>
    </head>
    <body align="center">
        <table class="tbl" align="center">
            <thead class="thead3" align="center" width="auto">
                <tr align="center">
                    <th colspan="2" align="center"><img src="<?php echo $img_logo; ?>" height="110px"/></th>
                    <th colspan="19" align="center"><?php echo $cdetails; ?><h5><?php echo $file_name; ?></h5></th>
                </tr>
            </thead>
            <form action="<?php echo $form_path; ?>" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed" width="auto" <?php if($excel_type == "print"){ echo 'style="display:none;"'; } ?>>
                    <tr>
                        <th colspan="21">
                            <div class="row">
                                <div class="m-2 form-group" style="width:120px;">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" />
                                </div>
                                <div class="m-2 form-group" style="width:120px;">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div>
                                <div class="m-2 form-group" style="width:230px;">
                                    <label for="plants">plant</label>
                                    <select name="plants" id="plants" class="form-control select2" style="width:220px;">
                                        <option value="all" <?php if($plants == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($sector_code as $bcode){ if($sector_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($plants == $bcode){ echo "selected"; } ?>><?php echo $sector_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <!--<div class="form-group" style="width:120px;">
                                    <label for="vis_type">Type</label>
                                    <select name="vis_type" id="vis_type" class="form-control select2" style="width:110px;">
                                        <option value="display" <?php //if($vis_type == "display"){ echo "selected"; } ?>>Display</option>
                                        <option value="WhatsApp" <?php //if($vis_type == "WhatsApp"){ echo "selected"; } ?>>WhatsApp</option>
                                    </select>
                                </div>
                                <div class="form-group" style="width:150px;">
                                    <label for="mob_list">Mobile</label>
                                    <input type="text" name="mob_list" id="mob_list" class="form-control" style="width:140px;" value="<?php echo $mob_list; ?>"/>
                                </div>
                            </div>
                            <div class="row">-->
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2" onchange="tableToExcel('main_table', '<?php echo $file_name; ?>','<?php echo $file_name; ?>', this.options[this.selectedIndex].value)">
                                        <option value="display" <?php if($excel_type == "display"){ echo "selected"; } ?>>-Display-</option>
                                        <option value="excel" <?php if($excel_type == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                        <option value="print" <?php if($excel_type == "print"){ echo "selected"; } ?>>-Print-</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width: 210px;">
                                    <label for="search_table">Search</label>
                                    <input type="text" name="search_table" id="search_table" class="form-control" style="padding:0;padding-left:2px;width:200px;" />
                                </div>
                                <div class="m-2 form-group">
                                    <br/>
                                    <button type="submit" name="submit_report" id="submit_report" class="btn btn-sm btn-success">Submit</button>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
            </form>
        </table>
        <table id="main_table" class="tbl" align="center">
            <?php
            $html = $nhtml = $fhtml = ''; $cflag = $i_cnt = 0;
            $html .= '<thead class="thead3" id="head_names">';

            $nhtml .= '<tr style="text-align:center;" align="center">';
            $fhtml .= '<tr style="text-align:center;" align="center">';

            $nhtml .= '<th>Sl.No.</th>'; $fhtml .= '<th id="order_num">Sl.No.</th>';
            $nhtml .= '<th>Date</th>'; $fhtml .= '<th id="order_date">Date</th>';
            $nhtml .= '<th>Trnum</th>'; $fhtml .= '<th id="order">Trnum</th>';
            $nhtml .= '<th>Dc. No.</th>'; $fhtml .= '<th id="order">Dc. No.</th>';
            $nhtml .= '<th>Received Type</th>'; $fhtml .= '<th id="order">Received Type</th>';
            $nhtml .= '<th>Received From</th>'; $fhtml .= '<th id="order">Received From</th>';
            $nhtml .= '<th>Item</th>'; $fhtml .= '<th id="order">Item</th>';
            $nhtml .= '<th>Gross Weight</th>'; $fhtml .= '<th id="order_num">Gross Weight</th>';
            $nhtml .= '<th>Tare Weight</th>'; $fhtml .= '<th id="order_num">Tare Weight</th>';
            $nhtml .= '<th>Net Weight</th>'; $fhtml .= '<th id="order_num">Net Weight</th>';
            $nhtml .= '<th>T.Mort. Birds</th>'; $fhtml .= '<th id="order_num">T.Mort. Birds</th>';
            $nhtml .= '<th>T.Mort. Weight</th>'; $fhtml .= '<th id="order_num">T.Mort. Weight</th>';
            $nhtml .= '<th>H.Mort. Birds</th>'; $fhtml .= '<th id="order_num">H.Mort. Birds</th>';
            $nhtml .= '<th>H.Mort. Weight</th>'; $fhtml .= '<th id="order_num">H.Mort. Weight</th>';
            $nhtml .= '<th>Received Birds</th>'; $fhtml .= '<th id="order_num">Received Birds</th>';
            $nhtml .= '<th>Received Weight</th>'; $fhtml .= '<th id="order_num">Received Weight</th>';
            $nhtml .= '<th>Shortage Birds</th>'; $fhtml .= '<th id="order_num">Shortage Birds</th>';
            $nhtml .= '<th>Shortage Weight</th>'; $fhtml .= '<th id="order_num">Shortage Weight</th>';
            $nhtml .= '<th>Excess Birds</th>'; $fhtml .= '<th id="order_num">Excess Birds</th>';
            $nhtml .= '<th>Excess Weight</th>'; $fhtml .= '<th id="order_num">Excess Weight</th>';
            $nhtml .= '<th>Remarks</th>'; $fhtml .= '<th id="order">Remarks</th>';
            $nhtml .= '<th>Processed Status</th>'; $fhtml .= '<th id="order">Processed Status</th>';

            $nhtml .= '</tr>';
            $fhtml .= '</tr>';
            $html .= $fhtml;
            $html .= '</thead>';
            $html .= '<tbody class="tbody1">';
            if(isset($_POST['submit_report']) == true){
                $trnum = $date = $billno = $receivein_type = $link_ttype = $cus_code = $batch_no = $icode = $gross_weight = $tare_weight = $net_weight = $rcvd_bird_nos = 
                $rcvd_bird_weight = $tmort_bird_nos = $tmort_bird_weight = $hmort_bird_nos = $hmort_bird_weight = $nrcvd_bird_nos = $nrcvd_bird_weight = $shortage_bird_nos = 
                $shortage_bird_weight = $excess_bird_nos = $excess_bird_weight = $load_start_time = $load_end_time = $load_hrs_time = $vehicle_in_time = $hanging_start_time = 
                $hanging_end_time = $holding_hrs_inplant = $process_time = $hallal_type = $hanging_area_temp = $warehouse = $remarks = $processed_flag = $rtno_alist = 
                $rtno_mcnt = $date_wtrno = "";

                $sector_fltr = ""; if($plants != "all"){ $sector_fltr = " AND `warehouse` = '$plants'"; }
                $sql = "SELECT * FROM `plant_bird_received_main_details` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$sector_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql); $old_trno = ""; $slno = 0;
                while($row = mysqli_fetch_assoc($query)){
                    $date = date("d.m.Y",strtotime($row['date']));
                    $trnum = $row['trnum'];
                    $billno = $row['billno'];
                    $rtype_code = $row['receivein_type'];
                    $link_ttype = $row['link_ttype'];
                    $cus_code = $row['cus_code'];
                    $batch_no = $row['batch_no'];
                    $icode = $row['item_code'];
                    $gross_weight = $row['gross_weight'];
                    $tare_weight = $row['tare_weight'];
                    $net_weight = $row['net_weight'];
                    $rcvd_bird_nos = $row['rcvd_bird_nos'];
                    $rcvd_bird_weight = $row['rcvd_bird_weight'];
                    $tmort_bird_nos = $row['tmort_bird_nos'];
                    $tmort_bird_weight = $row['tmort_bird_weight'];
                    $hmort_bird_nos = $row['hmort_bird_nos'];
                    $hmort_bird_weight = $row['hmort_bird_weight'];
                    $nrcvd_bird_nos = $row['nrcvd_bird_nos'];
                    $nrcvd_bird_weight = $row['nrcvd_bird_weight'];
                    $shortage_bird_nos = $row['shortage_bird_nos'];
                    $shortage_bird_weight = $row['shortage_bird_weight'];
                    $excess_bird_nos = $row['excess_bird_nos'];
                    $excess_bird_weight = $row['excess_bird_weight'];
                    $load_start_time = $row['load_start_time'];
                    $load_end_time = $row['load_end_time'];
                    $load_hrs_time = $row['load_hrs_time'];
                    $vehicle_in_time = $row['vehicle_in_time'];
                    $hanging_start_time = $row['hanging_start_time'];
                    $hanging_end_time = $row['hanging_end_time'];
                    $holding_hrs_inplant = $row['holding_hrs_inplant'];
                    $process_time = $row['process_time'];
                    $hallal_type = $row['hallal_type'];
                    $hanging_area_temp = $row['hanging_area_temp'];
                    $warehouse = $row['warehouse'];
                    $remarks = $row['remarks'];
                    if((int)$row['processed_flag'] == 1){ $rcv_status = "Processed"; } else{ $rcv_status = "Pending"; }

                    $slno++;
                    $html .= '<tr>';
                    $html .= '<td style="text-align:center;">'.$slno.'</td>';
                    $html .= '<td>'.$date.'</td>';
                    $html .= '<td>'.$trnum.'</td>';
                    $html .= '<td>'.$billno.'</td>';
                    $html .= '<td>'.$rcvd_type[$rtype_code].'</td>';
                    $html .= '<td>'.$farm_name[$cus_code].'</td>';
                    $html .= '<td>'.$item_name[$icode].'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind(round($gross_weight,5)).'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind(round($tare_weight,5)).'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind(round($net_weight,5)).'</td>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tmort_bird_nos,5))).'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind(round($tmort_bird_weight,5)).'</td>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($hmort_bird_nos,5))).'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind(round($hmort_bird_weight,5)).'</td>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($nrcvd_bird_nos,5))).'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind(round($nrcvd_bird_weight,5)).'</td>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($shortage_bird_nos,5))).'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind(round($shortage_bird_weight,5)).'</td>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($excess_bird_nos,5))).'</td>';
                    $html .= '<td style="text-align:right;">'.number_format_ind(round($excess_bird_weight,5)).'</td>';
                    $html .= '<td style="padding-left:5px;text-align:left;">'.$remarks.'</td>';
                    if($rcv_status == "Pending"){ $html .= '<td style="padding-left:5px;text-align:left;color:red;">'.$rcv_status.'</td>'; }
                    else{ $html .= '<td style="padding-left:5px;text-align:left;color:green;">'.$rcv_status.'</td>'; }
                    $html .= '</tr>';

                    $tg_wht += (float)$gross_weight;
                    $tt_wht += (float)$tare_weight;
                    $tn_wht += (float)$net_weight;
                    $tm_bds += (float)$tmort_bird_nos;
                    $tm_wht += (float)$tmort_bird_weight;
                    $th_bds += (float)$hmort_bird_nos;
                    $th_wht += (float)$hmort_bird_weight;
                    $tr_bds += (float)$nrcvd_bird_nos;
                    $tr_wht += (float)$nrcvd_bird_weight;
                    $ts_bds += (float)$shortage_bird_nos;
                    $ts_wht += (float)$shortage_bird_weight;
                    $te_bds += (float)$excess_bird_nos;
                    $te_wht += (float)$excess_bird_weight;

                }
                $html .= '</tbody>';
                $html .= '<tfoot class="thead3">';
                $html .= '<tr>';
                $html .= '<th style="text-align:left;" colspan="7">Total</th>';
                $html .= '<th style="text-align:right;">'.number_format_ind($tg_wht).'</th>';
                $html .= '<th style="text-align:right;">'.number_format_ind($tt_wht).'</th>';
                $html .= '<th style="text-align:right;">'.number_format_ind($tn_wht).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tm_bds)).'</th>';
                $html .= '<th style="text-align:right;">'.number_format_ind($tm_wht).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($th_bds)).'</th>';
                $html .= '<th style="text-align:right;">'.number_format_ind($th_wht).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tr_bds)).'</th>';
                $html .= '<th style="text-align:right;">'.number_format_ind($tr_wht).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($ts_bds)).'</th>';
                $html .= '<th style="text-align:right;">'.number_format_ind($ts_wht).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($te_bds)).'</th>';
                $html .= '<th style="text-align:right;">'.number_format_ind($te_wht).'</th>';
                $html .= '<th style="text-align:left;"></th>';
                $html .= '<th style="text-align:left;"></th>';
                $html .= '</tr>';
                $html .= '</tfoot>';
                /*while($row = mysqli_fetch_assoc($query)){
                    if($old_trno == "" || $old_trno != $row['trnum']){
                        $c = 0; $old_trno = $row['trnum'];
                        if(empty($date_wtrno[$row['date']]) || $date_wtrno[$row['date']] == ""){ $date_wtrno[$row['date']] = $row['trnum']; } else{ $date_wtrno[$row['date']] .= ",".$row['trnum']; }
                    }
                    else{ }
                    $c++; $key = $row['trnum']."@".$c;

                    $date[$key] = $row['date'];
                    $trnum[$key] = $row['trnum'];
                    $billno[$key] = $row['billno'];
                    $receivein_type[$key] = $row['receivein_type'];
                    $link_ttype[$key] = $row['link_ttype'];
                    $cus_code[$key] = $row['cus_code'];
                    $batch_no[$key] = $row['batch_no'];
                    $item_code[$key] = $row['item_code'];
                    $gross_weight[$key] = $row['gross_weight'];
                    $tare_weight[$key] = $row['tare_weight'];
                    $net_weight[$key] = $row['net_weight'];
                    $rcvd_bird_nos[$key] = $row['rcvd_bird_nos'];
                    $rcvd_bird_weight[$key] = $row['rcvd_bird_weight'];
                    $tmort_bird_nos[$key] = $row['tmort_bird_nos'];
                    $tmort_bird_weight[$key] = $row['tmort_bird_weight'];
                    $hmort_bird_nos[$key] = $row['hmort_bird_nos'];
                    $hmort_bird_weight[$key] = $row['hmort_bird_weight'];
                    $nrcvd_bird_nos[$key] = $row['nrcvd_bird_nos'];
                    $nrcvd_bird_weight[$key] = $row['nrcvd_bird_weight'];
                    $shortage_bird_nos[$key] = $row['shortage_bird_nos'];
                    $shortage_bird_weight[$key] = $row['shortage_bird_weight'];
                    $excess_bird_nos[$key] = $row['excess_bird_nos'];
                    $excess_bird_weight[$key] = $row['excess_bird_weight'];
                    $load_start_time[$key] = $row['load_start_time'];
                    $load_end_time[$key] = $row['load_end_time'];
                    $load_hrs_time[$key] = $row['load_hrs_time'];
                    $vehicle_in_time[$key] = $row['vehicle_in_time'];
                    $hanging_start_time[$key] = $row['hanging_start_time'];
                    $hanging_end_time[$key] = $row['hanging_end_time'];
                    $holding_hrs_inplant[$key] = $row['holding_hrs_inplant'];
                    $process_time[$key] = $row['process_time'];
                    $hallal_type[$key] = $row['hallal_type'];
                    $hanging_area_temp[$key] = $row['hanging_area_temp'];
                    $warehouse[$key] = $row['warehouse'];
                    $remarks[$key] = $row['remarks'];
                    $processed_flag[$key] = $row['processed_flag'];

                    $rtno_alist[$row['trnum']] = $row['trnum'];
                    $rtno_mcnt[$row['trnum']] = $c;
                }

                if(sizeof($rtno_alist) > 0){
                    $sin_id = $sin_trnum = $sin_swcode = $sin_dcno = $farm_batch = $sin_itmcode = $sin_jals = $sin_birds = $sin_weight = $sin_avgwt = $sin_price = 
                    $sin_amount = $sin_vehicle = array();
                    $rtrno_list = implode("','",$rtno_alist);
                    $sql = "SELECT * FROM `plant_bird_received_link_details` WHERE `trnum` IN ('$rtrno_list') AND `active` = '1' AND `dflag` = '0'";
                    $query = mysqli_query($conn,$sql); $old_trno = ""; $c = 0;
                    while($row = mysqli_fetch_assoc($query)){
                        if($old_trno == "" || $old_trno != $row['trnum']){ $c = 0; $old_trno = $row['trnum']; } else{ }
                        $c++; $key = $row['trnum']."@".$c;
                        
                        $sin_id[$key] = $row['sin_id'];
                        $sin_trnum[$key] = $row['sin_trnum'];
                        $sin_swcode[$key] = $row['sin_swcode'];
                        $sin_dcno[$key] = $row['sin_dcno'];
                        $farm_batch[$key] = $row['farm_batch'];
                        $sin_itmcode[$key] = $row['sin_itmcode'];
                        $sin_jals[$key] = $row['sin_jals'];
                        $sin_birds[$key] = $row['sin_birds'];
                        $sin_weight[$key] = $row['sin_weight'];
                        $sin_avgwt[$key] = $row['sin_avgwt'];
                        $sin_price[$key] = $row['sin_price'];
                        $sin_amount[$key] = $row['sin_amount'];
                        $sin_vehicle[$key] = $row['sin_vehicle'];

                        $rtno_lcnt[$row['trnum']] = $c;
                    }

                    $sec1 = $sec2 = 0;
                    for($cdate = strtotime($fdate); $cdate <= strtotime($tdate); $cdate += (86400)){
                        $adate = date("Y-m-d",$cdate);

                        if(empty($date_wtrno[$adate]) || $date_wtrno[$adate] == ""){ }
                        else{
                            $rcvd_trnos = array(); $rcvd_trnos = explode(",",$date_wtrno[$adate]);
                            foreach($rcvd_trnos as $trno){
                                if($trno == ""){ }
                                else{
                                    if(empty($rtno_mcnt[$trno]) || $rtno_mcnt[$trno] == ""){ $rtno_mcnt[$trno] = 0; }
                                    if(empty($rtno_lcnt[$trno]) || $rtno_lcnt[$trno] == ""){ $rtno_lcnt[$trno] = 0; }

                                    $m_cnt = 0; $m_cnt = (int)$rtno_mcnt[$trno];
                                    $l_cnt = 0; $l_cnt = (int)$rtno_lcnt[$trno];

                                    //Main Details Calculations
                                    if($m_cnt > 0){
                                        for($i = 1;$i <= $m_cnt;$i++){
                                            $key = $trno."@".$i;
                                            $sec1++;
                                            if(empty($date[$key]) || $date[$key] == ""){ $ddate = ""; } else{ $ddate = date("d.m.Y",strtotime($date[$key])); }
                                            if(empty($trnum[$key]) || $trnum[$key] == ""){ $dtrnum = ""; } else{ $dtrnum = $trnum[$key]; }
                                            if(empty($billno[$key]) || $billno[$key] == ""){ $ddcno = ""; } else{ $ddcno = $billno[$key]; }

                                            $date_col[$sec1] = '<td style="text-align:left;">'.$ddate.'</td>';
                                            $trno_col[$sec1] = '<td style="text-align:left;">'.$dtrnum.'</td>';
                                            $blno_col[$sec1] = '<td style="text-align:left;">'.$ddcno.'</td>';
                                        }
                                    }
                                    if((int)$l_cnt > $m_cnt){
                                        for($i = $m_cnt + 1;$i <= $l_cnt;$i++){
                                            $sec1++;
                                            $date_col[$sec1] = $trno_col[$sec1] = $blno_col[$sec1] = '<td></td>';
                                        }
                                    }

                                    //Link Details Calculations
                                    if($l_cnt > 0){
                                        for($j = 1;$j <= $l_cnt;$j++){
                                            $key = $trno."@".$j;
                                            $sec2++;
                                            if(empty($sin_trnum[$key]) || $sin_trnum[$key] == ""){ $dstrno = ""; } else{ $dstrno = $sin_trnum[$key]; }
                                            if(empty($sin_swcode[$key]) || $sin_swcode[$key] == ""){ $swname = ""; } else{ $swname = $farm_name[$sin_swcode[$key]]; }
                                            if(empty($sin_dcno[$key]) || $sin_dcno[$key] == ""){ $dsdcno = ""; } else{ $dsdcno = $sin_dcno[$key]; }

                                            $strno_col[$sec2] = '<td style="text-align:left;">'.$dstrno.'</td>';
                                            $sswcn_col[$sec2] = '<td style="text-align:left;">'.$swname.'</td>';
                                            $sblno_col[$sec2] = '<td style="text-align:left;">'.$dsdcno.'</td>';
                                        }
                                    }
                                    if((int)$m_cnt > $l_cnt){
                                        for($j = $l_cnt + 1;$j <= $m_cnt;$j++){
                                            $sec2++;
                                            $strno_col[$sec2] = $sswcn_col[$sec2] = $sblno_col[$sec2] = '<td></td>';
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //Display
                    for($k = 1;$k <= $sec1;$k++){
                        $html .= '<tr>';
                        $html .= $date_col[$k]."".$trno_col[$k]."".$blno_col[$k];
                        $html .= $strno_col[$k]."".$sswcn_col[$k]."".$sblno_col[$k];
                        $html .= '</tr>';
                    }
                }*/
            }
            echo $html;
        ?>
        </table><br/><br/><br/>
        <script>
            function table_sort() {
		        console.log("test");
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `.order-inactive span { visibility:hidden; } .order-inactive:hover span { visibility:visible; } .order-active span { visibility: visible; }`;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order').forEach(th_elem => {
                    console.log("test1");

                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    const arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
                    arr.sort((a, b) => {
                        const a_val = a.children[index].innerText;
                        const b_val = b.children[index].innerText;
                        return (asc) ? a_val.localeCompare(b_val) : b_val.localeCompare(a_val)
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector("tbody").appendChild(elem)
                    });
                    slnos();
                    asc = !asc;
                    })
                });
            }
            function convertDate(d){ var p = d.split("."); return (p[2]+p[1]+p[0]); }
            function table_sort3() {
                console.log("test");
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `
                        .order-inactive span {
                            visibility:hidden;
                        }
                        .order-inactive:hover span {
                            visibility:visible;
                        }
                        .order-active span {
                            visibility: visible;
                        }
                    `;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order_date').forEach(th_elem => {
                    console.log("test1");

                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order_date').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    const arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
                    arr.sort((a, b) => {
                        const a_val = convertDate(a.children[index].innerText);
                        const b_val = convertDate(b.children[index].innerText);
                        return (asc) ? a_val.localeCompare(b_val) : b_val.localeCompare(a_val)
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector("tbody").appendChild(elem)
                    });
                    slnos();
                    asc = !asc;
                    })
                });
            }

            function convertNumber(d) { var p = intval(d); return (p); }

            function table_sort2() {
                console.log("test");
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `
                        .order-inactive span {
                            visibility:hidden;
                        }
                        .order-inactive:hover span {
                            visibility:visible;
                        }
                        .order-active span {
                            visibility: visible;
                        }
                    `;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order_num').forEach(th_elem => {
                    console.log("test1");

                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order_num').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    
                    var arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
                    arr.sort((a, b) => {
                        const a_val = a.children[index].innerText;    
                        if(isNaN(a_val)){
                        a_val1 = a_val.split(',').join(''); }
                        else {
                            a_val1 = a_val; }
                        const b_val = b.children[index].innerText;
                        if(isNaN(b_val)){
                        b_val1 = b_val.split(',').join('');}
                        else {
                            b_val1 = b_val; }
                        return (asc) ? b_val1 - a_val1:  a_val1 - b_val1 
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector("tbody").appendChild(elem)
                    });
                    slnos();
                    asc = !asc;
                    })
                });
                
            }
            function slnos(){
                var rcount = document.getElementById("tbody1").rows.length;
                var myTable = document.getElementById('tbody1');
                var j = 0;
                for(var i = 1;i <= rcount;i++){ j = i - 1; myTable.rows[j].cells[0].innerHTML = i; }
            }

            table_sort();
            table_sort2();
            table_sort3();
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const searchInput = document.getElementById('search_table');
                const table = document.getElementById('main_table');
                const tableBody = table.querySelector('tbody');

                searchInput.addEventListener('input', () => {
                    const filter = searchInput.value.toLowerCase();
                    const rows = tableBody.querySelectorAll('tr');

                    rows.forEach(row => {
                        const cells = row.querySelectorAll('td');
                        let found = false;

                        cells.forEach(cell => {
                            if (cell.textContent.toLowerCase().includes(filter)) {
                                found = true;
                            }
                        });

                        row.style.display = found ? '' : 'none';
                    });
                });
            });
        </script>
        <script type="text/javascript">
            function tableToExcel(table, name, filename, chosen){
                if(chosen === 'excel'){
                    cdate_format1();
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html += '<?php echo $nhtml; ?>';
                    $('#head_names').append(html);
                    
                    var table = document.getElementById("main_table");
                    var workbook = XLSX.utils.book_new();
                    var worksheet = XLSX.utils.table_to_sheet(table);
                    XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
                    XLSX.writeFile(workbook, filename+".xlsx");
                    
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html += '<?php echo $fhtml; ?>';
                    document.getElementById("head_names").innerHTML = html;
                    
                    $('#export').select2();
                    document.getElementById("export").value = "display";
                    $('#export').select2();
                    cdate_format2();
                    table_sort();
                    table_sort2();
                    table_sort3();
                }
                else{ }
            }
            function cdate_format1() {
                const dateCells = document.querySelectorAll('#main_table .dates');
                var adate = [];
                dateCells.forEach(cell => {
                    let originalString = cell.textContent;
                    adate = []; adate = originalString.split(".");
                    cell.textContent = adate[2]+"-"+adate[1]+"-"+adate[0];
                });
            }
            function cdate_format2() {
                const dateCells = document.querySelectorAll('#main_table .dates');
                var adate = [];
                dateCells.forEach(cell => {
                    let originalString = cell.textContent;
                    adate = []; adate = originalString.split("-");
                    cell.textContent = adate[2]+"."+adate[1]+"."+adate[0];
                });
            }
        </script>
        <script>
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>