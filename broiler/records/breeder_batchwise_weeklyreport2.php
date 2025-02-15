<?php
//breeder_batchwise_weeklyreport2.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    include "header_head.php";
    $form_path = "breeder_batchwise_weeklyreport2.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    include "header_head.php";
    $form_path = "breeder_batchwise_weeklyreport2.php?db=$db&userid=".$user_code;
}
include "decimal_adjustments.php";
include "breeder_cal_ageweeks.php";
$file_name = "Breeder Weekly Report";

$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

//Breeder Extra Access
$sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Daily Entry' AND `field_function` = 'Display 2nd Feed Entry For Female Birds' AND `user_access` = 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $ffeed_2flag = mysqli_num_rows($query);
$sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Daily Entry' AND `field_function` = 'Display 2nd Feed Entry For Male Birds' AND `user_access` = 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $mfeed_2flag = mysqli_num_rows($query);

$sql = "SELECT * FROM `breeder_farms` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $farm_code = $farm_name = array();
while($row = mysqli_fetch_assoc($query)){ $farm_code[$row['code']] = $row['code']; $farm_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `breeder_units` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $unit_code = $unit_name = array();
while($row = mysqli_fetch_assoc($query)){ $unit_code[$row['code']] = $row['code']; $unit_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `breeder_sheds` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $shed_code = $shed_name = array();
while($row = mysqli_fetch_assoc($query)){ $shed_code[$row['code']] = $row['code']; $shed_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `breeder_batch` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $batch_code = $batch_name = $batch_breed = array();
while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_breed[$row['code']] = $row['breed_code']; }

$sql = "SELECT * FROM `breeder_shed_allocation` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $flock_code = $flock_name = array();
while($row = mysqli_fetch_assoc($query)){ $flock_code[$row['code']] = $row['code']; $flock_name[$row['code']] = $row['description']; }

//Breeder Breed Standards
$sql = "SELECT * FROM `breeder_breed_standards` WHERE `dflag` = '0' ORDER BY `breed_code`,`breed_age` ASC";
$query = mysqli_query($conn,$sql); $fstd_fpbird = $mstd_fpbird = $fstd_bweight = $mstd_bweight = $std_he_per = $std_hhe_pweek = $std_egg_wht = array();
while($row = mysqli_fetch_assoc($query)){
    $key1 = $row['breed_code']."@".$row['breed_age'];
    $fstd_live[$key1] = $row['livability'];
    $fstd_fpbird[$key1] = $row['ffeed_pbird'];
    $fstd_bweight[$key1] = $row['fbird_bweight'];
    $mstd_fpbird[$key1] = $row['mfeed_pbird'];
    $mstd_bweight[$key1] = $row['mbird_bweight'];
    $std_hd_per[$key1] = $row['hd_per'];
    $std_he_per[$key1] = $row['he_per'];
    $std_hhe_pweek[$key1] = $row['he_per'];
    $std_egg_wht[$key1] = $row['egg_weight'];
}

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

$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Hatch Egg%' AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC";
$query = mysqli_query($conn,$sql); $hegg_code = "";
while($row = mysqli_fetch_assoc($query)){ $hegg_code = $row['code']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql); $item_name = array();
while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }

$farms = $units = $sheds = "all"; $batches = "select"; $excel_type = "display"; $slno_flag = 0;
if(isset($_POST['submit_report']) == true){
    $farms = $_POST['farms'];
    $units = $_POST['units'];
    $sheds = $_POST['sheds'];
    $batches = $_POST['batches'];
    $excel_type = $_POST['export'];
}

?> 
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <link href="breeder_class_colors.css" rel="stylesheet">
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
            <form action="<?php echo $form_path; ?>" method="post" onsubmit="return check_val();">
                <thead class="thead2 text-primary layout-navbar-fixed" width="auto" <?php if($excel_type == "print"){ echo 'style="display:none;"'; } ?>>
                    <tr>
                        <th colspan="21">
                            <div class="row">
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
                                    <label for="units">Unit</label>
                                    <select name="units" id="units" class="form-control select2" style="width:220px;" onchange="fetch_flock_details(this.id);">
                                        <option value="all" <?php if($units == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($unit_code as $bcode){ if($unit_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($units == $bcode){ echo "selected"; } ?>><?php echo $unit_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width:230px;">
                                    <label for="sheds">Shed</label>
                                    <select name="sheds" id="sheds" class="form-control select2" style="width:220px;" onchange="fetch_flock_details(this.id);">
                                        <option value="all" <?php if($sheds == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($shed_code as $bcode){ if($shed_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($sheds == $bcode){ echo "selected"; } ?>><?php echo $shed_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width:230px;">
                                    <label for="batches">Batch</label>
                                    <select name="batches" id="batches" class="form-control select2" style="width:220px;">
                                        <option value="select" <?php if($batches == "select"){ echo "selected"; } ?>>-select-</option>
                                        <?php foreach($batch_code as $bcode){ if($batch_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($batches == $bcode){ echo "selected"; } ?>><?php echo $batch_name[$bcode]; ?></option>
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
            $html = $nhtml = $fhtml = ''; $e_cnt = $e_cnt + 3;
            $html .= '<thead class="thead3" id="head_names">';

            $nhtml .= '<tr style="text-align:center;" align="center">';
            $fhtml .= '<tr style="text-align:center;" align="center">';

            $nhtml .= '<th colspan="1"></th>'; $fhtml .= '<th colspan="1"></th>';
            $nhtml .= '<th colspan="2">Opening Birds</th>'; $fhtml .= '<th colspan="2">Opening Birds</th>';
            $nhtml .= '<th colspan="2">Mort.</th>'; $fhtml .= '<th colspan="2">Mort.</th>';
            $nhtml .= '<th colspan="2">Cum. Mort.</th>'; $fhtml .= '<th colspan="2">Cum. Mort.</th>';
            $nhtml .= '<th colspan="2">Culls</th>'; $fhtml .= '<th colspan="2">Culls</th>';
            $nhtml .= '<th colspan="2">Transfer-In</th>'; $fhtml .= '<th colspan="2">Transfer-In</th>';
            $nhtml .= '<th colspan="2">Transfer-Out</th>'; $fhtml .= '<th colspan="2">Transfer-Out</th>';
            $nhtml .= '<th colspan="2">Sales</th>'; $fhtml .= '<th colspan="2">Sales</th>';
            $nhtml .= '<th colspan="2">F.Depletion %</th>'; $fhtml .= '<th colspan="2">F.Depletion %</th>';
            $nhtml .= '<th colspan="2">M.Depletion %</th>'; $fhtml .= '<th colspan="2">M.Depletion %</th>';
            $nhtml .= '<th colspan="2">Feed</th>'; $fhtml .= '<th colspan="2">Feed</th>';
            $nhtml .= '<th colspan="2">Cum. Feed</th>'; $fhtml .= '<th colspan="2">Cum. Feed</th>';
            $nhtml .= '<th colspan="3">Livability</th>'; $fhtml .= '<th colspan="3">Livability</th>';
            $nhtml .= '<th colspan="'.$e_cnt.'">Production</th>'; $fhtml .= '<th colspan="'.$e_cnt.'">Production</th>';
            $nhtml .= '<th colspan="2">Production %</th>'; $fhtml .= '<th colspan="2">Production %</th>';
            $nhtml .= '<th colspan="2">Hatch Eggs %</th>'; $fhtml .= '<th colspan="2">Hatch Eggs %</th>';
            $nhtml .= '<th colspan="2">HHE</th>'; $fhtml .= '<th colspan="2">HHE</th>';
            $nhtml .= '<th colspan="2">HHHE</th>'; $fhtml .= '<th colspan="2">HHHE</th>';
            $nhtml .= '<th colspan="2">Feed /Bird</th>'; $fhtml .= '<th colspan="2">Feed /Bird</th>';
            $nhtml .= '<th colspan="2">Egg Weight</th>'; $fhtml .= '<th colspan="2">Egg Weight</th>';
            $nhtml .= '<th colspan="1">Feed / Egg</th>'; $fhtml .= '<th colspan="1">Feed / Egg</th>';
            $nhtml .= '<th colspan="4">Body Weight</th>'; $fhtml .= '<th colspan="4">Body Weight</th>';

            $nhtml .= '</tr>';
            $fhtml .= '</tr>';

            $nhtml .= '<tr style="text-align:center;" align="center">';
            $fhtml .= '<tr style="text-align:center;" align="center">';

            $nhtml .= '<th>Age</th>'; $fhtml .= '<th id="order_num">Age</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">Mortality</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">Culls</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">Mortality</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">Culls</th>';
            
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">M</th>';
            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">F</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">M</th>';
            
            $nhtml .= '<th>Female Std.</th>'; $fhtml .= '<th id="order_num">Female Std.</th>';
            $nhtml .= '<th>Female Act.</th>'; $fhtml .= '<th id="order_num">Female Act.</th>';
            $nhtml .= '<th>Male Act.</th>'; $fhtml .= '<th id="order_num">Male Act.</th>';

            foreach($egg_code as $eggs){ $nhtml .= '<th>'.$egg_name[$eggs].'</th>'; $fhtml .= '<th id="order_num">'.$egg_name[$eggs].'</th>'; }
            $nhtml .= '<th>Total Eggs</th>'; $fhtml .= '<th id="order_num">Total Eggs</th>';
            $nhtml .= '<th>Cum.HE</th>'; $fhtml .= '<th id="order_num">Cum.HE</th>';
            $nhtml .= '<th>Cum.Eggs</th>'; $fhtml .= '<th id="order_num">Cum.Eggs</th>';
            $nhtml .= '<th>Standard</th>'; $fhtml .= '<th id="order_num">Standard</th>';
            $nhtml .= '<th>Actual</th>'; $fhtml .= '<th id="order_num">Actual</th>';
            $nhtml .= '<th>Standard</th>'; $fhtml .= '<th id="order_num">Standard</th>';
            $nhtml .= '<th>Actual</th>'; $fhtml .= '<th id="order_num">Actual</th>';
            $nhtml .= '<th>Standard</th>'; $fhtml .= '<th id="order_num">Standard</th>';
            $nhtml .= '<th>Actual</th>'; $fhtml .= '<th id="order_num">Actual</th>';
            $nhtml .= '<th>Standard</th>'; $fhtml .= '<th id="order_num">Standard</th>';
            $nhtml .= '<th>Actual</th>'; $fhtml .= '<th id="order_num">Actual</th>';
            $nhtml .= '<th>Standard</th>'; $fhtml .= '<th id="order_num">Standard</th>';
            $nhtml .= '<th>Actual</th>'; $fhtml .= '<th id="order_num">Actual</th>';
            $nhtml .= '<th>Standard</th>'; $fhtml .= '<th id="order_num">Standard</th>';
            $nhtml .= '<th>Actual</th>'; $fhtml .= '<th id="order_num">Actual</th>';
            $nhtml .= '<th></th>'; $fhtml .= '<th id="order_num"></th>';

            $nhtml .= '<th>F. Std.</th>'; $fhtml .= '<th id="order_num">F. Std.</th>';
            $nhtml .= '<th>F. Act.</th>'; $fhtml .= '<th id="order_num">F. Act.</th>';
            $nhtml .= '<th>M. Std.</th>'; $fhtml .= '<th id="order_num">M. Std.</th>';
            $nhtml .= '<th>M. Act.</th>'; $fhtml .= '<th id="order_num">M. Act.</th>';

            $nhtml .= '</tr>';
            $fhtml .= '</tr>';
            $html .= $fhtml;
            $html .= '</thead>';
            $html .= '<tbody class="tbody1" id="tbody1">';
            if(isset($_POST['submit_report']) == true){
                $week_nos = array();
                //Check for egg production Start Date
                $sql = "SELECT * FROM `breeder_dayentry_produced` WHERE `batch_code` IN ('$batches') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`flock_code` ASC";
                $query = mysqli_query($conn,$sql); $egg_pdate = array();
                while($row = mysqli_fetch_assoc($query)){
                    $bird_wage = age_in_weeks($row['breed_age']); $weeks = fetch_cweek($bird_wage);
                    if($egg_pdate[$row['flock_code']] == "" || strtotime($egg_pdate[$row['flock_code']]) > strtotime($row['date'])){ $egg_pdate[$row['flock_code']] = $row['date']; }
                    $key1 = $row['item_code']."@".$weeks;
                    $egg_pqty[$key1] += (float)$row['quantity'];
                }

                //Opening Details
                $sql = "SELECT * FROM `breeder_shed_allocation` WHERE `batch_code` = '$batches' AND `dflag` = '0'";
                $query = mysqli_query($conn,$sql); $placed_fbirds = $placed_mbirds = $opn_fbirds = $opn_mbirds = 0; $flk_bch = "";
                while($row = mysqli_fetch_assoc($query)){
                    $bird_wage = age_in_weeks($row['start_age']); $weeks = fetch_cweek($bird_wage);
                    $placed_fbirds += (float)$row['opn_fbirds'];
                    $placed_mbirds += (float)$row['opn_mbirds'];
                    $opn_fbirds += (float)$row['opn_fbirds'];
                    $opn_mbirds += (float)$row['opn_mbirds'];
                    $breed_code = $batch_breed[$row['batch_code']];
                    if($egg_pdate[$row['code']] == "" || strtotime($row['date']) < strtotime($egg_pdate[$row['code']])){ } else{ $week_nos[$weeks] = $weeks; }
                }

                $sql = "SELECT * FROM `breeder_dayentry_consumed` WHERE `batch_code` IN ('$batches') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`batch_code` ASC";
                $query = mysqli_query($conn,$sql); $de_cnt = mysqli_num_rows($query); $f_cmort = $m_cmort = $f_cfcons = $m_cfcons = 0;
                $fmort_qty = $fcull_qty = $ffeed_qty = $fbody_weight = $mmort_qty = $mcull_qty = $mfeed_qty = $mbody_weight = $aegg_weight = array();
                if((int)$de_cnt > 0){
                    while($row = mysqli_fetch_assoc($query)){
                        $bird_wage = age_in_weeks($row['breed_age']); $weeks = fetch_cweek($bird_wage);
                        if($egg_pdate[$row['flock_code']] == "" || strtotime($row['date']) < strtotime($egg_pdate[$row['flock_code']])){
                            $opn_fbirds -= (float)$row['fmort_qty'];
                            $opn_fbirds -= (float)$row['fcull_qty'];
                            $opn_mbirds -= (float)$row['mmort_qty'];
                            $opn_mbirds -= (float)$row['mcull_qty'];

                            $f_cmort += (float)$row['fmort_qty'];
                            $m_cmort += (float)$row['mmort_qty'];
                            $f_cfcons += ((float)$row['ffeed_qty1'] + (float)$row['ffeed_qty2']);
                            $m_cfcons += ((float)$row['mfeed_qty1'] + (float)$row['mfeed_qty2']);
                        }
                        else{
                            $fmort_qty[$weeks] += (float)$row['fmort_qty'];
                            $fcull_qty[$weeks] += (float)$row['fcull_qty'];
                            $ffeed_qty[$weeks] += ((float)$row['ffeed_qty1'] + (float)$row['ffeed_qty2']);
                            if((float)$row['fbody_weight'] != 0){ $fbody_weight[$weeks] = (float)$row['fbody_weight']; }
                            $mmort_qty[$weeks] += (float)$row['mmort_qty'];
                            $mcull_qty[$weeks] += (float)$row['mcull_qty'];
                            $mfeed_qty[$weeks] += ((float)$row['mfeed_qty1'] + (float)$row['mfeed_qty2']);
                            if((float)$row['mbody_weight'] != 0){ $mbody_weight[$weeks] = (float)$row['mbody_weight']; }
                            if((float)$row['egg_weight'] != 0){ $aegg_weight[$weeks] = (float)$row['egg_weight']; }
                            $week_nos[$weeks] = $weeks;
                        }
                    }
                    
                    $sql = "SELECT * FROM `breeder_bird_transfer` WHERE `to_batch` IN ('$batches') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`to_batch` ASC";
                    $query = mysqli_query($conn,$sql); $ftrns_in = $mtrns_in = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $bird_wage = age_in_weeks($row['bird_age']); $weeks = fetch_cweek($bird_wage);
                        if($egg_pdate[$row['to_flock']] == "" || strtotime($row['date']) < strtotime($egg_pdate[$row['to_flock']])){
                            $opn_fbirds += (float)$row['female_bqty'];
                            $opn_mbirds += (float)$row['male_bqty'];
                        }
                        else{ 
                            $ftrns_in[$weeks] += (float)$row['female_bqty'];
                            $mtrns_in[$weeks] += (float)$row['male_bqty'];
                            $week_nos[$weeks] = $weeks;
                        }
                    }

                    $sql = "SELECT * FROM `breeder_bird_transfer` WHERE `from_batch` IN ('$batches') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`from_batch` ASC";
                    $query = mysqli_query($conn,$sql); $ftrns_out = $mtrns_out = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $bird_wage = age_in_weeks($row['bird_age']); $weeks = fetch_cweek($bird_wage);
                        if($egg_pdate[$row['from_flock']] == "" || strtotime($row['date']) < strtotime($egg_pdate[$row['from_flock']])){
                            $opn_fbirds -= (float)$row['female_bqty'];
                            $opn_mbirds -= (float)$row['male_bqty'];
                        }
                        else{ 
                            $ftrns_out[$weeks] += (float)$row['female_bqty'];
                            $mtrns_out[$weeks] += (float)$row['male_bqty'];
                            $week_nos[$weeks] = $weeks;
                        }
                    }

                    sort($week_nos);

                    $slno = $hegg_cqty = $tegg_cqty = $f_fbirds = $m_fbirds = 0;
                    foreach($week_nos as $weeks){
                        $slno++;
                        if($slno == 1){
                            $f_obirds = $opn_fbirds;
                            $m_obirds = $opn_mbirds;
                        }
                        else{
                            $f_obirds = $f_fbirds;
                            $m_obirds = $m_fbirds;
                        }
                        if(empty($fmort_qty[$weeks]) || $fmort_qty[$weeks] == ""){ $f_mbirds = 0; } else{ $f_mbirds = $fmort_qty[$weeks]; }
                        if(empty($mmort_qty[$weeks]) || $mmort_qty[$weeks] == ""){ $m_mbirds = 0; } else{ $m_mbirds = $mmort_qty[$weeks]; }

                        $f_cmort += (float)$f_mbirds;
                        $m_cmort += (float)$m_mbirds;

                        if(empty($fcull_qty[$weeks]) || $fcull_qty[$weeks] == ""){ $f_cbirds = 0; } else{ $f_cbirds = $fcull_qty[$weeks]; }
                        if(empty($mcull_qty[$weeks]) || $mcull_qty[$weeks] == ""){ $m_cbirds = 0; } else{ $m_cbirds = $mcull_qty[$weeks]; }

                        $f_slive = $fstd_live[$breed_code."@".$weeks];
                        $f_alive = 0; if((float)$f_obirds != 0){ $f_alive = (100 - (((float)$f_mbirds / (float)$f_obirds) * 100)); }
                        $m_alive = 0; if((float)$m_obirds != 0){ $m_alive = (100 - (((float)$m_mbirds / (float)$m_obirds) * 100)); }

                        //Transfer-In
                        $f_tibirds = (float)$ftrns_in[$weeks];
                        $m_tibirds = (float)$mtrns_in[$weeks];
                        //Transfer-Out
                        $f_tobirds = (float)$ftrns_out[$weeks];
                        $m_tobirds = (float)$mtrns_out[$weeks];

                        if(empty($ffeed_qty[$weeks]) || $ffeed_qty[$weeks] == ""){ $f_fcons = 0; } else{ $f_fcons = $ffeed_qty[$weeks]; }
                        if(empty($mfeed_qty[$weeks]) || $mfeed_qty[$weeks] == ""){ $m_fcons = 0; } else{ $m_fcons = $mfeed_qty[$weeks]; }

                        $f_cfcons += (float)$f_fcons;
                        $m_cfcons += (float)$m_fcons;

                        $fsfp_bird = $fstd_fpbird[$breed_code."@".$weeks];
                        $fafp_bird = 0; if((float)$f_obirds != 0){ $fafp_bird = (((float)$f_fcons / (float)$f_obirds) * 100); }

                        $fs_bwht = $fstd_bweight[$breed_code."@".$weeks];
                        $fa_bwht = $fbody_weight[$weeks];
                        $ms_bwht = $mstd_bweight[$breed_code."@".$weeks];
                        $ma_bwht = $mbody_weight[$weeks];

                        //Depletion %
                        $f_mdepper = 0; if((float)$placed_fbirds != 0){ $f_mdepper = (((float)$f_mbirds / (float)$placed_fbirds) * 100); }
                        $f_cdepper = 0; if((float)$placed_fbirds != 0){ $f_cdepper = (((float)$f_cbirds / (float)$placed_fbirds) * 100); }
                        $m_mdepper = 0; if((float)$placed_mbirds != 0){ $m_mdepper = (((float)$m_mbirds / (float)$placed_mbirds) * 100); }
                        $m_cdepper = 0; if((float)$placed_mbirds != 0){ $m_cdepper = (((float)$m_cbirds / (float)$placed_mbirds) * 100); }

                        $html .= '<tr>';
                        $html .= '<td style="text-align:center;">'.$weeks.'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($f_obirds,5))).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($m_obirds,5))).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($f_mbirds,5))).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($m_mbirds,5))).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($f_cmort,5))).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($m_cmort,5))).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($f_cbirds,5))).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($m_cbirds,5))).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($f_tibirds,5))).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($m_tibirds,5))).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($f_tobirds,5))).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($m_tobirds,5))).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(0)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(0)).'</td>';
                        $html .= '<td style="text-align:right;">'.decimal_adjustments($f_mdepper,2).'</td>';
                        $html .= '<td style="text-align:right;">'.decimal_adjustments($f_cdepper,2).'</td>';
                        $html .= '<td style="text-align:right;">'.decimal_adjustments($m_mdepper,2).'</td>';
                        $html .= '<td style="text-align:right;">'.decimal_adjustments($m_cdepper,2).'</td>';
                        $html .= '<td style="text-align:right;">'.decimal_adjustments($f_fcons,2).'</td>';
                        $html .= '<td style="text-align:right;">'.decimal_adjustments($m_fcons,2).'</td>';
                        $html .= '<td style="text-align:right;">'.decimal_adjustments($f_cfcons,2).'</td>';
                        $html .= '<td style="text-align:right;">'.decimal_adjustments($m_cfcons,2).'</td>';
                        $html .= '<td style="text-align:right;" class="f_std">'.decimal_adjustments($f_slive,2).'</td>';
                        $html .= '<td style="text-align:right;" class="f_act">'.decimal_adjustments($f_alive,2).'</td>';
                        $html .= '<td style="text-align:right;" class="m_act">'.decimal_adjustments($m_alive,2).'</td>';
                        
                        $egg_rqty = $hegg_rqty = 0;
                        foreach($egg_code as $eggs){
                            $key1 = $eggs."@".$weeks;
                            if(empty($egg_pqty[$key1]) || $egg_pqty[$key1] == ""){ $egg_qty = 0; } else{ $egg_qty = $egg_pqty[$key1]; }
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($egg_qty,5))).'</td>';
                            $egg_rqty += (float)$egg_qty;
                            if($hegg_code == $eggs){ $hegg_rqty += (float)$egg_qty; $hegg_cqty += (float)$egg_qty; }
                            $egg_cqty[$eggs] += (float)$egg_qty;
                            $tegg_cqty += (float)$egg_qty;
                        }
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($egg_rqty,5))).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($hegg_cqty,5))).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tegg_cqty,5))).'</td>';
                        
                        $std_egg_pper = $std_hd_per[$breed_code."@".$weeks];
                        $act_egg_pper = 0; if((float)$f_obirds != 0){ $act_egg_pper = round((((float)$egg_rqty / (float)$f_obirds) * 100),2); }
                        $html .= '<td style="text-align:right;" class="std">'.number_format_ind($std_egg_pper).'</td>';
                        $html .= '<td style="text-align:right;" class="act">'.number_format_ind($act_egg_pper).'</td>';

                        $std_hep = $std_he_per[$breed_code."@".$weeks];
                        $act_hep = 0; if((float)$egg_rqty != 0){ $act_hep = round((((float)$hegg_rqty / (float)$egg_rqty) * 100),2); }
                        $html .= '<td style="text-align:right;" class="std">'.number_format_ind($std_hep).'</td>';
                        $html .= '<td style="text-align:right;" class="act">'.number_format_ind($act_hep).'</td>';

                        $std_hhe = $std_hhe_pweek[$breed_code."@".$weeks];
                        $act_hhe = 0; if((float)$placed_fbirds != 0){ $act_hhe = round((((float)$egg_rqty / (float)$placed_fbirds) * 100),2); }
                        $html .= '<td style="text-align:right;" class="std">'.number_format_ind($std_hhe).'</td>';
                        $html .= '<td style="text-align:right;" class="act">'.number_format_ind($act_hhe).'</td>';

                        $std_hhhe = $std_hhe_pweek[$breed_code."@".$weeks];
                        $act_hhhe = 0; if((float)$placed_fbirds != 0){ $act_hhhe = round((((float)$hegg_rqty / (float)$placed_fbirds) * 100),2); }
                        $html .= '<td style="text-align:right;" class="std">'.number_format_ind($std_hhhe).'</td>';
                        $html .= '<td style="text-align:right;" class="act">'.number_format_ind($act_hhhe).'</td>';

                        $html .= '<td style="text-align:right;" class="std">'.decimal_adjustments($fsfp_bird,2).'</td>';
                        $html .= '<td style="text-align:right;" class="act">'.decimal_adjustments($fafp_bird,2).'</td>';

                        $std_ewht = $std_egg_wht[$breed_code."@".$weeks];
                        $act_ewht = $aegg_weight[$weeks];
                        $html .= '<td style="text-align:right;" class="std">'.decimal_adjustments($std_ewht,2).'</td>';
                        $html .= '<td style="text-align:right;" class="act">'.decimal_adjustments($act_ewht,2).'</td>';

                        $feed_pegg = 0; if((float)$f_fcons != 0){ $feed_pegg = round((((float)$egg_rqty / (float)$f_fcons) * 100),2); }
                        $html .= '<td style="text-align:right;">'.decimal_adjustments($feed_pegg,2).'</td>';

                        $html .= '<td style="text-align:right;" class="f_std">'.rtrim(rtrim(decimal_adjustments($fs_bwht,2),'0'),'.').'</td>';
                        $html .= '<td style="text-align:right;" class="f_act">'.rtrim(rtrim(decimal_adjustments($fa_bwht,2),'0'),'1').'</td>';
                        $html .= '<td style="text-align:right;" class="m_std">'.rtrim(rtrim(decimal_adjustments($ms_bwht,2),'0'),'1').'</td>';
                        $html .= '<td style="text-align:right;" class="m_act">'.rtrim(rtrim(decimal_adjustments($ma_bwht,2),'0'),'1').'</td>';
                        $html .= '</tr>';

                        //closing
                        $f_fbirds = (($f_obirds + $f_tibirds) - ($f_mbirds + $f_cbirds + $f_tobirds));
                        $m_fbirds = (($m_obirds + $m_tibirds) - ($m_mbirds + $m_cbirds + $m_tobirds));

                        //Totals
                        $tf_mbirds += (float)$f_mbirds;
                        $tm_mbirds += (float)$m_mbirds;
                        $tf_cbirds += (float)$f_cbirds;
                        $tm_cbirds += (float)$m_cbirds;
                        $tf_tibirds += (float)$f_tibirds;
                        $tm_tibirds += (float)$m_tibirds;
                        $tf_tobirds += (float)$f_tobirds;
                        $tm_tobirds += (float)$m_tobirds;
                        $tf_fcons += (float)$f_fcons;
                        $tm_fcons += (float)$m_fcons;
                        $tegg_rqty += (float)$egg_rqty;
                    }

                    $f_slive = 0;
                    $f_alive = 0; if((float)$opn_fbirds != 0){ $f_alive = (((float)$tf_mbirds / (float)$opn_fbirds) * 100); }
                    $m_alive = 0; if((float)$opn_mbirds != 0){ $m_alive = (((float)$tm_mbirds / (float)$opn_mbirds) * 100); }
                    $fafp_bird = 0; if((float)$opn_fbirds != 0){ $fafp_bird = (((float)$tf_fcons / (float)$opn_fbirds) * 100); }

                    $html .= '</tbody>';
                    $html .= '<tfoot class="thead3">';
                    $html .= '<tr>';
                    $html .= '<th style="text-align:left;" colspan="1">Total</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($opn_fbirds,5))).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($opn_mbirds,5))).'</th>';
                    //Mortality
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tf_mbirds,5))).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tm_mbirds,5))).'</th>';
                    //Cum. Mortality
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($f_cmort,5))).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($m_cmort,5))).'</th>';
                    //Culls
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tf_cbirds,5))).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tm_cbirds,5))).'</th>';
                    //Transfer-In
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tf_tibirds,5))).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tm_tibirds,5))).'</th>';
                    //Transfer-Out
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tf_tobirds,5))).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tm_tobirds,5))).'</th>';
                    //Sales
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(0)).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(0)).'</th>';
                    //Depletion %
                    $f_mdepper = 0; if((float)$placed_fbirds != 0){ $f_mdepper = (((float)$tf_mbirds / (float)$placed_fbirds) * 100); }
                    $f_cdepper = 0; if((float)$placed_fbirds != 0){ $f_cdepper = (((float)$tf_cbirds / (float)$placed_fbirds) * 100); }
                    $m_mdepper = 0; if((float)$placed_mbirds != 0){ $m_mdepper = (((float)$tm_mbirds / (float)$placed_mbirds) * 100); }
                    $m_cdepper = 0; if((float)$placed_mbirds != 0){ $m_cdepper = (((float)$tm_cbirds / (float)$placed_mbirds) * 100); }
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($f_mdepper,5))).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($f_cdepper,5))).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($m_mdepper,5))).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($m_cdepper,5))).'</th>';
                    //Feed Consumed Details
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tf_fcons,5))).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tm_fcons,5))).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($f_cfcons,5))).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($m_cfcons,5))).'</th>';
                    //Livability
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($f_slive,5))).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($f_alive,5))).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($m_alive,5))).'</th>';
                    //Egg Production
                    foreach($egg_code as $eggs){
                        $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($egg_cqty[$eggs],5))).'</th>';
                    }
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tegg_rqty,5))).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($hegg_cqty,5))).'</th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tegg_cqty,5))).'</th>';
                    $act_egg_pper = 0; if((float)$opn_fbirds != 0){ $act_egg_pper = round((((float)$tegg_rqty / (float)$opn_fbirds) * 100),2); }
                    $html .= '<th style="text-align:right;"></th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($act_egg_pper,5))).'</th>';
                    $act_hep = 0; if((float)$tegg_cqty != 0){ $act_hep = round((((float)$hegg_cqty / (float)$tegg_cqty) * 100),2); }
                    $html .= '<th style="text-align:right;"></th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($act_hep,5))).'</th>';
                    //HHE
                    $html .= '<th style="text-align:right;"></th>';
                    $html .= '<th style="text-align:right;"></th>';
                    //HHHE
                    $html .= '<th style="text-align:right;"></th>';
                    $html .= '<th style="text-align:right;"></th>';
                    //Feed per Bird
                    $html .= '<th style="text-align:right;"></th>';
                    $html .= '<th style="text-align:right;">'.number_format_ind(round($fafp_bird,5)).'</th>';
                    //Egg Weight
                    $html .= '<th style="text-align:right;"></th>';
                    $html .= '<th style="text-align:right;"></th>';
                    //Feed / Egg
                    $html .= '<th style="text-align:right;"></th>';
                    //Body Weight
                    $html .= '<th style="text-align:right;"></th>';
                    $html .= '<th style="text-align:right;"></th>';
                    $html .= '<th style="text-align:right;"></th>';
                    $html .= '<th style="text-align:right;"></th>';

                    $html .= '</tr>';
                    $html .= '</tfoot>';
                }
            }
            echo $html;
        ?>
        </table><br/><br/><br/>
        <script>
            function check_val(){
                var batches = document.getElementById("batches").value;
                var l = true;
                if(batches == "select"){
                    alert("Please select Batch");
                    l = false;
                }
                else{ }

                if(l == true){
                    return true;
                }
                else{
                    return false;
                }
            }
        </script>
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
                var slno = '<?php echo $slno_flag; ?>';
                if(parseInt(slno) == 1){
                    var rcount = document.getElementById("tbody1").rows.length;
                    var myTable = document.getElementById('tbody1');
                    var j = 0;
                    for(var i = 1;i <= rcount;i++){ j = i - 1; myTable.rows[j].cells[0].innerHTML = i; }
                }
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
            function fetch_flock_details(a){
                var farms = document.getElementById("farms").value;
                var units = document.getElementById("units").value;
                var sheds = document.getElementById("sheds").value;
                var batches = document.getElementById("batches").value;

                var user_code = '<?php echo $user_code; ?>';
                var ff_flag = uf_flag = sf_flag = bf_flag = fl_flag = 0;
                if(a == "farms"){ ff_flag = 1; }
                else if(a == "units"){ uf_flag = 1; }
                else if(a == "sheds"){ sf_flag = 1; }
                else if(a == "batches"){ bf_flag = 1; }
                else{ ff_flag = 1; }

                var fetch_fltrs = new XMLHttpRequest();
                var method = "GET";
                var url = "breeder_fetch_flock_filter_master.php?farms="+farms+"&units="+units+"&sheds="+sheds+"&batches="+batches+"&fetch_type=single&batch_type=select";
                //window.open(url);
                var asynchronous = true;
                fetch_fltrs.open(method, url, asynchronous);
                fetch_fltrs.send();
                fetch_fltrs.onreadystatechange = function(){
                    if(this.readyState == 4 && this.status == 200){
                        var fltr_dt1 = this.responseText;
                        var fltr_dt2 = fltr_dt1.split("[@$&]");
                        var farm_list = fltr_dt2[0];
                        var unit_list = fltr_dt2[1];
                        var shed_list = fltr_dt2[2];
                        var batch_list = fltr_dt2[3];

                        if(ff_flag == 1){
                            removeAllOptions(document.getElementById("units"));
                            removeAllOptions(document.getElementById("sheds"));
                            removeAllOptions(document.getElementById("batches"));
                            $('#units').append(unit_list);
                            $('#sheds').append(shed_list);
                            $('#batches').append(batch_list);
                        }
                        else if(uf_flag == 1){
                            removeAllOptions(document.getElementById("sheds"));
                            removeAllOptions(document.getElementById("batches"));
                            $('#sheds').append(shed_list);
                            $('#batches').append(batch_list);
                        }
                        else if(sf_flag == 1){
                            removeAllOptions(document.getElementById("batches"));
                            $('#batches').append(batch_list);
                        }
                        else{ }
                    }
                }
            }
            var f_cnt = 0;
            function set_auto_selectors(){
                if(f_cnt == 0){
                    var fx = "farms"; fetch_flock_details(fx); f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 1){
                    $('#units').select2();
                    var units = '<?php echo $units; ?>';
                    document.getElementById("units").value = units;
                    $('#units').select2();
                    var fx = "units"; fetch_flock_details(fx); f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 2){
                    $('#sheds').select2();
                    var sheds = '<?php echo $sheds; ?>';
                    document.getElementById("sheds").value = sheds;
                    $('#sheds').select2();
                    var fx = "sheds"; fetch_flock_details(fx); f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 3){
                    $('#batches').select2();
                    var batches = '<?php echo $batches; ?>';
                    document.getElementById("batches").value = batches;
                    $('#batches').select2();
                    f_cnt = f_cnt + 1;
                }
                else{ }
                
                if(f_cnt <= 3){ setTimeout(set_auto_selectors, 300); }
            }
            set_auto_selectors();
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>