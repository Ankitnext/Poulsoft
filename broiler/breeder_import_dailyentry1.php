<?php
//breeder_import_dailyentry1.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['dailyentry1'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = "breeder_add_dailyentry1.php"; //basename($path);
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
        $sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Daily Entry' AND `field_function` = 'Fetch Flocks Based On Shed Selection' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $shed_sflag = mysqli_num_rows($query);
        $sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Daily Entry' AND `field_function` = 'Display Only Stock Available Feed Items' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $feed_aflag = mysqli_num_rows($query);
        $sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Daily Entry' AND `field_function` = 'Display 2nd Feed Entry For Female Birds' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $ffeed_2flag = mysqli_num_rows($query);
        $sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Daily Entry' AND `field_function` = 'Display 2nd Feed Entry For Male Birds' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $mfeed_2flag = mysqli_num_rows($query);
        $sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Daily Entry' AND `field_function` = 'Feed Stock in Bags' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $bfstk_bags = mysqli_num_rows($query);
        $sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Module' AND `field_function` = 'Maintain Feed Stock in FARM/UNIT/SHED/BATCH/FLOCK' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $bfeed_stkon = ""; while($row = mysqli_fetch_assoc($query)){ $bfeed_stkon = $row['field_value']; } if($bfeed_stkon == ""){ $bfeed_stkon = "FLOCK"; }
        
        if((int)$shed_sflag == 1){
            $sql = "SELECT * FROM `breeder_sheds` WHERE `active` = '1' AND `dflag` = '0' AND `cls_flag` = '0' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); $bshed_code = $bshed_name = array();
            while($row = mysqli_fetch_assoc($query)){ $bshed_code[$row['code']] = $row['code']; $bshed_name[$row['code']] = $row['description']; }
        }
        else{
            $sql = "SELECT * FROM `breeder_shed_allocation` WHERE `active` = '1' AND `dflag` = '0' AND `cls_flag` = '0' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); $bflk_code = $bflk_name = array();
            while($row = mysqli_fetch_assoc($query)){ $bflk_code[$row['code']] = $row['code']; $bflk_name[$row['code']] = $row['description']; }
        }
    
        //Breeder Flag to fetch Femal and male Feed as Commom Feed Item
        $sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` LIKE 'Breeder Daily Entry' AND `field_function` LIKE 'Fetch male and Female Items Based on selection' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $bfm_fflag = mysqli_num_rows($query);

        //Breeder Feed Details
        $sql = "SELECT * FROM `item_category` WHERE `active` = '1' AND (`bffeed_flag` = '1' OR `bmfeed_flag` = '1') AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $icat_alist = $ibf_flag = $ibm_flag = array();
        while($row = mysqli_fetch_assoc($query)){ $icat_alist[$row['code']] = $row['code']; $ibf_flag[$row['code']] = $row['bffeed_flag']; $ibm_flag[$row['code']] = $row['bmfeed_flag']; }
        $icat_list = implode("','", $icat_alist);
        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $bffeed_code = $bffeed_name = $bmfeed_code = $bmfeed_name = array();
        while($row = mysqli_fetch_assoc($query)){
            if((int)$bfm_fflag > 0){
                $bffeed_code[$row['code']] = $row['code']; $bffeed_name[$row['code']] = $row['description'];
                $bmfeed_code[$row['code']] = $row['code']; $bmfeed_name[$row['code']] = $row['description'];
            }
            else{
                if($ibf_flag[$row['code']] == 1){ $bffeed_code[$row['code']] = $row['code']; $bffeed_name[$row['code']] = $row['description']; }
                if($ibm_flag[$row['code']] == 1){ $bmfeed_code[$row['code']] = $row['code']; $bmfeed_name[$row['code']] = $row['description']; }
            }
        }
        //Breeder Egg Details
        $sql = "SELECT * FROM `item_category` WHERE `active` = '1' AND `begg_flag` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $icat_alist = array();
        while($row = mysqli_fetch_assoc($query)){ $icat_alist[$row['code']] = $row['code']; }
        $icat_list = implode("','", $icat_alist);
        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $begg_code = $begg_name = $begg_name2 = array();
        while($row = mysqli_fetch_assoc($query)){ $begg_code[$row['code']] = $row['code']; $begg_name[$row['code']] = $row['description']; $begg_name2[$row['description']] = $row['code']; }
        $esize = sizeof($begg_code);

        $fcol_cnt = 4;
        $hhtml = '';
        $hhtml .= '<table id="main_table">';
        $hhtml .= '<tr>';
        $hhtml .= '<th colspan="3"></th>';
        if((int)$ffeed_2flag == 1){ $fcol_cnt += 2; $hhtml .= '<th colspan="7" style="background-color:#c94eff;text-align:center;">Female Birds</th>'; } else{ $hhtml .= '<th colspan="5" style="background-color:#c94eff;text-align:center;">Female Birds</th>'; }
        if((int)$mfeed_2flag == 1){ $fcol_cnt += 2; $hhtml .= '<th colspan="7" style="background-color:#ffb25b;text-align:center;">Male Birds</th>'; } else{ $hhtml .= '<th colspan="5" style="background-color:#ffb25b;text-align:center;">Male Birds</th>'; }
        $hhtml .= '<th></th>';
        $hhtml .= '<th colspan="'.($esize + 1).'" style="background-color:#0b9100;text-align:center;" class="egg_list">Egg Production</th>';
        $hhtml .= '<th></th>';
        $hhtml .= '</tr>';
        $hhtml .= '<tr>';
        $hhtml .= '<th style="text-align:center;"><label>Flock</label></th>';
        $hhtml .= '<th style="text-align:center;"><label>Date</label></th>';
        $hhtml .= '<th style="text-align:center;"><label>Age</label></th>';
        //Female Bird and Feed Details
        $hhtml .= '<th style="text-align:center;"><label>Mort.</label></th>';
        $hhtml .= '<th style="text-align:center;"><label>Culls</label></th>';
        $hhtml .= '<th style="text-align:center;"><label>B.Wt(Grams)</label></th>';
        $hhtml .= '<th style="text-align:center;"><label>Feed</label></th>';
        if((int)$bfstk_bags == 1){ $hhtml .= '<th style="text-align:center;"><label>Bags</label></th>'; }
        else{ $hhtml .= '<th style="text-align:center;"><label>Kgs</label></th>'; }
        if((int)$ffeed_2flag == 1){
            $hhtml .= '<th style="text-align:center;"><label>Feed-2</label></th>';
            if((int)$bfstk_bags == 1){ $hhtml .= '<th style="text-align:center;"><label>Bags</label></th>'; }
            else{ $hhtml .= '<th style="text-align:center;"><label>Kgs</label></th>'; }
        }
        //Male Bird and Feed Details
        $hhtml .= '<th style="text-align:center;"><label>Mort.</label></th>';
        $hhtml .= '<th style="text-align:center;"><label>Culls</label></th>';
        $hhtml .= '<th style="text-align:center;"><label>B.Wt(Grams)</label></th>';
        $hhtml .= '<th style="text-align:center;"><label>Feed</label></th>';
        if((int)$bfstk_bags == 1){ $hhtml .= '<th style="text-align:center;"><label>Bags</label></th>'; }
        else{ $hhtml .= '<th style="text-align:center;"><label>Kgs</label></th>'; }
        if((int)$mfeed_2flag == 1){
            $hhtml .= '<th style="text-align:center;"><label>Feed-</label></th>';
            if((int)$bfstk_bags == 1){ $hhtml .= '<th style="text-align:center;"><label>Bags</label></th>'; }
            else{ $hhtml .= '<th style="text-align:center;"><label>Kgs</label></th>'; }
        }
        $hhtml .= '<th style="text-align:center;"><label>Remarks</label></th>';
        //Egg Production Details
        foreach($begg_code as $icode){ $hhtml .= '<th style="text-align:center;">'.$begg_name[$icode].'</th>'; }
        if((int)$esize > 0){ $hhtml .= '<th style="text-align:center;" class="egg_list">Egg Wt.</th>'; }
        $hhtml .= '</tr>';
        $hhtml .= '</thead>';
        $hhtml .= '</table>';
?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
    <!-- Datepicker -->
    <link href="datepicker/jquery-ui.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        body{
            overflow: auto;
        }
        .form-control{
            font-size: 13px;
        }
        .f-info{ border-color: #c94eff !important; }
        /*.f-info .select2-selection{ border-color: #c94eff !important; }*/
        .m-info{ border-color: #ffb25b; }
        .p-info{ border-color: #0b9100; }
        ::-webkit-scrollbar { width: 8px; height:8px; } /*display: none;*/
        .row_body2{
            width:100%;
            overflow-y: auto;
        }
        .table1{
            transform: scale(0.8);
            transform-origin: top left;
        }
    </style>
    </head>
    <body class="m-0 hold-transition">
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Import Daily Entry</h3></div>
                        </div>
                        <div class="pl-2 card-body">
                            <form action="breeder_import_dailyentry1.php" method="post" role="form" enctype="multipart/form-data" onsubmit="return checkval2()">
                                <table align="center">
                                    <thead>
                                        <tr>
                                            <td>
                                                <a href="javascript:void(0)" id="download_file" title="download" onclick="download_dentry_excel();">
                                                    <img src="../images/Excel-Icon_1.png" height="40px"/>Download Format&ensp;
                                                </a>
                                            </td>
                                            <th>Upload Breeder-Daily Entry</th>
                                            <td>
                                                <input type="file" name="file_uploads" id="file_uploads" class="form-control-file" />
                                            </td>
                                            <th><button type="button" class="btn btn-success btn-sm" name="submit_import" id="submit_import" onclick="this.form.submit();">Import</button></th>
                                        </tr>
                                        <tr>
                                            <th colspan="4">
                                                <div id="table_container" style="display:none;">
                                                    <?php echo $hhtml; ?>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                </table>
                            </form>
                            <?php
                            if(!empty($_FILES['file_uploads']['name'])){
                                $file_name = $_FILES['file_uploads']['name'];
                                require_once('Classes/PHPExcel.php');
                                $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                                $allowed_ext = ['xls','csv','xlsx'];
                                if(in_array($file_ext, $allowed_ext)){
                                    $file_path = $_FILES['file_uploads']['tmp_name'];
                                    $read_excel = PHPExcel_IOFactory::createReaderForFile($file_path);
                                    $excel_obj = $read_excel->load($file_path);

                                    $excel_info = $excel_obj->getSheet('0');
                                    
                                    $act_rows = $excel_info->getHighestRow();
                                    $act_cols = $excel_info->getHighestDataColumn();
                                    $col_cno = PHPExcel_Cell::columnIndexFromString($act_cols);

                                    
                                    $heading_name = array();
                                    $row = 2;
                                    for($col = 0;$col < $col_cno;$col++){
                                        $hname = $excel_info->getCell(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getValue();
                                        $heading_name[$col] = $hname;
                                    }
                                    $html = ''; $cincr = 0;
                                    for($row = 3;$row <= $act_rows;$row++){
                                        $html .= '<tr>';
                                        $hvalue = $excel_info->getCell(PHPExcel_Cell::stringFromColumnIndex(0) . $row)->getValue();
                                        if($hvalue != ""){
                                            for($col = 0;$col < $col_cno;$col++){
                                                $hvalue = $excel_info->getCell(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getValue();
                                                if($col == 0){
                                                    $html .= '<td><input type="text" name="flock_code[]" id="flock_code['.$cincr.']" class="form-control" value="'.$hvalue.'" style="width:190px;" readonly /></td>';
                                                }
                                                else if($col == 1){
                                                    $html .= '<td><input type="text" name="date[]" id="date['.$cincr.']" class="form-control" value="'.$hvalue.'" style="width:110px;" readonly /></td>';
                                                }
                                                else if($col == 2){
                                                    $html .= '<td><input type="text" name="breed_wage[]" id="breed_wage['.$cincr.']" class="form-control text-right" value="'.$hvalue.'" style="width:60px;" readonly /></td>';
                                                }
                                                else if($col == 3){
                                                    $html .= '<td><input type="text" name="fmort_qty[]" id="fmort_qty['.$cincr.']" class="form-control f-info text-right" value="'.$hvalue.'" style="width:60px;" onkeyup="validate_count(this.id);" /></td>';
                                                }
                                                else if($col == 4){
                                                    $html .= '<td><input type="text" name="fcull_qty[]" id="fcull_qty['.$cincr.']" class="form-control f-info text-right" value="'.$hvalue.'" style="width:60px;" onkeyup="validate_count(this.id);" /></td>';
                                                }
                                                else if($col == 5){
                                                    $html .= '<td><input type="text" name="fbody_weight[]" id="fbody_weight['.$cincr.']" class="form-control f-info text-right" value="'.$hvalue.'" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                                                }
                                                else if($col == 6){
                                                    $html .= '<td><select name="ffeed_code1[]" id="ffeed_code1['.$cincr.']" class="form-control select2 f-info" style="width:190px;"><option value="select">-select-'.$hvalue.'</option>';
                                                    foreach($bffeed_code as $ucode){
                                                        if($hvalue == $ucode || strtolower($hvalue) == strtolower($bffeed_name[$ucode])){
                                                            $html .= '<option value="'.$ucode.'" selected>'.$bffeed_name[$ucode].'</option>';
                                                        }
                                                        else{
                                                            $html .= '<option value="'.$ucode.'">'.$bffeed_name[$ucode].'</option>';
                                                        }
                                                    }
                                                    $html .= '</select></td>';
                                                }
                                                else if($col == 7){
                                                    $html .= '<td><input type="text" name="ffeed_qty1[]" id="ffeed_qty1['.$cincr.']" class="form-control f-info text-right" value="'.$hvalue.'" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                                                }
                                                else if((int)$ffeed_2flag == 1 && $col == 8){
                                                    $html .= '<td><select name="ffeed_code2[]" id="ffeed_code2['.$cincr.']" class="form-control select2 f-info" style="width:190px;"><option value="select">-select-</option>';
                                                    foreach($bffeed_code as $ucode){
                                                        if($hvalue == $ucode || strtolower($hvalue) == strtolower($bffeed_name[$ucode])){
                                                            $html .= '<option value="'.$ucode.'" selected>'.$bffeed_name[$ucode].'</option>';
                                                        }
                                                        else{
                                                            $html .= '<option value="'.$ucode.'">'.$bffeed_name[$ucode].'</option>';
                                                        }
                                                    }
                                                    $html .= '</select></td>';
                                                }
                                                else if((int)$ffeed_2flag == 1 && $col == 9){
                                                    $html .= '<td><input type="text" name="ffeed_qty2[]" id="ffeed_qty2['.$cincr.']" class="form-control f-info text-right" value="'.$hvalue.'" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                                                }
                                                else if((int)$ffeed_2flag == 1 && $col == 10 || (int)$ffeed_2flag == 0 && $col == 8){
                                                    $html .= '<td><input type="text" name="mmort_qty[]" id="mmort_qty['.$cincr.']" class="form-control f-info text-right" value="'.$hvalue.'" style="width:60px;" onkeyup="validate_count(this.id);" /></td>';
                                                }
                                                else if((int)$ffeed_2flag == 1 && $col == 11 || (int)$ffeed_2flag == 0 && $col == 9){
                                                    $html .= '<td><input type="text" name="mcull_qty[]" id="mcull_qty['.$cincr.']" class="form-control f-info text-right" value="'.$hvalue.'" style="width:60px;" onkeyup="validate_count(this.id);" /></td>';
                                                }
                                                else if((int)$ffeed_2flag == 1 && $col == 12 || (int)$ffeed_2flag == 0 && $col == 10){
                                                    $html .= '<td><input type="text" name="mbody_weight[]" id="mbody_weight['.$cincr.']" class="form-control f-info text-right" value="'.$hvalue.'" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                                                }
                                                else if((int)$ffeed_2flag == 1 && $col == 13 || (int)$ffeed_2flag == 0 && $col == 11){
                                                    $html .= '<td><select name="mfeed_code1[]" id="mfeed_code1['.$cincr.']" class="form-control select2 f-info" style="width:190px;"><option value="select">-select-</option>';
                                                    foreach($bffeed_code as $ucode){
                                                        if($hvalue == $ucode || strtolower($hvalue) == strtolower($bffeed_name[$ucode])){
                                                            $html .= '<option value="'.$ucode.'" selected>'.$bffeed_name[$ucode].'</option>';
                                                        }
                                                        else{
                                                            $html .= '<option value="'.$ucode.'">'.$bffeed_name[$ucode].'</option>';
                                                        }
                                                    }
                                                    $html .= '</select></td>';
                                                }
                                                else if((int)$ffeed_2flag == 1 && $col == 14 || (int)$ffeed_2flag == 0 && $col == 12){
                                                    $html .= '<td><input type="text" name="mfeed_qty1[]" id="mfeed_qty1['.$cincr.']" class="form-control f-info text-right" value="'.$hvalue.'" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                                                }
                                                else if((int)$ffeed_2flag == 1 && (int)$mfeed_2flag == 1 && $col == 15 || (int)$ffeed_2flag == 0 && (int)$mfeed_2flag == 1 && $col == 13){
                                                    $html .= '<td><select name="mfeed_code2[]" id="mfeed_code2['.$cincr.']" class="form-control select2 f-info" style="width:190px;"><option value="select">-select-</option>';
                                                    foreach($bffeed_code as $ucode){
                                                        if($hvalue == $ucode || strtolower($hvalue) == strtolower($bffeed_name[$ucode])){
                                                            $html .= '<option value="'.$ucode.'" selected>'.$bffeed_name[$ucode].'</option>';
                                                        }
                                                        else{
                                                            $html .= '<option value="'.$ucode.'">'.$bffeed_name[$ucode].'</option>';
                                                        }
                                                    }
                                                    $html .= '</select></td>';
                                                }
                                                else if((int)$ffeed_2flag == 1 && (int)$mfeed_2flag == 1 && $col == 16 || (int)$ffeed_2flag == 0 && (int)$mfeed_2flag == 1 && $col == 14){
                                                    $html .= '<td><input type="text" name="mfeed_qty2[]" id="mfeed_qty2['.$cincr.']" class="form-control f-info text-right" value="'.$hvalue.'" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                                                }
                                                else if($heading_name[$col] == "Remarks"){
                                                    $html .= '<td><textarea name="remarks[]" id="remarks['.$cincr.']" class="form-control" style="padding:0;width:150px;height:28px;" onkeyup="validatename(this.id);">'.$hvalue.'</textarea></td>';
                                                }
                                                else if($heading_name[$col] == "Egg Wt."){
                                                    $html .= '<td><input type="text" name="egg_weight[]" id="egg_weight['.$cincr.']" class="form-control p-info text-right" value="'.$hvalue.'" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                                                }
                                                else if(!empty($begg_name2[$heading_name[$col]])){
                                                    $ikey = ""; $ikey = "egg_".$begg_name2[$heading_name[$col]];
                                                    $html .= '<td><input type="text" name="'.$ikey.'[]" id="'.$ikey.'['.$cincr.']" class="form-control p-info text-right" value="'.$hvalue.'" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                                                }
                                                else{ }
                                            }
                                            
                                            $html .= '<td style="visibility:hidden;"><input type="text" name="ffeed_sqty1[]" id="ffeed_sqty1['.$cincr.']" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>';
                                            $html .= '<td style="visibility:hidden;"><input type="text" name="ffeed_sprc1[]" id="ffeed_sprc1['.$cincr.']" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>';
                                            if((int)$ffeed_2flag == 1){
                                                $html .= '<td style="visibility:hidden;"><input type="text" name="ffeed_sqty2[]" id="ffeed_sqty2['.$cincr.']" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>';
                                                $html .= '<td style="visibility:hidden;"><input type="text" name="ffeed_sprc2[]" id="ffeed_sprc2['.$cincr.']" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>';
                                            }
                                            $html .= '<td style="visibility:hidden;"><input type="text" name="mfeed_sqty1[]" id="mfeed_sqty1['.$cincr.']" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>';
                                            $html .= '<td style="visibility:hidden;"><input type="text" name="mfeed_sprc1[]" id="mfeed_sprc1['.$cincr.']" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>';
                                            if((int)$ffeed_2flag == 1){
                                                $html .= '<td style="visibility:hidden;"><input type="text" name="mfeed_sqty2[]" id="mfeed_sqty2['.$cincr.']" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>';
                                                $html .= '<td style="visibility:hidden;"><input type="text" name="mfeed_sprc2[]" id="mfeed_sprc2['.$cincr.']" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>';
                                            }
                                            $html .= '<td style="visibility:hidden;"><input type="text" name="breed_age[]" id="breed_age['.$cincr.']" class="form-control text-right" style="width:20px;padding:0;padding-left:2px;" readonly /></td>';
                                            $html .= '</tr>';
                                            $cincr++;
                                        }
                                    }
                                    if($cincr > 0){ $cincr--; }
                                }
                            ?>
                            <form action="breeder_import_save_dailyentry1.php" method="post" role="form" onsubmit="return checkval()">
                                <div class="row row_body2">
                                    <table class="p-1 table1" style="width:auto;">
                                        <thead>
                                            <tr>
                                                <th colspan="3"></th>
                                                <?php if((int)$ffeed_2flag == 1){ $fcol_cnt += 2; ?><th colspan="7" style="background-color:#c94eff;text-align:center;">Female Birds</th><?php } else{ ?><th colspan="5" style="background-color:#c94eff;text-align:center;">Female Birds</th><?php } ?>
                                                <?php if((int)$mfeed_2flag == 1){ $fcol_cnt += 2; ?><th colspan="7" style="background-color:#ffb25b;text-align:center;">Male Birds</th><?php } else{ ?><th colspan="5" style="background-color:#ffb25b;text-align:center;">Male Birds</th><?php } ?>
                                                <th></th>
                                                <?php if((int)$esize > 0){ ?><th colspan="<?php echo $esize + 1; ?>" style="background-color:#0b9100;text-align:center;" class="egg_list">Egg Production</th><?php } ?>
                                                <th colspan="<?php echo $fcol_cnt; ?>" style="background-color:#00d7a3;text-align:center;visibility:hidden;">Stock</th>
                                            </tr>
                                            <tr>
                                                <th style="text-align:center;"><label>Flock</label></th>
                                                <th style="text-align:center;"><label>Date</label></th>
                                                <th style="text-align:center;"><label>Age</label></th>

                                                <!--Female Bird and Feed Details-->
                                                <th style="text-align:center;"><label>Mort.</label></th>
                                                <th style="text-align:center;"><label>Culls</label></th>
                                                <th style="text-align:center;"><label>B.Wt(Grams)</label></th>
                                                <th style="text-align:center;"><label>Feed</label></th>
                                                <?php if((int)$bfstk_bags == 1){ ?><th style="text-align:center;"><label>Bag's</label></th><?php } else{ ?><th style="text-align:center;"><label>Kg's</label></th><?php } ?>
                                                <?php if((int)$ffeed_2flag == 1){ ?>
                                                <th style="text-align:center;"><label>Feed-2</label></th>
                                                <?php if((int)$bfstk_bags == 1){ ?><th style="text-align:center;"><label>Bag's</label></th><?php } else{ ?><th style="text-align:center;"><label>Kg's</label></th><?php } ?>
                                                <?php } ?>
                                                    
                                                <!--Male Bird and Feed Details-->
                                                <th style="text-align:center;"><label>Mort.</label></th>
                                                <th style="text-align:center;"><label>Culls</label></th>
                                                <th style="text-align:center;"><label>B.Wt(Grams)</label></th>
                                                <th style="text-align:center;"><label>Feed</label></th>
                                                <?php if((int)$bfstk_bags == 1){ ?><th style="text-align:center;"><label>Bag's</label></th><?php } else{ ?><th style="text-align:center;"><label>Kg's</label></th><?php } ?>
                                                <?php if((int)$mfeed_2flag == 1){ ?>
                                                <th style="text-align:center;"><label>Feed-2</label></th>
                                                <?php if((int)$bfstk_bags == 1){ ?><th style="text-align:center;"><label>Bag's</label></th><?php } else{ ?><th style="text-align:center;"><label>Kg's</label></th><?php } ?>
                                                <?php } ?>
                                                <th style="text-align:center;"><label>Remarks</label></th>
                                                <!--Egg Production Details-->
                                                <?php
                                                foreach($begg_code as $icode){
                                                    echo '<th style="text-align:center;" class="egg_list">'.$begg_name[$icode].'</th>';
                                                }
                                                ?>
                                                <?php if((int)$esize > 0){ ?>
                                                    <th style="text-align:center;" class="egg_list">Egg Wt.</th>
                                                <?php } ?>
                                                <th style="text-align:center;visibility:hidden;">AG</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody">
                                            <?php echo $html; ?>
                                        </tbody>
                                    </table>
                                </div><br/>
                                <div class="row" style="visibility:hidden;">
                                    <div class="form-group" style="width:30px;">
                                        <label>IN</label>
                                        <input type="text" name="incr" id="incr" class="form-control" value="<?php echo $cincr; ?>" style="padding:0;width:20px;" readonly />
                                    </div>
                                    <div class="form-group" style="width:30px;">
                                        <label>EB</label>
                                        <input type="text" name="ebtncount" id="ebtncount" class="form-control" value="0" style="padding:0;width:20px;" readonly />
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group" align="center">
                                        <button type="submit" name="submit" id="submit" class="btn btn-sm bg-purple">Submit</button>&ensp;
                                        <button type="button" name="cancel" id="cancel" class="btn btn-sm bg-danger" onclick="return_back()">Cancel</button>
                                    </div>
                                </div>
                            </form>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- Datepicker -->
        <script src="datepicker/jquery/jquery.js"></script>
        <script src="datepicker/jquery-ui.js"></script>
        <script>
			function checkval2(){
                var file_uploads = document.getElementById("file_uploads").value;
                var l = true;
                if(file_uploads == ""){
                    alert("Please select a data file to Import.");
                    l = false;
                }
                if(l == true){ return true; } else{ return false; }
            }
			function checkval(){
				update_ebtn_status(1);
                var l = true;
                var incr = document.getElementById("incr").value;
                var flock_code = date = rdate = ""; var e = f = breed_wage = ffeed_qty1 = ffeed_sqty1 = ffeed_qty2 = ffeed_sqty2 = mfeed_qty1 = mfeed_sqty1 = mfeed_qty2 = mfeed_sqty2 = 0;
                var feed_aflag = '<?php echo $feed_aflag; ?>';
                var ffeed_2flag = '<?php echo $ffeed_2flag; ?>';
                var mfeed_2flag = '<?php echo $mfeed_2flag; ?>';
                for(var d = 0;d <= incr;d++){
                    if(l == true){
                        e = d + 1;
                        flock_code = document.getElementById("flock_code["+d+"]").value;
                        date = document.getElementById("date["+d+"]").value;
                        breed_wage = document.getElementById("breed_age["+d+"]").value;
                        ffeed_qty1 = document.getElementById("ffeed_qty1["+d+"]").value; if(ffeed_qty1 == ""){ ffeed_qty1 = 0; }
                        ffeed_sqty1 = document.getElementById("ffeed_sqty1["+d+"]").value; if(ffeed_sqty1 == ""){ ffeed_sqty1 = 0; }
                        if(parseInt(ffeed_2flag) == 1){
                            ffeed_qty2 = document.getElementById("ffeed_qty2["+d+"]").value; if(ffeed_qty2 == ""){ ffeed_qty2 = 0; }
                            ffeed_sqty2 = document.getElementById("ffeed_sqty2["+d+"]").value; if(ffeed_sqty2 == ""){ ffeed_sqty2 = 0; }
                        }
                        mfeed_qty1 = document.getElementById("mfeed_qty1["+d+"]").value; if(mfeed_qty1 == ""){ mfeed_qty1 = 0; }
                        mfeed_sqty1 = document.getElementById("mfeed_sqty1["+d+"]").value; if(mfeed_sqty1 == ""){ mfeed_sqty1 = 0; }
                        if(parseInt(mfeed_2flag) == 1){
                            mfeed_qty2 = document.getElementById("mfeed_qty2["+d+"]").value; if(mfeed_qty2 == ""){ mfeed_qty2 = 0; }
                            mfeed_sqty2 = document.getElementById("mfeed_sqty2["+d+"]").value; if(mfeed_sqty2 == ""){ mfeed_sqty2 = 0; }
                        }
                        if(flock_code == ""){
                            alert("Flock is empty in row: "+e);
                            document.getElementById("flock_code["+d+"]").focus();
                            l = false;
                        }
                        else if(date == ""){
                            alert("Date is empty in row: "+e);
                            document.getElementById("date["+d+"]").focus();
                            l = false;
                        }
                        else if(breed_age == ""){
                            alert("Age is empty in row: "+e);
                            document.getElementById("breed_age["+d+"]").focus();
                            l = false;
                        }
                        /*else if(parseInt(feed_aflag) == 1 && parseFloat(ffeed_qty1) > parseFloat(ffeed_sqty1)){
                            alert("Entered Female Feed Consumed Quantity is greater than Feed Stock Available.\n Available Stock is: "+ffeed_sqty1+" in row: "+e);
                            document.getElementById("ffeed_qty1["+d+"]").focus();
                            l = false;
                        }
                        else if(parseInt(ffeed_2flag) == 1 && parseInt(feed_aflag) == 1 && parseFloat(ffeed_qty2) > parseFloat(ffeed_sqty2)){
                            alert("Entered Female Feed-2 Consumed Quantity is greater than Feed Stock Available.\n Available Stock is: "+ffeed_sqty2+" in row: "+e);
                            document.getElementById("ffeed_qty2["+d+"]").focus();
                            l = false;
                        }
                        else if(parseInt(feed_aflag) == 1 && parseFloat(mfeed_qty1) > parseFloat(mfeed_sqty1)){
                            alert("Entered Male Feed Consumed Quantity is greater than Feed Stock Available.\n Available Stock is: "+mfeed_sqty1+" in row: "+e);
                            document.getElementById("mfeed_qty1["+d+"]").focus();
                            l = false;
                        }
                        else if(parseInt(mfeed_2flag) == 1 && parseInt(feed_aflag) == 1 && parseFloat(mfeed_qty2) > parseFloat(mfeed_sqty2)){
                            alert("Entered Male Feed-2 Consumed Quantity is greater than Feed Stock Available.\n Available Stock is: "+mfeed_sqty2+" in row: "+e);
                            document.getElementById("mfeed_qty2["+d+"]").focus();
                            l = false;
                        }*/
                        else{
                            for(var c = 0;c <= incr;c++){
                                if(l == true){
                                    if(c == d){ }
                                    else{
                                        rdate = document.getElementById("date["+c+"]").value;
                                        if(rdate == date){
                                            alert("Same date details already available in row: "+e);
                                            document.getElementById("date["+c+"]").focus();
                                            l = false;
                                        }
                                    }
                                }
                            } 
                        }
                    }
                }
                if(l == true){
                    var x = confirm("Are sure do you want to save this Entry?");
                    if(x == true){
                        return true;
                    }
                    else{
                        update_ebtn_status(0);
                        return false;
                    }
                }
                else{
                    update_ebtn_status(0);
                    return false;
                }
			}
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'breeder_display_dailyentry1.php?ccid='+ccid;
            }
            function download_dentry_excel(){
                var filename = 'breeder_import_dailyentry_flkwise';
                var table = document.getElementById("main_table");
                var workbook = XLSX.utils.book_new();
                var worksheet = XLSX.utils.table_to_sheet(table);
                XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
                XLSX.writeFile(workbook, filename+".xlsx");
            }
            function fetch_active_flock_list(){
                clear_data();
                removeAllOptions(document.getElementById("flock_code"));
                var shed_code = document.getElementById("shed_code").value;
                if(shed_code == "select" || shed_code == ""){ }
                else{
					var abrd_bchs = new XMLHttpRequest();
					var method = "GET";
					var url = "breeder_fetch_shedwise_avlflocks.php?shed_code="+shed_code;
                    //window.open(url);
					var asynchronous = true;
					abrd_bchs.open(method, url, asynchronous);
					abrd_bchs.send();
					abrd_bchs.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var bch_list = this.responseText;
                            $('#flock_code').append(bch_list);
						}
					}
                }
            }
            function fetch_flock_details(){
                update_ebtn_status(1);
                var flock_code = document.getElementById("flock_code").value;
                var incr = document.getElementById("incr").value;
                if(flock_code != "select"){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "breeder_fetch_flock_details.php?flock_code="+flock_code+"&incr="+incr;
                    //window.open(url);
					var asynchronous = true;
					oldqty.open(method, url, asynchronous);
					oldqty.send();
					oldqty.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var bbch_dt1 = this.responseText;
                            var bbch_dt2 = bbch_dt1.split("[@$&]");
                            var rows = bbch_dt2[0];
                            var date = bbch_dt2[1];
                            var breed_age = bbch_dt2[2];
                            var max_eflag = bbch_dt2[3];
                            if(parseInt(max_eflag) == 1){
                                alert("Upto date Daily Entry is available. Please check again.");
                                var a = "date["+rows+"]";
                                destroy_row(a);
                            }
                            else{
                                var breed_wage = calculate_age_weeks(breed_age);
                                document.getElementById("date["+rows+"]").value = date;
                                document.getElementById("breed_age["+rows+"]").value = breed_age;
                                document.getElementById("breed_wage["+rows+"]").value = breed_wage;
                            }
                            update_ebtn_status(0);
                            fetch_feedstock_items();
						}
                        else{
                            update_ebtn_status(0);
                            fetch_feedstock_items();
                        }
					}
                }
                else{
                    update_ebtn_status(0);
                    fetch_feedstock_items();
                }
            }
            function update_eggprod_fields(){
                var beps_flag = document.getElementById("beps_flag").value; if(beps_flag == ""){ beps_flag = 0; }
                var egg_list = document.getElementsByClassName("egg_list");
                for(let i = 0;i < egg_list.length;i++) {
                    if(parseInt(beps_flag) == 1){
                        egg_list[i].style.width = "90px";
                        egg_list[i].style.visibility = "visible";
                    }
                    else{
                        egg_list[i].style.width = "1px";
                        egg_list[i].style.visibility = "hidden";
                    }
                }
            }
            function fetch_feedstock_items(){
                var feed_aflag = '<?php echo $feed_aflag; ?>';
                if(parseInt(feed_aflag) == 1){
                    update_ebtn_status(1);
                    var shed_sflag = '<?php echo $shed_sflag; ?>';
                    var shed_code = "";
                    if(parseInt(shed_sflag) == 1){ shed_code = document.getElementById("shed_code").value; }
                    var flock_code = document.getElementById("flock_code").value;
                    var ffeed_2flag = '<?php echo $ffeed_2flag; ?>';
                    var mfeed_2flag = '<?php echo $mfeed_2flag; ?>';
                    
                    var d = document.getElementById("incr").value;
                    var date = document.getElementById("date["+d+"]").value;
                    removeAllOptions(document.getElementById("ffeed_code1["+d+"]"));
                    if(parseInt(ffeed_2flag) == 1){ removeAllOptions(document.getElementById("ffeed_code2["+d+"]")); }
                    removeAllOptions(document.getElementById("mfeed_code1["+d+"]"));
                    if(parseInt(mfeed_2flag) == 1){ removeAllOptions(document.getElementById("mfeed_code2["+d+"]")); }
                    
                    if(flock_code != "select" && date != ""){
                        var oldqty = new XMLHttpRequest();
                        var method = "GET";
                        var url = "breeder_fetch_avlstock_items.php?date="+date+"&flock_code="+flock_code+"&rows="+d+"&itype=feed&ftype=brd_dentry&ttype=add&shed_code="+shed_code;
                        //window.open(url);
                        var asynchronous = true;
                        oldqty.open(method, url, asynchronous);
                        oldqty.send();
                        oldqty.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var bfeed_dt1 = this.responseText;
                                var bfeed_dt2 = bfeed_dt1.split("[@$&]");
                                var err_flag = bfeed_dt2[0];
                                var err_msg = bfeed_dt2[1];
                                var rows = bfeed_dt2[2];
                                var ffeed_opt = bfeed_dt2[3];
                                var mfeed_opt = bfeed_dt2[4];
                                if(parseInt(err_flag) == 1){ alert(err_msg); }
                                else{ }
                                $('#ffeed_code1\\['+rows+'\\]').append(ffeed_opt);
                                if(parseInt(ffeed_2flag) == 1){ $('#ffeed_code2\\['+rows+'\\]').append(ffeed_opt); }
                                $('#mfeed_code1\\['+rows+'\\]').append(mfeed_opt);
                                if(parseInt(mfeed_2flag) == 1){ $('#mfeed_code2\\['+rows+'\\]').append(mfeed_opt); }
                                update_ebtn_status(0);
                            }
                        }
                    }
                }
                else{ update_ebtn_status(0); }
            }
            function fetch_feedstock_qty(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                update_ebtn_status(1);
                var shed_code = "";
                var flock_code = document.getElementById("flock_code").value;
                var date = document.getElementById("date["+d+"]").value;
                var feeds = document.getElementById(a).value;

                var sq_id = a.replace('code', 'sqty');
                var sp_id = a.replace('code', 'sprc');
                document.getElementById(sq_id).value = 0;
                document.getElementById(sp_id).value = 0;
                var ffeed_2flag = '<?php echo $ffeed_2flag; ?>';
                var mfeed_2flag = '<?php echo $mfeed_2flag; ?>';

                var incr = document.getElementById("incr").value;
                if(feeds == "" || feeds == "select" || flock_code == "" || flock_code == "select" || date == ""){ update_ebtn_status(0); }
                else{
                    var oldqty = new XMLHttpRequest();
                    var method = "GET";
                    var url = "breeder_fetch_avlstock_quantity.php?date="+date+"&flock_code="+flock_code+"&item_code="+feeds+"&rows="+d+"&itype=feed&ftype=brd_dentry&ttype=add&shed_code="+shed_code;
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
                                var ffeed_code1 = ffeed_code2 = mfeed_code1 = mfeed_code2 = "";
                                var ffeed_qty1 = ffeed_qty2 = mfeed_qty1 = mfeed_qty2 = 0;
                                for(var d = 0;d <= incr;d++){
                                    ffeed_code1 = document.getElementById("ffeed_code1["+d+"]").value;
                                    ffeed_qty1 = document.getElementById("ffeed_qty1["+d+"]").value; if(ffeed_qty1 == ""){ ffeed_qty1 = 0; }
                                    if(feeds == ffeed_code1){ item_qty = parseFloat(item_qty) - parseFloat(ffeed_qty1); }

                                    if(parseInt(ffeed_2flag) == 1){
                                        ffeed_code2 = document.getElementById("ffeed_code2["+d+"]").value;
                                        ffeed_qty2 = document.getElementById("ffeed_qty2["+d+"]").value; if(ffeed_qty2 == ""){ ffeed_qty2 = 0; }
                                        if(feeds == ffeed_code2){ item_qty = parseFloat(item_qty) - parseFloat(ffeed_qty2); }
                                    }
                                    mfeed_code1 = document.getElementById("mfeed_code1["+d+"]").value;
                                    mfeed_qty1 = document.getElementById("mfeed_qty1["+d+"]").value; if(mfeed_qty1 == ""){ mfeed_qty1 = 0; }
                                    if(feeds == mfeed_code1){ item_qty = parseFloat(item_qty) - parseFloat(mfeed_qty1); }

                                    if(parseInt(mfeed_2flag) == 1){
                                        mfeed_code2 = document.getElementById("mfeed_code2["+d+"]").value;
                                        mfeed_qty2 = document.getElementById("mfeed_qty2["+d+"]").value; if(mfeed_qty2 == ""){ mfeed_qty2 = 0; }
                                        if(feeds == mfeed_code2){ item_qty = parseFloat(item_qty) - parseFloat(mfeed_qty2); }
                                    }
                                }
                                document.getElementById(sq_id).value = parseFloat(item_qty).toFixed(2);
                                document.getElementById(sp_id).value = parseFloat(item_prc).toFixed(5);
                            }
                            update_ebtn_status(0);
                        }
                    }
                }
            }
            function calculate_rowwise_itemstk(){ }
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
            function calculate_age_weeks(d_age){
                var week_no = Math.floor(parseFloat(d_age) / 7);
                var age_no = parseFloat(d_age) % 7;
                if(parseInt(age_no) == 0){
                    age_no = 7; week_no = parseInt(week_no) - 1;
                }
                var age_weeks = week_no+"."+age_no;
                return parseFloat(age_weeks).toFixed(1);
            }
            function convert_age_weeks(){
                var incr = document.getElementById("incr").value;
                var breed_wage = breed_age = age1 = 0;
                for(var d = 0;d <= incr;d++){
                    breed_wage = breed_age = 0;
                    breed_wage = document.getElementById("breed_wage["+d+"]").value; if(breed_wage == ""){ breed_wage = 0; }
                    if(parseFloat(breed_wage) > 0){
                        var age1 = Math.floor(breed_wage);
                        var age2 = Math.round((parseFloat(breed_wage) - parseInt(age1)) * 10);
                        breed_age = ((parseInt(age1) * 7) + parseInt(age2));
                    }
                    document.getElementById("breed_age["+d+"]").value = parseInt(breed_age);
                }
                var week_no = Math.floor(parseFloat(d_age) / 7);
                var age_no = parseFloat(d_age) % 7;
                if(parseInt(age_no) == 0){
                    age_no = 7; week_no = parseInt(week_no) - 1;
                }
                var age_weeks = week_no+"."+age_no;
                return parseFloat(age_weeks).toFixed(1);
            }
            convert_age_weeks();
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