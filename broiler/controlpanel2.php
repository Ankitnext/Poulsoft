<?php 
include "newConfig.php";
//echo $_SERVER['REMOTE_ADDR'];
include "number_format_ind.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");

include "broiler_check_tableavailability.php";

$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; }
else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; }
else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; }
else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }

$batch_code = $batch_name = $batch_book = $batch_gcflag = $batch_farm = array();
$sql = "SELECT * FROM `broiler_batch` WHERE `gc_flag` = '0' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
  $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_book[$row['code']] = $row['book_num'];
  $batch_gcflag[$row['code']] = $row['gc_flag']; $batch_farm[$row['code']] = $row['farm_code'];
}

$farm_code = $farm_ccode = $farm_name = $farm_branch = $farm_line = $farm_supervisor = $farm_svr = $farm_farmer = array();
$closed_farm_list_filter = $closed_line_list_filter = $closed_branch_list_filter = $closed_supervisor_list_filter = "";
$closed_farm_list_filter = implode("','",$batch_farm);
$sql = "SELECT * FROM `broiler_farm` WHERE `code` IN ('$closed_farm_list_filter') ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." AND `description` NOT LIKE '%OLD FARMS%' AND `dflag` = '0' AND active = 1 ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $farm_code[$row['code']] = $row['code']; $farm_ccode[$row['code']] = $row['farm_code']; $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code'];
    $farm_supervisor[$row['code']] = $row['supervisor_code']; $farm_svr[$row['supervisor_code']] = $row['code'];
    $farm_farmer[$row['code']] = $row['farmer_code'];
}
$closed_line_list_filter = implode("','",$farm_line);
$closed_branch_list_filter = implode("','",$farm_branch);
$closed_supervisor_list_filter = implode("','",$farm_supervisor);

$branch_code = $branch_name = array();
$sql = "SELECT * FROM `location_branch` WHERE `code` IN ('$closed_branch_list_filter') ".$branch_access_filter1." AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }

$line_code = $line_name = $line_branch = array();
$sql = "SELECT * FROM `location_line` WHERE `code` IN ('$closed_line_list_filter') ".$line_access_filter1."".$branch_access_filter2." AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; $line_branch[$row['code']] = $row['branch_code']; }

$supervisor_code = $supervisor_name = array();
$sql = "SELECT * FROM `broiler_employee` WHERE `code` IN ('$closed_supervisor_list_filter') AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }

$fdate = date("Y-m-d"); $branches = $lines = $supervisors = $farms = "all";
if(isset($_POST['fdate'])){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));

    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    $supervisors = $_POST['supervisors'];
    $farms = $_POST['farms'];
}

$farm_list = "";
if($farms != "all"){
    $farm_filter = " AND farm_code = '$farms'";
}
else if($supervisors != "all"){
    foreach($farm_code as $fcode){ if($farm_supervisor[$fcode] == $supervisors){ if($farm_list == ""){ $farm_list = $fcode; } else{ $farm_list = $farm_list."','".$fcode; } } }
    $farm_filter = " AND farm_code IN ('$farm_list')";
}
else if($lines != "all"){
    foreach($farm_code as $fcode){
        if($farm_line[$fcode] == $lines){ if($farm_list == ""){ $farm_list = $fcode; } else{ $farm_list = $farm_list."','".$fcode; } } }
    $farm_filter = " AND farm_code IN ('$farm_list')";
}
else if($branches != "all"){
    foreach($farm_code as $fcode){ if($farm_branch[$fcode] == $branches){ if($farm_list == ""){ $farm_list = $fcode; } else{ $farm_list = $farm_list."','".$fcode; } } }
    $farm_filter = " AND farm_code IN ('$farm_list')";
}
else{
    foreach($farm_code as $fcode){ if($farm_list == ""){ $farm_list = $fcode; } else{ $farm_list = $farm_list."','".$fcode; } }
    $farm_filter = " AND farm_code IN ('$farm_list')";
}

$sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $display_dashboard_flag = $row['display_dashboard_flag']; }
$ip_addr = $_SERVER['REMOTE_ADDR'];
//if($user_name == "psbroiler" || $user_name == "EXPO" || $user_name == "kohinoor"){
if($display_dashboard_flag == 1 || $display_dashboard_flag == "1"){
    $colors[0] = 'cyan';
    $colors[1] = 'blue';
    $colors[2] = 'yellow';
    $colors[3] = 'red';
    $colors[4] = 'lime';
    $colors[5] = 'green';
    $colors[6] = 'orange';
    $colors[7] = 'purple';
    $colors[8] = 'brown';
    $colors[9] = 'coral';
    $colors[10] = 'silver';
    $colors[11] = 'maroon';
    $colors[12] = 'skyblue';
    $colors[13] = 'gray';
    $colors[14] = 'pink';
    $colors[15] = 'blue';
    $colors[16] = 'purple1';
    $colors[17] = 'muster';
    $colors[18] = 'black1';
    $colors[19] = 'cream1';
    $colors[20] = 'green1';
    
?>
<html lang="en">
  <head>
    <?php include "header_head.php"; ?>
    <!-- Datepicker -->
    <link href="datepicker/jquery-ui.css" rel="stylesheet">
    <style>
        body{
            background-color: #F7EDEF;
        }
        #openings{
            background-image: url("images/db_1.gif");
        }
        .bg-purple1{
            background-color: #a569bd;
            color: #a569bd;
        }
        .bg-brown{
            background-color: #a04000;
            color: #a04000;
        }
        .bg-silver{
            background-color: #95a5a6;
            color: #95a5a6;
        }
        .bg-coral{
            background-color: #45b39d;
            color: #45b39d;
        }
        .bg-muster{
            background-color: #7d6608;
            color: #7d6608;
        }
        .bg-black1{
            background-color: #2c3e50;
            color: #2c3e50;
        }
        .bg-cream1{
            background-color: #f6ddcc;
            color: #f6ddcc;
        }
        .bg-green1{
            background-color: #d5f5e3;
            color: #d5f5e3;
        }
    </style>
  </head>
  <body>
    <section class="content">
      <div class="container-fluid">
        <div class="content-header">
          <div class="container-fluid">
           <?php if($user_name != 'Breeder') { ?>
          <form action="controlpanel2.php" method="post">
            <div class="row">
                <!--- <div class="col-md-12" align="left"><h4><marquee direction="center" style="color:#fff;font-weight:bold;background-color: #008000;margin-bottom: 10px;padding: 5px;">Good news!!! WhatsApp is up and running now. Please call 8746822822/8746855855 to scan and activate.Thank you!!!</marquee></h4></div> --->
                <!--<div class="col-md-12" align="left"><h4><marquee direction="center" style="color:#fff;font-weight:bold;background-color: red;margin-bottom: 10px;padding: 5px;">Please call 8746822822/8746855855 to scan and activate.Thank you!!!</marquee></h4></div>-->
                <!--<div class="col-md-12" align="left"><h4><marquee direction="center" style="color:#fff;font-weight:bold;background-color: green;margin-bottom: 10px;padding: 5px;">The server is now up and running successfully! Thank you for your cooperation during this period, and we assure you that this issue will not recur in the future.</marquee></h4></div>-->
                <!--<div class="col-md-12" align="left"><h4><marquee direction="center" style="color:#fff;font-weight:bold;background-color: green;margin-bottom: 10px;padding: 5px;">Thank you for your warm wishes and continued support. Please note that our office has now shifted to Shivananda Circle, Sheshadripuram.</marquee></h4></div>-->
     
                <div class="col-md-2">
                  <h3 class="m-0" style="color:blue;font-size:20px;font-weight:bold;">Live Dashboard</h3>
                </div>
                <div class="col-md-10" style="text-align:right;">
                    <div class="row justify-content-end align-items right">
                        <div class="mr-2 form-group"><a href="broiler_projection_report1.php" style="font-size:20px;font-weight:bold;" target="_BLANK"><i class="fa fa-chart-pie" style="color:green;"></i>&ensp;Forecasting</a></div>
                        <div class="mr-2 form-group"><a href="controlpanel2_1.php" style="font-size:20px;font-weight:bold;"><i class="fa fa-chart-pie" style="color:green;"></i>&ensp;New Dashboards</a></div>
                        <div class="form-group"><a href="controlpanel_list.php" style="font-size:20px;font-weight:bold;"><i class="fa fa-chart-pie" style="color:green;"></i>&ensp;Click for Dashboards</a></div>
                    </div>
                </div>
            </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="row">
                    <div class="form-group" style="width:120px;">
                      <label for="fdate">Date</label>
                      <input type="text" name="fdate" id="fdate" class="form-control datepicker" value="<?php echo date('d.m.Y',strtotime($fdate)); ?>" style="width:110px;">
                    </div>
                    <div class="form-group" style="width:150px;">
                        <label for="branches">Branch</label>
                        <select name="branches" id="branches" class="form-control select2" style="width:140px;" onchange="fetch_farms_details(this.id)">
                            <option value="all" <?php if($branches == "all"){ echo "selected"; } ?>>-All-</option>
                            <?php foreach($branch_code as $bcode){ if($branch_name[$bcode] != ""){ ?>
                            <option value="<?php echo $bcode; ?>" <?php if($branches == $bcode){ echo "selected"; } ?>><?php echo $branch_name[$bcode]; ?></option>
                            <?php } } ?>
                        </select>
                    </div>
                    <div class="form-group" style="width:160px;">
                        <label for="lines">Line</label>
                        <select name="lines" id="lines" class="form-control select2" style="width:150px;" onchange="fetch_farms_details(this.id)">
                            <option value="all" <?php if($lines == "all"){ echo "selected"; } ?>>-All-</option>
                            <?php foreach($line_code as $lcode){ if($line_name[$lcode] != ""){ ?>
                            <option value="<?php echo $lcode; ?>" <?php if($lines == $lcode){ echo "selected"; } ?>><?php echo $line_name[$lcode]; ?></option>
                            <?php } } ?>
                        </select>
                    </div>
                    <div class="form-group" style="width:160px;">
                        <label for="supervisors">Supervisor</label>
                        <select name="supervisors" id="supervisors" class="form-control select2" style="width:150px;" onchange="fetch_farms_details(this.id)">
                            <option value="all" <?php if($supervisors == "all"){ echo "selected"; } ?>>-All-</option>
                            <?php foreach($supervisor_code as $scode){ if($supervisor_name[$scode] != ""){ ?>
                            <option value="<?php echo $scode; ?>" <?php if($supervisors == $scode){ echo "selected"; } ?>><?php echo $supervisor_name[$scode]; ?></option>
                            <?php } } ?>
                        </select>
                    </div>
                    <div class="form-group" style="width:300px;">
                        <label for="farms">Farm</label>
                        <select name="farms" id="farms" class="form-control select2" style="width:290px;">
                            <option value="all" <?php if($farms == "all"){ echo "selected"; } ?>>-All-</option>
                            <?php foreach($farm_code as $fcode){ if($farm_name[$fcode] != ""){ ?>
                            <option value="<?php echo $fcode; ?>" <?php if($farms == $fcode){ echo "selected"; } ?>><?php echo $farm_name[$fcode]; ?></option>
                            <?php } } ?>
                        </select>
                    </div>
                    <div class="form-group" style="width:120px;"><br/>
                      <button type="submit" name="submit" id="submit" class="btn btn-sm btn-success">Submit</button>
                    </div>
                  </div>
                </div>
              </div>
            </form>
            <?php } ?>
          </div>
        </div>
        <?php
            $sql = "SELECT * FROM `broiler_batch` WHERE `gc_flag` = '0'".$farm_filter." AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $batch_list = $farm_list = ""; $batch_array = $farm_array = array();
            while($row = mysqli_fetch_assoc($query)){
                $batch_array[$row['code']] = $row['code'];
                $batch_name[$row['code']] = $row['description'];
                $farm_array[$row['code']] = $row['farm_code'];
                if($batch_list == ""){ $batch_list = $row['code']; } else{ $batch_list = $batch_list."','".$row['code']; }
                if($farm_list == ""){ $farm_list = $row['farm_code']; } else{ $farm_list = $farm_list."','".$row['farm_code']; }
            }

            $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Broiler Bird%' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $birdcat_list = "";
            while($row = mysqli_fetch_assoc($query)){ if($birdcat_list == ""){ $birdcat_list = $row['code']; } else{ $birdcat_list = $birdcat_list."','".$row['code']; } $broiler_bird_cat = $row['code']; }

            $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Broiler Chick%' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ if($birdcat_list == ""){ $birdcat_list = $row['code']; } else{ $birdcat_list = $birdcat_list."','".$row['code']; } }

            $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$birdcat_list') AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $item_list = "";
            while($row = mysqli_fetch_assoc($query)){ if($item_list == ""){ $item_list = $row['code']; } else{ $item_list = $item_list."','".$row['code']; } if($row['category'] == $broiler_bird_cat && $row['description'] == "BROILER BIRDS" || $row['category'] == $broiler_bird_cat && $row['description'] == "Broiler Birds"){ $broiler_bird_code = $row['code']; } }

            $sql = "SELECT SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty,farm_batch FROM `broiler_purchases` WHERE `date` <= '$fdate' AND `icode` IN ('$item_list') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `farm_batch` ORDER BY `farm_batch` ASC";
            $query = mysqli_query($conn,$sql); $opndate_purchase_chicks = array();
            while($row = mysqli_fetch_assoc($query)){
                if(empty($opndate_purchase_chicks[$row['farm_batch']])){
                    $opndate_purchase_chicks[$row['farm_batch']] =  ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                }
                else{
                    $opndate_purchase_chicks[$row['farm_batch']] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                }
            }
            $sql = "SELECT SUM(quantity) as quantity,to_batch FROM `item_stocktransfers` WHERE `date` <= '$fdate' AND `code` IN ('$item_list') AND `to_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `to_batch` ORDER BY `to_batch` ASC";
            $query = mysqli_query($conn,$sql); $opndate_transin_chicks = array();
            while($row = mysqli_fetch_assoc($query)){
                $opndate_transin_chicks[$row['to_batch']] = $opndate_transin_chicks[$row['to_batch']] + ($row['quantity']);
            }
            $sql = "SELECT SUM(mortality) as mortality,SUM(culls) as culls,MAX(brood_age) as brood_age,batch_code FROM `broiler_daily_record` WHERE `date` < '$fdate' AND `batch_code` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `batch_code` ORDER BY `batch_code` ASC";
            $query = mysqli_query($conn,$sql); $opndate_dentry_chicks = array();
            while($row = mysqli_fetch_assoc($query)){
                if(empty($opndate_dentry_chicks[$row['batch_code']])){
                    $opndate_dentry_chicks[$row['batch_code']] = ((float)$row['mortality'] + (float)$row['culls']);
                }
                else{
                    $opndate_dentry_chicks[$row['batch_code']] += ((float)$row['mortality'] + (float)$row['culls']);
                }
                
                $brood_age[$row['batch_code']] = $row['brood_age'];
            }
            $sql = "SELECT SUM(birds) as birds,SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty,farm_batch FROM `broiler_sales` WHERE `date` < '$fdate' AND `icode` IN ('$item_list') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `farm_batch` ORDER BY `farm_batch` ASC";
            $query = mysqli_query($conn,$sql); $opndate_sale_birdwt = $opndate_sale_birdno = array();
            while($row = mysqli_fetch_assoc($query)){
                if(empty($opndate_sale_birdno[$row['farm_batch']])){
                    $opndate_sale_birdno[$row['farm_batch']] = ((float)$row['birds']);
                }
                else{
                    $opndate_sale_birdno[$row['farm_batch']] += ((float)$row['birds']);
                }
                if(empty($opndate_sale_birdwt[$row['farm_batch']])){
                    $opndate_sale_birdwt[$row['farm_batch']] = ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                }
                else{
                    $opndate_sale_birdwt[$row['farm_batch']] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                }
            }
            if($count131 > 0){
                $sql = "SELECT SUM(birds) as birds,SUM(weight) as rcd_qty,from_batch FROM `broiler_bird_transferout` WHERE `date` < '$fdate' AND `item_code` IN ('$item_list') AND `from_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `from_batch` ORDER BY `from_batch` ASC";
                $query = mysqli_query($conn,$sql); //$opndate_sale_birdwt = $opndate_sale_birdno = array();
                while($row = mysqli_fetch_assoc($query)){
                    if(empty($opndate_sale_birdno[$row['from_batch']])){
                        $opndate_sale_birdno[$row['from_batch']] = ((float)$row['birds']);
                    }
                    else{
                        $opndate_sale_birdno[$row['from_batch']] += ((float)$row['birds']);
                    }
                    if(empty($opndate_sale_birdwt[$row['from_batch']])){
                        $opndate_sale_birdwt[$row['from_batch']] = ((float)$row['rcd_qty']);
                    }
                    else{
                        $opndate_sale_birdwt[$row['from_batch']] += ((float)$row['rcd_qty']);
                    }
                    
                }
            }
            $sql = "SELECT SUM(quantity) as quantity,from_batch FROM `item_stocktransfers` WHERE `date` < '$fdate' AND `code` IN ('$item_list') AND `from_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `from_batch` ORDER BY `from_batch` ASC";
            $query = mysqli_query($conn,$sql); $opndate_transout_chicks = array();
            while($row = mysqli_fetch_assoc($query)){
                if(empty($opndate_transout_chicks[$row['from_batch']])){
                    $opndate_transout_chicks[$row['from_batch']] = ((float)$row['quantity']);
                }
                else{
                    $opndate_transout_chicks[$row['from_batch']] += ((float)$row['quantity']);
                }
                
            }
            
            //echo "<br/>".
            $sql = "SELECT SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty,farm_batch FROM `broiler_purchases` WHERE `date` = '$fdate' AND `icode` IN ('$item_list') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `farm_batch` ORDER BY `farm_batch` ASC";
            $query = mysqli_query($conn,$sql); $curdate_purchase_chicks = array();
            while($row = mysqli_fetch_assoc($query)){
                if(empty($curdate_purchase_chicks[$row['farm_batch']])){
                    $curdate_purchase_chicks[$row['farm_batch']] = ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                }
                else{
                    $curdate_purchase_chicks[$row['farm_batch']] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                }
                
            }
            $sql = "SELECT SUM(quantity) as quantity,to_batch FROM `item_stocktransfers` WHERE `date` = '$fdate' AND `code` IN ('$item_list') AND `to_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `to_batch` ORDER BY `to_batch` ASC";
            $query = mysqli_query($conn,$sql); $curdate_transin_chicks = array();
            while($row = mysqli_fetch_assoc($query)){
                if(empty($curdate_transin_chicks[$row['to_batch']])){
                    $curdate_transin_chicks[$row['to_batch']] = ((float)$row['quantity']);
                }
                else{
                    $curdate_transin_chicks[$row['to_batch']] += ((float)$row['quantity']);
                }
                
            }
            $live_farms = 0; $brood_curage = array();
            $sql = "SELECT * FROM `broiler_daily_record` WHERE `date` = '$fdate' AND `batch_code` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `batch_code` ASC";
            $query = mysqli_query($conn,$sql); $live_farms = mysqli_num_rows($query);
            
            $sql = "SELECT SUM(mortality) as mortality,SUM(culls) as culls,MAX(brood_age) as brood_age,batch_code FROM `broiler_daily_record` WHERE `date` = '$fdate' AND `batch_code` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `batch_code` ORDER BY `batch_code` ASC";
            $query = mysqli_query($conn,$sql); $curdate_dentry_chicks = array();
            while($row = mysqli_fetch_assoc($query)){
                if(empty($curdate_dentry_chicks[$row['batch_code']])){
                    $curdate_dentry_chicks[$row['batch_code']] = ((float)$row['mortality'] + (float)$row['culls']);
                }
                else{
                    $curdate_dentry_chicks[$row['batch_code']] += ((float)$row['mortality'] + (float)$row['culls']);
                }
                $brood_age[$row['batch_code']] = $row['brood_age'];
                $brood_curage[$row['batch_code']] = $row['brood_age'];
                $dentry_batches[$row['batch_code']] = $row['batch_code'];
            }
            $sql = "SELECT SUM(birds) as birds,SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty,farm_batch FROM `broiler_sales` WHERE `date` = '$fdate' AND `icode` IN ('$item_list') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `farm_batch` ORDER BY `farm_batch` ASC";
            $query = mysqli_query($conn,$sql); $curdate_sale_birdwt = $curdate_sale_birdno = array();
            while($row = mysqli_fetch_assoc($query)){
                if(empty($curdate_sale_birdno[$row['farm_batch']])){
                    $curdate_sale_birdno[$row['farm_batch']] = ((float)$row['birds']);
                }
                else{
                    $curdate_sale_birdno[$row['farm_batch']] += ((float)$row['birds']);
                }
                if(empty($curdate_sale_birdwt[$row['farm_batch']])){
                    $curdate_sale_birdwt[$row['farm_batch']] = ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                }
                else{
                    $curdate_sale_birdwt[$row['farm_batch']] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                }
            }
            if($count131 > 0){
                $sql = "SELECT SUM(birds) as birds,SUM(`weight`) as rcd_qty,from_batch FROM `broiler_bird_transferout` WHERE `date` = '$fdate' AND `item_code` IN ('$item_list') AND `from_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `from_batch` ORDER BY `from_batch` ASC";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    if(empty($curdate_sale_birdno[$row['from_batch']])){
                        $curdate_sale_birdno[$row['from_batch']] = ((float)$row['birds']);
                    }
                    else{
                        $curdate_sale_birdno[$row['from_batch']] += ((float)$row['birds']);
                    }
                    if(empty($curdate_sale_birdwt[$row['from_batch']])){
                        $curdate_sale_birdwt[$row['from_batch']] = ((float)$row['rcd_qty']);
                    }
                    else{
                        $curdate_sale_birdwt[$row['from_batch']] += ((float)$row['rcd_qty']);
                    }
                }
            }
         
            //Item Wise Sale Details
            $birds_code = $birds_name = array();
            $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Birds%' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $iw_birds_list = "";
            while($row = mysqli_fetch_assoc($query)){ if($iw_birds_list == ""){ $iw_birds_list = $row['code']; } else{ $iw_birds_list = $iw_birds_list."','".$row['code']; } }

            $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$iw_birds_list') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $iw_item_list = "";
            while($row = mysqli_fetch_assoc($query)){ $birds_code[$row['code']] = $row['code']; $birds_name[$row['code']] = $row['description']; if($iw_item_list == ""){ $iw_item_list = $row['code']; } else{ $iw_item_list = $iw_item_list."','".$row['code']; } }

            $sql = "SELECT * FROM `broiler_sales` WHERE `date` = '$fdate' AND `icode` IN ('$iw_item_list') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`id` ASC";
            $query = mysqli_query($conn,$sql); $iw_sale_birds = $iw_sale_weight = $iw_sale_amount = array(); $sale_broiler_bird_nos = $sale_broiler_bird_qty = 0;
            while($row = mysqli_fetch_assoc($query)){
                if(empty($iw_sale_birds[$row['icode']])){ $iw_sale_birds[$row['icode']] = 0; }
                if(empty($iw_sale_weight[$row['icode']])){ $iw_sale_weight[$row['icode']] = 0; }
                if(empty($iw_sale_amount[$row['icode']])){ $iw_sale_amount[$row['icode']] = 0; }

                $iw_sale_birds[$row['icode']] += ((float)$row['birds']);
                $iw_sale_weight[$row['icode']] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                $iw_sale_amount[$row['icode']] += ((float)$row['item_tamt']);

                if($row['icode'] == $broiler_bird_code){
                    $sale_broiler_bird_nos += ((float)$row['birds']);
                    $sale_broiler_bird_qty += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                }
            }
            $sql = "SELECT * FROM `broiler_purchases` WHERE `date` = '$fdate' AND `icode` IN ('$iw_item_list') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`id` ASC";
            $query = mysqli_query($conn,$sql); $iw_pur_birds = $iw_pur_weight = $iw_pur_amount = array(); $pur_broiler_bird_nos = $pur_broiler_bird_qty = 0;
            while($row = mysqli_fetch_assoc($query)){
                if(empty($iw_pur_birds[$row['icode']])){ $iw_pur_birds[$row['icode']] = 0; }
                if(empty($iw_pur_weight[$row['icode']])){ $iw_pur_weight[$row['icode']] = 0; }
                if(empty($iw_pur_amount[$row['icode']])){ $iw_pur_amount[$row['icode']] = 0; }

                $iw_pur_birds[$row['icode']] += ((float)$row['birds']);
                $iw_pur_weight[$row['icode']] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                $iw_pur_amount[$row['icode']] += ((float)$row['item_tamt']);

                if($row['icode'] == $broiler_bird_code){
                    $pur_broiler_bird_nos += ((float)$row['birds']);
                    $pur_broiler_bird_qty += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                }
            }
            //Cash/Bank Balance
            $crb_code = $crb_name = array();
            $sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash','Bank') AND `dflag` = '0' ORDER BY `ctype`,`description` ASC"; $query = mysqli_query($conn,$sql); $crb_coa_list = "";
            while($row = mysqli_fetch_assoc($query)){ $crb_code[$row['code']] = $row['code']; $crb_name[$row['code']] = $row['description']; if($crb_coa_list == ""){ $crb_coa_list = $row['code']; } else{ $crb_coa_list = $crb_coa_list."','".$row['code']; } }

            $sql = "SELECT SUM(amount) as amount,coa_code,crdr FROM `account_summary` WHERE `date` <= '$fdate' AND `coa_code` IN ('$crb_coa_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `coa_code`,`crdr` ORDER BY `coa_code`,`crdr` ASC";
            $query = mysqli_query($conn,$sql); $crb_cr_amt = $crb_dr_amt = array();
            while($row = mysqli_fetch_assoc($query)){
                if(empty($crb_cr_amt[$row['coa_code']])){ $crb_cr_amt[$row['coa_code']] = 0; }
                if(empty($crb_dr_amt[$row['coa_code']])){ $crb_dr_amt[$row['coa_code']] = 0; }

                if($row['crdr'] == "CR"){
                    $crb_cr_amt[$row['coa_code']] += $row['amount'];
                }
                else if($row['crdr'] == "DR"){
                    $crb_dr_amt[$row['coa_code']] += $row['amount'];
                }
                else{ }
            }

            //Day -1 Lifting Details
            $yday_date = date('Y-m-d', strtotime('-1 days', strtotime($fdate)));
            $sql = "SELECT SUM(birds) as birds,SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty,farm_batch FROM `broiler_sales` WHERE `date` = '$yday_date' AND `icode` IN ('$item_list') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `farm_batch` ORDER BY `farm_batch` ASC";
            $query = mysqli_query($conn,$sql); $yesterday_sale_birdwt = $yesterday_sale_birdno = array();
            while($row = mysqli_fetch_assoc($query)){
                if(empty($yesterday_sale_birdno[$row['farm_batch']])){
                    $yesterday_sale_birdno[$row['farm_batch']] = ((float)$row['birds']);
                }
                else{
                    $yesterday_sale_birdno[$row['farm_batch']] += ((float)$row['birds']);
                }
                if(empty($yesterday_sale_birdwt[$row['farm_batch']])){
                    $yesterday_sale_birdwt[$row['farm_batch']] = ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                }
                else{
                    $yesterday_sale_birdwt[$row['farm_batch']] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                }
                
            }
            $sql = "SELECT SUM(quantity) as quantity,from_batch FROM `item_stocktransfers` WHERE `date` = '$fdate' AND `code` IN ('$item_list') AND `from_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `from_batch` ORDER BY `from_batch` ASC";
            $query = mysqli_query($conn,$sql); $curdate_transout_chicks = array();
            while($row = mysqli_fetch_assoc($query)){
                if(empty($curdate_transout_chicks[$row['from_batch']])){
                    $curdate_transout_chicks[$row['from_batch']] = ((float)$row['quantity']);
                }
                else{
                    $curdate_transout_chicks[$row['from_batch']] += ((float)$row['quantity']);
                }
                
            }
            
            $branch_opening_birds = $branch_curmort_birds = $branch_cur_birdno = $branch_cur_birdwt = $branch_closing_birds = $farm_branch = $branch_list = array();
            $total_opn_birds = $total_popn_birds = $opening_birds = $cur_mortality = $total_cur_mort = $total_opn_mort = $total_in_chicks = $cur_sale_birdno = $cur_sale_birdwt = $total_cur_birdno = $total_cur_birdwt = 
            $closing_birds = $total_cls_birds = $abv_40_opn = $abv_40_mrt = $abv_40_sle = $abv_40_cls = $tm_farm1 = $tm_farm2 = $tm_farm3 = $tm_farm4 = $farm1 = $farm2 = $farm3 = $farm4 = 
            $age_7 = $age_14 = $age_21 = $age_28 = $age_35 = $age_42 = $age_49 = $age_56 = $age_oth = $age_farm7 = $age_farm14 = $age_farm21 = $age_farm28 = $age_farm35 = $age_farm42 = $age_grt42 = $age_farmgrt42 = $age_farm49 = $age_farm56 = $age_farmoth = 0;
            $brh_code = "";
            $sql1 = "SELECT * FROM `broiler_farm` WHERE `code` IN ('$farm_list') ORDER BY `description` ASC"; $query1 = mysqli_query($conn,$sql1); $dcount1 = mysqli_num_rows($query1);
            while($row1 = mysqli_fetch_assoc($query1)){ $farm_branch[$row1['code']] = $row1['branch_code']; }

            $i = $p_tot = $m_tot = $s_tot = $cm_tot = $cs_tot = $total_yest_birdno = $total_yest_birdwt = $total_farms = 0;
            $week1_mort = $week2_mort = $week3_mort = $week4_mort = $week5_mort = $week6_mort = $week7_mort = $week8_mort = 0;
            $farm1_mort = $farm2_mort = $farm3_mort = $farm4_mort = $farm5_mort = $farm6_mort = $farm7_mort = $farm8_mort = 0;
            $week1_opnb = $week2_opnb = $week3_opnb = $week4_opnb = $week5_opnb = $week6_opnb = $week7_opnb = $week8_opnb = 0;
            $tm_per1 = $tm_per2 = $tm_per3 = $tm_per4 = "";
            foreach($batch_array as $bcode){
                $brh_code = ""; $frm1 = $farm_array[$bcode]; $brh_code = $farm_branch[$frm1]; $branch_list[$brh_code] = $brh_code;
                if(empty($opndate_purchase_chicks[$bcode])){ $opndate_purchase_chicks[$bcode] = 0; }
                if(empty($opndate_transin_chicks[$bcode])){ $opndate_transin_chicks[$bcode] = 0; }
                if(empty($opndate_dentry_chicks[$bcode])){ $opndate_dentry_chicks[$bcode] = 0; }
                if(empty($opndate_sale_birdno[$bcode])){ $opndate_sale_birdno[$bcode] = 0; }
                if(empty($opndate_transout_chicks[$bcode])){ $opndate_transout_chicks[$bcode] = 0; }
                if(empty($curdate_dentry_chicks[$bcode])){ $curdate_dentry_chicks[$bcode] = 0; }
                if(empty($curdate_sale_birdno[$bcode])){ $curdate_sale_birdno[$bcode] = 0; }
                if(empty($curdate_sale_birdwt[$bcode])){ $curdate_sale_birdwt[$bcode] = 0; }
                if(empty($yesterday_sale_birdno[$bcode])){ $yesterday_sale_birdno[$bcode] = 0; }
                if(empty($yesterday_sale_birdwt[$bcode])){ $yesterday_sale_birdwt[$bcode] = 0; }
                if(empty($branch_opening_birds[$brh_code])){ $branch_opening_birds[$brh_code] = 0; }
                if(empty($branch_curmort_birds[$brh_code])){ $branch_curmort_birds[$brh_code] = 0; }
                if(empty($branch_cur_birdno[$brh_code])){ $branch_cur_birdno[$brh_code] = 0; }
                if(empty($branch_cur_birdwt[$brh_code])){ $branch_cur_birdwt[$brh_code] = 0; }
                if(empty($branch_yest_birdno[$brh_code])){ $branch_yest_birdno[$brh_code] = 0; }
                if(empty($branch_yest_birdwt[$brh_code])){ $branch_yest_birdwt[$brh_code] = 0; }
                if(empty($branch_closing_birds[$brh_code])){ $branch_closing_birds[$brh_code] = 0; }

                $p_tot = $p_tot + $opndate_purchase_chicks[$bcode] + $opndate_transin_chicks[$bcode];
                $m_tot = $m_tot + $opndate_dentry_chicks[$bcode];
                $s_tot = $s_tot + $opndate_sale_birdno[$bcode] + $opndate_transout_chicks[$bcode];
                $cm_tot = $cm_tot + $curdate_dentry_chicks[$bcode];
                $cs_tot = $cs_tot + $curdate_sale_birdno[$bcode];

                /*Opening Birds Calculations*/
                $opening_birds = 0; $opening_birds = (($opndate_purchase_chicks[$bcode] + $opndate_transin_chicks[$bcode]) - ($opndate_dentry_chicks[$bcode] + $opndate_sale_birdno[$bcode] + $opndate_transout_chicks[$bcode]));
                $branch_opening_birds[$brh_code] = $branch_opening_birds[$brh_code] + $opening_birds;
                $total_opn_birds = $total_opn_birds + $opening_birds;

                $total_in_chicks = 0;
                $total_in_chicks = ($opndate_purchase_chicks[$bcode] + $opndate_transin_chicks[$bcode]);
                $total_opn_mort = $total_opn_mort + $opndate_dentry_chicks[$bcode];
                /*Current Mortality and Culls*/
                $cur_mortality = 0; $cur_mortality = $curdate_dentry_chicks[$bcode];
                $branch_curmort_birds[$brh_code] = $branch_curmort_birds[$brh_code] + $cur_mortality;
                $total_cur_mort = $total_cur_mort + $cur_mortality;

                /*Live Farm Counts*/
                //if($_SERVER['REMOTE_ADDR'] == "49.207.201.30" || $_SERVER['REMOTE_ADDR'] == "42.105.124.193"){ echo "<br/>".$batch_name[$bcode]."@".$total_in_chicks."@".$total_farms; }
                if($total_in_chicks != 0){
                    $total_farms++;
                }
                /*Current Lifting Details*/
                $cur_sale_birdno = 0; $cur_sale_birdno = $curdate_sale_birdno[$bcode];
                $branch_cur_birdno[$brh_code] = $branch_cur_birdno[$brh_code] + $cur_sale_birdno;
                $total_cur_birdno = $total_cur_birdno + $cur_sale_birdno;

                $cur_sale_birdwt = 0; $cur_sale_birdwt = $curdate_sale_birdwt[$bcode];
                $branch_cur_birdwt[$brh_code] = $branch_cur_birdwt[$brh_code] + $cur_sale_birdwt;
                $total_cur_birdwt = $total_cur_birdwt + $cur_sale_birdwt;

                /*Day -1 Lifting Details*/
                $yest_sale_birdno = 0; $yest_sale_birdno = $yesterday_sale_birdno[$bcode];
                $branch_yest_birdno[$brh_code] = $branch_yest_birdno[$brh_code] + $yest_sale_birdno;
                $total_yest_birdno = $total_yest_birdno + $yest_sale_birdno;

                $yest_sale_birdwt = 0; $yest_sale_birdwt = $yesterday_sale_birdwt[$bcode];
                $branch_yest_birdwt[$brh_code] = $branch_yest_birdwt[$brh_code] + $yest_sale_birdwt;
                $total_yest_birdwt = $total_yest_birdwt + $yest_sale_birdwt;

                /*Closing Birds Calculations*/
                $closing_birds = 0; $closing_birds = ($opening_birds - ($cur_mortality + $cur_sale_birdno));
                $branch_closing_birds[$brh_code] = $branch_closing_birds[$brh_code] + $closing_birds;
                $total_cls_birds = $total_cls_birds + $closing_birds;

                /*if($ip_addr == "49.205.130.10"){
                    if($batch_name[$bcode] == "NW-NAW001-2"){
                        echo "<br/>(($opndate_purchase_chicks[$bcode] + $opndate_transin_chicks[$bcode]) - ($opndate_dentry_chicks[$bcode] + $opndate_sale_birdno[$bcode] + $opndate_transout_chicks[$bcode]))";
                        echo "<br/>".$bcode."@".$batch_name[$bcode]."@<-----($opening_birds - ($cur_mortality + $cur_sale_birdno))----->".$closing_birds."@".$total_cls_birds;
                    }
                }*/

                /*Current Total Avg Mortality*/
               if(empty($dentry_batches[$bcode])){ }
                else{
                    /*Present Day Daily entry Openings*/
                    $total_popn_birds = $total_popn_birds + $opening_birds;

                    $i++;
                    if($cur_mortality == 0 || $cur_mortality == ""){ $cur_mortality = 0; $t_mort = 0; }
                    else{
                        if((float)$opening_birds != 0){
                            $t_mort = round((((float)$cur_mortality / (float)$opening_birds) * 100),3);
                        }
                        else{
                            $t_mort = 0;
                        }
                        
                    }
                    
                    if((float)$t_mort >= 0 && (float)$t_mort <= 0.25){
                        $tm_farm1 = $tm_farm1 + $cur_mortality;
                        $tm_per1 .= "-".$t_mort;
                        $farm1++;
                    }
                    else if((float)$t_mort > 0.25 && (float)$t_mort <= 0.50){
                        $tm_farm2 = $tm_farm2 + $cur_mortality;
                        $tm_per2 .= "-".$t_mort;
                        $farm2++;
                    }
                    else if((float)$t_mort > 0.50 && (float)$t_mort <= 1){
                        $tm_farm3 = $tm_farm3 + $cur_mortality;
                        $tm_per3 .= "-".$t_mort;
                        $farm3++;
                    }
                    else if((float)$t_mort > 1){
                        $tm_farm4 = $tm_farm4 + $cur_mortality;
                        $tm_per4 .= "-".$t_mort."(".$cur_mortality."-".$opening_birds.")&";
                        //echo "<br/>".$bcode;
                        $farm4++;
                    }
                    else{ }
                }
                
                if(empty($brood_age[$bcode]) && empty($opndate_purchase_chicks[$bcode]) && empty($opndate_transin_chicks[$bcode]) && empty($curdate_purchase_chicks[$bcode]) && empty($curdate_transin_chicks[$bcode])){ }
                else if(empty($brood_age[$bcode]) || $brood_age[$bcode] == "" || $brood_age[$bcode] >= 0 &&  $brood_age[$bcode] <= 7){
                    $age_7 = $age_7 + $closing_birds;
                    $age_farm7++;
                }
                else if($brood_age[$bcode] >= 8 &&  $brood_age[$bcode] <= 14){
                    $age_14 = $age_14 + $closing_birds;
                    $age_farm14++;
                }
                else if($brood_age[$bcode] >= 15 &&  $brood_age[$bcode] <= 21){
                    $age_21 = $age_21 + $closing_birds;
                    $age_farm21++;
                }
                else if($brood_age[$bcode] >= 22 &&  $brood_age[$bcode] <= 28){
                    $age_28 = $age_28 + $closing_birds;
                    $age_farm28++;
                }
                else if($brood_age[$bcode] >= 29 &&  $brood_age[$bcode] <= 35){
                    $age_35 = $age_35 + $closing_birds;
                    $age_farm35++;
                }
                else if($brood_age[$bcode] >= 36 &&  $brood_age[$bcode] <= 42){
                    $age_42 = $age_42 + $closing_birds;
                    $age_farm42++;
                }
                else if($brood_age[$bcode] >= 43 &&  $brood_age[$bcode] <= 49){
                    $age_49 = $age_49 + $closing_birds;
                    $age_farm49++;
                }
                else if($brood_age[$bcode] >= 50 &&  $brood_age[$bcode] <= 56){
                    $age_56 = $age_56 + $closing_birds;
                    $age_farm56++;
                }
                else{
                    $age_oth = $age_oth + $closing_birds;
                    $age_farmoth++;
                }

                /*Week Wise Mortality Details*/
                if($brood_curage[$bcode] >= 1 &&  $brood_curage[$bcode] <= 7){ $farm1_mort++; $week1_mort += (float)$cur_mortality; $week1_opnb += (float)$opening_birds; }
                else if($brood_curage[$bcode] >= 8 &&  $brood_curage[$bcode] <= 14){ $farm2_mort++; $week2_mort += (float)$cur_mortality; $week2_opnb += (float)$opening_birds; }
                else if($brood_curage[$bcode] >= 15 &&  $brood_curage[$bcode] <= 21){ $farm3_mort++; $week3_mort += (float)$cur_mortality; $week3_opnb += (float)$opening_birds; }
                else if($brood_curage[$bcode] >= 22 &&  $brood_curage[$bcode] <= 28){ $farm4_mort++; $week4_mort += (float)$cur_mortality; $week4_opnb += (float)$opening_birds; }
                else if($brood_curage[$bcode] >= 29 &&  $brood_curage[$bcode] <= 35){ $farm5_mort++; $week5_mort += (float)$cur_mortality; $week5_opnb += (float)$opening_birds; }
                else if($brood_curage[$bcode] >= 36 &&  $brood_curage[$bcode] <= 42){ $farm6_mort++; $week6_mort += (float)$cur_mortality; $week6_opnb += (float)$opening_birds; }
                else if($brood_curage[$bcode] >= 43 &&  $brood_curage[$bcode] <= 49){ $farm7_mort++; $week7_mort += (float)$cur_mortality; $week7_opnb += (float)$opening_birds; }
                else if($brood_curage[$bcode] > 49){ $farm8_mort++; $week8_mort += (float)$cur_mortality; $week8_opnb += (float)$opening_birds; }
                
                //if($_SERVER['REMOTE_ADDR'] == "49.205.133.247"){ echo "<br/>".$batch_name[$bcode]."@".$brood_age[$bcode]."@".$closing_birds; }
                if(!empty($brood_age[$bcode]) && $brood_age[$bcode] > 42){
                    $age_grt42 = $age_grt42 + $closing_birds;
                    //if($_SERVER['REMOTE_ADDR'] == "49.207.201.30"){ echo "<br/>".$batch_name[$bcode]."@".$brood_age[$bcode]."@".$age_grt42; }
                    $age_farmgrt42++;
                }
                /*Above 40 Days Calculations*/
                if(!empty($brood_age[$bcode]) && $brood_age[$bcode] > 40){
                    /*Opening*/
                    $abv_40_opn = $abv_40_opn + $opening_birds;
                    $abv_40_mrt = $abv_40_mrt + $cur_mortality;
                    $abv_40_sle = $abv_40_sle + $cur_sale_birdno;
                    $abv_40_cls = $abv_40_cls + $closing_birds;
                }
            }
            //echo "<br/>".$p_tot."@".$m_tot."@".$s_tot."@".$cm_tot."@".$cs_tot;
            $opening_bird_details = $mortality_bird_details = $lifting_bird_details = $yesterday_lifting_bird_details = $closing_bird_details = $total_mortality_bird_details = $agewise_available_birds = 0;
            $sql1 = "SELECT * FROM `master_dashboard_links` WHERE `field_name` LIKE 'Broiler' AND `user_code` = '$user_code' ORDER BY `sort_order` ASC";
            $query1 = mysqli_query($conn,$sql1); $dcount1 = mysqli_num_rows($query1);
            if($dcount1 > 0){
                $dboard_flag = 1;
                while($row1 = mysqli_fetch_assoc($query1)){
                    if($row1['panel_name'] == "Opening Birds-List"){ $opening_bird_details = $row1['active']; }
                    if($row1['panel_name'] == "Mortality Birds-Doughnut"){ $mortality_bird_details = $row1['active']; }
                    if($row1['panel_name'] == "Lifting Birds-Bar Chart"){ $lifting_bird_details = $row1['active']; }
                    if($row1['panel_name'] == "Previous Day Lifting Birds-Bar Chart"){ $yesterday_lifting_bird_details = $row1['active']; }
                    if($row1['panel_name'] == "Closing Birds-List"){ $closing_bird_details = $row1['active']; }
                    if($row1['panel_name'] == "Total Mortality Per-List"){ $total_mortality_bird_details = $row1['active']; }
                    if($row1['panel_name'] == "Customer Supplier Balance-List"){ $Customer_Supplier_Balance_details = $row1['active']; }
                    if($row1['panel_name'] == "Age Wise Available Birds-Bar Chart"){ $agewise_available_birds = $row1['active']; }
                    if($row1['panel_name'] == "Live Farms-List"){ $live_farm_details = $row1['active']; }
                    if($row1['panel_name'] == "Week Wise Mortality-List"){ $week_wise_mort_details = $row1['active']; }
                    if($row1['panel_name'] == "Cash/Bank Balance"){ $cash_or_bank_balance_details = $row1['active']; }
                    if($row1['panel_name'] == "Date wise Sale Details"){ $date_wise_sale_details = $row1['active']; }
                    if($row1['panel_name'] == "Date wise Broiler Birds Stock Details"){ $date_wise_broilerbird_stock_details = $row1['active']; }
                    $sorts[$row1['sort_order']] = $row1['panel_name'];
                }
                ksort($sorts);
            }
            else{
                $dboard_flag = 0;
            }
            if($display_dashboard_flag == "1" || $display_dashboard_flag == 1){
                if($dboard_flag == "1" || $dboard_flag == 1){
                ?>
                    <div class="row">
                    <?php foreach($sorts as $sorting){ ?>

                    <?php if($sorting == "Opening Birds-List" && $opening_bird_details == "1" || $sorting == "Opening Birds-List" && $opening_bird_details == 1){ ?>
                        <div class="col-lg-3 col-6">
                            <!-- Main content -->
                            <section class="content">
                                <div class="container-fluid">
                                    <div class="row">
                                    <div class="col-md-12">
                                        
                                        <!-- DONUT CHART -->
                                        <div class="card card-danger">
                                        <div class="card-body bg-light" id="openings1">
                                            
                                            <table class="w-100">
                                                <tr style="text-align:center;">
                                                    <th colspan="3" style="text-align:center;"><label for="">Opening Birds</label></th>
                                                </tr>
                                            
                                            <?php
                                            $i = 0;
                                            foreach($branch_code as $bch_code){
                                                if(!empty($branch_list[$bch_code])){
                                            ?>
                                            <tr style="width:100%;border-bottom:none;">
                                                <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn bg-".$colors[$i]."' title='btn bg-".$colors[$i]."' style='width:30px;height:10px;border-radius:none;'></button> ".$branch_name[$bch_code]; ?></h6></th>
                                                <th style="text-align:left;"><h6>:</th>
                                                <td style="text-align:right;">
                                                    <h6>
                                                        <a href="javascript:void(0)" id="/records/broiler_liveflocksummary_masterreport.php?submit_report=true&branches=<?php echo $bch_code; ?>&lines=all&supervisors=all&farms=all&manual_nxtfeed=3&export=display" onclick="broiler_openurl(this.id);">
                                                            <?php echo str_replace(".00","",number_format_ind($branch_opening_birds[$bch_code])); ?>
                                                        </a>
                                                    </h6>
                                                </td>
                                            </tr>
                                            <?php
                                                    $i++;
                                                }
                                            }
                                            ?>
                                            <tr style="width:100%;border-top: 0.1vh dashed black;">
                                                <td colspan="1" style="text-align:left;"><h6>Total:</h6></td>
                                                <td colspan="2" style="text-align:right;"><h6><a href="javascript:void(0)" id="/records/broiler_liveflocksummary_masterreport.php?submit=true" onclick="broiler_openurl(this.id);"><b><?php echo str_replace(".00","",number_format_ind($total_opn_birds)); ?></b></a></h6></td>
                                            </tr>
                                            </table>

                                            <!--<div style="width: 60%; height: 50px; position: absolute; top: 60%; left: 5%; margin-top: -20px; line-height:19px; text-align: center; z-index: 999999999999999"><b style="font-size:18px;"><?php //echo "Opening Total<br/>".str_replace(".00","",number_format_ind($total_opn_birds)); ?></b></div>
                                            <canvas id="dbdopnbirds" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>-->
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    <?php } ?>
                    <?php if($sorting == "Live Farms-List" && $live_farm_details == "1" || $sorting == "Live Farms-List" && $live_farm_details == 1){ ?>
                        <div class="col-lg-3 col-6">
                            <!-- Main content -->
                            <section class="content">
                                <div class="container-fluid">
                                    <div class="row">
                                    <div class="col-md-12">
                                        
                                        <!-- DONUT CHART -->
                                        <div class="card card-danger">
                                        <div class="card-body bg-light" id="openings1">
                                            
                                            <table class="w-100">
                                                <tr style="text-align:center;">
                                                    <th colspan="3" style="text-align:center;"><label for="">Live Farms Count</label></th>
                                                </tr>
                                                <tr style="width:100%;border-bottom:none;">
                                                    <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn bg-".$colors[0]."' style='width:30px;height:10px;border-radius:none;'></button> Live Farms"; ?></h6></th>
                                                    <th style="text-align:left;"><h6>:</th>
                                                    <td style="text-align:right;"><h6><a href="javascript:void(0)" id="/records/broiler_liveflocksummary_masterreport.php?submit=true" onclick="broiler_openurl(this.id);"><?php echo str_replace(".00","",number_format_ind($total_farms)); ?></a></h6></td>
                                                </tr>
                                                <tr style="width:100%;border-bottom:none;">
                                                    <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn bg-".$colors[1]."' style='width:30px;height:10px;border-radius:none;'></button> Visited Farms"; ?></h6></th>
                                                    <th style="text-align:left;"><h6>:</th>
                                                    <td style="text-align:right;"><h6><a href="javascript:void(0)" id="/records/broiler_dayrecord_masterreport.php?submit=true" onclick="broiler_openurl(this.id);"><?php echo str_replace(".00","",number_format_ind($live_farms)); ?></a></h6></td>
                                                </tr>
                                                <tr style="width:100%;border-bottom:none;">
                                                    <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn bg-".$colors[2]."' style='width:30px;height:10px;border-radius:none;'></button> Not-Visited Farms"; ?></h6></th>
                                                    <th style="text-align:left;"><h6>:</th>
                                                    <td style="text-align:right;"><h6><a href="javascript:void(0)" id="/records/broiler_dailyentry_gapdays.php?submit=true" onclick="broiler_openurl(this.id);"><?php echo str_replace(".00","",number_format_ind($total_farms - $live_farms)); ?></a></h6></td>
                                                </tr>
                                            </table>

                                            <!--<div style="width: 60%; height: 50px; position: absolute; top: 60%; left: 5%; margin-top: -20px; line-height:19px; text-align: center; z-index: 999999999999999"><b style="font-size:18px;"><?php //echo "Opening Total<br/>".str_replace(".00","",number_format_ind($total_opn_birds)); ?></b></div>
                                            <canvas id="dbdopnbirds" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>-->
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    <?php } ?>
                    <?php if($sorting == "Mortality Birds-Doughnut" && $mortality_bird_details == "1" || $sorting == "Mortality Birds-Doughnut" && $mortality_bird_details == 1){ ?>
                        <div class="col-lg-4 col-6">
                            <!-- Main content -->
                            <section class="content">
                                <div class="container-fluid">
                                    <div class="row">
                                    <div class="col-md-12">
                                        
                                        <!-- DONUT CHART -->
                                        <div class="card card-danger">
                                        <div class="card-body bg-light" align="center">
                                            <div class="w-100" style="width:100%;text-align:center;background-color:powderblue;"><h6 class="card-title"><b>Mortality and Culls</b></h6></div>
                                            <?php
                                                if($total_cur_mort > 0 && $total_popn_birds > 0){
                                                    $ft_mcount = $total_cur_mort / $total_popn_birds;
                                                }
                                                else{
                                                    $ft_mcount = 0;
                                                }
                                            ?>
                                            <div style="width: 65%; height: 50px; position: absolute; top: 55%; left: 0; margin-top: -20px; line-height:19px; text-align: center; z-index: 999999999999999"><b style="font-size:13px;"><?php echo str_replace(".00","",number_format_ind($total_cur_mort))."<br/>(".number_format_ind(round((($ft_mcount) * 100),2))."%)"; ?></b></div>
                                            <canvas id="donutChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    <?php } ?>
                    <?php if($sorting == "Lifting Birds-Bar Chart" && $lifting_bird_details == "1" || $sorting == "Lifting Birds-Bar Chart" && $lifting_bird_details == 1){ ?>
                        <div class="col-lg-5 col-6">
                            <!-- Main content -->
                            <section class="content">
                                <div class="container-fluid">
                                <div class="card card-danger">
                                            <div class="p-0 card-body bg-light" id="openings1">
                                                <div class="card card-success">
                                                    <div class="card-body">
                                                        <h6><b>Lifting Details</b></h6>
                                                        <?php
                                                            if($total_cur_birdwt > 0 && $total_cur_birdno > 0){
                                                                $ft_ldcount = $total_cur_birdwt / $total_cur_birdno;
                                                            }
                                                            else{
                                                                $ft_ldcount = 0;
                                                            }
                                                        ?>
                                                        <h6 style='color:green;'><?php echo "Total: Birds: <b>".str_replace(".00","",number_format_ind($total_cur_birdno))."</b> Weight: <b>".number_format_ind($total_cur_birdwt)."</b> Avg Wt: <b>".number_format_ind(round(($ft_ldcount),2))."</b>"; ?></h6>
                                                        <div class="chart">
                                                        <canvas id="barChart" style="min-height: 200px; height: 200px; max-height: 200px; max-width: 100%;"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </section>
                        </div>
                    <?php } ?>
                    <?php if($sorting == "Previous Day Lifting Birds-Bar Chart" && $yesterday_lifting_bird_details == "1" || $sorting == "Previous Day Lifting Birds-Bar Chart" && $yesterday_lifting_bird_details == 1){ ?>
                        <div class="col-lg-5 col-6">
                            <!-- Main content -->
                            <section class="content">
                                <div class="container-fluid">
                                <div class="card card-danger">
                                            <div class="p-0 card-body bg-light" id="openings1">
                                                <div class="card card-success">
                                                    <div class="card-body">
                                                        <h6><b>Lifting Details</b></h6>
                                                        <?php
                                                            if($total_yest_birdwt > 0 && $total_yest_birdno > 0){
                                                                $ft_ld2count = $total_yest_birdwt / $total_yest_birdno;
                                                            }
                                                            else{
                                                                $ft_ld2count = 0;
                                                            }
                                                        ?>
                                                        <h6 style='color:green;'><?php echo "Total: Birds: <b>".str_replace(".00","",number_format_ind($total_yest_birdno))."</b> Weight: <b>".number_format_ind($total_yest_birdwt)."</b> Avg Wt: <b>".number_format_ind(round(($ft_ld2count),2))."</b>"; ?></h6>
                                                        <div class="chart">
                                                        <canvas id="barChart3" style="min-height: 200px; height: 200px; max-height: 200px; max-width: 100%;"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </section>
                        </div>
                    <?php } ?>
                    <?php if($sorting == "Closing Birds-List" && $closing_bird_details == "1" || $sorting == "Closing Birds-List" && $closing_bird_details == 1){ ?>
                        <div class="col-lg-3 col-6">
                            <!-- Main content -->
                            <section class="content">
                                <div class="container-fluid">
                                    <div class="row">
                                    <div class="col-md-12">
                                        
                                        <!-- DONUT CHART -->
                                        <div class="card card-danger">
                                        <div class="card-body bg-light" id="openings1">
                                            
                                            <table class="w-100">
                                                <tr style="text-align:center;">
                                                    <th colspan="3" style="text-align:center;"><label for="">Closing Birds</label></th>
                                                </tr>
                                            
                                            <?php
                                            $i = 0;
                                            foreach($branch_code as $bch_code){
                                                if(!empty($branch_list[$bch_code])){
                                            ?>
                                            <tr style="width:100%;border-bottom:none;">
                                                <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn bg-".$colors[$i]."' style='width:30px;height:10px;border-radius:none;'></button> ".$branch_name[$bch_code]; ?></h6></th>
                                                <th style="text-align:left;"><h6>:</th>
                                                <td style="text-align:right;">
                                                    <h6>
                                                        <a href="javascript:void(0)" id="/records/broiler_liveflocksummary_masterreport.php?submit_report=true&branches=<?php echo $bch_code; ?>&lines=all&supervisors=all&farms=all&manual_nxtfeed=3&export=display" onclick="broiler_openurl(this.id);">
                                                            <?php echo str_replace(".00","",number_format_ind($branch_closing_birds[$bch_code])); ?>
                                                        </a>
                                                    </h6>
                                                </td>
                                            </tr>
                                            <?php
                                                    $i++;
                                                }
                                            }
                                            ?>
                                            <tr style="width:100%;border-top: 0.1vh dashed black;">
                                                <td colspan="1" style="text-align:left;"><h6>Total:</h6></td>
                                                <td colspan="2" style="text-align:right;"><h6><a href="javascript:void(0)" id="/records/broiler_liveflocksummary_masterreport.php?submit=true" onclick="broiler_openurl(this.id);"><b><?php echo str_replace(".00","",number_format_ind($total_cls_birds)); ?></b></a></h6></td>
                                            </tr>
                                            </table>
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    <?php } ?>
                    <?php if($sorting == "Total Mortality Per-List" && $total_mortality_bird_details == "1" || $sorting == "Total Mortality Per-List" && $total_mortality_bird_details == 1){ ?>
                        <div class="col-lg-4 col-6">
                            <!-- Main content -->
                            <section class="content">
                                <div class="container-fluid">
                                    <div class="row">
                                    <div class="col-md-12">
                                        
                                        <!-- DONUT CHART -->
                                        <div class="card card-danger">
                                        <div class="card-body bg-light" id="openings1">


                                            <table class="w-100">
                                                <tr style="text-align:center;">
                                                    <th colspan="3" style="text-align:center;"><label for="">Total Mortality + Culls</label></th>
                                                </tr>
                                                <tr style="width:100%;border-bottom:none;">
                                                    <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn bg-".$colors[0]."' style='width:30px;height:10px;border-radius:none;'></button> 0.00 - 0.25%"; ?></h6></th>
                                                    <th style="text-align:left;"><h6>:</th>
                                                    <td style="text-align:left;"><h6><?php echo str_replace(".00","",number_format_ind($tm_farm1)); ?></h6></td>
                                                    <td style="text-align:right;" title="<?php echo $tm_per1; ?>"><h6><?php echo str_replace(".00","",number_format_ind(round($farm1,2)))." Farms"; ?></h6></td>
                                                </tr>
                                                <tr style="width:100%;border-bottom:none;">
                                                    <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn bg-".$colors[1]."' style='width:30px;height:10px;border-radius:none;'></button> 0.25 - 0.50%"; ?></h6></th>
                                                    <th style="text-align:left;"><h6>:</th>
                                                    <th style="text-align:left;"><h6><?php echo str_replace(".00","",number_format_ind($tm_farm2)); ?></th>
                                                    <td style="text-align:right;" title="<?php echo $tm_per2; ?>"><h6><?php echo str_replace(".00","",number_format_ind(round($farm2,2)))." Farms"; ?></h6></td>
                                                </tr>
                                                <tr style="width:100%;border-bottom:none;">
                                                    <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn bg-".$colors[2]."' style='width:30px;height:10px;border-radius:none;'></button> 0.50 - 1.00%"; ?></h6></th>
                                                    <th style="text-align:left;"><h6>:</th>
                                                    <td style="text-align:left;"><h6><?php echo str_replace(".00","",number_format_ind($tm_farm3)); ?></h6></td>
                                                    <td style="text-align:right;" title="<?php echo $tm_per3; ?>"><h6><?php echo str_replace(".00","",number_format_ind(round($farm3,2)))." Farms"; ?></h6></td>
                                                </tr>
                                                <tr style="width:100%;border-bottom:none;">
                                                    <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn bg-".$colors[3]."' style='width:30px;height:10px;border-radius:none;'></button> > 1.00%"; ?></h6></th>
                                                    <th style="text-align:left;"><h6>:</th>
                                                    <td style="text-align:left;"><h6><?php echo str_replace(".00","",number_format_ind($tm_farm4)); ?></h6></td>
                                                    <td style="text-align:right;" title="<?php echo $tm_per4; ?>"><h6><?php echo str_replace(".00","",number_format_ind(round($farm4,2)))." Farms"; ?></h6></td>
                                                </tr>
                                            
                                                <tr style="width:100%;border-top: 0.1vh dashed black;">
                                                    <td colspan="1" style="text-align:left;"><h6>Total:</h6></td>
                                                    <td colspan="2" style="text-align:left;"><h6><b><?php echo str_replace(".00","",number_format_ind($total_cur_mort)); ?></b></h6></td>
                                                    <td colspan="2" style="text-align:right;"><h6><b><?php echo str_replace(".00","",number_format_ind(round(($farm1 + $farm2 + $farm3 + $farm4))))." Farms"; ?></b></h6></td>
                                                </tr>
                                            </table>
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    <?php } ?>
                    <?php
                        if($sorting == "Customer Supplier Balance-List" && $Customer_Supplier_Balance_details == "1" || $sorting == "Customer Supplier Balance-List" && $Customer_Supplier_Balance_details == 1){
                            $today = date('Y-m-d');
                            $cus_sales = $cus_receipts = $cus_returns = $cus_ccn = $cus_cdn = $cus_contra_cr = $cus_contra_dr = $cus_obcramt = $cus_obdramt = $sup_obcramt = $sup_obdramt = $today_rct = 0;
                            $old_inv = $cus_list = $sup_list = $cus_filter = $sup_filter = "";
                            $cus_code = $sup_code = $cus_obtype = $cus_obamt = $sup_obtype = $sup_obamt = array();
                            if($count98 > 0){
                                $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE 'C' AND `active` ='1' AND `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
                                while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; if($row['obtype'] == "Cr" || $row['obtype'] == "CR"){ $cus_obcramt += (float)$row['obamt']; } else{ $cus_obdramt += (float)$row['obamt']; } }
                                $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE 'S' AND `active` ='1' AND `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
                                while($row = mysqli_fetch_assoc($query)){ $sup_code[$row['code']] = $row['code']; if($row['obtype'] == "Cr" || $row['obtype'] == "CR"){ $sup_obcramt += (float)$row['obamt']; } else{ $sup_obdramt += (float)$row['obamt']; } }
                                $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE 'S&C' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
                                while($row = mysqli_fetch_assoc($query)){
                                    $cus_code[$row['code']] = $row['code'];
                                    $sup_code[$row['code']] = $row['code'];
                                    if($row['obtype'] == "Cr" || $row['obtype'] == "CR"){ $cus_obcramt += (float)$row['obamt']; } else{ $cus_obdramt += (float)$row['obamt']; }
                                }
                                $cus_list = implode("','",$cus_code);
                                $sup_list = implode("','",$sup_code);
                            }
                            if($count65 > 0){
                                $sql_record = "SELECT * FROM `broiler_sales` WHERE `date` <= '$today' AND `vcode` IN ('$cus_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                                $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ if($old_inv != $row['trnum']){ $cus_sales += (float)$row['finl_amt']; $old_inv = $row['trnum']; } } }
                            }
                            if($count63 > 0){
                                $sql_record = "SELECT SUM(amount) as amount FROM `broiler_receipts` WHERE `date` <= '$today' AND `ccode` IN ('$cus_list') AND `vtype` IN ('Customer') AND `active` = '1' AND `dflag` = '0'";
                                $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $cus_receipts += (float)$row['amount']; } }
                                
                                $sql_record = "SELECT SUM(amount) as amount FROM `broiler_receipts` WHERE `date` = '$fdate' AND `ccode` IN ('$cus_list') AND `vtype` IN ('Customer') AND `active` = '1' AND `dflag` = '0'";
                                $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $today_rct += (float)$row['amount']; } }
                            }
                            if($count54 > 0){
                                $sql_record = "SELECT SUM(amount) as amount FROM `broiler_itemreturns` WHERE `date` <= '$today' AND `vcode` IN ('$cus_list') AND `type` IN ('Customer') AND `active` = '1' AND `dflag` = '0'";
                                $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $cus_returns += (float)$row['amount']; } }
                            }
                            if($count17 > 0){
                                $sql_record = "SELECT SUM(amount) as amount,crdr FROM `broiler_crdrnote` WHERE `date` <= '$today' AND `vcode` IN ('$cus_list') AND `type` IN ('Customer') AND `active` = '1' AND `dflag` = '0' GROUP BY `crdr` ORDER BY `crdr` ASC";
                                $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ if($row['crdr'] == "Credit"){ $cus_ccn += (float)$row['amount']; } else{ $cus_cdn += (float)$row['amount']; } } }
                            }
                            if($count7 > 0){
                                $sql_record = "SELECT SUM(amount) as amount FROM `account_contranotes` WHERE `date` <= '$today' AND `fcoa` IN ('$cus_list') AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0'";
                                $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $cus_contra_cr += (float)$row['amount']; } }
                                
                                $sql_record = "SELECT SUM(amount) as amount FROM `account_contranotes` WHERE `date` <= '$today' AND `tcoa` IN ('$cus_list') AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0'";
                                $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $cus_contra_dr += (float)$row['amount']; } }
                            }
                            $tot_cus_bal = (($cus_sales + $cus_cdn + $cus_contra_dr + $cus_obdramt) - ($cus_receipts + $cus_returns + $cus_ccn + $cus_contra_cr + $cus_obcramt));
                            
                            $sup_ccn = $sup_cdn = $sup_returns = $sup_payments = $sup_purchases = $sup_contra_cr = $sup_contra_dr = $today_pay = 0;
                            if($count61 > 0){
                                $sql_record = "SELECT * FROM `broiler_purchases` WHERE `date` <= '$today' AND `vcode` IN ('$sup_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                                $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); } $old_inv = "";
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ if($old_inv != $row['trnum']){ $sup_purchases += (float)$row['finl_amt']; $old_inv = $row['trnum']; } } }
                            }
                            if($count59 > 0){
                                $sql_record = "SELECT SUM(amount) as amount FROM `broiler_payments` WHERE `date` <= '$today' AND `ccode` IN ('$sup_list') AND `vtype` IN ('Supplier') AND `active` = '1' AND `dflag` = '0'";
                                $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $sup_payments += (float)$row['amount']; } }
                                $sql_record = "SELECT SUM(amount) as amount FROM `broiler_payments` WHERE `date` = '$fdate' AND `ccode` IN ('$sup_list') AND `vtype` IN ('Supplier') AND `active` = '1' AND `dflag` = '0'";
                                $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $today_pay += (float)$row['amount']; } }
                            }
                            if($count54 > 0){
                                $sql_record = "SELECT SUM(amount) as amount FROM `broiler_itemreturns` WHERE `date` <= '$today' AND `vcode` IN ('$sup_list') AND `type` IN ('Supplier') AND `active` = '1' AND `dflag` = '0'";
                                $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $sup_returns += (float)$row['amount']; } }
                            }
                            if($count17 > 0){
                                $sql_record = "SELECT SUM(amount) as amount,crdr FROM `broiler_crdrnote` WHERE `date` <= '$today' AND `vcode` IN ('$sup_list') AND `type` IN ('Supplier') AND `active` = '1' AND `dflag` = '0' GROUP BY `crdr` ORDER BY `crdr` ASC";
                                $query = mysqli_query($conn,$sql_record); $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ if($row['crdr'] == "Credit"){ $sup_ccn += (float)$row['amount']; } else{ $sup_cdn += (float)$row['amount']; } } }
                            }
                            if($count7 > 0){
                                $sql_record = "SELECT SUM(amount) as amount FROM `account_contranotes` WHERE `date` <= '$today' AND `fcoa` IN ('$sup_list') AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0'";
                                $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $sup_contra_cr += (float)$row['amount']; } }
                                
                                $sql_record = "SELECT SUM(amount) as amount FROM `account_contranotes` WHERE `date` <= '$today' AND `tcoa` IN ('$sup_list') AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0'";
                                $query = mysqli_query($conn,$sql_record); $i = 0; $transaction_count = 0; if(!empty($query)){ $transaction_count = mysqli_num_rows($query); }
                                if($transaction_count > 0){ while($row = mysqli_fetch_assoc($query)){ $sup_contra_dr += (float)$row['amount']; } }
                            }
                            $tot_sup_bal = (($sup_purchases + $sup_ccn + $sup_obdramt + $sup_contra_cr) - ($sup_payments + $sup_cdn + $sup_obcramt + $sup_returns + $sup_contra_dr));
                            $sup_title = "$tot_sup_bal = (($sup_purchases + $sup_ccn + $sup_obdramt + $sup_contra_cr) - ($sup_payments + $sup_cdn + $sup_obcramt + $sup_returns + $sup_contra_dr))";
                    ?>
                        <div class="col-lg-4 col-6">
                            <!-- Main content -->
                            <section class="content">
                                <div class="container-fluid">
                                    <div class="row">
                                    <div class="col-md-12">
                                        
                                        <!-- DONUT CHART -->
                                        <div class="card card-danger">
                                        <div class="card-body bg-light" id="openings1">


                                            <table class="w-100">
                                                <tr style="text-align:center;">
                                                    <th colspan="2" style="text-align:center;"><label for="">Customer & Supplier Balances</label></th>
                                                </tr>
                                                <tr style="width:100%;border-bottom:none;">
                                                    <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn bg-".$colors[0]."' style='width:30px;height:10px;border-radius:none;'></button>Total Receivables"; ?></h6></th>
                                                    <th style="text-align:left;"><h6>:</th>
                                                    <td style="text-align:left;"><h6><?php echo number_format_ind($tot_cus_bal); ?></h6></td>
                                                </tr>
                                                <tr style="width:100%;border-bottom:none;">
                                                    <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn bg-".$colors[1]."' style='width:30px;height:10px;border-radius:none;'></button>Total Receipts"; ?></h6></th>
                                                    <th style="text-align:left;"><h6>:</th>
                                                    <td style="text-align:left;"><h6><?php echo number_format_ind($today_rct); ?></h6></td>
                                                </tr>
                                                <tr style="width:100%;border-bottom:none;">
                                                    <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn bg-".$colors[2]."' style='width:30px;height:10px;border-radius:none;'></button>Total Payables"; ?></h6></th>
                                                    <th style="text-align:left;"><h6>:</th>
                                                    <td style="text-align:left;"><h6 title="<?php echo $sup_title; ?>"><?php echo number_format_ind($tot_sup_bal); ?></h6></td>
                                                </tr>
                                                <tr style="width:100%;border-bottom:none;">
                                                    <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn bg-".$colors[3]."' style='width:30px;height:10px;border-radius:none;'></button>Total Payments"; ?></h6></th>
                                                    <th style="text-align:left;"><h6>:</th>
                                                    <td style="text-align:left;"><h6><?php echo number_format_ind($today_pay); ?></h6></td>
                                                </tr>
                                            </table>
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    <?php } ?>
                    <?php if($sorting == "Week Wise Mortality-List" && $week_wise_mort_details == "1" || $sorting == "Week Wise Mortality-List" && $week_wise_mort_details == 1){ ?>
                        <div class="col-lg-4 col-6">
                            <!-- Main content -->
                            <section class="content">
                                <div class="container-fluid">
                                    <div class="row">
                                    <div class="col-md-12">
                                        
                                        <!-- DONUT CHART -->
                                        <div class="card card-danger">
                                        <div class="card-body bg-light" id="openings1">


                                            <table class="w-100">
                                                <tr style="text-align:center;">
                                                    <th colspan="3" style="text-align:center;"><label for="">Week Wise Mortality</label></th>
                                                </tr>
                                                <tr style="width:100%;border-bottom:none;">
                                                    <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn bg-".$colors[0]."' style='width:30px;height:10px;border-radius:none;'></button> 1st week"; ?></h6></th>
                                                    <th style="text-align:left;"><h6>:</th>
                                                    <td style="text-align:left;"><h6><?php echo str_replace(".00","",number_format_ind($week1_mort)); if($week1_opnb != 0){ $t1 = 0; $t1 = round((($week1_mort / $week1_opnb) * 100),2); echo " (<b>".$t1."%</b>)"; } else{ echo " ( 0.00 )";} ?></h6></td>
                                                    <td style="text-align:right;" title="<?php echo $week1_mort; ?>"><h6><?php echo str_replace(".00","",number_format_ind(round($farm1_mort,2)))." Farms"; ?></h6></td>
                                                </tr>
                                                <tr style="width:100%;border-bottom:none;">
                                                    <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn bg-".$colors[1]."' style='width:30px;height:10px;border-radius:none;'></button> 2nd week"; ?></h6></th>
                                                    <th style="text-align:left;"><h6>:</th>
                                                    <th style="text-align:left;"><h6><?php echo str_replace(".00","",number_format_ind($week2_mort)); if($week2_opnb != 0){ $t1 = 0; $t1 = round((($week2_mort / $week2_opnb) * 100),2); echo " (<b>".$t1."%</b>)"; } else{ echo " ( 0.00 )";} ?></th>
                                                    <td style="text-align:right;" title="<?php echo $week2_mort; ?>"><h6><?php echo str_replace(".00","",number_format_ind(round($farm2_mort,2)))." Farms"; ?></h6></td>
                                                </tr>
                                                <tr style="width:100%;border-bottom:none;">
                                                    <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn bg-".$colors[2]."' style='width:30px;height:10px;border-radius:none;'></button> 3rd week"; ?></h6></th>
                                                    <th style="text-align:left;"><h6>:</th>
                                                    <td style="text-align:left;"><h6><?php echo str_replace(".00","",number_format_ind($week3_mort)); if($week3_opnb != 0){ $t1 = 0; $t1 = round((($week3_mort / $week3_opnb) * 100),2); echo " (<b>".$t1."%</b>)"; } else{ echo " ( 0.00 )";} ?></h6></td>
                                                    <td style="text-align:right;" title="<?php echo $week3_mort; ?>"><h6><?php echo str_replace(".00","",number_format_ind(round($farm3_mort,2)))." Farms"; ?></h6></td>
                                                </tr>
                                                <tr style="width:100%;border-bottom:none;">
                                                    <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn bg-".$colors[3]."' style='width:30px;height:10px;border-radius:none;'></button> 4th week"; ?></h6></th>
                                                    <th style="text-align:left;"><h6>:</th>
                                                    <td style="text-align:left;"><h6><?php echo str_replace(".00","",number_format_ind($week4_mort)); if($week4_opnb != 0){ $t1 = 0; $t1 = round((($week4_mort / $week4_opnb) * 100),2); echo " (<b>".$t1."%</b>)"; } else{ echo " ( 0.00 )";} ?></h6></td>
                                                    <td style="text-align:right;" title="<?php echo $week4_mort; ?>"><h6><?php echo str_replace(".00","",number_format_ind(round($farm4_mort,2)))." Farms"; ?></h6></td>
                                                </tr>
                                                <tr style="width:100%;border-bottom:none;">
                                                    <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn bg-".$colors[4]."' style='width:30px;height:10px;border-radius:none;'></button> 5th week"; ?></h6></th>
                                                    <th style="text-align:left;"><h6>:</th>
                                                    <td style="text-align:left;"><h6><?php echo str_replace(".00","",number_format_ind($week5_mort)); if($week5_opnb != 0){ $t1 = 0; $t1 = round((($week5_mort / $week5_opnb) * 100),2); echo " (<b>".$t1."%</b>)"; } else{ echo " ( 0.00 )";} ?></h6></td>
                                                    <td style="text-align:right;" title="<?php echo $week5_mort; ?>"><h6><?php echo str_replace(".00","",number_format_ind(round($farm5_mort,2)))." Farms"; ?></h6></td>
                                                </tr>
                                                <tr style="width:100%;border-bottom:none;">
                                                    <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn bg-".$colors[5]."' style='width:30px;height:10px;border-radius:none;'></button> 6th week"; ?></h6></th>
                                                    <th style="text-align:left;"><h6>:</th>
                                                    <td style="text-align:left;"><h6><?php echo str_replace(".00","",number_format_ind($week6_mort)); if($week6_opnb != 0){ $t1 = 0; $t1 = round((($week6_mort / $week6_opnb) * 100),2); echo " (<b>".$t1."%</b>)"; } else{ echo " ( 0.00 )";} ?></h6></td>
                                                    <td style="text-align:right;" title="<?php echo $week6_mort; ?>"><h6><?php echo str_replace(".00","",number_format_ind(round($farm6_mort,2)))." Farms"; ?></h6></td>
                                                </tr>
                                                <tr style="width:100%;border-bottom:none;">
                                                    <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn bg-".$colors[6]."' style='width:30px;height:10px;border-radius:none;'></button> 7th week"; ?></h6></th>
                                                    <th style="text-align:left;"><h6>:</th>
                                                    <td style="text-align:left;"><h6><?php echo str_replace(".00","",number_format_ind($week7_mort)); if($week7_opnb != 0){ $t1 = 0; $t1 = round((($week7_mort / $week7_opnb) * 100),2); echo " (<b>".$t1."%</b>)"; } else{ echo " ( 0.00 )";} ?></h6></td>
                                                    <td style="text-align:right;" title="<?php echo $week7_mort; ?>"><h6><?php echo str_replace(".00","",number_format_ind(round($farm7_mort,2)))." Farms"; ?></h6></td>
                                                </tr>
                                                <tr style="width:100%;border-bottom:none;">
                                                    <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn bg-".$colors[7]."' style='width:30px;height:10px;border-radius:none;'></button> > 7th week"; ?></h6></th>
                                                    <th style="text-align:left;"><h6>:</th>
                                                    <td style="text-align:left;"><h6><?php echo str_replace(".00","",number_format_ind($week8_mort)); if($week8_opnb != 0){ $t1 = 0; $t1 = round((($week8_mort / $week8_opnb) * 100),2); echo " (<b>".$t1."%</b>)"; } else{ echo " ( 0.00 )";} ?></h6></td>
                                                    <td style="text-align:right;" title="<?php echo $week8_mort; ?>"><h6><?php echo str_replace(".00","",number_format_ind(round($farm8_mort,2)))." Farms"; ?></h6></td>
                                                </tr>
                                            
                                                <tr style="width:100%;border-top: 0.1vh dashed black;">
                                                    <td colspan="1" style="text-align:left;"><h6>Total:</h6></td>
                                                    <td colspan="2" style="text-align:left;"><h6><b><?php echo str_replace(".00","",number_format_ind($total_cur_mort));  echo " (<b>".round((($ft_mcount) * 100),2)."%</b>)";?></b></h6></td>
                                                    <td colspan="2" style="text-align:right;"><h6><b><?php echo str_replace(".00","",number_format_ind(round(($farm1 + $farm2 + $farm3 + $farm4))))." Farms"; ?></b></h6></td>
                                                </tr>
                                            </table>
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    <?php } ?>
                    <?php if($sorting == "Cash/Bank Balance" && $cash_or_bank_balance_details == "1" || $sorting == "Cash/Bank Balance" && $cash_or_bank_balance_details == 1){ ?>
                        <div class="col-lg-4 col-6">
                            <section class="content">
                                <div class="container-fluid">
                                    <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-danger">
                                        <div class="card-body bg-light" id="openings1">
                                            <table class="w-100">
                                                <tr style="text-align:center;">
                                                    <th colspan="3" style="text-align:center;"><label for="">Cash/Bank</label></th>
                                                </tr>
                                                <tr style="text-align:center;">
                                                    <th colspan="1" style="text-align:center;"><label for="">name</label></th>
                                                    <th colspan="1" style="text-align:center;"></th>
                                                    <th colspan="1" style="text-align:center;"><label for="">Balance</label></th>
                                                </tr>
                                                <?php
                                                $cb_incr = $crb_tbal_amt = 0;
                                                foreach($crb_code as $crbs){
                                                    $cr_amt = $dr_amt = 0;
                                                    $cr_amt = round($crb_cr_amt[$crbs],5);
                                                    $dr_amt = round($crb_dr_amt[$crbs],5);
                                                    $bl_amt = (float)$dr_amt - (float)$cr_amt;
                                                    if(number_format_ind($bl_amt) != "0.00"){
                                                        $cb_incr++;
                                                        $crb_tbal_amt += (float)$bl_amt;
                                                ?>
                                                <tr style="width:100%;border-bottom:none;">
                                                    <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn bg-".$colors[$cb_incr]."' style='width:30px;height:10px;border-radius:none;'></button> ".$crb_name[$crbs]; ?></h6></th>
                                                    <th style="text-align:left;"><h6>:</th>
                                                    <td style="text-align:right;"><h6><?php echo number_format_ind(round($bl_amt,2)); ?></h6></td>
                                                </tr>
                                                <?php } } ?>
                                                <tr style="width:100%;border-top: 0.1vh dashed black;">
                                                    <td style="text-align:left;"><h6>Total:</h6></td>
                                                    <th style="text-align:left;"><h6>:</th>
                                                    <td style="text-align:right;"><h6><?php echo number_format_ind(round($crb_tbal_amt,2)); ?></h6></td>
                                                </tr>
                                            </table>
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    <?php } ?>
                    <?php if($sorting == "Date wise Sale Details" && $date_wise_sale_details == "1" || $sorting == "Date wise Sale Details" && $date_wise_sale_details == 1){ ?>
                        <div class="col-lg-8 col-8">
                            <section class="content">
                                <div class="container-fluid">
                                    <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-danger">
                                        <div class="card-body bg-light" id="openings1">
                                            <table class="w-100">
                                                <tr style="text-align:center;">
                                                    <th colspan="1" style="text-align:center;"></th>
                                                    <th colspan="2" style="text-align:center;"><label for="">Purchse Details</label></th>
                                                    <th colspan="2" style="text-align:center;"><label for="">Sale Details</label></th>
                                                    <th colspan="2" style="text-align:center;"><label for="">Loss Details</label></th>
                                                </tr>
                                                <tr style="text-align:center;">
                                                    <th colspan="1" style="text-align:center;"><label for="">Item</label></th>
                                                    <th colspan="1" style="text-align:right;"><label for="">Birds</label></th>
                                                    <th colspan="1" style="text-align:right;">Weight</th>
                                                    <!--<th colspan="1" style="text-align:right;"><label for="">Amount</label></th>-->
                                                    <th colspan="1" style="text-align:right;"><label for="">Birds</label></th>
                                                    <th colspan="1" style="text-align:right;">Weight</th>
                                                    <!--<th colspan="1" style="text-align:right;"><label for="">Amount</label></th>-->
                                                    <th colspan="1" style="text-align:right;"><label for="">Birds</label></th>
                                                    <th colspan="1" style="text-align:right;">Weight</th>
                                                    <!--<th colspan="1" style="text-align:right;"><label for="">Amount</label></th>-->
                                                </tr>
                                                <?php
                                                $cb_incr = $dws_tbirds = $dws_tweight = $dws_tamount = 0;
                                                foreach($birds_code as $sbds){
                                                    $rpbirds = $rpweight = $rpamount = $rpprice = $rsbirds = $rsweight = $rsamount = $rlbirds = $rlweight = $rlamount = 0;
                                                    $rpbirds = round($iw_pur_birds[$sbds],5);
                                                    $rpweight = round($iw_pur_weight[$sbds],5);
                                                    $rpamount = round($iw_pur_amount[$sbds],5);
                                                    if((float)$rpweight != 0){ $rpprice = (float)$rpamount / (float)$rpweight; } else{ $rpprice = 0; }
                                                    $rsbirds = round($iw_sale_birds[$sbds],5);
                                                    $rsweight = round($iw_sale_weight[$sbds],5);
                                                    $rsamount = round($iw_sale_amount[$sbds],5);
                                                    
                                                    $rlbirds = (float)$rpbirds - (float)$rsbirds;
                                                    $rlweight = (float)$rpweight - (float)$rsweight;
                                                    $rlamount = round(((float)$rlweight * (float)$rpprice),2);

                                                    if(number_format_ind($rpweight) != "0.00" || number_format_ind($rsweight) != "0.00"){
                                                        $cb_incr++;
                                                        $dwp_tbirds += (float)$rpbirds;
                                                        $dwp_tweight += (float)$rpweight;
                                                        $dwp_tamount += (float)$rpamount;
                                                        $dws_tbirds += (float)$rsbirds;
                                                        $dws_tweight += (float)$rsweight;
                                                        $dws_tamount += (float)$rsamount;
                                                        $dwl_tbirds += (float)$rlbirds;
                                                        $dwl_tweight += (float)$rlweight;
                                                        $dwl_tamount += (float)$rlamount;
                                                ?>
                                                <tr style="width:100%;border-bottom:none;">
                                                    <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn bg-".$colors[$cb_incr]."' style='width:30px;height:10px;border-radius:none;'></button> ".$birds_name[$sbds]; ?></h6></th>
                                                    <td style="text-align:right;"><h6><?php echo str_replace(".00","",number_format_ind(round($rpbirds,2))); ?></h6></td>
                                                    <td style="text-align:right;"><h6><?php echo number_format_ind(round($rpweight,2)); ?></h6></td>
                                                    <!--<td style="text-align:right;"><h6><?php //echo number_format_ind(round($rpamount,2)); ?></h6></td>-->
                                                    <td style="text-align:right;"><h6><?php echo str_replace(".00","",number_format_ind(round($rsbirds,2))); ?></h6></td>
                                                    <td style="text-align:right;"><h6><?php echo number_format_ind(round($rsweight,2)); ?></h6></td>
                                                    <!--<td style="text-align:right;"><h6><?php //echo number_format_ind(round($rsamount,2)); ?></h6></td>-->
                                                    <td style="text-align:right;"><h6><?php echo str_replace(".00","",number_format_ind(round($rlbirds,2))); ?></h6></td>
                                                    <td style="text-align:right;"><h6><?php echo number_format_ind(round($rlweight,2)); ?></h6></td>
                                                    <!--<td style="text-align:right;"><h6><?php //echo number_format_ind(round($rlamount,2)); ?></h6></td>-->
                                                </tr>
                                                <?php } } ?>
                                                <tr style="width:100%;border-top: 0.1vh dashed black;">
                                                    <td style="text-align:left;"><h6>Total:</h6></td>
                                                    <td style="text-align:right;"><h6><?php echo str_replace(".00","",number_format_ind(round($dwp_tbirds,2))); ?></h6></td>
                                                    <td style="text-align:right;"><h6><?php echo number_format_ind(round($dwp_tweight,2)); ?></h6></td>
                                                    <!--<td style="text-align:right;"><h6><?php //echo number_format_ind(round($dwp_tamount,2)); ?></h6></td>-->
                                                    <td style="text-align:right;"><h6><?php echo str_replace(".00","",number_format_ind(round($dws_tbirds,2))); ?></h6></td>
                                                    <td style="text-align:right;"><h6><?php echo number_format_ind(round($dws_tweight,2)); ?></h6></td>
                                                    <!--<td style="text-align:right;"><h6><?php //echo number_format_ind(round($dws_tamount,2)); ?></h6></td>-->
                                                    <td style="text-align:right;"><h6><?php echo str_replace(".00","",number_format_ind(round($dwl_tbirds,2))); ?></h6></td>
                                                    <td style="text-align:right;"><h6><?php echo number_format_ind(round($dwl_tweight,2)); ?></h6></td>
                                                    <!--<td style="text-align:right;"><h6><?php //echo number_format_ind(round($dwl_tamount,2)); ?></h6></td>-->
                                                </tr>
                                            </table>
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    <?php } ?>
                    <?php if($sorting == "Date wise Broiler Birds Stock Details" && $date_wise_broilerbird_stock_details == "1" || $sorting == "Date wise Broiler Birds Stock Details" && $date_wise_broilerbird_stock_details == 1){ ?>
                        <div class="col-lg-4 col-6">
                            <section class="content">
                                <div class="container-fluid">
                                    <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-danger">
                                        <div class="card-body bg-light" id="openings1">
                                            <table class="w-100">
                                                <tr style="text-align:center;">
                                                    <th colspan="3" style="text-align:center;"><label for="">Broiler Birds</label></th>
                                                </tr>
                                                <tr style="text-align:center;">
                                                    <th colspan="1" style="text-align:center;"><label for="">Transaction</label></th>
                                                    <th colspan="1" style="text-align:right;"><label for="">Birds</label></th>
                                                    <th colspan="1" style="text-align:right;">Weight</th>
                                                </tr>
                                                <?php
                                                $loss_broiler_bird_nos =  $loss_broiler_bird_qty = 0;
                                                if(number_format_ind($sale_broiler_bird_qty) != "0.00" || number_format_ind($pur_broiler_bird_qty) != "0.00"){
                                                    $loss_broiler_bird_nos = (float)$pur_broiler_bird_nos - (float)$sale_broiler_bird_nos;
                                                    $loss_broiler_bird_qty = (float)$pur_broiler_bird_qty - (float)$sale_broiler_bird_qty;
                                                ?>
                                                <tr style="width:100%;border-bottom:none;">
                                                    <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn bg-".$colors[0]."' style='width:30px;height:10px;border-radius:none;'></button> Purchases"; ?></h6></th>
                                                    <td style="text-align:right;"><h6><?php echo str_replace(".00","",number_format_ind(round($pur_broiler_bird_nos,2))); ?></h6></td>
                                                    <td style="text-align:right;"><h6><?php echo number_format_ind(round($pur_broiler_bird_qty,2)); ?></h6></td>
                                                </tr>
                                                <tr style="width:100%;border-bottom:none;">
                                                    <th style="text-align:left;"><h6><?php echo "<button type='button' class='btn bg-".$colors[1]."' style='width:30px;height:10px;border-radius:none;'></button> Sales"; ?></h6></th>
                                                    <td style="text-align:right;"><h6><?php echo str_replace(".00","",number_format_ind(round($sale_broiler_bird_nos,2))); ?></h6></td>
                                                    <td style="text-align:right;"><h6><?php echo number_format_ind(round($sale_broiler_bird_qty,2)); ?></h6></td>
                                                </tr>
                                                <?php } ?>
                                                <tr style="width:100%;border-top: 0.1vh dashed black;">
                                                    <td style="text-align:left;"><h6>Loss:</h6></td>
                                                    <td style="text-align:right;"><h6><?php echo str_replace(".00","",number_format_ind(round($loss_broiler_bird_nos,2))); ?></h6></td>
                                                    <td style="text-align:right;"><h6><?php echo number_format_ind(round($loss_broiler_bird_qty,2)); ?></h6></td>
                                                </tr>
                                            </table>
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    <?php } ?>
                    <?php if($sorting == "Age Wise Available Birds-Bar Chart" && $agewise_available_birds == "1" || $sorting == "Age Wise Available Birds-Bar Chart" && $agewise_available_birds == 1){ ?>
                        <div class="col-lg-5 col-6">
                            <!-- Main content -->
                            <section class="content">
                                <div class="container-fluid">
                                <div class="card card-danger">
                                            <div class="p-0 card-body bg-light" id="openings1">
                                                <div class="card card-success">
                                                    <div class="card-body">
                                                        <h6>Age wise available Birds: <b><?php echo str_replace(".00","",number_format_ind($total_cls_birds)); ?></b></h6>
                                                        <div class="chart">
                                                        <canvas id="barChart2" style="min-height: 200px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </section>
                        </div>
                    <?php } } ?>
                    </div>
                <?php
                }
            }
            ?>
        
        </div>
      </div>
    </section>
    <!-- Datepicker -->
    <script src="datepicker/jquery/jquery.js"></script>
    <script src="datepicker/jquery-ui.js"></script>
    <?php include "header_foot_dashboard.php"; ?>
    <?php //foreach($branch_code as $bch_code){ if(!empty($branch_list[$bch_code])){ } } ?>
    
    <script>
            function fetch_farms_details(a){
                var branches = document.getElementById("branches").value;
                var lines = document.getElementById("lines").value;
                var supervisors = document.getElementById("supervisors").value;

                if(a.match("branches")){
                    if(!branches.match("all")){
                        //Update Line Details
                        removeAllOptions(document.getElementById("lines"));
                        myselect1 = document.getElementById("lines");
                        theOption1=document.createElement("OPTION");
                        theText1=document.createTextNode("-All-");
                        theOption1.value = "all"; 
                        theOption1.appendChild(theText1); 
                        myselect1.appendChild(theOption1);
                        <?php
                            foreach($line_code as $fcode){
                                $b_code = $line_branch[$fcode];
                                echo "if(branches == '$b_code'){";
                        ?>
                            theOption1=document.createElement("OPTION");
                            theText1=document.createTextNode("<?php echo $line_name[$fcode]; ?>");
                            theOption1.value = "<?php echo $line_code[$fcode]; ?>";
                            theOption1.appendChild(theText1); myselect1.appendChild(theOption1);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                $f_code = $farm_svr[$fcode]; $b_code = $farm_branch[$f_code];
                                echo "if(branches == '$b_code' && '$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $fcode; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                                $b_code = $farm_branch[$fcode];
                                echo "if(branches == '$b_code'){";
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            echo "}";
                            }
                        ?>
                    }
                    else{
                        //Update Line Details
                        removeAllOptions(document.getElementById("lines"));
                        myselect1 = document.getElementById("lines");
                        theOption1=document.createElement("OPTION");
                        theText1=document.createTextNode("-All-");
                        theOption1.value = "all"; 
                        theOption1.appendChild(theText1); 
                        myselect1.appendChild(theOption1);
                        <?php
                            foreach($line_code as $fcode){
                        ?>
                            theOption1=document.createElement("OPTION");
                            theText1=document.createTextNode("<?php echo $line_name[$fcode]; ?>");
                            theOption1.value = "<?php echo $line_code[$fcode]; ?>";
                            theOption1.appendChild(theText1); myselect1.appendChild(theOption1);
                        <?php
                            }
                        ?>
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                $f_code = $farm_svr[$fcode];
                                echo "if('$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            }
                        ?>
                    }
                }
                else if(a.match("lines")){
                    if(!lines.match("all")){
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                $f_code = $farm_svr[$fcode]; $l_code = $farm_line[$f_code];
                                echo "if(lines == '$l_code' && '$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                                $l_code = $farm_line[$fcode];
                                echo "if(lines == '$l_code'){";
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            echo "}";
                            }
                        ?>
                    }
                    else if(!branches.match("all")){
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                $f_code = $farm_svr[$fcode]; $b_code = $farm_branch[$f_code];
                                echo "if(branches == '$b_code' && '$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                                $b_code = $farm_branch[$fcode];
                                echo "if(branches == '$b_code'){";
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            echo "}";
                            }
                        ?>
                    }
                    else{
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                $f_code = $farm_svr[$fcode];
                                echo "if('$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            }
                        ?>
                    }
                }
                else if(a.match("supervisors")){
                    if(!supervisors.match("all")){
                        if(!lines.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $l_code = $farm_line[$fcode]; $s_code = $farm_supervisor[$fcode];
                                    echo "if(lines == '$l_code' && supervisors == '$s_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else if(!branches.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $b_code = $farm_branch[$fcode]; $s_code = $farm_supervisor[$fcode];
                                    echo "if(branches == '$b_code' && supervisors == '$s_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else{
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $s_code = $farm_supervisor[$fcode];
                                    echo "if(supervisors == '$s_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                    }
                    else{
                        if(!lines.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $l_code = $farm_line[$fcode];
                                    echo "if(lines == '$l_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else if(!branches.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $b_code = $farm_branch[$fcode];
                                    echo "if(branches == '$b_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else{
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                }
                            ?>
                        }
                    }
                }
                else{ }
            }
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <script>
            
            $(function (){
                var name1 = name2 = name3 = name4 = label_names = ""; var opn_value = names = names2 = value = []; var i = 0;
                <?php
                $key_names = $key_val = $opn_names = $opnings = "";
                foreach($branch_code as $bch_code){
                    if(!empty($branch_list[$bch_code])){
                        $opnings = str_replace(".00","",number_format_ind($branch_opening_birds[$bch_code]));
                        if($opn_names == ""){
                            $opn_names = $branch_name[$bch_code]."-".$opnings;
                        }
                        else{
                            $opn_names = $opn_names."@".$branch_name[$bch_code]."-".$opnings;
                        }
                        if($key_names == ""){
                            $key_names = $branch_name[$bch_code];
                        }
                        else{
                            $key_names = $key_names."@".$branch_name[$bch_code];
                        }
                        
                    }
                }
                ?>
                <?php
                $key_val = $opn_birds = $opnings = $sale_bridno = $bno = $bwt = $sale_birdwt = $sale_bridno2 = $bno2 = $bwt2 = $sale_birdwt2 = "";
                foreach($branch_code as $bch_code){
                    if(!empty($branch_list[$bch_code])){
                        $mcount = str_replace(".00","",$branch_curmort_birds[$bch_code]);
                        if($key_val == ""){
                            $key_val = $mcount;
                        }
                        else{
                            $key_val = $key_val."@".$mcount;
                        }
                        $opnings = str_replace(".00","",$branch_opening_birds[$bch_code]);
                        if($opn_birds == ""){
                            $opn_birds = $opnings;
                        }
                        else{
                            $opn_birds = $opn_birds."@".$opnings;
                        }
                        $bno = str_replace(".00","",$branch_cur_birdno[$bch_code]);
                        if($sale_bridno == ""){
                            $sale_bridno = $bno;
                        }
                        else{
                            $sale_bridno = $sale_bridno."@".$bno;
                        }
                        $bwt = str_replace(".00","",$branch_cur_birdwt[$bch_code]);
                        if($sale_birdwt == ""){
                            $sale_birdwt = $bwt;
                        }
                        else{
                            $sale_birdwt = $sale_birdwt."@".$bwt;
                        }
                        /*Day -1 Lifting Details*/
                        $bno2 = str_replace(".00","",$branch_yest_birdno[$bch_code]);
                        if($sale_bridno2 == ""){
                            $sale_bridno2 = $bno2;
                        }
                        else{
                            $sale_bridno2 = $sale_bridno2."@".$bno2;
                        }
                        $bwt2 = str_replace(".00","",$branch_yest_birdwt[$bch_code]);
                        if($sale_birdwt2 == ""){
                            $sale_birdwt2 = $bwt2;
                        }
                        else{
                            $sale_birdwt2 = $sale_birdwt2."@".$bwt2;
                        }
                    }
                }
                ?>
                name1 = '<?php echo $key_names; ?>'; names = name1.split("@");
                name1 = '<?php echo $key_val; ?>'; value = name1.split("@");
                var donutChartCanvas = $('#donutChart').get(0).getContext('2d');
    
                var donutData = {
                    labels: names,
                    datasets: [
                        {
                            data: value,
                            backgroundColor : ['cyan', 'blue', 'yellow', 'red', 'lime', 'green', 'orange','purple', 'brown', 'coral', 'silver', 'maroon', 'skyblue', 'gray', 'pink', 'lavender'],
                            borderWidth: 1
                        }
                    ]
                }
                var donutOptions = {
                    layout: {
                        padding: { left: 0, right: 0, top: 0, bottom: 0, }
                    },
                    responsive : true,
                    cutoutPercentage: 70,
                    legend: { position: 'right' },
                    title: { display: false, text: 'Mortality Details' },
                }
                /*let doughnutlabel = {
                    id: 'doughnutlabel',
                    beforeDatasetsDraw(Chart, args, pluginOptions){
                        const { ctx, data } = chart;
                        ctx.save();
                        const xCoor = chart.getDatasetMeta(0).data[0].x;
                        const yCoor = chart.getDatasetMeta(0).data[0].y;
                        ctx.font = 'bold 30px sans-serif';
                        ctx.fillStyle = 'rgba(54, 162, 235, 1)';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText('Text', xCoor, yCoor);
                    }
                }*/
                new Chart(donutChartCanvas, {
                    type: 'doughnut',
                    data: donutData,
                    options: donutOptions,
                    //plugins: [doughnutlabel],
                });


                <?php if($lifting_bird_details == "1" || $lifting_bird_details == 1){ ?>
                var birdno = birdwt = [];
                name3 = '<?php echo $sale_bridno; ?>'; birdno = name3.split("@");
                name3 = '<?php echo $sale_birdwt; ?>'; birdwt = name3.split("@");

                var areaChartData = {
                    labels  : names,
                    datasets: [
                        {
                        label               : 'Weight',
                        backgroundColor     : 'rgba(60,141,188,0.9)',
                        borderColor         : 'rgba(60,141,188,0.8)',
                        pointRadius         : false,
                        pointStrokeColor    : '#c1c7d1',
                        pointStrokeColor    : 'rgba(60,141,188,1)',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(60,141,188,1)',
                        maxBarThickness: 45,
                        data                : birdwt
                        },
                        {
                        label               : 'Birds',
                        backgroundColor     : 'rgba(210, 214, 222, 1)',
                        borderColor         : 'rgba(210, 214, 222, 1)',
                        pointRadius          : false,
                        pointColor          : '#3b8bba',
                        pointColor          : 'rgba(210, 214, 222, 1)',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        maxBarThickness: 45,
                        data                : birdno
                        },
                    ]
                }

                var barChartCanvas = $('#barChart').get(0).getContext('2d')
                var barChartData = $.extend(true, {}, areaChartData)
                var temp0 = areaChartData.datasets[0]
                var temp1 = areaChartData.datasets[1]
                barChartData.datasets[0] = temp1
                barChartData.datasets[1] = temp0

                var barChartOptions = {
                    responsive              : true,
                    maintainAspectRatio     : false,
                    datasetFill             : false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    },
                }

                new Chart(barChartCanvas, {
                type: 'bar',
                data: barChartData,
                options: barChartOptions
                });
                <?php } ?>
               
                var age_name = age_birds = [];
                name4 = '<?php echo "[0 - 7,".$age_farm7."][@8 - 14,".$age_farm14."]@[15 - 21,".$age_farm21."]@[22 - 28,".$age_farm28."]@[29 - 35,".$age_farm35."]@[36 - 42,".$age_farm42."]@[42+Days,".$age_farmgrt42."]"; ?>'; age_name = name4.split("@");
                <?php
                $abird7 = str_replace(".00","",number_format_ind($age_7));
                $abird14 = str_replace(".00","",number_format_ind($age_14));
                $abird21 = str_replace(".00","",number_format_ind($age_21));
                $abird28 = str_replace(".00","",number_format_ind($age_28));
                $abird35 = str_replace(".00","",number_format_ind($age_35));
                $abird42 = str_replace(".00","",number_format_ind($age_42));
                $abirdgrt42 = str_replace(".00","",number_format_ind($age_grt42));
                ?>
                age_birds[0] = '<?php echo str_replace(".00","",$age_7); ?>';
                age_birds[1] = '<?php echo str_replace(".00","",$age_14); ?>';
                age_birds[2] = '<?php echo str_replace(".00","",$age_21); ?>';
                age_birds[3] = '<?php echo str_replace(".00","",$age_28); ?>';
                age_birds[4] = '<?php echo str_replace(".00","",$age_35); ?>';
                age_birds[5] = '<?php echo str_replace(".00","",$age_42); ?>';
                age_birds[6] = '<?php echo str_replace(".00","",$age_grt42); ?>';
                var areaChartData2 = {
                    labels  : [['0 - 7','<?php echo $age_farm7." Farms"; ?>','<?php echo $abird7; ?>'],['8 - 14','<?php echo $age_farm14." Farms"; ?>','<?php echo $abird14; ?>'],['15 - 21','<?php echo $age_farm21." Farms"; ?>','<?php echo $abird21; ?>'],['22 - 28','<?php echo $age_farm28." Farms"; ?>','<?php echo $abird28; ?>'],['29 - 35','<?php echo $age_farm35." Farms"; ?>','<?php echo $abird35; ?>'],['36 - 42','<?php echo $age_farm42." Farms"; ?>','<?php echo $abird42; ?>'],['42+Days','<?php echo $age_farmgrt42." Farms"; ?>','<?php echo $abirdgrt42; ?>']],
                    datasets: [
                        {
                        label               : 'Birds',
                        backgroundColor     : 'rgb(0,139,139)',
                        borderColor         : 'rgba(60,141,188,0.8)',
                        pointRadius         : false,
                        pointStrokeColor    : '#c1c7d1',
                        pointStrokeColor    : 'rgba(60,141,188,1)',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(60,141,188,1)',
                        maxBarThickness: 45,
                        data                : age_birds
                        },
                    ]
                }

                var agewiseavail_birds = $('#barChart2').get(0).getContext('2d')
                var agewavailbirdsData = $.extend(true, {}, areaChartData2)
                var temp0 = areaChartData2.datasets[0]
                //var temp1 = areaChartData2.datasets[1]
                //agewavailbirdsData.datasets[0] = temp1
                agewavailbirdsData.datasets[0] = temp0

                var agewavailbirdsOptions = {
                    responsive              : true,
                    maintainAspectRatio     : false,
                    datasetFill             : false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    },
                }

                new Chart(agewiseavail_birds, {
                type: 'bar',
                data: agewavailbirdsData,
                options: agewavailbirdsOptions
                });

                
                /*Day -1 Lifting Details*/
                var birdno2 = birdwt2 = [];
                var name5 = '<?php echo $sale_bridno2; ?>'; birdno2 = name5.split("@");
                var name6 = '<?php echo $sale_birdwt2; ?>'; birdwt2 = name6.split("@");

                var yesterdayLiftingDetails = {
                    labels  : names,
                    datasets: [
                        {
                        label               : 'Weight',
                        backgroundColor     : 'rgba(60,141,188,0.9)',
                        borderColor         : 'rgba(60,141,188,0.8)',
                        pointRadius         : false,
                        pointStrokeColor    : '#c1c7d1',
                        pointStrokeColor    : 'rgba(60,141,188,1)',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(60,141,188,1)',
                        maxBarThickness: 45,
                        data                : birdwt2
                        },
                        {
                        label               : 'Birds',
                        backgroundColor     : 'rgba(210, 214, 222, 1)',
                        borderColor         : 'rgba(210, 214, 222, 1)',
                        pointRadius          : false,
                        pointColor          : '#3b8bba',
                        pointColor          : 'rgba(210, 214, 222, 1)',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        maxBarThickness: 45,
                        data                : birdno2
                        },
                    ]
                }

                <?php if($yesterday_lifting_bird_details == "1" || $yesterday_lifting_bird_details == 1){ ?>
                var yestChartCanvas2 = $('#barChart3').get(0).getContext('2d')
                var yestChartData2 = $.extend(true, {}, yesterdayLiftingDetails)
                var temp02 = yesterdayLiftingDetails.datasets[0]
                var temp12 = yesterdayLiftingDetails.datasets[1]
                yestChartData2.datasets[0] = temp12
                yestChartData2.datasets[1] = temp02

                var yestChartOptions2 = {
                responsive              : true,
                maintainAspectRatio     : false,
                datasetFill             : false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    },
                }

                new Chart(yestChartCanvas2, {
                type: 'bar',
                data: yestChartData2,
                options: yestChartOptions2
                });
                <?php } ?>
               
                var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
                var pieData = donutData;
                var pieOptions = {
                    maintainAspectRatio : false,
                    responsive : true,
                }
                new Chart(pieChartCanvas, {
                    type: 'pie',
                    data: pieData,
                    options: pieOptions,
                    options: {
                        legend: {
                            position: 'right'
                        }
                    }
                });

            })
        </script>
        <script>
            function broiler_openurl(a){
                window.open(a, "_blank");
            }
        </script>
        
    </body>
</html>
<?php
}
?>