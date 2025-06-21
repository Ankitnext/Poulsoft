<?php
//broiler_daily_mortality.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!empty($_GET['db'])){ $db = $_SESSION['db'] = $_GET['db']; } else { $db = ''; }

if($db == ''){
    include "../newConfig.php";
    
    $sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
    if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
    include $num_format_file;

    include "header_head.php";
    $user_code = $_SESSION['userid'];
}
else{
    include "APIconfig.php";
    include "number_format_ind.php";
    include "header_head.php";
    $user_code = $_GET['userid'];
}

$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Arial";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Arial, sans-serif";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Helvetica";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Helvetica, Arial, sans-serif";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Verdana, sans-serif";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Tahoma, sans-serif";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Trebuchet MS";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Trebuchet MS', sans-serif";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Times New Roman'";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Times New Roman', serif";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Georgia, serif";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Garamond, serif";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Courier New', monospace";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Courier, monospace";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Optima";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Segoe";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Calibri";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Candara";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Lucida Grande";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Lucida Sans Unicode";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Gill Sans";
$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Source Sans Pro', 'Arial', sans-serif";

for($i = 0;$i <= 30;$i++){ $fsizes[$i."px"] = $i."px"; }

$i = 0;

$branch_access_code = $line_access_code = $farm_access_code = $sector_access_code = $branch_access_filter1 = $branch_access_filter2 = $line_access_filter1 = 
$line_access_filter2 = $farm_access_filter1 = "";
$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; }
else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; }
else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; }
else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }

$branch_code = $branch_name = array();
$sql = "SELECT * FROM `location_branch` WHERE `active` = '1' ".$branch_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }

$line_code = $line_name = array();
$sql = "SELECT * FROM `location_line` WHERE `active` = '1' ".$line_access_filter1."".$branch_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; }

$farm_code = $farm_ccode = $farm_name = $farm_branch = $farm_line = $farm_supervisor = $farm_farmer = array();
$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $farm_code[$row['code']] = $row['code']; $farm_ccode[$row['code']] = $row['farm_code']; $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code']; $farm_supervisor[$row['code']] = $row['supervisor_code']; $farm_farmer[$row['code']] = $row['farmer_code'];
}

$batch_code = $batch_name = $batch_book = $batch_gcflag = array();
$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_book[$row['code']] = $row['book_num']; $batch_gcflag[$row['code']] = $row['gc_flag']; }

$bstd_body_weight = $bstd_daily_gain = $bstd_avg_daily_gain = $bstd_fcr = $bstd_cum_feed = array();
$sql = "SELECT * FROM `broiler_breedstandard` WHERE `dflag` = '0' ORDER BY `age` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $bstd_body_weight[$row['age']] = $row['body_weight']; $bstd_daily_gain[$row['age']] = $row['daily_gain']; 
    $bstd_avg_daily_gain[$row['age']] = $row['avg_daily_gain']; $bstd_fcr[$row['age']] = $row['fcr']; $bstd_cum_feed[$row['age']] = $row['cum_feed']; }

$supervisor_code = $supervisor_name = array();
$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }

$dieases_name = array();
$sql = "SELECT * FROM `broiler_diseases`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $dieases_name[$row['trnum']] = $row['name']; }

$item_code = $item_name = array();
$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

$chick_code = "";
$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Chick%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; $chick_cats = $row['category']; }

$sql = "SELECT * FROM `item_details` WHERE `description` LIKE 'Broil%'"; $query = mysqli_query($conn,$sql); $birdchiccode = array();
while($row = mysqli_fetch_assoc($query)){
    $birdchiccode[$row['code']] = $row['code'];
}

$farmer_name = $farmer_mobile1 = $farmer_mobile2 = array();
$sql = "SELECT * FROM `broiler_farmer`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farmer_name[$row['code']] = $row['name']; $farmer_mobile1[$row['code']] = $row['mobile1']; $farmer_mobile2[$row['code']] = $row['mobile2']; }

$bird_code = "";
$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Bird%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; $bird_cats = $row['category']; }

