<?php
//breeder_dayrecord_report3.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    include "header_head.php";
    $form_path = "breeder_dayrecord_report3.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    include "header_head.php";
    $form_path = "breeder_dayrecord_report3.php?db=$db&userid=".$user_code;
}
include "decimal_adjustments.php";
include "breeder_cal_ageweeks.php";
$file_name = "Breeder Detailed Daily entry Report";

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

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Hatch Egg%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $hegg_ccode = array();
while($row = mysqli_fetch_assoc($query)){ $hegg_ccode[$row['code']] = $row['code']; } $hegg_clist = implode("','", $hegg_ccode);
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$hegg_clist') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC";
$query = mysqli_query($conn,$sql); $hegg_code = array();
while($row = mysqli_fetch_assoc($query)){ $hegg_code[$row['code']] = $row['code']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql); $item_name = array();
while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }

$fdate = $tdate = date("Y-m-d"); $farms = $units = $sheds = $batches = "all"; $flocks = "select"; $excel_type = "display"; $slno_flag = 0;
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $farms = $_POST['farms'];
    $units = $_POST['units'];
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
            <form action="<?php echo $form_path; ?>" method="post" onsubmit="return checkval();">
                <thead class="thead2 text-primary layout-navbar-fixed" width="auto" <?php if($excel_type == "print"){ echo 'style="display:none;"'; } ?>>
                    <tr>
                        <th colspan="21">
                            <div class="row">
                                <div class="m-2 form-group" style="width:120px;">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" readonly />
                                </div>
                                <div class="m-2 form-group" style="width:120px;">
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
                                        <option value="select" <?php if($flocks == "select"){ echo "selected"; } ?>>-select-</option>
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
            $html = $nhtml = $fhtml = ''; $e_cnt = $e_cnt + 5; $ffcon_cnt = $mfcon_cnt = 4; if((int)$ffeed_2flag == 1){  $ffcon_cnt += 2; } if((int)$mfeed_2flag == 1){  $mfcon_cnt += 2; }
            $html .= '<thead class="thead3" id="head_names">';

            $nhtml .= '<tr style="text-align:center;" align="center">';
            $fhtml .= '<tr style="text-align:center;" align="center">';

            $nhtml .= '<th colspan="1"></th>'; $fhtml .= '<th colspan="1"></th>';
            $nhtml .= '<th colspan="1"></th>'; $fhtml .= '<th colspan="1"></th>';
            $nhtml .= '<th colspan="1"></th>'; $fhtml .= '<th colspan="1"></th>';
            $nhtml .= '<th colspan="1"></th>'; $fhtml .= '<th colspan="1"></th>';
            $nhtml .= '<th colspan="1"></th>'; $fhtml .= '<th colspan="1"></th>';
            $nhtml .= '<th colspan="1"></th>'; $fhtml .= '<th colspan="1"></th>';
            $nhtml .= '<th colspan="1"></th>'; $fhtml .= '<th colspan="1"></th>';
            $nhtml .= '<th colspan="1"></th>'; $fhtml .= '<th colspan="1"></th>';
            $nhtml .= '<th colspan="2">Opening Birds</th>'; $fhtml .= '<th colspan="2">Opening Birds</th>';
            $nhtml .= '<th colspan="2">Mortality</th>'; $fhtml .= '<th colspan="2">Mortality</th>';
            $nhtml .= '<th colspan="2">Culls</th>'; $fhtml .= '<th colspan="2">Culls</th>';
            $nhtml .= '<th colspan="2">Sales</th>'; $fhtml .= '<th colspan="2">Sales</th>';
            $nhtml .= '<th colspan="2">Transfer In</th>'; $fhtml .= '<th colspan="2">Transfer In</th>';
            $nhtml .= '<th colspan="2">Transfer Out</th>'; $fhtml .= '<th colspan="2">Transfer Out</th>';
            $nhtml .= '<th colspan="2">Closing Birds</th>'; $fhtml .= '<th colspan="2">Closing Birds</th>';
            $nhtml .= '<th colspan="4">Body Weight</th>'; $fhtml .= '<th colspan="4">Body Weight</th>';
            $nhtml .= '<th colspan="2">Egg Weight</th>'; $fhtml .= '<th colspan="2">Egg Weight</th>';
            $nhtml .= '<th colspan="'.$ffcon_cnt.'">Female Feed Consumption</th>'; $fhtml .= '<th colspan="'.$ffcon_cnt.'">Female Feed Consumption</th>';
            $nhtml .= '<th colspan="'.$mfcon_cnt.'">Male Feed Consumption</th>'; $fhtml .= '<th colspan="'.$mfcon_cnt.'">Male Feed Consumption</th>';
            $nhtml .= '<th colspan="'.$e_cnt.'">Production</th>'; $fhtml .= '<th colspan="'.$e_cnt.'">Production</th>';
            $nhtml .= '<th colspan="1"></th>'; $fhtml .= '<th colspan="1"></th>';

            $nhtml .= '</tr>';
            $fhtml .= '</tr>';

            $nhtml .= '<tr style="text-align:center;" align="center">';
            $fhtml .= '<tr style="text-align:center;" align="center">';

            $nhtml .= '<th>Sl.No</th>'; $fhtml .= '<th id="order_num">Sl.No</th>';
            $nhtml .= '<th>Farm</th>'; $fhtml .= '<th id="order">Farm</th>';
            $nhtml .= '<th>Unit</th>'; $fhtml .= '<th id="order">Unit</th>';
            $nhtml .= '<th>Shed</th>'; $fhtml .= '<th id="order">Shed</th>';
            $nhtml .= '<th>Batch</th>'; $fhtml .= '<th id="order">Batch</th>';
            $nhtml .= '<th>Flock No.</th>'; $fhtml .= '<th id="order">Flock No.</th>';
            $nhtml .= '<th>Date</th>'; $fhtml .= '<th id="order_num">Date</th>';
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
            $nhtml .= '<th>F.Std</th>'; $fhtml .= '<th id="order_num">F.Std</th>';
            $nhtml .= '<th>F.Act</th>'; $fhtml .= '<th id="order_num">F.Act</th>';
            $nhtml .= '<th>M.Std</th>'; $fhtml .= '<th id="order_num">M.Std</th>';
            $nhtml .= '<th>M.Act</th>'; $fhtml .= '<th id="order_num">M.Act</th>';
            $nhtml .= '<th>Standard</th>'; $fhtml .= '<th id="order_num">Standard</th>';
            $nhtml .= '<th>Actual</th>'; $fhtml .= '<th id="order_num">Actual</th>';
            $nhtml .= '<th>F.Feed</th>'; $fhtml .= '<th id="order_num">F.Feed</th>';
            $nhtml .= '<th>Qty</th>'; $fhtml .= '<th id="order_num">Qty</th>';
            if((int)$ffeed_2flag == 1){
                $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">F.Feed-2</th>';
                $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">Qty-2</th>';
            }
            $nhtml .= '<th>Std. Feed / Bird</th>'; $fhtml .= '<th id="order_num">Std. Feed / Bird</th>';
            $nhtml .= '<th>Act. Feed / Bird</th>'; $fhtml .= '<th id="order_num">Act. Feed / Bird</th>';

            $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">M.Feed</th>';
            $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">Qty</th>';
            if((int)$mfeed_2flag == 1){
                $nhtml .= '<th>F</th>'; $fhtml .= '<th id="order_num">M.Feed-2</th>';
                $nhtml .= '<th>M</th>'; $fhtml .= '<th id="order_num">Qty</th>';
            }
            $nhtml .= '<th>Std. Feed / Bird</th>'; $fhtml .= '<th id="order_num">Std. Feed / Bird</th>';
            $nhtml .= '<th>Act. Feed / Bird</th>'; $fhtml .= '<th id="order_num">Act. Feed / Bird</th>';
            foreach($egg_code as $eggs){
                $nhtml .= '<th>'.$egg_name[$eggs].'</th>'; $fhtml .= '<th id="order_num">'.$egg_name[$eggs].'</th>';
            }
            $nhtml .= '<th>Total Eggs</th>'; $fhtml .= '<th id="order_num">Total Eggs</th>';
            $nhtml .= '<th>Std. Prod %</th>'; $fhtml .= '<th id="order_num">Std. Prod %</th>';
            $nhtml .= '<th>Prod. %</th>'; $fhtml .= '<th id="order_num">Prod. %</th>';
            $nhtml .= '<th>Std. HE %</th>'; $fhtml .= '<th id="order_num">Std. HE %</th>';
            $nhtml .= '<th>HE %</th>'; $fhtml .= '<th id="order_num">HE %</th>';
            $nhtml .= '<th>Remarks</th>'; $fhtml .= '<th id="order">Remarks</th>';

            $nhtml .= '</tr>';
            $fhtml .= '</tr>'; 
            $html .= $fhtml;
            $html .= '</thead>';
            $html .= '<tbody class="tbody1" id="tbody1">';
            if(isset($_POST['submit_report']) == true){
                if(sizeof($flock_alist) > 0){
                    $flock_list = implode("','",$flock_alist);
                    $sql = "SELECT * FROM `breeder_dayentry_consumed` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `flock_code` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`flock_code` ASC";
                    $query = mysqli_query($conn,$sql);
                    $fmort_qty = $fcull_qty = $fbody_weight = $ffeed_code1 = $ffeed_qty1 = $ffeed_code2 = $ffeed_qty2 = $mfeed_code1 = $mfeed_qty1 = $mfeed_code2 = $mfeed_qty2 = $mmort_qty = $mcull_qty = 
                    $mbody_weight = $egg_weight = $flock_alist = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $key1 = $row['flock_code'];
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
                }
                $html .= '</tbody>';
                $html .= '<tfoot class="thead3">';
                $html .= '<tr>';
                $html .= '<th style="text-align:left;" colspan="3">Total</th>';
                //Opening Birds
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tfopn_birds,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tmopn_birds,5))).'</th>';
                //Mortality
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tfmort_birds,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tmmort_birds,5))).'</th>';
                //Culls
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tfcull_birds,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tmcull_birds,5))).'</th>';
                //Transfer-In
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tftrin_birds,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tmtrin_birds,5))).'</th>';
                //transfer-out
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tftrout_birds,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tmtrout_birds,5))).'</th>';
                //Closing Birds
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tfflk_cbirds,5))).'</th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tmflk_cbirds,5))).'</th>';
                //Feed Consumed Details
                $html .= '<th style="text-align:right;"></th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($ff_qty1,5))).'</th>';
                if((int)$ffeed_2flag == 1){
                    $html .= '<th style="text-align:right;"></th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($ff_qty2,5))).'</th>';
                }
                $html .= '<th style="text-align:right;"></th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mf_qty1,5))).'</th>';
                if((int)$mfeed_2flag == 1){
                    $html .= '<th style="text-align:right;"></th>';
                    $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($mf_qty2,5))).'</th>';
                }
                //Feed per Bird
                $act_ffpbird = 0; if((float)$tfopn_birds != 0){ $act_ffpbird = round((((float)$tff_qty / (float)$tfopn_birds) * 1000),2); }
                $html .= '<th style="text-align:right;">'.number_format_ind(round($act_ffpbird,5)).'</th>';
                $act_mfpbird = 0; if((float)$tmopn_birds != 0){ $act_mfpbird = round((((float)$tmf_qty / (float)$tmopn_birds) * 1000),2); }
                $html .= '<th style="text-align:right;">'.number_format_ind(round($act_mfpbird,5)).'</th>';

                //Egg Prod Details
                $avg_sdeper = 0; if((float)$slno != 0){ $avg_sdeper = round((((float)$tstd_egg_pper / (float)$slno)),2); }
                $tot_hdper = 0; if((float)$tfopn_birds != 0){ $tot_hdper = round((((float)$tegg_rqty / (float)$tfopn_birds) * 100),2); }
                foreach($egg_code as $eggs){
                    if($hegg_code == $eggs){
                        $hegg_per = 0; if((float)$tegg_rqty != 0){ $hegg_per = (((float)$egg_cqty[$eggs] / (float)$tegg_rqty) * 100); }
                        $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($egg_cqty[$eggs],5))).'</th>';
                        $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($hegg_per,5))).'</th>';
                    }
                    else{
                        $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($egg_cqty[$eggs],5))).'</th>';
                    }
                }
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tegg_rqty,5))).'</th>';
                $html .= '<th style="text-align:right;"></th>';
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($tot_hdper,5))).'</th>';
                $html .= '<th style="text-align:right;"></th>';

                $html .= '</tr>';
                $html .= '</tfoot>';
            }
            echo $html;
        ?>
        </table><br/><br/><br/>
        <script>
            function checkval(){
                var flocks = document.getElementById("flocks").value;
                var l = false;
                if(flocks == "select"){
                    alert("Please select Flock");
                    document.getElementById("flocks").focus();
                    l = false;
                }
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
            function fetch_flock_details(a){
                var farms = document.getElementById("farms").value;
                var units = document.getElementById("units").value;
                var sheds = document.getElementById("sheds").value;
                var batches = document.getElementById("batches").value;
                var flocks = document.getElementById("flocks").value;

                var user_code = '<?php echo $user_code; ?>';
                var ff_flag = uf_flag = sf_flag = bf_flag = fl_flag = 0;
                if(a == "farms"){ ff_flag = 1; }
                else if(a == "units"){ uf_flag = 1; }
                else if(a == "sheds"){ sf_flag = 1; }
                else if(a == "batches"){ bf_flag = 1; }
                else if(a == "flocks"){ fl_flag = 1; }
                else{ ff_flag = 1; }

                var fetch_fltrs = new XMLHttpRequest();
                var method = "GET";
                var url = "breeder_fetch_flock_filter_master.php?farms="+farms+"&units="+units+"&sheds="+sheds+"&batches="+batches+"&flocks="+flocks+"&fetch_type=single&flock_type=select";
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
                        var flock_list = fltr_dt2[4];

                        if(ff_flag == 1){
                            removeAllOptions(document.getElementById("units"));
                            removeAllOptions(document.getElementById("sheds"));
                            removeAllOptions(document.getElementById("batches"));
                            removeAllOptions(document.getElementById("flocks"));
                            $('#units').append(unit_list);
                            $('#sheds').append(shed_list);
                            $('#batches').append(batch_list);
                            $('#flocks').append(flock_list);
                        }
                        else if(uf_flag == 1){
                            removeAllOptions(document.getElementById("sheds"));
                            removeAllOptions(document.getElementById("batches"));
                            removeAllOptions(document.getElementById("flocks"));
                            $('#sheds').append(shed_list);
                            $('#batches').append(batch_list);
                            $('#flocks').append(flock_list);
                        }
                        else if(sf_flag == 1){
                            removeAllOptions(document.getElementById("batches"));
                            removeAllOptions(document.getElementById("flocks"));
                            $('#batches').append(batch_list);
                            $('#flocks').append(flock_list);
                        }
                        else if(bf_flag == 1){
                            removeAllOptions(document.getElementById("flocks"));
                            $('#flocks').append(flock_list);
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
                    var fx = "batches"; fetch_flock_details(fx); f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 4){
                    $('#flocks').select2();
                    var flocks = '<?php echo $flocks; ?>';
                    document.getElementById("flocks").value = flocks;
                    $('#flocks').select2();
                    f_cnt = f_cnt + 1;
                }
                else{ }
                
                if(f_cnt <= 4){ setTimeout(set_auto_selectors, 300); }
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