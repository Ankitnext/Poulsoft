<?php
//breeder_flockwise_detaileddayrecord1.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    include "header_head.php";
    $form_path = "breeder_flockwise_detaileddayrecord1.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    include "header_head.php";
    $form_path = "breeder_flockwise_detaileddayrecord1.php?db=$db&userid=".$user_code;
}
include "decimal_adjustments.php";

/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("breeder_farms", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_farms LIKE poulso6_admin_breeder_breedermaster.breeder_farms;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_units", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_units LIKE poulso6_admin_breeder_breedermaster.breeder_units;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_sheds", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_sheds LIKE poulso6_admin_breeder_breedermaster.breeder_sheds;"; mysqli_query($conn,$sql1); }
if(in_array("breeder_shed_allocation", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_shed_allocation LIKE poulso6_admin_breeder_breedermaster.breeder_shed_allocation;"; mysqli_query($conn,$sql1); }

$file_name = "Flock Report";
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

$sql = "SELECT * FROM `breeder_farms` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $farm_code = $farm_name = $farm_ccode = array();
while($row = mysqli_fetch_assoc($query)){ $farm_code[$row['code']] = $row['code']; $farm_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `breeder_units` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $unit_code = $unit_name = $unit_ccode = array();
while($row = mysqli_fetch_assoc($query)){ $unit_code[$row['code']] = $row['code']; $unit_name[$row['code']] = $row['description'];  }

$sql = "SELECT * FROM `breeder_sheds` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $shed_code = $shed_name = $shed_ccode = array();
while($row = mysqli_fetch_assoc($query)){ $shed_code[$row['code']] = $row['code']; $shed_name[$row['code']] = $row['description'];  }


$sql = "SELECT * FROM `breeder_batch` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $batch_code = $batch_name = $batch_breed = array();
while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_breed[$row['code']] = $row['breed_code']; }

$sql = "SELECT * FROM `breeder_shed_allocation` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $flock_code = $flock_name = $flock_sdate = $flock_sage = $flock_batch = array();
while($row = mysqli_fetch_assoc($query)){ $flock_code[$row['code']] = $row['code']; $flock_name[$row['code']] = $row['description']; $flock_sdate[$row['code']] = $row['start_date']; $flock_sage[$row['code']] = $row['start_age']; $flock_batch[$row['code']] = $row['batch_code']; }


$fdate = $tdate = date("Y-m-d"); $farms = $units = $sheds = $batches = $flocks = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_REQUEST['fdate']));
    $tdate = date("Y-m-d",strtotime($_REQUEST['tdate']));
    $units = $_POST['units'];
    $farms = $_POST['farms'];
    $sheds = $_POST['sheds'];
    $batches = $_POST['batches'];
    $flocks = $_POST['flocks'];
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
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" readonly />
                                </div><div class="m-2 form-group" style="width:120px;">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" readonly />
                                </div>
                                <div class="m-2 form-group" style="width:230px;">
                                    <label for="farms">Farm</label>
                                    <select name="farms" id="farms" class="form-control select2" style="width:220px;" onchange="fetch_flock_details(this.id);">
                                        <option value="all" <?php if($farms == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($farm_code as $bcode){ if($farm_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($farms == $bcode){ echo "selected"; } ?>><?php echo $farm_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div> 
                                <div class="m-2 form-group" style="width:230px;">
                                    <label for="unit_code">Unit</label>
                                    <select name="units" id="units" class="form-control select2" style="width:220px;" onchange="fetch_flock_details(this.id);">
                                        <option value="all" <?php if($units == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($unit_code as $ucode){ if($unit_name[$ucode] != ""){ ?>
                                        <option value="<?php echo $ucode; ?>" <?php if($units == $ucode){ echo "selected"; } ?>><?php echo $unit_name[$ucode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div> <div class="m-2 form-group" style="width:230px;">
                                    <label for="unit_code">Shed</label>
                                    <select name="sheds" id="sheds" class="form-control select2" style="width:220px;" onchange="fetch_flock_details(this.id);">
                                        <option value="all" <?php if($sheds == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($shed_code as $scode){ if($shed_name[$scode] != ""){ ?>
                                        <option value="<?php echo $scode; ?>" <?php if($sheds == $scode){ echo "selected"; } ?>><?php echo $shed_name[$scode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width:230px;">
                                    <label for="batches">Batch</label>
                                    <select name="batches" id="batches" class="form-control select2" style="width:220px;" onchange="fetch_flock_details(this.id);">
                                        <option value="all" <?php if($batches == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($batch_code as $bcode){ if($batch_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($batches == $bcode){ echo "selected"; } ?>><?php echo $batch_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width:230px;">
                                    <label for="flocks">Flock</label>
                                    <select name="flocks" id="flocks" class="form-control select2" style="width:220px;" onchange="fetch_flock_details(this.id);">
                                        <option value="all" <?php if($flocks == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($flock_code as $bcode){ if($flock_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($flocks == $bcode){ echo "selected"; } ?>><?php echo $flock_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
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

           
            $nhtml .= '<th>Unit</th>'; $fhtml .= '<th id="order">Unit</th>';
            $nhtml .= '<th>Shed</th>'; $fhtml .= '<th id="order">Shed</th>';
            $nhtml .= '<th>Flock No.</th>'; $fhtml .= '<th id="order">Flock No.</th>';
            $nhtml .= '<th>Date</th>'; $fhtml .= '<th id="order">Date</th>';
            $nhtml .= '<th>Age</th>'; $fhtml .= '<th id="order_num">Age</th>';
            $nhtml .= '<th>Opening Birds</th>'; $fhtml .= '<th id="order_num" colspan="2">Opening Birds</th>';
            $nhtml .= '<th>Mortality</th>'; $fhtml .= '<th id="order_num" colspan="2">Mortality</th>';
            $nhtml .= '<th>Culls</th>'; $fhtml .= '<th id="order_num" colspan="2">Culls</th>';
            $nhtml .= '<th>Sales</th>'; $fhtml .= '<th id="order_num" colspan="2">Sales</th>';
            $nhtml .= '<th>Transfer In</th>'; $fhtml .= '<th id="order_num" colspan="2">Transfer In</th>';
            $nhtml .= '<th>Transfer Out</th>'; $fhtml .= '<th id="order_num" colspan="2">Transfer Out</th>';
            $nhtml .= '<th>Closing Birds</th>'; $fhtml .= '<th id="order_num" colspan="2">Closing Birds</th>';
            $nhtml .= '<th>Body Weight</th>'; $fhtml .= '<th id="order_num" colspan="4">Body Weight</th>';
            $nhtml .= '<th>Egg Weight</th>'; $fhtml .= '<th id="order_num" colspan="2">Egg Weight</th>';
            $nhtml .= '<th>Female Feed Consumption</th>'; $fhtml .= '<th id="order_num" colspan="4">Female Feed Consumption</th>';
            $nhtml .= '<th>Male Feed Consumption</th>'; $fhtml .= '<th id="order_num" colspan="4">Male Feed Consumption</th>';
            $nhtml .= '<th>Production</th>'; $fhtml .= '<th id="order_num" colspan="8">Production</th>';
            
            $nhtml .= '</tr>';
            $fhtml .= '</tr>';

            $nhtml .= '<tr style="text-align:center;" align="center">';
            $fhtml .= '<tr style="text-align:center;" align="center">';

           
            $nhtml .= '<th></th>'; $fhtml .= '<th id="order" colspan="5"></th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order" >F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order" >M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order" >F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order" >M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order" >F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order" >M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order" >F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order" >M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order" >F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order" >M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order" >F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order" >M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order" >F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order" >M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order" >F. Std</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order" >F. Act</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order" >M. Std</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order" >M. Act</th>';
            
            $nhtml .= '<th>Standard</th>'; $fhtml .= '<th id="order">Standard</th>';
            $nhtml .= '<th>Actual</th>'; $fhtml .= '<th id="order">Actual</th>';

            $nhtml .= '<th>Feed Type</th>'; $fhtml .= '<th id="order">Feed Type</th>';
            $nhtml .= '<th>Qty ( in Kg )</th>'; $fhtml .= '<th id="order">Qty ( in Kg )</th>';
            $nhtml .= '<th>Std. Feed</th>'; $fhtml .= '<th id="order">Std. Feed</th>';
            $nhtml .= '<th>Act. Feed</th>'; $fhtml .= '<th id="order">Act. Feed</th>';
            
            $nhtml .= '<th>Feed Type</th>'; $fhtml .= '<th id="order">Feed Type</th>';
            $nhtml .= '<th>Qty ( in Kg )</th>'; $fhtml .= '<th id="order">Qty ( in Kg )</th>';
            $nhtml .= '<th>Std. Feed</th>'; $fhtml .= '<th id="order">Std. Feed</th>';
            $nhtml .= '<th>Act. Feed</th>'; $fhtml .= '<th id="order">Act. Feed</th>';

            $nhtml .= '<th>T.E.</th>'; $fhtml .= '<th id="order_num">T.E.</th>';
            $nhtml .= '<th>H.E.</th>'; $fhtml .= '<th id="order_num">H.E.</th>';
            $nhtml .= '<th>C.E.</th>'; $fhtml .= '<th id="order_num">C.E.</th>';
            $nhtml .= '<th>Total Eggs</th>'; $fhtml .= '<th id="order_num">Total Eggs</th>';
            $nhtml .= '<th>Std. Prod.</th>'; $fhtml .= '<th id="order_num">Std. Prod.</th>';
            $nhtml .= '<th>Prod. %</th>'; $fhtml .= '<th id="order_num">Prod. %</th>';
            $nhtml .= '<th>Std. HE %</th>'; $fhtml .= '<th id="order_num">Std. HE %</th>';
            $nhtml .= '<th>HE %</th>'; $fhtml .= '<th id="order_num">HE %</th>';

            $nhtml .= '</tr>';
            $fhtml .= '</tr>';
            $html .= $fhtml;
            $html .= '</thead>';
            $html .= '<tbody class="tbody1" id="tbody1">';
            if(isset($_POST['submit_report']) == true){
              
                $farm_fltr = ""; if($farms != "all"){ $farm_fltr = " AND `farm_code` = '$farms'"; }
                $unit_fltr = ""; if($units != "all"){ $unit_fltr = " AND `unit_code` = '$units'"; }
                $shed_fltr = ""; if($sheds != "all"){ $shed_fltr = " AND `shed_code` = '$sheds'"; }
                $batch_fltr = ""; if($batches != "all"){ $batch_fltr = " AND `batch_code` = '$batches'"; }
                $fbatch_fltr = ""; if($batches != "all"){ $fbatch_fltr = " AND `farm_batch` = '$batches'"; }
                $flock_fltr = ""; if($flocks != "all"){ $flock_fltr = " AND `code` = '$flocks'"; }

                $sql = "SELECT * FROM `breeder_farms` WHERE `dflag` = '0'".$farm_fltr." ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $farm_code = $farm_name = $farm_ccode = $farm_alist = array();
                while($row = mysqli_fetch_assoc($query)){ $farm_alist[$row['code']] = $row['code'];  }
                $farm_list = ""; $farm_list = implode("','", $farm_alist);

                $sql = "SELECT * FROM `breeder_batch` WHERE `dflag` = '0'".$batch_fltr." ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $farm_code = $farm_name = $farm_ccode = $batch_alist = array();
                while($row = mysqli_fetch_assoc($query)){ $batch_alist[$row['code']] = $row['code'];  }
                $batch_list = ""; $batch_list = implode("','", $batch_alist);

                                
                //Breeder Egg Details
                $sql = "SELECT * FROM `item_category` WHERE `dflag` = '0' AND `begg_flag` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $cegg_code = $icat_iac = array();
                while($row = mysqli_fetch_assoc($query)){ $cegg_code[$row['code']] = $row['code']; $icat_iac[$row['code']] = $row['iac']; } $egg_list = implode("','", $cegg_code);
                $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$egg_list') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql); $egg_code = $egg_name = array();
                while($row = mysqli_fetch_assoc($query)){ $egg_code[$row['code']] = $row['code']; $egg_name[$row['code']] = $row['description']; }
                $e_cnt = sizeof($egg_code);

                //Breeder Bird Details
                $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Breeder Birds%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $cbird_code = array();
                while($row = mysqli_fetch_assoc($query)){ $cbird_code[$row['code']] = $row['code']; $icat_iac[$row['code']] = $row['iac']; } $bird_list = implode("','", $cbird_code);
                $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$bird_list') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql); $fbird_code = $mbird_code = "";
                while($row = mysqli_fetch_assoc($query)){ if($row['description'] == "Female birds"){ $fbird_code = $row['code']; } else if($row['description'] == "Male birds"){ $mbird_code = $row['code']; } }

                //Breeder Feed Details
                $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $feed_alist = array();
                while($row = mysqli_fetch_assoc($query)){ $feed_alist[$row['code']] = $row['code']; }
                $feed_list = implode("','", $feed_alist);
                $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$feed_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $feed_code = $feed_name = array();
                while($row = mysqli_fetch_assoc($query)){ $feed_code[$row['code']] = $row['code']; $feed_name[$row['code']] = $row['description']; }

                //Breeder MedVac Details
                $sql = "SELECT * FROM `item_category` WHERE (`description` LIKE '%medicine%' OR `description` LIKE '%vaccine%') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $medvac_alist = array();
                while($row = mysqli_fetch_assoc($query)){ $medvac_alist[$row['code']] = $row['code']; }
                $medvac_list = implode("','", $medvac_alist);
                $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$medvac_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $medvac_code = $medvac_name = array();
                while($row = mysqli_fetch_assoc($query)){ $medvac_code[$row['code']] = $row['code']; $medvac_name[$row['code']] = $row['description']; }



               $sql = "SELECT * FROM `breeder_shed_allocation` WHERE `start_date` >= '$fdate' AND `start_date` <= '$tdate' AND `active` = '1'  ".$farm_fltr."".$batch_fltr."".$unit_fltr."".$shed_fltr."".$flock_fltr." AND `dflag` = '0' ORDER BY `addedtime` ASC";
                $query = mysqli_query($conn,$sql); $b_code  = 0; $flock_alist = array();
                while($row = mysqli_fetch_assoc($query)){ $flock_alist[$row['code']] = $row['code']; $b_code = $row['code']; }

                
            
                if (sizeof($flock_alist) > 0) {
                    echo "Condition met.";
                    $flock_list = implode("','", $flock_alist);
                   
                     //Calculations
                    //Purchase
                 echo   $sql = "SELECT * FROM `broiler_purchases` WHERE `warehouse` IN ('$farm_list') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $it_alist = $chk_mdate = $csin_qty = $csinb_qty = $csin_amt = $csinb_amt = $fsin_qty = $fsin_amt = $mvsin_qty = $mvsin_amt = array();
                    while($row = mysqli_fetch_assoc($query)){
                       // $key1 = $row['flock_code']."@".$weeks;
                       $it_alist[$row['icode']] = $row['icode'];
                       $date1 = strtotime($row['date']);
                       $fk = $row['flock_code'];
                       $ic = $row['icode'];
                        $key1 = $date1."@".$fk."".$ic;
                        if($row['icode'] == $mbird_code || $row['icode'] == $fbird_code ){
                            //Chicks
                            //check and store Opening Birds/transfer-In date
                            if(strtotime($row['date']) > strtotime($fdate)){ 

                                //Chick-In Quantity and Amount
                                $csin_qty[$key1] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                                $csin_amt[$key1] += (float)$row['item_tamt'];
                            } else {
                                $csinb_qty[$key1] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                                $csinb_amt[$key1] += (float)$row['item_tamt'];
                            }
                        }
                        else if(!empty($feed_code[$row['icode']]) && $feed_code[$row['icode']] == $row['icode']){
                            //Feeds
                            //Feed-In Quantity and Amount
                            $fsin_qty[$key1] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                            $fsin_amt[$key1] += (float)$row['item_tamt'];

                        }
                        else if(!empty($medvac_code[$row['icode']]) && $medvac_code[$row['icode']] == $row['icode']){
                            //Medvacs
                            //Medvac-In Quantity and Amount
                            $mvsin_qty[$key1] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                            $mvsin_amt[$key1] += (float)$row['item_tamt'];
                            
                        }
                        else{ }
                    }

                     //Stock Transfer-In
                    $sql = "SELECT * FROM `item_stocktransfers` WHERE `towarehouse` IN ('$farm_list') AND `from_flock` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){
                    // $key1 = $row['to_batch'];
                        $date1 = strtotime($row['date']);
                        $fk = $row['from_flock'];
                        $ic = $row['code'];
                        $key1 = $date1."@".$fk."".$ic;
                        // if($row['code'] == $chick_code){
                            if($row['code'] == $mbird_code || $row['code'] == $fbird_code ){
                            //Chicks
                            //check and store Opening Birds-In date
                            if(strtotime($row['date']) > strtotime($fdate)){ 
                                //Chick-In Quantity and Amount
                                $csin_qty[$key1] += (float)$row['quantity'];
                                $csin_amt[$key1] += (float)$row['amount'];
                            } else {
                                $csinb_qty[$key1] += (float)$row['quantity'];
                                $csinb_amt[$key1] += (float)$row['amount'];

                            }
                        }
                        else if(!empty($feed_code[$row['code']]) && $feed_code[$row['code']] == $row['code']){
                            //Feeds
                            //Feed-In Quantity and Amount
                            $fsin_qty[$key1] += (float)$row['quantity'];
                            $fsin_amt[$key1] += (float)$row['amount'];

                        }
                        else if(!empty($medvac_code[$row['code']]) && $medvac_code[$row['code']] == $row['code']){
                            //Medvacs
                            //Medvac-In Quantity and Amount
                            $mvsin_qty[$key1] += (float)$row['quantity'];
                            $mvsin_amt[$key1] += (float)$row['amount'];
                            
                        }
                        else{ }
                    }

                    //Stock Transfer-Out
                    $sql = "SELECT * FROM `item_stocktransfers` WHERE `fromwarehouse` IN ('$farm_list') AND `from_flock` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql);$fsout_qty = $fsout_amt = $mvsout_qty = $mvsout_amt = array();
                    while($row = mysqli_fetch_assoc($query)){
                        // $key1 = $row['from_batch'];
                        $date1 = strtotime($row['date']);
                        $fk = $row['from_flock'];
                        $ic = $row['code'];
                        $key1 = $date1."@".$fk."".$ic;
                        if(!empty($feed_code[$row['code']]) && $feed_code[$row['code']] == $row['code']){
                            //Feeds
                            //Feed-In Quantity and Amount
                            $fsout_qty[$key1] += (float)$row['quantity'];
                            $fsout_amt[$key1] += (float)$row['amount'];

                        }
                        else if(!empty($medvac_code[$row['code']]) && $medvac_code[$row['code']] == $row['code']){
                            //Medvacs
                            //Medvac-In Quantity and Amount
                            $mvsout_qty[$key1] += (float)$row['quantity'];
                            $mvsout_amt[$key1] += (float)$row['amount'];
                            
                        }
                        else{ }
                    }

                    $it_list = implode("','",$it_alist);
                     //Chick/Bird Sale
                    $sql = "SELECT * FROM `customer_sales` WHERE `warehouse` IN ('$farm_list') AND `itemcode` IN ('$fbird_code','$mbird_code') AND `active` = '1' AND `tdflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $csale_nos = $csale_qty = $csale_amt = array();
                    while($row = mysqli_fetch_assoc($query)){
                    //$key1 = $row['farm_batch'];
                    $date1 = strtotime($row['date']);
                    $fk = $row['from_flock'];
                    $ic = $row['code'];
                    $key1 = $date1."@".$fk."".$ic;
                    //Chick-In Quantity and Amount
                    $csale_nos[$key1] = $row['birds'];
                    $csale_qty[$key1] += (float)$row['netweight'] ;
                    $csale_amt[$key1] += (float)$row['totalamt'];
                    }

                    $flock_list = implode("','",$flock_alist);
                    $sql = "SELECT * FROM `breeder_dayentry_consumed` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `flock_code` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`flock_code` ASC";
                    $query = mysqli_query($conn,$sql);
                    $fmort_qty = $fcull_qty = $fbody_weight = $ffeed_code1 = $ffeed_qty1 = $ffeed_code2 = $ffeed_qty2 = $mfeed_code1 = $mfeed_qty1 = $mfeed_code2 = $mfeed_qty2 = $mmort_qty = $mcull_qty = 
                    $mbody_weight = $egg_weight = $flock_alist = array();
                    while($row = mysqli_fetch_assoc($query)){
                        // $key1 = $row['flock_code'];
                        $date1 = strtotime($row['date']);
                        $fk = $row['from_flock'];
                        $ic = $row['code'];
                        $key1 = $date1."@".$fk."".$ic;

                        $fmort_qty[$key1] += (float)$row['fmort_qty'];
                        $fcull_qty[$key1] += (float)$row['fcull_qty'];
                        $fbody_weight[$key1] = (float)$row['fbody_weight'];
                        $ffeed_code1[$key1] = $row['ffeed_code1'];
                        $ffeed_qty1[$key1] = (float)$row['ffeed_qty1'];
                        $ffeed_code2[$key1] = $row['ffeed_code2'];
                        $ffeed_qty2[$key1] = (float)$row['ffeed_qty2'];
                        $mfeed_code1[$key1] = $row['mfeed_code1'];
                        $mfeed_qty1[$key1] = (float)$row['mfeed_qty1'];
                        $mfeed_code2[$key1] = $row['mfeed_code2'];
                        $mfeed_qty2[$key1] = (float)$row['mfeed_qty2'];
                        $mmort_qty[$key1] += (float)$row['mmort_qty'];
                        $mcull_qty[$key1] += (float)$row['mcull_qty'];
                        $mbody_weight[$key1] = (float)$row['mbody_weight'];
                        $egg_weight[$key1] = (float)$row['egg_weight'];
                        $breed_wage[$key1] = $row['breed_wage'];
                        $breed_age[$key1] = $row['breed_age'];
                        $de_remarks[$key1] = $row['remarks'];

                        $flock_alist[$key1] = $key1;
                    }

                    $flock_list = implode("','",$flock_alist); $coa_list = implode("','",$icat_iac);
                    $sql = "SELECT * FROM `account_summary` WHERE `coa_code` IN ('$coa_list') AND `date` <= '$tdate' AND `flock_code` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC,`crdr` DESC";
                    $query = mysqli_query($conn,$sql);
                    $fflk_obirds = $fflk_cr_birds = $fflk_dr_birds = $mflk_obirds = $mflk_cr_birds = $mflk_dr_birds = $egg_pqty = $flock_alist = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $icrdr = $row['crdr']; $idate = $row['date']; $icode = $row['item_code']; $iqty = $row['quantity']; $ietype = $row['etype']; $key1 = $row['flock_code'];

                        if($icode == $fbird_code){
                            //Female Bird Calculations
                            if(strtotime($idate) < strtotime($fdate)){
                                if($icrdr == "DR"){
                                    $fflk_obirds[$key1] += (float)$iqty;
                                }
                                else if($icrdr == "CR"){
                                    $fflk_obirds[$key1] -= (float)$iqty;
                                }
                                else{ }
                            }
                            else{
                                if($icrdr == "DR" && ($ietype == "Breeder-Female Bird Transfer In" || $ietype == "Breeder-Female Opening Birds")){
                                    $fflk_dr_birds[$key1] += (float)$iqty;
                                }
                                else if($icrdr == "CR" && $ietype == "Breeder-Female Bird Transfer Out"){
                                    $fflk_cr_birds[$key1] += (float)$iqty;
                                }
                                else{ }
                            }
                        }
                        else if($icode == $mbird_code){
                            //Male Bird Calculations
                            if(strtotime($idate) < strtotime($fdate)){
                                if($icrdr == "DR"){
                                    $mflk_obirds[$key1] += (float)$iqty;
                                }
                                else if($icrdr == "CR"){
                                    $mflk_obirds[$key1] -= (float)$iqty;
                                }
                                else{ }
                            }
                            else{
                                if($icrdr == "DR" && ($ietype == "Breeder-Male Bird Transfer In" || $ietype == "Breeder-Male Opening Birds")){
                                    $mflk_dr_birds[$key1] += (float)$iqty;
                                }
                                else if($icrdr == "CR" && $ietype == "Breeder-Male Bird Transfer Out"){
                                    $mflk_cr_birds[$key1] += (float)$iqty;
                                }
                                else{ }
                            }
                        }
                        else if(!empty($egg_code[$icode]) && $egg_code[$icode] == $icode){
                            //Egg Calculations
                            if(strtotime($idate) < strtotime($fdate)){

                            }
                            else{
                                if($icrdr == "DR" && $ietype == "Breeder-Egg Production"){
                                    $key2 = strtotime($row['date'])."@".$key1."@".$icode;
                                    $egg_pqty[$key2] += (float)$iqty;
                                }
                            }
                        }

                        $flock_alist[$key1] = $key1;
                    }


                    $sql = "SELECT * FROM `breeder_dayentry_produced` WHERE `flock_code` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`flock_code` ASC";
                    $query = mysqli_query($conn,$sql); $egg_pdate = $date_fltr = ""; $egg_pqty = array();
                    while($row = mysqli_fetch_assoc($query)){
                    $bird_wage = age_in_weeks($row['breed_age']); $weeks = fetch_cweek($bird_wage);
                    if($egg_pdate == "" || strtotime($egg_pdate) > strtotime($row['date'])){ $egg_pdate = $row['date']; }
                    // $key1 = $row['item_code']."@".$weeks;
                    $date1 = strtotime($row['date']);
                    $fk = $row['from_flock'];
                    $ic = $row['code'];
                    $key1 = $date1."@".$fk."".$ic;

                    $egg_pqty[$key1] += (float)$row['quantity'];
                   }

                   $sql = "SELECT * FROM `breeder_medicine_consumed` WHERE `flock_code` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`flock_code` ASC";
                    $query = mysqli_query($conn,$sql); $med_pdate = $date_fltr = ""; $med_pqty = array();
                    while($row = mysqli_fetch_assoc($query)){
                    $bird_wage = age_in_weeks($row['breed_age']); $weeks = fetch_cweek($bird_wage);
                    if($med_pdate == "" || strtotime($med_pdate) > strtotime($row['date'])){ $med_pdate = $row['date']; }
                    // $key1 = $row['item_code']."@".$weeks;
                    $date1 = strtotime($row['date']);
                    $fk = $row['flock_code'];
                    $ic = $row['item_code'];
                    $key1 = $date1."@".$fk."".$ic;

                    $med_pqty[$key1] += (float)$row['quantity'];
                    }
                 
                    foreach($key1 as $key){
                         $key1;
                        $slno++;
                        $flk_name = $flock_name[$key];
                        // $bird_age = $bird_wage = 0;
                        // if(!empty($flock_sdate[$key1]) && !empty($flock_sage[$key1])){
                        //     $bird_age = (INT)((strtotime($tdate) - strtotime($flock_sdate[$key1])) / 60 / 60 / 24) + (int)$flock_sage[$key1];
                        //     $bird_wage = age_in_weeks($bird_age);
                        // }
                        // $key2 = $batch_breed[$flock_batch[$key1]]."@".$bird_age;
                        
                        // if(empty($fflk_obirds[$key1]) || $fflk_obirds[$key1] == ""){ $fopn_birds = 0; } else{ $fopn_birds = $fflk_obirds[$key1]; }
                        // if(empty($fmort_qty[$key1]) || $fmort_qty[$key1] == ""){ $fmort_birds = 0; } else{ $fmort_birds = $fmort_qty[$key1]; }
                        // if(empty($fcull_qty[$key1]) || $fcull_qty[$key1] == ""){ $fcull_birds = 0; } else{ $fcull_birds = $fcull_qty[$key1]; }
                        // if(empty($fflk_dr_birds[$key1]) || $fflk_dr_birds[$key1] == ""){ $ftrin_birds = 0; } else{ $ftrin_birds = $fflk_dr_birds[$key1]; }
                        // if(empty($fflk_cr_birds[$key1]) || $fflk_cr_birds[$key1] == ""){ $ftrout_birds = 0; } else{ $ftrout_birds = $fflk_cr_birds[$key1]; }
                        // $fflk_cbirds += ((int)$fopn_birds - (int)$fmort_birds - (int)$fcull_birds - (int)$ftrout_birds + (int)$ftrin_birds);
                        // if(empty($ffeed_qty[$key1]) || $ffeed_qty[$key1] == ""){ $ffeed_cqty = 0; } else{ $ffeed_cqty = $ffeed_qty[$key1]; }

                        // $fstd_fbird = $fstd_fpbird[$key2];
                        // $fact_fbird = $ffeed_cqty;
                        // $fstd_bwht = $fstd_bweight[$key2];
                        // $fact_bwht = $fbody_weight[$key1]; 

                        // if(empty($mflk_obirds[$key1]) || $mflk_obirds[$key1] == ""){ $mopn_birds = 0; } else{ $mopn_birds = $mflk_obirds[$key1]; }
                        // if(empty($mmort_qty[$key1]) || $mmort_qty[$key1] == ""){ $mmort_birds = 0; } else{ $mmort_birds = $mmort_qty[$key1]; }
                        // if(empty($mcull_qty[$key1]) || $mcull_qty[$key1] == ""){ $mcull_birds = 0; } else{ $mcull_birds = $mcull_qty[$key1]; }
                        // if(empty($mflk_cr_birds[$key1]) || $mflk_cr_birds[$key1] == ""){ $mtrout_birds = 0; } else{ $mtrout_birds = $mflk_cr_birds[$key1]; }
                        // if(empty($mflk_dr_birds[$key1]) || $mflk_dr_birds[$key1] == ""){ $mtrin_birds = 0; } else{ $mtrin_birds = $mflk_dr_birds[$key1]; }
                        // $mflk_cbirds += ((int)$mopn_birds - (int)$mmort_birds - (int)$mcull_birds - (int)$mtrout_birds + (int)$mtrin_birds);
                        // if(empty($mfeed_qty[$key1]) || $mfeed_qty[$key1] == ""){ $mfeed_cqty = 0; } else{ $mfeed_cqty = $mfeed_qty[$key1]; }
                        
                        // $mstd_fbird = $mstd_fpbird[$key2];
                        // $mact_fbird = $mfeed_cqty;
                        // $mstd_bwht = $mstd_bweight[$key2];
                        // $mact_bwht = $mbody_weight[$key1];

                        $html .= '<tr>';
                        $html .= '<td class="">'.$slno.'</td>';
                        $html .= '<td>'.$flk_name.'</td>';
                        // $html .= '<td style="text-align:center;">'.$bird_wage.'</td>';
                        // //Female Details
                        // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($fopn_birds,5))).'</td>';
                        // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($fmort_birds,5))).'</td>';
                        // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($fcull_birds,5))).'</td>';
                        // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($ftrin_birds,5))).'</td>';
                        // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($ftrout_birds,5))).'</td>';
                        // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($fflk_cbirds,5))).'</td>';
                        // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($ffeed_cqty,5))).'</td>';
                        // $html .= '<td style="text-align:right;" class="f_std">'.str_replace(".00","",number_format_ind(round($fstd_fbird,5))).'</td>';
                        // $html .= '<td style="text-align:right;" class="f_act">'.str_replace(".00","",number_format_ind(round($fact_fbird,5))).'</td>';
                        // $html .= '<td style="text-align:right;" class="f_std">'.decimal_adjustments($fstd_bwht,2).'</td>';
                        // $html .= '<td style="text-align:right;" class="f_act">'.decimal_adjustments($fact_bwht,2).'</td>';

                        // //Male Details
                        // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mopn_birds,5))).'</td>';
                        // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mmort_birds,5))).'</td>';
                        // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mcull_birds,5))).'</td>';
                        // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mtrin_birds,5))).'</td>';
                        // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mtrout_birds,5))).'</td>';
                        // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mflk_cbirds,5))).'</td>';
                        // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mfeed_cqty,5))).'</td>';
                        // $html .= '<td style="text-align:right;" class="m_std">'.str_replace(".00","",number_format_ind(round($mstd_fbird,5))).'</td>';
                        // $html .= '<td style="text-align:right;" class="m_act">'.str_replace(".00","",number_format_ind(round($mact_fbird,5))).'</td>';
                        // $html .= '<td style="text-align:right;" class="m_std">'.decimal_adjustments($mstd_bwht,2).'</td>';
                        // $html .= '<td style="text-align:right;" class="m_act">'.decimal_adjustments($mact_bwht,2).'</td>';
                        
                        // $egg_rqty = $hegg_rqty = 0;
                        // foreach($egg_code as $eggs){
                        //     $key3 = $key1."@".$eggs;
                        //     if(empty($egg_pqty[$key3]) || $egg_pqty[$key3] == ""){ $egg_qty = 0; } else{ $egg_qty = $egg_pqty[$key3]; }
                        //     $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($egg_qty,5))).'</td>';
                        //     $egg_rqty += (float)$egg_qty;
                        //     if($hegg_code == $eggs){ $hegg_rqty += (float)$egg_qty; }
                        //     $egg_cqty[$eggs] += (float)$egg_qty;
                        // }
                        // $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($egg_rqty,5))).'</td>';

                        // $std_egg_pper = $hd_per[$key1];
                        // $act_egg_pper = 0; if((float)$fopn_birds != 0){ $act_egg_pper = round((((float)$egg_rqty / (float)$fopn_birds) * 100),2); }
                        // $dif_egg_pper = 0; round(((float)$act_egg_pper - (float)$std_egg_pper),2);
                        // $html .= '<td style="text-align:right;" class="std">'.number_format_ind($std_egg_pper).'</td>';
                        // $html .= '<td style="text-align:right;" >'.number_format_ind($act_egg_pper).'</td>';
                        // $html .= '<td style="text-align:right;">'.number_format_ind($dif_egg_pper).'</td>';

                        // $std_hep = $std_he_per[$key1];
                        // $act_hep = 0; if((float)$egg_rqty != 0){ $act_hep = round((((float)$hegg_rqty / (float)$egg_rqty) * 100),2); }
                        // $dif_hep = 0; round(((float)$act_hep - (float)$std_hep),2);
                        // $html .= '<td style="text-align:right;" class="std">'.number_format_ind($std_hep).'</td>';
                        // $html .= '<td style="text-align:right;" >'.number_format_ind($act_hep).'</td>';
                        // $html .= '<td style="text-align:right;">'.number_format_ind($dif_hep).'</td>';
    
                        // $egg_wht = $egg_weight[$key1];
                        // $html .= '<td style="text-align:right;">'.number_format_ind($egg_wht).'</td>';
                        // $html .= '</tr>';


                        // $tfopn_birds += (float)$fopn_birds;
                        // $tfmort_birds += (float)$fmort_birds;
                        // $tfcull_birds += (float)$fcull_birds;
                        // $tftrin_birds += (float)$ftrin_birds;
                        // $tftrout_birds += (float)$ftrout_birds;
                        // $tffeed_cqty += (float)$ffeed_cqty;
                        // $tfstd_bwht += (float)$fstd_bwht;
                        // $tfact_bwht += (float)$fact_bwht;

                        // $tmopn_birds += (float)$mopn_birds;
                        // $tmmort_birds += (float)$mmort_birds;
                        // $tmcull_birds += (float)$mcull_birds;
                        // $tmtrin_birds += (float)$mtrin_birds;
                        // $tmtrout_birds += (float)$mtrout_birds;
                        // $tmfeed_cqty += (float)$mfeed_cqty;
                        // $tmstd_bwht += (float)$mstd_bwht;
                        // $tmact_bwht += (float)$mact_bwht;

                        // $tegg_rqty += (float)$egg_rqty;
                        // $tstd_egg_pper += (float)$std_egg_pper;
                        // $tstd_hep += (float)$std_hep;
                        // $tact_hep += (float)$act_hep;
                    }
                    
                } else {
                    echo "Condition not met.";
                }
                //    $flk_name = $flock_name[$key1];





                 
                //     $html .= '<tr>';
                //     $html .= '<td>'.$fname.'</td>';
                //     $html .= '<td>'.$u_name.'</td>';
                //     $html .= '<td>'.$s_name.'</td>';
                //     $html .= '<td>'.$fl_name.'</td>';
                //     $html .= '<td style="text-align:right;">'.rtrim(rtrim(number_format_ind(round($age_weeks, 5)), '0'), '.').'</td>';
                //     $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($start_age,5))).'</td>';
                //     $html .= '<td>'.$start_date.'</td>';
                //     $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($opn_fbirds,5))).'</td>';
                //     $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($opn_mbirds,5))).'</td>';
                    
                //     $html .= '</tr>';

                //     $tot_fbirds += (float)$opn_fbirds;
                //     $tot_mbirds += (float)$opn_mbirds;
                 
                $html .= '</tbody>';
                $html .= '<tfoot class="thead3">';
                $html .= '<tr>';
                $html .= '<th style="text-align:left;" colspan="7">Total</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tot_fbirds,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tot_mbirds,5))).'</th>';
                $html .= '</tr>';
                $html .= '</tfoot>';
            }
            echo $html;
        ?>
        </table><br/><br/><br/>
        <script>
            function table_sort() {
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `.order-inactive span { visibility:hidden; } .order-inactive:hover span { visibility:visible; } .order-active span { visibility: visible; }`;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order').forEach(th_elem => {

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
            function convertDate(d){ var p = d.split("."); return (p[2]+p[1]+p[0]); }
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