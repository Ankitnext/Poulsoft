<?php
//breeder_coldroom_stocks1.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    global $page_title; $page_title = "Cold Room Stock Report";
    include "header_head.php";
    $form_path = "breeder_coldroom_stocks1.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    global $page_title; $page_title = "Cold Room Stock Report";
    include "header_head.php";
    $form_path = "breeder_coldroom_stocks1.php?db=$db&userid=".$user_code;
}
include "decimal_adjustments.php";

$file_name = "Cold Room Stock Report";
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

/*Check for Table Availability*/
$database_name = $dbname; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("main_officetypes", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.main_officetypes LIKE poulso6_admin_broiler_broilermaster.main_officetypes;"; mysqli_query($conn,$sql1); }
if(in_array("inv_sectors", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.inv_sectors LIKE poulso6_admin_broiler_broilermaster.inv_sectors;"; mysqli_query($conn,$sql1); }
if(in_array("plant_bird_received_main_details", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.plant_bird_received_main_details LIKE poulso6_admin_broiler_broilermaster.plant_bird_received_main_details;"; mysqli_query($conn,$sql1); }
if(in_array("plant_bird_received_link_details", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.plant_bird_received_link_details LIKE poulso6_admin_broiler_broilermaster.plant_bird_received_link_details;"; mysqli_query($conn,$sql1); }
if(in_array("plant_bird_grading_details", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.plant_bird_grading_details LIKE poulso6_admin_broiler_broilermaster.plant_bird_grading_details;"; mysqli_query($conn,$sql1); }
if(in_array("plant_bird_grading_item_stocks", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.plant_bird_grading_item_stocks LIKE poulso6_admin_broiler_broilermaster.plant_bird_grading_item_stocks;"; mysqli_query($conn,$sql1); }
if(in_array("plant_bird_portioning_consumed_details", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.plant_bird_portioning_consumed_details LIKE poulso6_admin_broiler_broilermaster.plant_bird_portioning_consumed_details;"; mysqli_query($conn,$sql1); }
if(in_array("plant_bird_portioning_produced_details", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.plant_bird_portioning_produced_details LIKE poulso6_admin_broiler_broilermaster.plant_bird_portioning_produced_details;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_sales", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_sales LIKE poulso6_admin_broiler_broilermaster.broiler_sales;"; mysqli_query($conn,$sql1); }

//Breeder Egg Details
$sql = "SELECT * FROM `item_category` WHERE `dflag` = '0' AND `begg_flag` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $cegg_code = $icat_iac = array();
while($row = mysqli_fetch_assoc($query)){ $cegg_code[$row['code']] = $row['code']; $icat_iac[$row['code']] = $row['iac']; } $egg_list = implode("','", $cegg_code);
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$egg_list') AND `description` NOT IN ('JU') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql); $egg_code = $egg_name = array();
while($row = mysqli_fetch_assoc($query)){ $egg_code[$row['code']] = $row['code']; $egg_name[$row['code']] = $row['description']; }
$e_cnt = sizeof($egg_code);

$sql = "SELECT * FROM `item_details` WHERE (`description` LIKE '%Hatch Egg%' OR `description` LIKE 'HE') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC";
$query = mysqli_query($conn,$sql); $hegg_code = "";
while($row = mysqli_fetch_assoc($query)){ $hegg_code = $row['code']; }

$sql = "SELECT * FROM `main_officetypes` WHERE `description` LIKE '%Cold Room%' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $sector_alist = array(); 
while($row = mysqli_fetch_assoc($query)){ $sector_alist[$row['code']] = $row['code']; }

$hatchery_list = implode("','",$sector_alist);
$sql = "SELECT * FROM `inv_sectors` WHERE `type` IN ('$hatchery_list') AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$tdate = date("Y-m-d"); $sectors = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $tdate = date("Y-m-d",strtotime($_REQUEST['tdate']));
    $sectors = $_POST['sectors'];
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
        <table id="main_table" class="tbl" align="center">
            <thead class="thead3" align="center" width="auto">
                <tr align="center">
                    <th colspan="4" align="center"><img src="<?php echo $img_logo; ?>" height="110px"/></th>
                    <th colspan="20" align="center"><?php echo $cdetails; ?><h5><?php echo $file_name; ?></h5></th>
                </tr>
            </thead>
            <form action="<?php echo $form_path; ?>" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed" width="auto" <?php if($excel_type == "print"){ echo 'style="display:none;"'; } ?>>
                    <tr>
                        <th colspan="24">
                            <div class="row">
                                <div class="m-2 form-group" style="width:120px;">
                                    <label> Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" readonly />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Cold Room</label>
                                    <select name="sectors" id="sectors" class="form-control select2">
                                        <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($sector_code as $scode){ if($sector_name[$scode] != ""){ ?>
                                        <option value="<?php echo $scode; ?>" <?php if($sectors == $scode){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2" onchange="download_to_excel('main_table','<?php echo $file_name; ?>');">
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
            <?php
            
            $html = $nhtml = $fhtml = '';
            $html .= '<thead class="thead3" id="head_names">';

            $nhtml .= '<tr style="text-align:center;" align="center">';
            $fhtml .= '<tr style="text-align:center;" align="center">';

            $nhtml .= '<th>Sl.No.</th>'; $fhtml .= '<th id="order_num">Sl.No.</th>';
            $nhtml .= '<th>Flock</th>'; $fhtml .= '<th id="order">Flock</th>';
            $nhtml .= '<th>Age</th>'; $fhtml .= '<th id="order">Age</th>';
            $nhtml .= '<th>T. Eggs</th>'; $fhtml .= '<th id="order_num">T. Eggs</th>';
            foreach($egg_code as $eggs){ $nhtml .= '<th>'.$egg_name[$eggs].'</th>'; $fhtml .= '<th id="order_num">'.$egg_name[$eggs].'</th>'; }
            $nhtml .= '<th>Opening</th>'; $fhtml .= '<th id="order_num">Opening</th>';
            $nhtml .= '<th>Setting</th>'; $fhtml .= '<th id="order_num">Setting</th>';
            $nhtml .= '<th>Sales</th>'; $fhtml .= '<th id="order_num">Sales</th>';
            $nhtml .= '<th>Balance</th>'; $fhtml .= '<th id="order_num">Balance</th>';

            $nhtml .= '</tr>';
            $fhtml .= '</tr>';
            $html .= $fhtml;
            $html .= '</thead>';
            $html .= '<tbody class="tbody1" id="tbody1">';

            if(isset($_POST['submit_report']) == true){
                $sec_fltr = ""; if($sectors != "all"){ $sec_fltr = " AND `sector_code` IN ('$sectors')"; }

                $sql = "SELECT * FROM `broiler_secunit_mapping` WHERE `dflag` = '0'".$sec_fltr."";
                $query = mysqli_query($conn,$sql); $unit_alist = array();
                while($row = mysqli_fetch_array($query)){ $unit_alist[$row['unit_code']] = $row['unit_code']; }
                $unit_list = implode("','", $unit_alist);

                $sql = "SELECT * FROM `breeder_shed_allocation` WHERE `dflag` = '0' AND `unit_code` IN ('$unit_list')";
                $query = mysqli_query($conn,$sql); $flock_name = $flock_alist = array();
                while($row = mysqli_fetch_array($query)){ $flock_alist[$row['code']] = $row['code'];$flock_name[$row['code']] = $row['description']; $flock_code[$row['code']] = $row['code']; }
                $flock_list = implode("','",$flock_alist);

                $sql = "SELECT * FROM `breeder_dayentry_produced` WHERE `date` <= '$tdate' AND `flock_code` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0'";
                $query = mysqli_query($conn,$sql); $odep_qty = $bdep_qty = $breed_wage = array();
                while($row = mysqli_fetch_array($query)){
                    $key1 = $row['flock_code']."@".$row['item_code'];  $flk = $row['flock_code'];
                    if(strtotime($row['date']) < strtotime($tdate)){ if($row['item_code'] == $hegg_code){ $odep_qty[$flk] += (float)$row['quantity']; } } 
                    else{
                        if($row['item_code'] == $hegg_code){ $bdep_qty[$key1] += (float)$row['quantity']; }
                        if(empty($breed_wage[$flk]) || $breed_wage[$flk] == ""){ $breed_wage[$flk] = decimal_adjustments($row['breed_wage'],1); }
                    }
                }

                $sql = "SELECT flock_code,MAX(breed_wage) as breed_wage FROM `breeder_dayentry_consumed` WHERE `date` <= '$tdate' AND `flock_code` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `flock_code` ORDER BY `flock_code` ASC";
                $query = mysqli_query($conn,$sql); //$breed_wage = array();
                while($row = mysqli_fetch_array($query)){
                    $flk = $row['flock_code'];
                    if(empty($breed_wage[$flk]) || $breed_wage[$flk] == ""){ $breed_wage[$flk] = decimal_adjustments($row['breed_wage'],1); }
                }

                $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `from_flock` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0'";
                $query = mysqli_query($conn,$sql); $oetr_qty = $betr_qty = array();
                while($row = mysqli_fetch_array($query)){
                    $key1 = $row['from_flock']; 
                    if(strtotime($row['date']) < strtotime($tdate)){  $oetr_qty[$key1] += (float)$row['quantity']; } 
                    else{ $betr_qty[$key1] += (float)$row['quantity']; }
                }

                $sql = "SELECT * FROM `broiler_sales` WHERE `date` <= '$tdate' AND `flock_code` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0'";
                $query = mysqli_query($conn,$sql); $osal_qty = $bsal_qty = array();
                while($row = mysqli_fetch_array($query)){
                    $key1 = $row['flock_code'];
                    if(strtotime($row['date']) < strtotime($tdate)){ $osal_qty[$key1] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']); } 
                    else{ $bsal_qty[$key1] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']); }
                }

                $sql = "SELECT * FROM `breeder_egg_conversion` WHERE `date` <= '$tdate' AND `flock_code` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0'";
                $query = mysqli_query($conn,$sql); $otegc_qty = $odegc_qty = $ofegc_qty = $btegc_qty = $bfegc_qty = $bdegc_qty = array();
                while($row = mysqli_fetch_array($query)){
                    $key1 = $row['flock_code']."@".$row['from_item'];
                    $key2 = $row['flock_code']."@".$row['to_item'];
                    $key3 = $row['flock_code'];
                    if(strtotime($row['date']) < strtotime($tdate)){ 
                        $otegc_qty[$key3] += (float)$row['to_qty'];
                        $odegc_qty[$key3] += (float)$row['disposed_qty'];
                        $ofegc_qty[$key3] += (float)$row['to_qty'] + (float)$row['disposed_qty']; 
                    }
                    else{ 
                        $btegc_qty[$key2] += (float)$row['to_qty'];
                        $bdegc_qty[$key2] += (float)$row['disposed_qty'];
                        $bfegc_qty[$key1] += (float)$row['to_qty'] + (float)$row['disposed_qty'];
                        /*if($row['flock_code'] == "BFLK-0009"){
                            echo "<br/>".$row['to_qty']."@".$row['disposed_qty']."@".$bfegc_qty[$key1];
                        }*/
                    }
                }

                $slno = 0; $tegg_qty = array();
                foreach($flock_code as $key){
                    //if(!empty($breed_wage[$key]) && $breed_wage[$key] != ""){
                        $opn_qty = (float)$odep_qty[$key] - (float)$oetr_qty[$key] - (float)$osal_qty[$key] - (float)$ofegc_qty[$key];
                        $rhe_qty = $teg_qty = 0;
                        foreach($egg_code as $eggs){
                            $key2 = $key."@".$eggs;
                            if($eggs == $hegg_code){
                                if(empty($bdep_qty[$key2]) || $bdep_qty[$key2] == ""){ } else{ $rhe_qty += (float)$bdep_qty[$key2]; $teg_qty += (float)$bdep_qty[$key2]; }
                                if(empty($bfegc_qty[$key2]) || $bfegc_qty[$key2] == ""){ } else{ $rhe_qty -= (float)$bfegc_qty[$key2]; }
                            }
                        }
                        $fbal_qty = 0; $fbal_qty = ((float)$opn_qty + (float)$rhe_qty - ((float)$betr_qty[$key] + (float)$bsal_qty[$key]));
                        //echo "<br/>$flock_name[$key]@((float)$opn_qty + (float)$rhe_qty - ((float)$betr_qty[$key] + (float)$bsal_qty[$key]))";
                        if((float)$opn_qty > 0 || (float)$betr_qty[$key] > 0 || (float)$bsal_qty[$key] > 0 || (float)$fbal_qty > 0 || (float)$teg_qty > 0){
                            $slno++;
                            $html .= '<tr>';
                            $html .= '<td style="text-align:center;">'.$slno.'</td>';
                            $html .= '<td style="text-align:center;">'.$flock_name[$key].'</td>';
                            $html .= '<td style="text-align:center;">'.$breed_wage[$key].'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($teg_qty,5))).'</td>';
                            foreach($egg_code as $eggs){
                                $key2 = $key."@".$eggs;
                                if($eggs == $hegg_code){
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($rhe_qty,5))).'</td>';
                                    $tegg_qty[$eggs] += (float)$rhe_qty;
                                }
                                else{
                                    if(empty($btegc_qty[$key2]) || $btegc_qty[$key2] == ""){ $egg_qty = 0; } else{ $egg_qty = $btegc_qty[$key2]; }
                                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($egg_qty,5))).'</td>';
                                    $tegg_qty[$eggs] += (float)$egg_qty;
                                }
                            }
                            
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($opn_qty,5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($betr_qty[$key],5))).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($bsal_qty[$key],5))).'</td>';

                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($fbal_qty,5))).'</td>';
                            $html .= '</tr>';

                            $topn_qty += (float)$opn_qty;
                            $tteg_qty += (float)$teg_qty;
                            $tbetr_qty += (float)$betr_qty[$key];
                            $tbsal_qty += (float)$bsal_qty[$key];
                            $tfbal_qty += (float)$fbal_qty;
                        }
                    //}
                }
            }
            $html .= '</tbody>';
            $html .= '<tr class="thead2">';
            $html .= '<th colspan="3">Total</th>';
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tteg_qty,5))).'</th>';
            foreach($egg_code as $eggs){
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tegg_qty[$eggs],5))).'</th>';
            }
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($topn_qty,5))).'</th>';
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tbetr_qty,5))).'</th>';
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tbsal_qty,5))).'</th>';
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tfbal_qty,5))).'</th>';
            $html .= '</tr>';

            echo $html;
        ?>
        </table><br/><br/><br/>
        <script type="text/javascript" src="table_sorting_wauto_slno.js"></script>
        <script type="text/javascript" src="table_search_fields.js"></script>
        <script type="text/javascript" src="table_download_excel.js"></script>
        <script type="text/javascript" src="table_column_date_format_change.js"></script>
        <script type="text/javascript">
            function table_file_details1(){
                var dbname = '<?php echo $dbname; ?>';
                var fname = '<?php echo $wsfile_path; ?>';
                var wapp_msg = '<?php echo $file_name; ?>';
                var sms_type = '<?php echo $sms_type; ?>';
                return dbname+"[@$&]"+fname+"[@$&]"+wapp_msg+"[@$&]"+sms_type;
            }
            function table_heading_to_normal1(){
                document.getElementById("head_names").innerHTML = "";
                var html = '';
                html += '<?php echo $nhtml; ?>';
                $('#head_names').append(html);
            }
            function table_heading_to_normal2(){
                document.getElementById("head_names").innerHTML = "";
                var html = '';
                html += '<?php echo $hhtml; ?>';
                html += '<?php echo $nhtml; ?>';
                $('#head_names').append(html);
            }
            function table_heading_to_standard_filters(){
                document.getElementById("head_names").innerHTML = "";
                var html = '';
                html += '<?php echo $fhtml; ?>';
                document.getElementById("head_names").innerHTML = html;
                    
                $('#export').select2();
                document.getElementById("export").value = "display";
                $('#export').select2();
                table_sort();
                table_sort2();
                table_sort3();
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