$feed_code = array();
$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
while($row = mysqli_fetch_assoc($query)){ if( $item_cat = ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $feed_code[$row['code']] = $row['code']; }

$medvac_code = array();
$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%medicine%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
while($row = mysqli_fetch_assoc($query)){ if( $item_cat = ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%vaccine%'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ if( $item_cat = ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $medvac_code[$row['code']] = $row['code']; }

$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Daily Entry' AND `field_function` LIKE 'Bags' AND `flag` = 1"; 
$query = mysqli_query($conn,$sql); $bag_access_flag = mysqli_num_rows($query);

$bag_size = array();
$sql = "SELECT code,bag_size FROM `feed_bagcapacity` WHERE `code` LIKE '$itemcode' AND `active` = '1' AND `dflag` = '0'";  $query = mysqli_query($conn,$sql); 
while($row = mysqli_fetch_assoc($query)){ $bag_size[$row['code']] = $row['bag_size']; }

$mort_perce = 0.00;
$fdate = $tdate = date("Y-m-d"); $branches = $lines = $supervisors = $farms = "all"; $excel_type = "display";
$font_stype = ""; $font_size = "11px";
if(isset($_POST['submit_report']) == true){
    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    $supervisors = $_POST['supervisors'];
    $mort_perce = $_POST['mort_per'];
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));

    $export_Branch = $branch_name[$_POST['branches']]; 
    if ( $export_Branch == "" || $export_Branch == "all") { $export_Branch = "All"; }
    $export_Line = $line_name[$_POST['lines']];
    if ( $export_Line == "" || $export_Line == "all") { $export_Line = "All"; }
    $export_Supervisor = $supervisor_name[$_POST['supervisors']];
    if ( $export_Supervisor == "" || $export_Supervisor == "all") { $export_Supervisor = "All"; }
    $export_mort = $_POST['mort_per'];

    $font_stype = $_POST['font_stype'];
    $font_size = $_POST['font_size'];

    $farm_list = "";
     if($supervisors != "all"){
        foreach($farm_code as $fcode){
            if($farm_supervisor[$fcode] == $supervisors){
                if($farm_list == ""){
                    $farm_list = $fcode;
                }
                else{
                    $farm_list = $farm_list."','".$fcode;
                }
            }
        }
        $farm_query = " AND farm_code IN ('$farm_list')";
    }
    else if($lines != "all"){
        foreach($farm_code as $fcode){
            if($farm_line[$fcode] == $lines){
                if($farm_list == ""){
                    $farm_list = $fcode;
                }
                else{
                    $farm_list = $farm_list."','".$fcode;
                }
            }
        }
        $farm_query = " AND farm_code IN ('$farm_list')";
    }
    else if($branches != "all"){
        foreach($farm_code as $fcode){
            if($farm_branch[$fcode] == $branches){
                if($farm_list == ""){
                    $farm_list = $fcode;
                }
                else{
                    $farm_list = $farm_list."','".$fcode;
                }
            }
        }
        $farm_query = " AND farm_code IN ('$farm_list')";
    }
    else{
        foreach($farm_code as $fcode){
            if($farm_list == ""){
                $farm_list = $fcode;
            }
            else{
                $farm_list = $farm_list."','".$fcode;
            }
        }
        $farm_query = " AND farm_code IN ('$farm_list')";
    }
	$excel_type = $_POST['export'];
    $export_tdate = $_POST['tdate'];

    if ($export_tdate == "")
    { $filename = "Mortality Summary"; }
     else {
    $filename = "Mortality Summary_".$export_tdate; }
    

	//$url = "../PHPExcel/Examples/broiler_daily_mortality-Excel.php?todate=".$tdate."&branches=".$branches."&lines=".$lines."&supervisors=".$supervisors."&farm=".$farms."&mort_per=".$mort_perce;
}

?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
       
        <?php
           if($excel_type == "print"){
            include "headerstyle_wprint_font.php";  
        }
        else{
           
            include "headerstyle_woprint_font.php";   
        }
        ?>
    </head>
    <body align="center">
        <table class="tbl" align="center" <?php if($excel_type == "print"){ echo ' id="mine"'; } else{ echo 'width="1300px"'; } ?>>
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" style="width:1212px;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="3" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Mortality Record Report</h5></th>
                    <th colspan="12" align="center" style="border-left:none;"></th>
                </tr>
            </thead>
            <?php } ?>
            <?php if($db == ''){?>
            <form action="broiler_daily_mortality.php" method="post">
                <?php } else { ?>
                <form action="broiler_daily_mortality.php?db=<?php echo $db; ?>" method="post">
                <?php } ?>
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:1212px;">
                    <tr>
                        <th colspan="15">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div>
                               <div class="m-2 form-group">
                                    <label>Branch</label>
                                    <select name="branches" id="branches" class="form-control select2">
                                        <option value="all" <?php if($branches == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($branch_code as $bcode){ if(!empty($branch_name[$bcode])){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($branches == $bcode){ echo "selected"; } ?>><?php echo $branch_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Line</label>
                                    <select name="lines" id="lines" class="form-control select2">
                                        <option value="all" <?php if($lines == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($line_code as $lcode){ if(!empty($line_name[$lcode])){ ?>
                                        <option value="<?php echo $lcode; ?>" <?php if($lines == $lcode){ echo "selected"; } ?>><?php echo $line_name[$lcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div> 
                                <div class="m-2 form-group">
                                    <label>Supervisor</label>
                                    <select name="supervisors" id="supervisors" class="form-control select2">
                                        <option value="all" <?php if($supervisors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($supervisor_code as $scode){ if($supervisor_name[$scode] != ""){ ?>
                                        <option value="<?php echo $scode; ?>" <?php if($supervisors == $scode){ echo "selected"; } ?>><?php echo $supervisor_name[$scode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Mort Percent</label>
                                    <input type="text" name="mort_per" id="mort_per" placeholder="Enter Mort per" value="<?php echo $mort_perce; ?>" class="form-control"  />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Font Style</label>
                                    <select name="font_stype" id="font_stype" class="form-control select2"> <!-- onchange="update_font_family()"-->
                                        <option value="" <?php if($font_stype == ""){ echo "selected"; } ?>>-Defalut-</option>
                                        <?php
                                        foreach($font_family_code as $i){
                                        ?>
                                        <option value="<?php echo $font_family_name[$i]; ?>" <?php if($font_stype == $font_family_name[$i]){ echo "selected"; } ?>><?php echo $font_family_name[$i]; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Font Size</label>
                                    <select name="font_size" id="font_size" class="form-control select2">
                                        <?php
                                        foreach($fsizes as $i){
                                        ?>
                                        <option value="<?php echo $fsizes[$i]; ?>" <?php if($font_size == $fsizes[$i]){ echo "selected"; } ?>><?php echo $fsizes[$i]; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2"  onchange="tableToExcel('main_body', 'Mortality Summary','<?php echo $filename;?>', this.options[this.selectedIndex].value)">
                                        <option value="display" <?php if($excel_type == "display"){ echo "selected"; } ?>>-Display-</option>
                                        <option value="excel" <?php if($excel_type == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                        <option value="print" <?php if($excel_type == "print"){ echo "selected"; } ?>>-Print-</option>
                                    </select>
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
            <?php if($excel_type == "print"){ } else{ ?>
                </table>
                

        <table class="tbl_toggle" style="position: relative;  left: 35px;">
            <tr><td><br></td></tr> 
        </table>
        <table id="main_body" class="tbl" align="center"  style="width:1300px;">
         
        <div class="row" style="padding-left:100px;">
            <div class="m-2 form-group">
                                    
            <input style="width: 300px;padding-left:100px;" type="text" class="cd-search table-filter" data-table="tbl" placeholder="Search here..." />
            <br/>
            </div>
            
            </div>
            <thead class="thead1" align="center" style="width:1212px;  display:none; ">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
                <tr align="center">
                    <th colspan="15" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Mortality Record Report</h5></th>

                </tr>
            <?php } ?>


            <tr>
                       
                       <th colspan="15">
                                   <div class="row">
                                       
                                       <div class="m-2 form-group">
                                           <label>Placement To Date: <?php echo date("d.m.Y",strtotime($tdate)); ?></label>
                                       </div>
                                       <div class="m-2 form-group">
                                           <label>Branch: <?php echo $export_Branch; ?></label>
                                       </div>
                                       <div class="m-2 form-group">
                                           <label>Line: <?php echo $export_Line; ?></label>
                                       </div>
                                       <div class="m-2 form-group">
                                           <label>Supervisor: <?php echo $export_Supervisor; ?></label>
                                       </div>
                                       <div class="m-2 form-group">
                                           <label>Mort Percent: <?php echo $export_mort; ?></label>
                                       </div>
                                       
                                      <div class="m-2 form-group">
                                           <label><br/></label>
                   
                                       </div>
                                       
                               </th>
                           
                       </tr>
       
            </thead>
            
       <!-- <table id="mine" class="tbl" align="center"  style="width:1300px;"> -->
        <?php } ?>
        <thead class="thead3" align="center" style="width:1212px;">            
       
            
       <tr align="center" id="header_sorting">
        
                    <th id="order_num">Sl.No.</th>
                    <th id="order">Branch</th>
                    <th id="order">Line</th>
                    <th id="order">Supervisor</th>
                    <th id="order">Farm Name</th>
                    <th id="order">Batch</th>
                    <th id="order">Book No</th>
                    <th id="order_num">Age</th>
                    <th id="order_num">Housed Birds</th>
                    <th id="order_num">Opening Birds</th>
                    <th id="order_num">Before Yesterday Mort</th>
                    <th id="order_num">Yesterday Mortality</th>
                    <th id="order_num">Today Mortality</th>
                    <th id="order_num">Mort%</th>
                    <th id="order_num">Cum Mort</th>
                    <th id="order_num">Balance Birds</th>
                    <th id="order">Diseases Details</th>
                </tr>
            </thead>
            <thead class="thead3" align="center" style="width:1212px; display:none;">            
       
            
       <tr align="center" >
        
                    <th>Sl.No.</th>
                    <th>Branch</th>
                    <th>Line</th>
                    <th>Supervisor</th>
                    <th>Farm Name</th>
                    <th>Batch</th>
                    <th>Book No</th>
                    <th>Age</th>
                    <th>Housed Birds</th>
                    <th>Opening Birds</th>
                    <th>Before Yesterday Mort</th>
                    <th>Yesterday Mortality</th>
                    <th>Today Mortality</th>
                    <th>Mort%</th>
                    <th>Cum Mort</th>
                    <th>Balance Birds</th>
                    <th>Diseases Details</th>
                </tr>
            </thead>
        
            <?php
            if(isset($_POST['submit_report']) == true){
                $sql = "SELECT * FROM `broiler_batch` WHERE gc_flag = '0'".$farm_query." AND active = '1' AND dflag = '0'"; $query = mysqli_query($conn,$sql);
                $batch_all = "";
                while($row = mysqli_fetch_assoc($query)){
                    if($batch_all == ""){
                        $batch_all = $row['code'];
                    }
                    else{
                        $batch_all = $batch_all."','".$row['code'];
                    }
                }

                $chicbirdc = implode("','",$birdchiccode);
                $sql = "SELECT * FROM `broiler_purchases` WHERE `active` = '1' AND `farm_batch` IN ('$batch_all') AND `date` <= '$tdate' AND `icode` IN ('$chicbirdc')"; // echo $sql;
                $query = mysqli_query($conn,$sql); $housed = array();
                while($row = mysqli_fetch_assoc($query)){
                    $key = $row['farm_batch'];
                    $housed[$key] = $row['rcd_qty'] + $row['fre_qty'];
                }

                $sql = "SELECT * FROM `item_stocktransfers` WHERE `active` = '1' AND `to_batch` IN ('$batch_all') AND `date` <= '$tdate' AND `code` IN ('$chicbirdc')";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $key = $row['to_batch'];
                    $housed[$key] += $row['quantity'];
                }

                $batch_slist = $batch_arr_list = $batch_age = $batch_farm = $dieases_codes = array();
                $sql = "SELECT batch_code,farm_code,MAX(brood_age) as brood_age,MAX(date) as edate, sum(mortality + culls) as mort FROM `broiler_daily_record` WHERE `active` = '1'".$farm_query." AND `batch_code` IN ('$batch_all') AND gc_flag = '0' AND dflag = '0'  AND `date` <= '$tdate' GROUP BY `batch_code` ORDER BY brood_age DESC";
                $query = mysqli_query($conn,$sql); $i = 0;
                while($row = mysqli_fetch_assoc($query)){
                    $i++;
                    $batch_slist[$i] = $row['batch_code'];
                    $batch_arr_list[$row['batch_code']] = $row['batch_code'];
                    $batch_age[$row['batch_code']] = $row['brood_age'];
                    $batch_farm[$row['batch_code']] = $row['farm_code'];
                    $batch_edate[$row['batch_code']] = $row['edate'];
                    $tmort[$row['batch_code']] = $row['mort'];
                }
                $sql = "SELECT * FROM `broiler_daily_record` WHERE `active` = '1'".$farm_query." AND `batch_code` IN ('$batch_all') AND gc_flag = '0' AND dflag = '0'  AND `date` = '$tdate' ORDER BY brood_age DESC";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $dieases_codes[$row['batch_code']] = $row['dieases_codes']; }
                $opn_bird_sale = $tdy_bird_sale = array();
                $sql = "SELECT * FROM `broiler_sales` WHERE `active` = '1' AND `farm_batch` IN ('$batch_all') AND dflag = '0'  AND `date` <= '$tdate' ORDER BY `date`,`farm_batch` ASC";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    if(strtotime($row['date']) < strtotime($tdate)){
                        $opn_bird_sale[$row['farm_batch']] += (float)$row['birds'];
                    }
                    if(strtotime($row['date']) == strtotime($tdate)){
                        $tdy_bird_sale[$row['farm_batch']] += (float)$row['birds'];
                    }
                }

                $icat_iac = $icat_srac = $day1 =array();
                $sql = "SELECT * FROM `item_category` WHERE `code` IN ('$chick_cats','$bird_cats') AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $icat_iac[$row['code']] = $row['iac']; $icat_srac[$row['code']] = $row['srac']; }
                
                $sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['category']; }

                //$chick_code = $bird_code = $chick_cats = $bird_cats = "";
                $coa_list = ""; $coa_list .= implode("','", $icat_iac); $coa_list .= "','".implode("','", $icat_srac);
                $batch_list = ""; $batch_list = implode("','", $batch_arr_list);
                $yest_day = date("Y-m-d", strtotime($tdate. ' - 1 days'));
                $bfyest_day = date("Y-m-d", strtotime($tdate. ' - 2 days'));
                $sql = "SELECT * FROM `account_summary` WHERE `coa_code` IN ('$coa_list')  AND `date` <= '$tdate' AND `item_code` IN ('$chick_code') AND `batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC,`crdr` DESC";
                $query = mysqli_query($conn,$sql); $stkin_qty = $stk_qty = $mort_qty = $opening_birds = $day0 = $day1 = $day2 = array();
                while($row = mysqli_fetch_assoc($query)){
                    if(empty($stk_qty[$row['batch']])){ $stk_qty[$row['batch']] = 0; }
                    if(empty($mort_qty[$row['batch']])){ $mort_qty[$row['batch']] = 0; }
                    
                    if(!empty($icat_iac[$icat_code[$row['item_code']]]) && $icat_iac[$icat_code[$row['item_code']]] == $row['coa_code']){
                        if($row['crdr'] == "DR"){
                            $stk_qty[$row['batch']] += (float)$row['quantity'];
                            $stkin_qty[$row['batch']] += (float)$row['quantity'];
                        }
                        else if($row['crdr'] == "CR"){
                            $stk_qty[$row['batch']] = (float)$stk_qty[$row['batch']] - (float)$row['quantity'];
                        }
                        else{ }
                        if(strtotime($row['date']) < strtotime($tdate)){
                            $opening_birds[$row['batch']] = $stk_qty[$row['batch']];
                        }
                    }
                    else if(!empty($icat_srac[$icat_code[$row['item_code']]]) && $icat_srac[$icat_code[$row['item_code']]] == $row['coa_code']){
                        if($row['crdr'] == "DR" && $row['etype'] == "DayEntryMortality"){
                            $mort_qty[$row['batch']] += (float)$row['quantity'];
                            if(strtotime($row['date']) == strtotime($tdate)){
                                $day0[$row['batch']] += (float)$row['quantity'];
                            }
                            else if(strtotime($row['date']) == strtotime($yest_day)){
                                $day1[$row['batch']] += (float)$row['quantity'];
                            }
                            else if(strtotime($row['date']) == strtotime($bfyest_day)){
                                $day2[$row['batch']] += (float)$row['quantity'];
                            }
                            else{ }
                        }
                    }
                }
            ?>
                <tbody class="tbody1" id = "tbody1">
                <?php
                foreach($batch_slist as $bcode){
                    if(strtotime($batch_edate[$bcode]) == strtotime($tdate)){
                        //if($display_obirds > 0 || $present_chicks_in > 0 || $display_feeds_open > 0 || $display_feeds_in > 0 || $display_feed_stock > 0){
                            $slno++;
                            $fcode = $batch_farm[$bcode];

                            $display_branch = $branch_name[$farm_branch[$fcode]];
                            $display_line = $line_name[$farm_line[$fcode]];
                            $display_supervisor = $supervisor_name[$farm_supervisor[$fcode]];
                            $display_farmname = $farm_name[$fcode];
                            $display_farmbatch = $batch_name[$bcode];
                            $display_batchbook = $batch_book[$bcode];
                            $display_age = $batch_age[$bcode];

                            //$opn_bird_sale = $tdy_bird_sale = array();
                            $display_present_obirds = (float)$opening_birds[$bcode] - (float)$opn_bird_sale[$bcode];
                            $pday_mort = $day2[$bcode];
                            $yday_mort = $day1[$bcode];
                            $display_mort = $day0[$bcode];
                            if((float)$stkin_qty[$bcode] != 0){
                                $display_mortper = round((((float)$day0[$bcode] / (float)$stkin_qty[$bcode]) * 100),2);
                            }
                            else{
                                $display_mortper = 0;
                            }
                            $display_bbirds = (float)$stk_qty[$bcode] - (float)$opn_bird_sale[$bcode] - (float)$tdy_bird_sale[$bcode];
                            $ftothbird += $housed[$bcode];
                            $totmort += $tmort[$bcode];
                    if($display_mortper >= $mort_perce ){
                        $display_total_present_obirds += (float)$display_present_obirds;
                        $total_pdaymort += (float)$pday_mort;
                        $total_ydaymort += (float)$yday_mort;
                        $total_mort += (float)$display_mort;
                        $total_bbirds += (float)$display_bbirds;

                        $str_arr = explode (",", $dieases_codes[$bcode]); 
                        $display_dieases_codes = "";
                        for($a = 0;$a<count($str_arr);$a++){
                            if($a == 0){
                                $display_dieases_codes = $dieases_name[$str_arr[$a]];
                            }else{
                                $display_dieases_codes .= ",".$dieases_name[$str_arr[$a]];
                            }
                        }
                ?>
                    <tr>
                        <td title="Sl.No."><?php echo $slno; ?></td>
                        <td title="Farm Name"><?php echo $display_branch; ?></td>
                        <td title="Farm Name"><?php echo $display_line; ?></td>
                        <td title="Supervisor"><?php echo $display_supervisor; ?></td>
                        <td title="Farm Name"><?php  echo $display_farmname; ?></td>
                        <td title="Farm Name"><?php echo $display_farmbatch; ?></td>
                        <td title="Farm Name"><?php echo $display_batchbook; ?></td>
                        <td style="text-align:right;" title="Age"><?php echo str_replace(".00","",number_format_ind(round($display_age))); ?></td>
                        <td style="text-align:right;" title="Housed Birds"><?php echo str_replace(".00","",number_format_ind(round($housed[$bcode]))); ?></td>

                        <td style="text-align:right;" title="opening Birds"><?php  echo str_replace(".00","",number_format_ind(round($display_present_obirds))); ?></td>
                        <td style="text-align:right;" title="Previous day Mortality"><?php echo str_replace(".00","",number_format_ind(round($pday_mort))); ?></td>
                        <td style="text-align:right;" title="Yesterday Mortality"><?php echo str_replace(".00","",number_format_ind(round($yday_mort))); ?></td>
                        <td style="text-align:right;" title="Mortality"><?php echo str_replace(".00","",number_format_ind(round($display_mort))); ?></td>
                        <td style="text-align:right;" title="Mortality %"><?php echo number_format_ind(round($display_mortper,2)); ?></td>
                        <td style="text-align:right;" title="cum mort"><?php echo number_format_ind(round($tmort[$bcode],2)); ?></td>
                        <td style="text-align:right;" title="closeing Birds"><?php echo str_replace(".00","",number_format_ind(round($display_bbirds))); ?></td>
                        <td style="text-align:left;" title="Dieases"><?php echo $display_dieases_codes; ?></td>   
                    </tr>
                <?php
                    }
                        //}
                    //}
                    }
                }
                ?>
                </tbody>
                <tfoot>
                <tr class="thead4">
                    <th colspan="8" style="text-align:center;">Total</th>
                    <th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($ftothbird)); ?></th>
                    <th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($display_total_present_obirds)); ?></th>
                    <th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($total_pdaymort)); ?></th>
                    <th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($total_ydaymort)); ?></th>
                    <th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($total_mort)); ?></th>
                    <th style="text-align:right;">
                    <?php
                    if($display_total_present_obirds > 0){
                        echo number_format_ind(($total_mort / $display_total_present_obirds) * 100);
                    }
                    else{
                        echo number_format_ind(0);
                    }
                    ?></th>
                    <th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($totmort)); ?></th>
                    <th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($total_bbirds)); ?></th>
                    <th style="text-align:left;"><?php echo ""; ?></th>
                    
                </tr>
                </tfoot>
            <?php
            }
        ?>
        </table>
        <script>
            function convertDate(d) {
                var p = d.split(".");
                return (p[2]+p[1]+p[0]);
            }
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
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
        <script type="text/javascript">
              function tableToExcel(table, name, filename, chosen){ 
              
              var uri = 'data:application/vnd.ms-excel;base64,'
                  , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
                  , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
                  , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
            //  return function(table, name, filename, chosen) {
                  if (chosen === 'excel') { 
                    $('#header_sorting').empty();
                  if (!table.nodeType) table = document.getElementById(table)
                  var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
                  //window.location.href = uri + base64(format(template, ctx))
                  var link = document.createElement("a");
                                  link.download = filename+".xls";
                                  link.href = uri + base64(format(template, ctx));
                                  link.click();
                  
                var html = '';
                html += '<th id="order_num">Sl.No.</th>';
                html += '<th id="order">Branch</th>';
                html += '<th id="order">Line</th>';
                html += '<th id="order">Supervisor</th>';
                html += '<th id="order">Farm Name</th>';
                html += '<th id="order">Batch</th>';
                html += '<th id="order">Book No</th>';
                html += '<th id="order_num">Age</th>';
                html += '<th id="order_num">Opening Birds</th>';
                html += '<th id="order_num">Before Yesterday Mort</th>';
                html += '<th id="order_num">Yesterday Mortality</th>';
                html += '<th id="order_num">Today Mortality</th>';
                html += '<th id="order_num">Mort%</th>';
                html += '<th id="order_num">Balance Birds</th>';
                html += '<th id="order">Diseases Details</th>';
                $('#header_sorting').append(html);
                table_sort();
                table_sort2();
                table_sort3();
  
          }
        }

       
        </script>
       
       
       <script src="../table_search_filter/Search_Script.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>