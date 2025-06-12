<?php
//broiler_farm_details.php
$requested_data = json_decode(file_get_contents('php://input'),true);


    
session_start();
    
$db = $_SESSION['db'] = $_GET['db']; $client = $_SESSION['client'];
if($db == ''){

    include "../newConfig.php";
    
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

    include "header_head.php";
    $user_code = $_SESSION['userid'];
}else{

    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    include "header_head.php";
    $user_code = $_GET['userid'];
}



$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; }
else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; }
else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; }
else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }

$sql = "SELECT * FROM `location_region` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $region_code[$row['code']] = $row['code']; $region_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_branch` WHERE `active` = '1' ".$branch_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_line` WHERE `active` = '1' ".$line_access_filter1."".$branch_access_filter2."  ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `country_states` WHERE `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $state_code[$row['code']] = $row['code']; $state_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);

while($row = mysqli_fetch_assoc($query)){
    $farm_code[$row['code']] = $row['code'];
    $farm_ccode[$row['code']] = $row['farm_code'];
    $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code'];
    $farm_line[$row['code']] = $row['line_code'];
    $farm_supervisor[$row['code']] = $row['supervisor_code'];
    $sector_code[$row['code']] = $row['code'];
    $sector_name[$row['code']] = $row['description'];
}

$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_vehicle`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['registration_number']; }

$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `broiler_farmer`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farmer_code[$row['code']] = $row['code']; $farmer_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `item_category` ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

$fdate = $tdate = date("Y-m-d"); $regions = $branches = $lines = $vendors = $sectors = $status = "all"; $excel_type = "display";

if(isset($_POST['submit_report']) == true){
    //$regions = $_POST['regions'];
    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    $supervisors = $_POST['supervisors'];
    $farms = $_POST['farms'];

    $status = $_POST['status'];
    if($status == "all"){ $status_filter = ""; } else{ $status_filter = " AND `active` = '$status'"; }

    $farm_filter = "";
    //if($regions != "all"){ $farm_filter .= " AND `region_code` = '$regions'"; }
    if($branches != "all"){ $farm_filter .= " AND `branch_code` = '$branches'"; }
    if($lines != "all"){ $farm_filter .= " AND `line_code` = '$lines'"; }
    if($supervisors != "all"){ $farm_filter .= " AND `supervisor_code` = '$supervisors'"; }
    if($farms != "all"){ $farm_filter .= " AND `code` = '$farms'"; }

	$excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/broiler_farm_details-Excel.php?branches=".$branches."&lines=".$lines."&supervisors=".$supervisors."&farms=".$farms."&status=".$status;
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <!-- Datatable CSS 
        <link href='../../col/jquery.dataTables.min.css' rel='stylesheet' type='text/css'>-->

        <!-- jQuery Library -->
        <script src="../../col/jquery-3.5.1.js"></script>
        
        <!-- Datatable JS -->
        <script src="../../col/jquery.dataTables.min.js"></script>
        <script>
            var exptype = '<?php echo $excel_type; ?>';
            var url = '<?php echo $url; ?>';
            if(exptype.match("excel")){ window.open(url,"_BLANK"); }
        </script>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <style>
            
		/*	.thead3 th {
                top: 0;
                position: relative;
                background-color: #9cc2d5;
 
			}*/
            .col-md-6 {
                position: relative;  left: 200px;
            max-width: 0%;
}
.col-md-5{
                position: relative;  left: 200px;
            
}
            div.dataTables_wrapper div.dataTables_filter {
                
                text-align: left;
            }
            table thead,
            table tfoot {
  position: sticky;
}
table thead {
  inset-block-start: 0; /* "top" */
}
table tfoot {
  inset-block-end: 0; /* "bottom" */
}

        </style>
        <?php
            if($excel_type == "print"){
                echo '<style>body { padding:10px;text-align:center; }
               .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
				
                .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
                .thead2 { display:none;background-image: linear-gradient(#9CC2D5,#9CC2D5);}
                .thead2_empty_row { display:none; }
                .tbl_toggle { display:none; }
                .dataTables_filter { display:none; }
                .thead3 { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
                .thead4 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
                .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
                .report_head { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
                .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); font-weight:bold; }</style>';
            }
            else{
                echo '<style>body { left:0;width:auto;overflow:auto; } table { white-space: nowrap; }
                table.tbl { left:0;margin-right: auto;visibility:visible; }
                table.tbl2 { left:0;margin-right: auto; }
                .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
                .thead2 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
                .thead3 { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
                .thead4 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
                .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
                .report_head { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
                .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); }</style>';
                
            }
        ?>
    </head>
    <body align="center">
    
        
        <table class="tbl" align="center"   width="1300px">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" width="1212px">
                <tr align="center">
                    <th colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></th>
                    <th colspan="12" align="center"><?php echo $row['cdetails']; ?><h5>Farm Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <?php if($db == ''){?>
            <form action="broiler_farm_details.php" method="post">
                <?php } else { ?>
                <form action="broiler_farm_details.php?db=<?php echo $db; ?>" method="post">
                <?php } ?>
            <form action="broiler_farm_details.php" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed" width="1212px">
                    <tr>
                        <th colspan="14">
                            <div class="row">
                                <!--<div class="m-2 form-group">
                                    <label>Region</label>
                                    <select name="regions" id="regions" class="form-control select2">
                                        <option value="all" <?php if($regions == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($region_code as $rcode){ if($region_name[$rcode] != ""){ ?>
                                        <option value="<?php echo $rcode; ?>" <?php if($regions == $rcode){ echo "selected"; } ?>><?php echo $region_name[$rcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>-->
                                <div class="m-2 form-group">
                                    <label>Branch</label>
                                    <select name="branches" id="branches" class="form-control select2">
                                        <option value="all" <?php if($branches == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($branch_code as $bcode){ if($branch_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($branches == $bcode){ echo "selected"; } ?>><?php echo $branch_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Line</label>
                                    <select name="lines" id="lines" class="form-control select2">
                                        <option value="all" <?php if($lines == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($line_code as $lcode){ if($line_name[$lcode] != ""){ ?>
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
                                    <label>Farm/Location</label>
                                    <select name="farms" id="farms" class="form-control select2">
                                        <option value="all" <?php if($farms == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($farm_code as $fcode){ if($farm_name[$fcode] != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($farms == $fcode){ echo "selected"; } ?>><?php echo $farm_name[$fcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Status</label>
                                    <select name="status" id="status" class="form-control select2">
                                        <option value="all" <?php if($status == "all"){ echo "selected"; } ?>>-All-</option>
                                        <option value="1" <?php if($status == "1"){ echo "selected"; } ?>>Active</option>
                                        <option value="0" <?php if($status == "0"){ echo "selected"; } ?>>In-active</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2">
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
            
            </table>
            
    
    <table class="tbl_toggle" style="position: relative;  left: 35px;">
    <tr><td><br></td></tr> 
    <tr>
        <!--<td>
        <b>Header Columns: </b><a class="toggle-vis" data-column="0" style="border:2px solid Violet;color:MediumSeaGreen;">Farm Code</a> - 
        <a class="toggle-vis" data-column="1" style="border:2px solid Violet;color:MediumSeaGreen;">Farm Name</a> - 
        <a class="toggle-vis" data-column="2" style="border:2px solid Violet;color:MediumSeaGreen;">Region</a> - 
        <a class="toggle-vis" data-column="3" style="border:2px solid Violet;color:MediumSeaGreen;">Branch</a> - 
        <a class="toggle-vis" data-column="4" style="border:2px solid Violet;color:MediumSeaGreen;">Line</a> - 
        <a class="toggle-vis" data-column="5" style="border:2px solid Violet;color:MediumSeaGreen;">Supervisor</a>
        <a class="toggle-vis" data-column="6" style="border:2px solid Violet;color:MediumSeaGreen;">Farmer</a> - 
        <a class="toggle-vis" data-column="7" style="border:2px solid Violet;color:MediumSeaGreen;">Farm Type</a> - 
        <a class="toggle-vis" data-column="8" style="border:2px solid Violet;color:MediumSeaGreen;">Farm Capacity</a>
        </td>
        <td>-->
        <td>
        <div id='control_sh'>
        <input type="checkbox" class="hide_show"><span>Region</span>
        <input type="checkbox" class="hide_show"><span>Branch</span>
        <input type="checkbox" class="hide_show"><span>Line</span>
        <input type="checkbox" class="hide_show"><span>Supervisor</span>
        <input type="checkbox" class="hide_show"><span>Farm Code</span>
        <input type="checkbox" class="hide_show"><span>Farm Name</span>
        <input type="checkbox" class="hide_show"><span>Farmer</span>
        <input type="checkbox" class="hide_show"><span>Farm Type</span>
        <input type="checkbox" class="hide_show"><span>Own Or Lease</span>
        <input type="checkbox" class="hide_show"><span>Farm Capacity</span>
        <input type="checkbox" class="hide_show"><span>Farm Status</span>
        <input type="checkbox" class="hide_show"><span>Farm Location</span>
        <input type="checkbox" class="hide_show"><span>Farm Image</span>
        <input type="checkbox" class="hide_show"><span>State</span>
        <input type="checkbox" class="hide_show"><span>District</span>
        <input type="checkbox" class="hide_show"><span>Farm Address</span>
        <input type="checkbox" class="hide_show"><span>Agreement Months</span>
        <input type="checkbox" class="hide_show"><span>Agreement Copy</span>
        <input type="checkbox" class="hide_show"><span>Security Cheque-1</span>
        <input type="checkbox" class="hide_show"><span>Security Cheque-2</span>
        <input type="checkbox" class="hide_show"><span>Other Docs</span>
        <input type="checkbox" class="hide_show"><span>Remarks</span>
        <!--<input type="checkbox" id="hide_show_all"><span>All</span>-->
        </div>
        </td>
    </tr>    
    <tr><td><br></td></tr>                                
    </table>  
    

             
        <!--<table id="mine" class="display" width="100%" cellspacing="0">      -->                         
                                
            <table id="mine" class="tbl" align="center"  style="width:1300px;">
            <thead class="thead3" align="center" style="width:1212px;">
                <tr align="center">
                    <th>Region</th>
                    <th>Branch</th>
                    <th>Line</th>
                    <th>Supervisor</th>
                    <th>Farm Code</th>
                    <th>Farm Name</th>
                    <th>Farmer</th>
                    <th>Farm Type</th>
                    <th>Own Or Lease</th>
                    <th class="a">Farm Capacity</th>
                    <th>Farm Status</th>
                    <th>Farm Location</th>
                    <th>Farm Image</th>
                    <th>State</th>
                    <th>District</th>
                    <th>Area Name</th>
                    <th>Farm Address</th>
                    <th>Agreement Months</th>
                    <th>Agreement Copy</th>
                    <th>Security Cheque-1</th>
                    <th>Security Cheque-2</th>
                    <th>Other Docs</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            
            <?php
            if(isset($_POST['submit_report']) == true){
            ?>
            <tbody class="tbody1">
                <?php
                $sql_record = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0'".$farm_filter."".$status_filter." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql_record); $slno = 0;
                while($row = mysqli_fetch_assoc($query)){
                    $display_farmlatitude = $row['latitude'];
                    $display_farmlongitude = $row['longitude'];
                    $display_farmname = $row['description'];

                    if(!empty($display_farmlatitude) && !empty($display_farmlongitude)){
                        $slno++;
                        $display_farm_location = "https://broiler.poulsoft.org/records/ShowLocation.php?db=".$db."&lat=".$display_farmlatitude."&lng=".$display_farmlongitude."&farm_name=".$display_farmname."&type=Farm Location";
                    }
                    else{
                        $display_farm_location = "";
                    }
                ?>
                <tr>
                    <td title="Region"><?php echo $region_name[$row['region_code']]; ?></td>
                    <td title="Branch"><?php echo $branch_name[$row['branch_code']]; ?></td>

                    <td title="Line"><?php echo $line_name[$row['line_code']]; ?></td>
                    <td title="Supervisor"><?php echo $emp_name[$row['supervisor_code']]; ?></td>
                    <td title="Farm Code"><?php echo $row['farm_code']; ?></td>
                    <td title="Farm Name"><?php echo $row['description']; ?></td>

                    <td title="Farmer"><?php echo $farmer_name[$row['farmer_code']]; ?></td>
                    <td title="Farm Type"><?php echo $row['farm_type']; ?></td>
                    <td title="Own Or Lease"><?php echo $row['farm_type2']; ?></td>
                    <td title="Farm Capacity"><?php echo $row['farm_capacity']; ?></td>
                    <td title="Farm Status"><?php if($row['active'] == "1"){ echo "Active"; } else{ echo "In-Active"; } ?></td>
                    <?php if(!empty($display_farm_location)){ ?>
                    <td style="text-align:right;" title="Farm Location"><a href='<?php echo $display_farm_location; ?>' target="_BLANK"><?php echo "Location-".$slno; ?></a></td> <?php }
                    else{ ?> <td title="Farm Location"></td> <?php } ?>
                    <td title="Farm Image">
                        <?php
                        $farm_img1 = $farm_img2 = $farm_img3 = "";
                        //$farm_img1 = str_replace("_",":",$row['farm_image']);
                        $farm_img1 = $row['farm_image'];
                        if($farm_img1 == "" || $farm_img1 == "0"){ }
                        else{
                            if($farm_img1 != ""){
                                if( $row['addedtime'] < "2024-04-22 08:18:13" ){
                                    $farm_img2 = "../AndroidApp_API/clientimages/".$client."/farmimages/".$farm_img1;
                                }else{
                                    $farm_img2 = "..".$farm_img1;
                                }
                                if( $row['addedtime'] < "2024-04-22 08:18:13" ){
                                    $farm_img3 = "../AndroidApp_API/clientimages/".$client."/farmimages/".$farm_img1;
                                }else{
                                    $farm_img3 = "..".$farm_img1;
                                }
                            }
                            echo "<script> alert(".$farm_img3."); </script>";
                            ?>
                            <a href="<?php echo $farm_img2; ?>" title="<?php echo $farm_img2; ?>" download ><i class="fa fa-download" style="color:blue;"></i></a>&ensp;
                            <?php if($farm_img3 != ""){?>
                            <a href="javascript:void(0)" onclick="openUrl(event, '<?php echo $farm_img3; ?>');" title="<?php echo $farm_img3; ?>"><i class="fa fa-eye" style="color:brown;"></i></a>
                            <?php
                            }
                        }
                        ?>
                    </td>
                    <td title="State"><?php echo $state_name[$row['state_code']]; ?></td>
                    <td title="District"><?php echo $row['district_name']; ?></td>
                    <td title="Area"><?php echo $row['area_name']; ?></td>
                    <td title="Farm Address"><?php echo $row['farm_address']; ?></td>
                    <td title="Agreement Months"><?php echo $row['agreement_months']; ?></td>
                    <td title="Agreement Copy">
                        <?php
                        $agr_img1 = $agr_img2 = $agr_img3 = "";
                        $agr_img1 = $row['agreement_copy_path'];
                        if($agr_img1 == "" || $agr_img1 == "0"){ }
                        else if($agr_img1 != "" || $agr_img1 != "0"){
                            $agr_img2 = "../".$agr_img1;
                            $agr_img3 = "window.open('../".$agr_img1."');";
                        ?>
                        <a href="<?php echo $agr_img2; ?>" title="<?php echo $agr_img2; ?>" download ><i class="fa fa-download" style="color:blue;"></i></a>&ensp;
                        <a href="javascript:void(0)" onclick="<?php echo $agr_img3; ?>" title="<?php echo $agr_img3; ?>" target="_BLANK"><i class="fa fa-eye" style="color:brown;"></i></a>
                        <?php
                        }
                        else{
                            $agr_img2 = $agr_img3 = "";
                        }
                        
                        ?>
                    </td>
                    <td title="Security Cheque-1"><?php echo $row['security_cheque1']; ?></td>
                    <td title="Security Cheque-2"><?php echo $row['security_cheque2']; ?></td>
                    <td title="Other Docs">
                        <?php
                        $otd_img1 = $otd_img2 = $otd_img3 = "";
                        $otd_img1 = $row['other_doc_path'];
                        if($otd_img1 == "" || $otd_img1 == "0"){ }
                        else if($otd_img1 != "" || $otd_img1 != "0"){
                            $otd_img2 = "../".$otd_img1;
                            $otd_img3 = "window.open('../".$otd_img1."');";
                            ?>
                            <a href="<?php echo $otd_img2; ?>" title="<?php echo $otd_img2; ?>" download ><i class="fa fa-download" style="color:blue;"></i></a>&ensp;
                            <a href="javascript:void(0)" onclick="<?php echo $otd_img3; ?>" title="<?php echo $otd_img3; ?>" target="_BLANK"><i class="fa fa-eye" style="color:brown;"></i></a>
                            <?php
                        }
                        else{
                            $otd_img2 = $otd_img3 = "";
                        }
                        
                        ?>
                    </td>
                    <td title="Remarks"><?php echo $row['remarks']; ?></td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        <?php
            }
        ?>
        </table><br/><br/><br/>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
         <!-- Script -->
       <!--  <script>
        $(document).ready(function () {
    var table = $('#example').DataTable({
        //scrollY: '600px',
       
        paging: false,
    });
 
    $('a.toggle-vis').on('click', function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = table.column($(this).attr('data-column'));
 
        // Toggle the visibility
        column.visible(!column.visible());
    });
});
        </script>-->

<script>
  $(document).ready(function(){
    var table =  $('#mine').DataTable({
        //scrollY: '600px',
       
        paging: false,
    });
	
	$("#hide_show_all").on("change",function(){
	var hide = $(this).is(":checked");
	$(".hide_show").prop("checked", hide);

	if(hide){
		$('#mine tr th').hide(100);
		$('#mine tr td').hide(100);
	}else{
		$('#mine tr th').show(100);
		$('#mine tr td').show(100);
	}
});

$(".hide_show").on("change",function(){
	var hide = $(this).is(":checked");
	
	var all_ch = $(".hide_show:checked").length == $(".hide_show").length;

	$("#hide_show_all").prop("checked", all_ch);
	
	var ti = $(this).index(".hide_show");
	
$('#mine tr').each(function(){
	if(hide){
		$('td:eq(' + ti + ')',this).hide(100);
		$('th:eq(' + ti + ')',this).hide(100);
	}else{
		$('td:eq(' + ti + ')',this).show(100);
		$('th:eq(' + ti + ')',this).show(100);
	}
});

});
$('#mine tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );

$('#myInput').keyup( function() {
        table.draw();
    } );
    $('input.column_filter').on( 'keyup click', function () {
           filterColumn( $(this).parents('tr').attr('data-column') );
       });
	   
       });
</script>
        <script>
            function openUrl(event, url) {
                // Prevent the default link behavior
                event.preventDefault();

                // Open the desired URL in a new window or tab
                window.open(url, '_blank');
            }
        </script>
    </body>
</html>
<?php
include "header_foot.php";
?>