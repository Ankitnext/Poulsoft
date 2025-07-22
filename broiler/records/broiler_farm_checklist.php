<?php
//broiler_farm_checklist.php
$requested_data = json_decode(file_get_contents('php://input'),true);

session_start();
    
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){

    include "../newConfig.php";
    
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
    global $page_title; $page_title = "Farm Check List Report";
    include "header_head.php";
    $user_code = $_SESSION['userid'];
}else{

    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    global $page_title; $page_title = "Farm Check List Report";
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


$sql = "SELECT * FROM `location_branch` WHERE `active` = '1' ".$branch_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_line` WHERE `active` = '1' ".$line_access_filter1."".$branch_access_filter2."  ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description'];$line_branch[$row['code']] = $row['branch_code']; }

$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

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

$sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%super%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $desig_code = "";
while($row = mysqli_fetch_assoc($query)){ if($desig_code == ""){ $desig_code = $row['code']; } else{ $desig_code = $desig_code."','".$row['code']; } }

$sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_code') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `main_access`"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
while($row = mysqli_fetch_assoc($query)){ $db_emp_code[$row['empcode']] = $row['db_emp_code']; $sp_emp_code[$row['db_emp_code']] = $row['empcode']; }

$sql = "SELECT * FROM `item_category` ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

//$sql = "SELECT * FROM `extra_access` WHERE `field_name` IN ('Decimal','Purchase Qty') AND `user_access` LIKE '%$user_code%' OR `field_name` IN ('Decimal','Purchase Qty') AND `user_access` LIKE 'all'"; $query = mysqli_query($conn,$sql);
//while($row = mysqli_fetch_assoc($query)){ if($row['field_name'] == "Decimal"){ $decimal_no = $row['flag']; } if($row['field_name'] == "Purchase Qty"){ $qty_on_sqty_flag = $row['flag']; } }
$fdate = $tdate = date("Y-m-d"); $supervisors = "all"; $excel_type = "display"; //$branches = $lines = 
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    $farms = $_POST['farms'];
    $supervisors = $_POST['supervisors'];
    $supervisors = "all";
    $farm_list = "";
    if($farms != "all"){
        $cod = " AND farm_code IN ('$farms')";
    }
    else if($supervisors != "all"){
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
        $cod = " AND farm_code IN ('$farm_list')";
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
         $cod = " AND farm_code IN ('$farm_list')";
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
         $cod = " AND farm_code IN ('$farm_list')";
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
        $cod = " AND farm_code IN ('$farm_list')";
    }

    

	$excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/SalesReport-Excel.php?fromdate=".$fdate."&todate=".$tdate."&supervisors=".$supervisors."&branches=".$branches."&lines=".$lines;
}
else{
    $url = "";
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <script>
            var exptype = '<?php echo $excel_type; ?>';
            var url = '<?php echo $url; ?>';
            if(exptype.match("excel")){ window.open(url,"_BLANK"); }
        </script>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <style>
        .thead3 th {
                top: 0;
                position: sticky;
                background-color: #9cc2d5;
 
			}
         
        </style>
       
        <?php
          if($excel_type == "print"){
            echo '<style>body { padding:10px;text-align:center; }
            .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
            .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
            .thead2 { display:none;background-image: linear-gradient(#9CC2D5,#9CC2D5); }
            .thead2_empty_row { display:none; }
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
        <table class="tbl" align="center">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" style="width:1212px;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="10" align="center"><?php echo $row['cdetails']; ?><h5>Farm Check List Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <?php if($db == ''){?>
            <form action="broiler_farm_checklist.php" method="post">
                  <?php } else { ?>
                <form action="broiler_farm_checklist.php?db=<?php echo $db; ?>" method="post">
                <?php } ?>
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:1212px;">
                    <tr>
                        <th colspan="12">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Branch</label>
                                    <select name="branches" id="branches" class="form-control select2"  onchange="fetch_farms_details1(this.id)">
                                        <option value="all" <?php if($branches == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($branch_code as $bcode){ if(!empty($branch_name[$bcode])){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($branches == $bcode){ echo "selected"; } ?>><?php echo $branch_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Line</label>
                                    <select name="lines" id="lines" class="form-control select2"  onchange="fetch_farms_details1(this.id)">
                                        <option value="all" <?php if($lines == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($line_code as $lcode){ if(!empty($line_name[$lcode])){ ?>
                                        <option value="<?php echo $lcode; ?>" <?php if($lines == $lcode){ echo "selected"; } ?>><?php echo $line_name[$lcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Farm</label>
                                    <select name="farms" id="farms" class="form-control select2">
                                        <option value="all" <?php if($farm == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($farm_code as $farms){ if($farm_code[$farms] != ""){ ?>
                                        <option value="<?php echo $farms; ?>" <?php if($farm == $farms){ echo "selected"; } ?>><?php echo $farm_name[$farms]; ?></option>
                                        <?php } } ?>
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
            <thead class="thead3" align="center">
                <tr align="center">
                    <th>S.No</th>
                    <th>Date</th>
                    <th>Line</th>
                    <th>Supervisor</th>
                    <th>Farm</th>
                    <th>Action</th>
                </tr>
            </thead>
            <?php
            if(isset($_POST['submit_report']) == true){
            ?>
            <tbody class="tbody1">
                <?php
                $age = array();
                $sql_record = "SELECT DISTINCT(farm_code),date,supervisor_code,line_code,trnum FROM `farm_check_list_record` WHERE `date` >= '$fdate' AND `date` <= '$tdate' and dflag = 0 $cod ORDER BY `date`,`trnum`,`id` ASC";
                $client = $_SESSION['client'];
                $query = mysqli_query($conn,$sql_record); $tot_amt = 0;
                while($row = mysqli_fetch_assoc($query)){
                    
                    $trip_key = date("d.m.Y",strtotime($row['date']))."@".$row['trnum'];
                    $fi_code[$trip_key] = $trip_key;
                    $date[$trip_key] = $row['date'];
                    $farm_code[$trip_key] = $row['farm_code'];
                    $trnum[$trip_key] = $row['trnum'];
                    $supervisor_code[$trip_key] = $row['supervisor_code'];
                    $line_code[$trip_key] = $row['line_code'];
                   
                 
                }
                
                
                foreach($fi_code as $po){ $slno++; $i++;
                          
                   

                        ?>
                        <tr>
                            <td style="text-align:left;"><?php echo $slno; ?></td>
                            <td style="text-align:left;"><?php $t1 = array(); $t1 = explode("@",$po); echo $t1[0]; ?></td>
                            <td style="text-align:left;"><?php echo $line_name[$line_code[$po]] ; ?></td>
                            <td style="text-align:left;"><?php echo $emp_name[$supervisor_code[$po]]; ?></td>
                            <td style="text-align:left;"><?php echo $farm_name[$farm_code[$po]] ?></td>
                            <td style="text-align:left;"><a href="/print/Examples/farmchecklist_print.php?trnum=<?php echo $trnum[$po]; ?>" title="Create Report" target="_blank"><i style="font-size:24px" class="fa">&#xf1c1;</i></a></td>
                            
                            
                        </tr>
                    <?php
                      
                    }
                    
                    
                
            ?>
            </tbody>
           
        <?php
            }
        ?>
        </table><br/><br/><br/>
        <script>
             function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }

function fetch_farms_details1(a){
    var branches = document.getElementById("branches").value;
    var lines = document.getElementById("lines").value;
   
    //var supervisors = document.getElementById("supervisors").value;

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
            /*removeAllOptions(document.getElementById("supervisors"));
            myselect2 = document.getElementById("supervisors");
            theOption2=document.createElement("OPTION");
            theText2=document.createTextNode("-All-");
            theOption2.value = "all"; 
            theOption2.appendChild(theText2); 
            myselect2.appendChild(theOption2);
            <?php
                foreach($supervisor_code as $fcode){
                    if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
                    if(!empty($farm_branch[$fcode])){ $b_code = $farm_branch[$fcode]; } else{ $b_code = ""; }
                    
                    echo "if(branches == '$b_code' && '$f_code' != ''){";
            ?>
                theOption2=document.createElement("OPTION");
                theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                theOption2.value = "<?php echo $fcode; ?>";
                theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
            <?php
                echo "}";
                }
            ?>*/
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
            /*removeAllOptions(document.getElementById("supervisors"));
            myselect2 = document.getElementById("supervisors");
            theOption2=document.createElement("OPTION");
            theText2=document.createTextNode("-All-");
            theOption2.value = "all"; 
            theOption2.appendChild(theText2); 
            myselect2.appendChild(theOption2);
            <?php
                foreach($supervisor_code as $fcode){
                    if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
                    echo "if('$f_code' != ''){";
            ?>
                theOption2=document.createElement("OPTION");
                theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
            <?php
                echo "}";
                }
            ?>*/
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
            /*removeAllOptions(document.getElementById("supervisors"));
            myselect2 = document.getElementById("supervisors");
            theOption2=document.createElement("OPTION");
            theText2=document.createTextNode("-All-");
            theOption2.value = "all"; 
            theOption2.appendChild(theText2); 
            myselect2.appendChild(theOption2);
            <?php
                foreach($supervisor_code as $fcode){
                    if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
                    if(!empty($farm_line[$fcode])){ $l_code = $farm_line[$fcode]; } else{ $l_code = ""; }
                    
                    echo "if(lines == '$l_code' && '$f_code' != ''){";
            ?>
                theOption2=document.createElement("OPTION");
                theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
            <?php
                echo "}";
                }
            ?>*/
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
            /*removeAllOptions(document.getElementById("supervisors"));
            myselect2 = document.getElementById("supervisors");
            theOption2=document.createElement("OPTION");
            theText2=document.createTextNode("-All-");
            theOption2.value = "all"; 
            theOption2.appendChild(theText2); 
            myselect2.appendChild(theOption2);
            <?php
                foreach($supervisor_code as $fcode){
                    if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
                    if(!empty($farm_branch[$fcode])){ $b_code = $farm_branch[$fcode]; } else{ $b_code = ""; }
                    
                    echo "if(branches == '$b_code' && '$f_code' != ''){";
            ?>
                theOption2=document.createElement("OPTION");
                theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
            <?php
                echo "}";
                }
            ?>*/
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
            /*removeAllOptions(document.getElementById("supervisors"));
            myselect2 = document.getElementById("supervisors");
            theOption2=document.createElement("OPTION");
            theText2=document.createTextNode("-All-");
            theOption2.value = "all"; 
            theOption2.appendChild(theText2); 
            myselect2.appendChild(theOption2);
            <?php
                foreach($supervisor_code as $fcode){
                    if(!empty($farm_svr[$fcode])){
                        $f_code = $farm_svr[$fcode];
                    }
                    else{
                        $f_code = "";
                    }
                    echo "if('$f_code' != ''){";
            ?>
                theOption2=document.createElement("OPTION");
                theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
            <?php
                echo "}";
                }
            ?>*/
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
            function fetch_row_height(){
                var table_elements = document.querySelector("table>tbody");
                var i; var max_height = 0;
                for(i = 1; i <= table_elements.rows.length; i++){
                    var row_selector = "table>tbody>tr:nth-child(" + [i] + ")";
                    var table_row = document.querySelector(row_selector);
                    var vertical_spacing = window.getComputedStyle(table_row).getPropertyValue("-webkit-border-vertical-spacing");
                    var margin_top = window.getComputedStyle(table_row).getPropertyValue("margin-top");
                    var margin_bottom = window.getComputedStyle(table_row).getPropertyValue("margin-bottom");
                    var row_height= parseInt(vertical_spacing, 10)+parseInt(margin_top, 10)+parseInt(margin_bottom, 10)+table_row.offsetHeight;
                    if(max_height <= row_height){
                        max_height = row_height;
                    }
                }
                //alert("The height is: "+max_height+"px");
                document.getElementById("thead2_empty_row").style.height = max_height+"px";
            }
            fetch_row_height();
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>