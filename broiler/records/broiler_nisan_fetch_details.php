<?php
//broiler_nisan_fetch_details.php
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../newConfig.php";
    
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
    global $page_title; $page_title = "Nisan APIs";
    include "header_head.php";
}
else{
    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    global $page_title; $page_title = "Nisan APIs";
    include "header_head.php";
}

$farm_code = $farm_ccode = $farm_name = $farm_branch = $farm_line = $farm_supervisor = $farm_svr = $farm_farmer = array();
$sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $farm_code[$row['code']] = $row['code']; $farm_ccode[$row['code']] = $row['farm_code']; $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code'];
    $farm_supervisor[$row['code']] = $row['supervisor_code']; $farm_svr[$row['supervisor_code']] = $row['code'];
    $farm_farmer[$row['code']] = $row['farmer_code'];
}
$branch_code = $branch_name = array();
$sql = "SELECT * FROM `location_branch` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }

$line_code = $line_name = $line_branch = array();
$sql = "SELECT * FROM `location_line` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; $line_branch[$row['code']] = $row['branch_code']; }

$supervisor_code = $supervisor_name = array();
$sql = "SELECT * FROM `broiler_employee` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }

$farmer_code = $farmer_name = array();
$sql = "SELECT * FROM `broiler_farmer` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farmer_code[$row['code']] = $row['code']; $farmer_name[$row['code']] = $row['name']; $frm_mobl[$row['code']] = $row['mobile1']; }

$tdate = date("Y-m-d"); $sync_type = "all"; $excel_type = "display"; $report_view = "hd"; $url = "";
if(isset($_POST['submit_report']) == true){
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $sync_type = $_POST['sync_type'];
}
$sql = "SELECT * FROM `broiler_nisan_credentials` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $psnc_count = mysqli_num_rows($query);
if($psnc_count > 0){ while($row = mysqli_fetch_array($query)){ $psn_version = $row['version']; $psn_company_code = $row['company_code']; $psn_password = $row['password']; } }

$addedemp = $_SESSION['userid']; $addedtime = date('Y-m-d H:i:s');
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
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
            echo '<style>body { left:0;width:auto;overflow:auto;text-align:center; } table { white-space: nowrap; }
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
    <body>
        <table class="tbl" align="center">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" style="width:auto;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="57" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Nisan APIs</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <?php if($db == ''){?>
            <form action="broiler_nisan_fetch_details.php" method="post"  onsubmit="return checkval()">
                 <?php } else { ?>
                <form action="broiler_nisan_fetch_details.php?db=<?php echo $db; ?>" method="post" onsubmit="return checkval()">
                <?php } ?>
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:auto;">
                    <tr style="padding:10px;">
                        <th colspan="59">
                            <div class="p-2 row">
                                <div class="form-group" style="width:290px;">
                                    <label for="sync_type">Sync. Type</label>
                                    <select name="sync_type" id="sync_type" class="form-control select2" style="width:280px;">
                                        <option value="pullweighmentallbydate" <?php if($sync_type == "pullweighmentallbydate"){ echo "selected"; } ?>>pullWeightments_All_ByDate</option>
                                        <option value="pullweighments" <?php if($sync_type == "pullweighments"){ echo "selected"; } ?>>pullWeightments_Detailed</option>
                                        <option value="getBranches" <?php if($sync_type == "getBranches"){ echo "selected"; } ?>>getBranches</option>
                                        <option value="getSupervisor" <?php if($sync_type == "getSupervisor"){ echo "selected"; } ?>>getSupervisor</option>
                                        <option value="getFarmers" <?php if($sync_type == "getFarmers"){ echo "selected"; } ?>>getFarmers</option>
                                        <option value="getSites" <?php if($sync_type == "getSites"){ echo "selected"; } ?>>getSites</option>
                                        <option value="getTraders" <?php if($sync_type == "getTraders"){ echo "selected"; } ?>>getTraders</option>
                                    </select>
                                </div>
                                <div class="form-group" style="width:120px;">
                                    <label>Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div>
                                
                                <div class="form-group" style="width:100px;">
                                    <br/>
                                    <button type="submit" name="submit_report" id="submit_report" class="btn btn-sm btn-success">Submit</button>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
            </form>
                <?php
                if(isset($_POST['submit_report']) == true){
                    if($sync_type == "pullweighmentallbydate"){
                ?>
                <thead class="thead3" align="center">
                    <tr align="center">
                        <th id="order_num">Sl.No.</th>
                        <th id="order_date">Date</th>
                        <th id="order">Dc No</th>
                        <th id="order_num">Mort Wt.</th>
                        <th id="order_num">Birds</th>
                        <th id="order_num">Total Wt.</th>
                        <th id="order_num">Rate</th>
                        <th id="order_num">Amount</th>
                        <th id="order_num">Nof Weightments</th>
                        <th id="order">Vehicle</th>
                        <th id="order">Scale No</th>
                        <th id="order">Remarks</th>
                        <th id="order">Driver Name</th>
                        <th id="order">Veh In-Time</th>
                        <th id="order">Veh Out-Time</th>
                        <th id="order">GPS Location</th>
                        <th id="order">Status</th>
                    </tr>
                </thead>
                <?php
                        $date1 = date("y-m-d", strtotime($tdate)); $date2 = date("Y-m-d", strtotime($tdate));

                        $exist_pullval = array();
                        $sql = "SELECT * FROM `broiler_nisan_pullweightments` WHERE `startdate` = '$date2' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){
                            $a1_companycode = $row['companycode'];
                            $a2_schedulecode = $row['schedulecode'];
                            $a3_isv_schedulecode = $row['isv_schedulecode'];
                            $a4_dc_no = $row['dc_no'];
                            $a5_mort_wt = $row['mort_wt'];
                            $a6_repl_wt = $row['repl_wt'];
                            $a7_totalweight = $row['totalweight'];
                            $a8_noofbirds = $row['noofbirds'];
                            $a9_rate = $row['rate'];
                            $a10_amount = $row['amount'];
                            $a11_startdate = $row['startdate'];
                            $a12_starttime = $row['starttime'];
                            $a13_enddate = $row['enddate'];
                            $a14_endtime = $row['endtime'];
                            $a15_noofweighments = $row['noofweighments'];
                            $a16_vehicle = $row['vehicle'];
                            $a17_scaleno = $row['scaleno'];
                            $a18_slipno = $row['slipno'];
                            $a19_lotno = $row['lotno'];
                            $a20_remark = $row['remark'];
                            $a21_drivername = $row['drivername'];
                            $a22_vehsnap = $row['vehsnap'];
                            $a23_errorcount = $row['errorcount'];
                            $a24_oknob = $row['oknob'];
                            $a25_lamenob = $row['lamenob'];
                            $a26_feedstock = $row['feedstock'];
                            $a27_vehintime = $row['vehintime'];
                            $a28_vehouttime = $row['vehouttime'];
                            $a29_gps_location = $row['gps_location'];
                            $a30_abortcount = $row['abortcount'];
                            $a31_trsc_schedule = $row['trsc_schedule'];

                            $exist_pullval[$a1_companycode."$&@".$a2_schedulecode."$&@".$a3_isv_schedulecode."$&@".$a4_dc_no."$&@".$a5_mort_wt."$&@".$a6_repl_wt."$&@".$a7_totalweight."$&@".$a8_noofbirds."$&@".$a9_rate."$&@".$a10_amount."$&@".$a11_startdate."$&@".$a12_starttime."$&@".$a13_enddate."$&@".$a14_endtime."$&@".$a15_noofweighments."$&@".$a16_vehicle."$&@".$a17_scaleno."$&@".$a18_slipno."$&@".$a19_lotno."$&@".$a20_remark."$&@".$a21_drivername."$&@".$a22_vehsnap."$&@".$a23_errorcount."$&@".$a24_oknob."$&@".$a25_lamenob."$&@".$a26_feedstock."$&@".$a27_vehintime."$&@".$a28_vehouttime."$&@".$a29_gps_location."$&@".$a30_abortcount."$&@".$a31_trsc_schedule] = $a1_companycode."$&@".$a2_schedulecode."$&@".$a3_isv_schedulecode."$&@".$a4_dc_no."$&@".$a5_mort_wt."$&@".$a6_repl_wt."$&@".$a7_totalweight."$&@".$a8_noofbirds."$&@".$a9_rate."$&@".$a10_amount."$&@".$a11_startdate."$&@".$a12_starttime."$&@".$a13_enddate."$&@".$a14_endtime."$&@".$a15_noofweighments."$&@".$a16_vehicle."$&@".$a17_scaleno."$&@".$a18_slipno."$&@".$a19_lotno."$&@".$a20_remark."$&@".$a21_drivername."$&@".$a22_vehsnap."$&@".$a23_errorcount."$&@".$a24_oknob."$&@".$a25_lamenob."$&@".$a26_feedstock."$&@".$a27_vehintime."$&@".$a28_vehouttime."$&@".$a29_gps_location."$&@".$a30_abortcount."$&@".$a31_trsc_schedule;
                        }
                        
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                        CURLOPT_URL => 'nisanapps.com/npickweb/nxinfoservice.asmx?wsdl=null',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS =>'<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
                        <soapenv:Header/>
                        <soapenv:Body>
                            <tem:pullWeightments_All_ByDate>
                                <tem:version>'.$psn_version.'</tem:version>
                                <tem:password>'.$psn_password.'</tem:password>
                                <tem:companycode>'.$psn_company_code.'</tem:companycode>
                                <tem:trscdate>'.$date1.'</tem:trscdate>
                            </tem:pullWeightments_All_ByDate>
                        </soapenv:Body>
                        </soapenv:Envelope>',
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type: text/xml',
                            'charset: utf-8'
                        ),
                        ));
    
                        $response = curl_exec($curl);
    
                        curl_close($curl);
    
    
                        $xml = file_get_contents($response);
                        $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
                        $xml = simplexml_load_string($xml);
                        $json = json_encode($xml);
                        $responseArray = json_decode($json,true);
    
                    
                    ?>
                    <tbody class="tbody1">
                    <?php
                        $slno = 0;
                        foreach($responseArray as $r1){
                            foreach($r1 as $r2){
                                foreach($r2 as $r3){
                                    foreach($r3 as $r4){
                                        foreach($r4 as $r5){
                                            $d1_companycode = $r5['companycode'];
                                            $d2_schedulecode = $r5['schedulecode'];
                                            if(is_array($r5['isv_schedulecode'])){ $d3_isv_schedulecode = implode("@",$r5['isv_schedulecode']); } else{ $d3_isv_schedulecode = $r5['isv_schedulecode']; }
                                            $d4_dc_no = $r5['dc_no'];
                                            $d5_mort_wt = $r5['mort_wt'];
                                            $d6_repl_wt = $r5['repl_wt'];
                                            $d7_totalweight = $r5['totalweight'];
                                            $d8_noofbirds = $r5['noofbirds'];
                                            $d9_rate = $r5['rate'];
                                            $d10_amount = $r5['amount'];
                                            $d11_startdate = date("Y-m-d",strtotime($r5['startdate']));
                                            $d12_starttime = date("Y-m-d H:i:sA",strtotime($r5['starttime']));
                                            $d13_enddate = date("Y-m-d",strtotime($r5['enddate']));
                                            $d14_endtime = date("Y-m-d H:i:sA",strtotime($r5['endtime']));
                                            $d15_noofweighments = $r5['noofweighments'];
                                            $d16_vehicle = $r5['vehicle'];
                                            $d17_scaleno = $r5['scaleno'];
                                            if(is_array($r5['slipno'])){ $d18_slipno = implode("@",$r5['slipno']); } else{ $d18_slipno = $r5['slipno']; }
                                            if(is_array($r5['lotno'])){ $d19_lotno = implode("@",$r5['lotno']); } else{ $d19_lotno = $r5['lotno']; }
                                            $d20_remark = $r5['remark'];
                                            $d21_drivername = $r5['drivername'];
                                            $d22_vehsnap = $r5['vehsnap'];
                                            $d23_errorcount = $r5['errorcount'];
                                            $d24_oknob = $r5['oknob'];
                                            $d25_lamenob = $r5['lamenob']; 
                                            $d26_feedstock = $r5['feedstock'];
                                            $d27_vehintime = $r5['vehintime'];
                                            $d28_vehouttime = $r5['vehouttime'];
                                            $d29_gps_location = $r5['gps_location'];
                                            $d30_abortcount = $r5['abortcount'];
                                            if(is_array($r5['trsc_schedule'])){ $d31_trsc_schedule = implode("@",$r5['trsc_schedule']); } else{ $d31_trsc_schedule = $r5['trsc_schedule']; }

                                            $value = "";
                                            $value = $d1_companycode."$&@".$d2_schedulecode."$&@".$d3_isv_schedulecode."$&@".$d4_dc_no."$&@".$d5_mort_wt."$&@".$d6_repl_wt."$&@".$d7_totalweight."$&@".$d8_noofbirds."$&@".$d9_rate."$&@".$d10_amount."$&@".$d11_startdate."$&@".$d12_starttime."$&@".$d13_enddate."$&@".$d14_endtime."$&@".$d15_noofweighments."$&@".$d16_vehicle."$&@".$d17_scaleno."$&@".$d18_slipno."$&@".$d19_lotno."$&@".$d20_remark."$&@".$d21_drivername."$&@".$d22_vehsnap."$&@".$d23_errorcount."$&@".$d24_oknob."$&@".$d25_lamenob."$&@".$d26_feedstock."$&@".$d27_vehintime."$&@".$d28_vehouttime."$&@".$d29_gps_location."$&@".$d30_abortcount."$&@".$d31_trsc_schedule;
                                            $flag = 0;
                                            if(!empty($exist_pullval[$value])){
                                                $flag = 1;
                                            }
                                            else{
                                                $sql = "INSERT INTO `broiler_nisan_pullweightments` (companycode,schedulecode,isv_schedulecode,dc_no,mort_wt,repl_wt,totalweight,noofbirds,rate,amount,startdate,starttime,enddate,endtime,noofweighments,vehicle,scaleno,slipno,lotno,remark,drivername,vehsnap,errorcount,oknob,lamenob,feedstock,vehintime,vehouttime,gps_location,abortcount,trsc_schedule,addedemp,addedtime,updatedemp) 
                                                VALUES ('$d1_companycode','$d2_schedulecode','$d3_isv_schedulecode','$d4_dc_no','$d5_mort_wt','$d6_repl_wt','$d7_totalweight','$d8_noofbirds','$d9_rate','$d10_amount','$d11_startdate','$d12_starttime','$d13_enddate','$d14_endtime','$d15_noofweighments','$d16_vehicle','$d17_scaleno','$d18_slipno','$d19_lotno','$d20_remark','$d21_drivername','$d22_vehsnap','$d23_errorcount','$d24_oknob','$d25_lamenob','$d26_feedstock','$d27_vehintime','$d28_vehouttime','$d29_gps_location','$d30_abortcount','$d31_trsc_schedule','$addedemp','$addedtime','$addedtime')";
                                                if(!mysqli_query($conn, $sql)){ $flag = 2; echo "<br/>".mysqli_error($conn); } else{ $flag = 0; }
                                            }
                                            $slno++;
                                        ?>
                                        <tr>
                                            <td title="Sl.No." style="text-align:center;"><?php echo $slno; ?></td><!--<input type="text" name="detailed_value" id="detailed_value" style="width:10px;visibility:hidden;" value="<?php //echo $value; ?>" />-->
                                            <td title="Date"><?php echo date("d.m.Y",strtotime($d11_startdate)); ?></td>
                                            <td title="Dc No"><?php echo $d4_dc_no; ?></td>
                                            <td title="Mort Wt." style="text-align:right;"><?php echo number_format_ind($d5_mort_wt); ?></td>
                                            <td title="Birds" style="text-align:right;"><?php echo number_format_ind($d8_noofbirds); ?></td>
                                            <td title="Total Wt." style="text-align:right;"><?php echo number_format_ind($d7_totalweight); ?></td>
                                            <td title="Rate" style="text-align:right;"><?php echo number_format_ind($d9_rate); ?></td>
                                            <td title="Amount" style="text-align:right;"><?php echo number_format_ind($d10_amount); ?></td>
                                            <td title="Nof Weightments" style="text-align:right;"><?php echo number_format_ind($d15_noofweighments); ?></td>
                                            <td title="Vehicle"><?php echo $d16_vehicle; ?></td>
                                            <td title="Scale No"><?php echo $d17_scaleno; ?></td>
                                            <td title="Remarks"><?php echo $d20_remark; ?></td>
                                            <td title="Driver Name"><?php echo $d21_drivername; ?></td>
                                            <td title="Veh In-Time"><?php echo $d27_vehintime; ?></td>
                                            <td title="Veh Out-Time"><?php echo $d28_vehouttime; ?></td>
                                            <td title="GPS Location"><?php echo $d29_gps_location; ?></td>
                                            <?php
                                            if($flag == 1){
                                                echo '<td style="color:blue;font-weight:bold;">Data Exist</td>';
                                            }
                                            else if($flag == 0){
                                                echo '<td style="color:green;font-weight:bold;">Data Inserted</td>';
                                            }
                                            else if($flag == 2){
                                                echo '<td style="color:red;font-weight:bold;">Error Inserting</td>';
                                            }
                                            else{ }
                                            ?>
                                        </tr>
                                        <?php
                                        }
                                    }
                                }
                            }
                        }
                    ?>
                </tbody>
                <thead class="thead2">
                    <tr>
                        <td colspan="17"></td>
                    </tr>
                </thead>
                <?php
                    }
                    else if($sync_type == "getSupervisor"){
                        $date1 = date("y-m-d", strtotime($tdate)); $date2 = date("Y-m-d", strtotime($tdate));
                        
                        ?>
                        <thead class="thead3" align="center">
                            <tr align="center">
                                <th id="order_num">Sl.No.</th>
                                <th id="order">Supervisor Code</th>
                                <th id="order">Supervisor Name</th>
                                <th id="order">IMEI</th>
                                <th id="order">Dcno Start</th>
                                <th id="order">Dcno End</th>
                                <th id="order">Dcno Current</th>
                                <th id="order">Company Code</th>
                                <th id="order">Branch</th>
                                <th id="order">Return Code</th>
                                <th id="order">Status</th>
                            </tr>
                        </thead>
                        <?php 
                        $sql='SHOW COLUMNS FROM `broiler_nisan_getsupervisor`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
                        while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
                        if(in_array("isv_supervisorcode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getsupervisor` ADD `isv_supervisorcode` VARCHAR(200) NULL DEFAULT NULL AFTER `id`"; mysqli_query($conn,$sql); }
                        if(in_array("supervisorname", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getsupervisor` ADD `supervisorname` VARCHAR(200) NULL DEFAULT NULL AFTER `isv_supervisorcode`"; mysqli_query($conn,$sql); }
                        if(in_array("imeinumber", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getsupervisor` ADD `imeinumber` VARCHAR(200) NULL DEFAULT NULL AFTER `supervisorname`"; mysqli_query($conn,$sql); }
                        if(in_array("dcno_start", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getsupervisor` ADD `dcno_start` VARCHAR(200) NULL DEFAULT NULL AFTER `imeinumber`"; mysqli_query($conn,$sql); }
                        if(in_array("dcno_end", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getsupervisor` ADD `dcno_end` VARCHAR(200) NULL DEFAULT NULL AFTER `dcno_start`"; mysqli_query($conn,$sql); }
                        if(in_array("dcno_current", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getsupervisor` ADD `dcno_current` VARCHAR(200) NULL DEFAULT NULL AFTER `dcno_end`"; mysqli_query($conn,$sql); }
                        if(in_array("companycode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getsupervisor` ADD `companycode` VARCHAR(200) NULL DEFAULT NULL AFTER `dcno_current`"; mysqli_query($conn,$sql); }
                        if(in_array("isv_branchcode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getsupervisor` ADD `isv_branchcode` VARCHAR(200) NULL DEFAULT NULL AFTER `companycode`"; mysqli_query($conn,$sql); }
                        if(in_array("returncode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getsupervisor` ADD `returncode` VARCHAR(200) NULL DEFAULT NULL AFTER `isv_branchcode`"; mysqli_query($conn,$sql); }
                        
                        $exist_pullval = array();
                        $sql = "SELECT * FROM `broiler_nisan_getsupervisor`"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){
                            $a1_isv_supervisorcode = $row['isv_supervisorcode'];
                            $a2_supervisorname = $row['supervisorname'];
                            $a3_imeinumber = $row['imeinumber'];
                            $a4_dcno_start = $row['dcno_start'];
                            $a5_dcno_end = $row['dcno_end'];
                            $a6_dcno_current = $row['dcno_current'];
                            $a7_companycode = $row['companycode'];
                            $a8_isv_branchcode = $row['isv_branchcode'];
                            $a9_returncode = $row['returncode'];

                            $exist_pullval[$a1_isv_supervisorcode."$&@".$a2_supervisorname."$&@".$a3_imeinumber."$&@".$a4_dcno_start."$&@".$a5_dcno_end."$&@".$a6_dcno_current."$&@".$a7_companycode."$&@".$a8_isv_branchcode."$&@".$a9_returncode] = $a1_isv_supervisorcode."$&@".$a2_supervisorname."$&@".$a3_imeinumber."$&@".$a4_dcno_start."$&@".$a5_dcno_end."$&@".$a6_dcno_current."$&@".$a7_companycode."$&@".$a8_isv_branchcode."$&@".$a9_returncode;
                        }
                        //'.$psn_version.' = "4.0"; '.$psn_password.' = "srfNx@1"; '.$psn_company_code.' = "1209"; '.$status.' = "A";
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                        CURLOPT_URL => 'nisanapps.com/npickweb/nxinfoservice.asmx?wsdl=null',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS =>'<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
                        <soapenv:Header/>
                        <soapenv:Body>
                            <tem:getSupervisorByCompany>
                                <!--Optional:-->
                                <tem:version>'.$psn_version.'</tem:version>
                                <!--Optional:-->
                                <tem:password>'.$psn_password.'</tem:password>
                                <tem:companyCode>'.$psn_company_code.'</tem:companyCode>
                                <!--Optional:-->
                                <tem:myisvsupervisorcode></tem:myisvsupervisorcode>
                            </tem:getSupervisorByCompany>
                        </soapenv:Body>
                        </soapenv:Envelope>',
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type: text/xml',
                            'charset: utf-8'
                        ),
                        ));

                        $response = curl_exec($curl);
                        curl_close($curl);

                        $xml = file_get_contents($response);
                        $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
                        $xml = simplexml_load_string($xml);
                        $json = json_encode($xml);
                        $responseArray = json_decode($json,true);
                        
                        $slno = 0; $old_scode = $d1_isv_supervisorcode = "";
                        foreach($responseArray as $r1){
                            foreach($r1 as $r2){
                                foreach($r2 as $r3){
                                    foreach($r3 as $r4){
                                        foreach($r4 as $r5){
                                            
                                            if(is_array($r5) && is_array($r5['isv_supervisorcode'])){
                                                $d1_isv_supervisorcode = implode("@",$r5['isv_supervisorcode']);
                                            }
                                            else if(is_array($r5)){
                                                $d1_isv_supervisorcode = $r5['isv_supervisorcode'];
                                            }
                                            if(is_array($r5)){
                                                $d2_supervisorname = $r5['supervisorname'];
                                                if(is_array($r5['imeinumber'])){ $d3_imeinumber = implode("@",$r5['imeinumber']); } else{ $d3_imeinumber = $r5['imeinumber']; }
                                                $d4_dcno_start = $r5['dcno_start'];
                                                $d5_dcno_end = $r5['dcno_end'];
                                                $d6_dcno_current = $r5['dcno_current'];
                                                $d7_companycode = $r5['companycode'];
                                                $d8_isv_branchcode = $r5['isv_branchcode'];
                                                $d9_returncode = $r5['ReturnCode'];
                                            }
                                            else{
                                                $d1_isv_supervisorcode = $r4['isv_supervisorcode'];
                                                $d2_supervisorname = $r4['supervisorname'];
                                                if(is_array($r4['imeinumber'])){ $d3_imeinumber = implode("@",$r4['imeinumber']); } else{ $d3_imeinumber = $r4['imeinumber']; }
                                                $d4_dcno_start = $r4['dcno_start'];
                                                $d5_dcno_end = $r4['dcno_end'];
                                                $d6_dcno_current = $r4['dcno_current'];
                                                $d7_companycode = $r4['companycode'];
                                                $d8_isv_branchcode = $r4['isv_branchcode'];
                                                $d9_returncode = $r4['ReturnCode'];
                                            }
                                            $value = "";
                                            $value = $d1_isv_supervisorcode."$&@".$d2_supervisorname."$&@".$d3_imeinumber."$&@".$d4_dcno_start."$&@".$d5_dcno_end."$&@".$d6_dcno_current."$&@".$d7_companycode."$&@".$d8_isv_branchcode."$&@".$d9_returncode;
                                            
                                            $flag = 0;
                                            if(!empty($exist_pullval[$value])){
                                                $flag = 1;
                                            }
                                            else{
                                                if($old_scode != $d1_isv_supervisorcode){
                                                    $sql = "INSERT INTO `broiler_nisan_getsupervisor` (isv_supervisorcode,supervisorname,imeinumber,dcno_start,dcno_end,dcno_current,companycode,isv_branchcode,returncode,addedemp,addedtime,updatedemp) 
                                                    VALUES ('$d1_isv_supervisorcode','$d2_supervisorname','$d3_imeinumber','$d4_dcno_start','$d5_dcno_end','$d6_dcno_current','$d7_companycode','$d8_isv_branchcode','$d9_returncode','$addedemp','$addedtime','$addedtime')";
                                                    if(!mysqli_query($conn, $sql)){ $flag = 2; echo "<br/>".mysqli_error($conn); } else{ $flag = 0; }
                                                    $old_scode = $d1_isv_supervisorcode;
                                                }
                                                else{
                                                    $flag = 1;
                                                }
                                            }
                                            $slno++;
                                            ?>
                                            <tr>
                                                <td title="Sl.No."><?php echo $slno; ?></td>
                                                <td title="Supervisor Code"><?php echo $d1_isv_supervisorcode; ?></td>
                                                <td title="Supervisor Name"><?php echo $d2_supervisorname; ?></td>
                                                <td title="IMEI"><?php echo $d3_imeinumber; ?></td>
                                                <td title="Dcno Start"><?php echo $d4_dcno_start; ?></td>
                                                <td title="Dcno End"><?php echo $d5_dcno_end; ?></td>
                                                <td title="Dcno Current"><?php echo $d6_dcno_current; ?></td>
                                                <td title="Company Code"><?php echo $d7_companycode; ?></td>
                                                <td title="Branch"><?php echo $d8_isv_branchcode; ?></td>
                                                <td title="Return Code"><?php echo $d9_returncode; ?></td>
                                                <?php
                                                if($flag == 1){
                                                    echo '<td style="color:blue;font-weight:bold;">Data Exist</td>';
                                                }
                                                else if($flag == 0){
                                                    echo '<td style="color:green;font-weight:bold;">Data Inserted</td>';
                                                }
                                                else if($flag == 2){
                                                    echo '<td style="color:red;font-weight:bold;">Error Inserting</td>';
                                                }
                                                else{ }
                                                ?>
                                            </tr>
                                            <?php
                                        }
                                    }
                                }
                            }
                        }
                    }
                    else if($sync_type == "getFarmers"){
                        $date1 = date("y-m-d", strtotime($tdate)); $date2 = date("Y-m-d", strtotime($tdate));
                        //'.$psn_version.' = "4.0"; '.$psn_password.' = "srfNx@1"; '.$psn_company_code.' = "1209"; '.$status.' = "A";
                        ?>
                        <thead class="thead3" align="center">
                            <tr align="center">
                                <th id="order_num">Sl.No.</th>
                                <th id="order">Farmer Code</th>
                                <th id="order">Farmer Name</th>
                                <th id="order">Mobile</th>
                                <th id="order">Company Code</th>
                                <th id="order">IMEI</th>
                                <th id="order">Branch</th>
                                <th id="order">Status</th>
                                <th id="order">Return Code</th>
                                <th id="order">Status</th>
                            </tr>
                        </thead>
                        <?php 
                        $sql='SHOW COLUMNS FROM `broiler_nisan_getfarmers`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
                        while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
                        if(in_array("isv_farmercode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getfarmers` ADD `isv_farmercode` VARCHAR(200) NULL DEFAULT NULL AFTER `id`"; mysqli_query($conn,$sql); }
                        if(in_array("farmername", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getfarmers` ADD `farmername` VARCHAR(200) NULL DEFAULT NULL AFTER `isv_farmercode`"; mysqli_query($conn,$sql); }
                        if(in_array("mobileno", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getfarmers` ADD `mobileno` VARCHAR(200) NULL DEFAULT NULL AFTER `farmername`"; mysqli_query($conn,$sql); }
                        if(in_array("companycode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getfarmers` ADD `companycode` VARCHAR(200) NULL DEFAULT NULL AFTER `mobileno`"; mysqli_query($conn,$sql); }
                        if(in_array("imeinumber", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getfarmers` ADD `imeinumber` VARCHAR(200) NULL DEFAULT NULL AFTER `companycode`"; mysqli_query($conn,$sql); }
                        if(in_array("isv_branchcode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getfarmers` ADD `isv_branchcode` VARCHAR(200) NULL DEFAULT NULL AFTER `imeinumber`"; mysqli_query($conn,$sql); }
                        if(in_array("status", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getfarmers` ADD `status` VARCHAR(200) NULL DEFAULT NULL AFTER `isv_branchcode`"; mysqli_query($conn,$sql); }
                        if(in_array("returncode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getfarmers` ADD `returncode` VARCHAR(200) NULL DEFAULT NULL AFTER `status`"; mysqli_query($conn,$sql); }
                        
                        
                        $exist_pullval = array();
                        $sql = "SELECT * FROM `broiler_nisan_getfarmers`"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){
                            $a1_isv_farmercode = $row['isv_farmercode'];
                            $a2_farmername = $row['farmername'];
                            $a3_mobileno = $row['mobileno'];
                            $a4_companycode = $row['companycode'];
                            $a5_imeinumber = $row['imeinumber'];
                            $a6_isv_branchcode = $row['isv_branchcode'];
                            $a7_status = $row['status'];
                            $a8_returncode = $row['returncode'];

                            $exist_pullval[$a1_isv_farmercode."$&@".$a2_farmername."$&@".$a3_mobileno."$&@".$a4_companycode."$&@".$a5_imeinumber."$&@".$a6_isv_branchcode."$&@".$a7_status."$&@".$a8_returncode] = $a1_isv_farmercode."$&@".$a2_farmername."$&@".$a3_mobileno."$&@".$a4_companycode."$&@".$a5_imeinumber."$&@".$a6_isv_branchcode."$&@".$a7_status."$&@".$a8_returncode;

                        }

                        $sql='SHOW COLUMNS FROM `broiler_farm`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
                        while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
                        if(in_array("nisan_aflag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_farm` ADD `nisan_aflag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Nisan synchronization Flag' AFTER `dflag`"; mysqli_query($conn,$sql); }
                    
                        $sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql); $c = 0;
                        while($row = mysqli_fetch_assoc($query)){ $frm_name[$row['farm_code']] = $row['farm_code']; }
                        
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                        CURLOPT_URL => 'nisanapps.com/npickweb/nxinfoservice.asmx?wsdl=null',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS =>'<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
                        <soapenv:Header/>
                        <soapenv:Body>
                            <tem:getFarmers>
                                <!--Optional:-->
                                <tem:version>'.$psn_version.'</tem:version>
                                <!--Optional:-->
                                <tem:password>'.$psn_password.'</tem:password>
                                <tem:companyCode>'.$psn_company_code.'</tem:companyCode>
                                <!--Optional:-->
                                <tem:status>'.$status.'</tem:status>
                            </tem:getFarmers>
                        </soapenv:Body>
                        </soapenv:Envelope>',
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type: text/xml',
                            'charset: utf-8'
                        ),
                        ));

                        $response = curl_exec($curl);
                        curl_close($curl);

                        $xml = file_get_contents($response);
                        $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
                        $xml = simplexml_load_string($xml);
                        $json = json_encode($xml);
                        $responseArray = json_decode($json,true);
                        
                        $slno = 0;
                        foreach($responseArray as $r1){
                            foreach($r1 as $r2){
                                foreach($r2 as $r3){
                                    foreach($r3 as $r4){
                                        foreach($r4 as $r5){
                                            $d1_isv_farmercode = $r5['isv_farmercode'];
                                            $d2_farmername = $r5['farmername'];
                                            if(is_array($r5['mobileno'])){ $d3_mobileno = implode("@",$r5['mobileno']); } else{ $d3_mobileno = $r5['mobileno']; }
                                            $d4_companycode = $r5['companycode'];
                                            if(is_array($r5['imeinumber'])){ $d5_imeinumber = implode("@",$r5['imeinumber']); } else{ $d5_imeinumber = $r5['imeinumber']; }
                                            $d6_isv_branchcode = $r5['isv_branchcode'];
                                            $d7_status = $r5['status'];
                                            $d8_returncode = $r5['ReturnCode'];
                                            
                                            $value = "";
                                            $value = $d1_isv_farmercode."$&@".$d2_farmername."$&@".$d3_mobileno."$&@".$d4_companycode."$&@".$d5_imeinumber."$&@".$d6_isv_branchcode."$&@".$d7_status."$&@".$d8_returncode;
                                            $flag = 0;
                                            if(!empty($exist_pullval[$value])){
                                                $flag = 1;
                                            }
                                            else{
                                                $sql = "INSERT INTO `broiler_nisan_getfarmers` (isv_farmercode,farmername,mobileno,companycode,imeinumber,isv_branchcode,status,returncode,addedemp,addedtime,updatedemp) 
                                                VALUES ('$d1_isv_farmercode','$d2_farmername','$d3_mobileno','$d4_companycode','$d5_imeinumber','$d6_isv_branchcode','$d7_status','$d8_returncode','$addedemp','$addedtime','$addedtime')";
                                                if(!mysqli_query($conn, $sql)){ $flag = 2; echo "<br/>".mysqli_error($conn); } else{ $flag = 0; }
                                            }
                                            if(!empty($frm_name[$d1_isv_farmercode])){
                                                $sqln = "UPDATE `broiler_farm` SET `nisan_aflag` = '1' WHERE `farm_code` = '$d1_isv_farmercode' AND `active` = '1' AND `dflag` = '0'";
                                                mysqli_query($conn,$sqln);
                                            }
                                            $slno++;
                                            ?>
                                            <tr>
                                                <td title="Sl.No."><?php echo $slno; ?></td>
                                                <td title="Farmer Code"><?php echo $d1_isv_farmercode; ?></td>
                                                <td title="Farmer Name"><?php echo $d2_farmername; ?></td>
                                                <td title="Mobile No."><?php echo $d3_mobileno; ?></td>
                                                <td title="Company Code"><?php echo $d4_companycode; ?></td>
                                                <td title="IMEI"><?php echo $d5_imeinumber; ?></td>
                                                <td title="Branch"><?php echo $d6_isv_branchcode; ?></td>
                                                <td title="Status"><?php echo $d7_status; ?></td>
                                                <td title="Return Code"><?php echo $d8_returncode; ?></td>
                                                <?php
                                                if($flag == 1){
                                                    echo '<td style="color:blue;font-weight:bold;">Data Exist</td>';
                                                }
                                                else if($flag == 0){
                                                    echo '<td style="color:green;font-weight:bold;">Data Inserted</td>';
                                                }
                                                else if($flag == 2){
                                                    echo '<td style="color:red;font-weight:bold;">Error Inserting</td>';
                                                }
                                                else{ }
                                                ?>
                                            </tr>
                                            <?php
                                        }
                                    }
                                }
                            }
                        }
                    }
                    else if($sync_type == "getBranches"){
                        //$date1 = date("y-m-d", strtotime($tdate)); $date2 = date("Y-m-d", strtotime($tdate));
                        //'.$psn_version.' = "4.0"; '.$psn_password.' = "srfNx@1"; '.$psn_company_code.' = "1209"; '.$status.' = "A";
                    ?>
                    <thead class="thead3" align="center">
                        <tr align="center">
                            <th id="order_num">Sl.No.</th>
                            <th id="order">Branch Code</th>
                            <th id="order">Company Code</th>
                            <th id="order">Branch Name</th>
                            <th id="order">Return Code</th>
                            <th id="order">Status</th>
                        </tr>
                    </thead>
                    <?php
                        $sql='SHOW COLUMNS FROM `broiler_nisan_getbranches`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
                        while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
                        if(in_array("isv_branchcode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getbranches` ADD `isv_branchcode` VARCHAR(200) NULL DEFAULT NULL AFTER `id`"; mysqli_query($conn,$sql); }
                        if(in_array("companycode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getbranches` ADD `companycode` VARCHAR(200) NULL DEFAULT NULL AFTER `isv_branchcode`"; mysqli_query($conn,$sql); }
                        if(in_array("branchname", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getbranches` ADD `branchname` VARCHAR(200) NULL DEFAULT NULL AFTER `companycode`"; mysqli_query($conn,$sql); }
                        if(in_array("returncode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getbranches` ADD `returncode` VARCHAR(200) NULL DEFAULT NULL AFTER `branchname`"; mysqli_query($conn,$sql); }
                       
                        $exist_pullval = array();
                        $sql = "SELECT * FROM `broiler_nisan_getbranches`"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){
                            $a1_isv_branchcode = $row['isv_branchcode'];
                            $a4_companycode = $row['companycode'];
                            $a5_branchname = $row['branchname'];
                            $a7_returncode = $row['returncode'];

                            $exist_pullval[$a1_isv_branchcode."$&@".$a4_companycode."$&@".$a5_branchname."$&@".$a7_returncode] = $a1_isv_branchcode."$&@".$a4_companycode."$&@".$a5_branchname."$&@".$a7_returncode;

                        }
                        
                        $sql='SHOW COLUMNS FROM `location_branch`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
                        while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
                        if(in_array("nisan_aflag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `location_branch` ADD `nisan_aflag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Nisan synchronization Flag' AFTER `dflag`"; mysqli_query($conn,$sql); }
                    
                        $sql = "SELECT * FROM `location_branch` WHERE `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql); $c = 0;
                        while($row = mysqli_fetch_assoc($query)){ $brh_name[$row['description']] = $row['description']; }

                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                          CURLOPT_URL => 'nisanapps.com/npickweb/nxinfoservice.asmx?wsdl=null',
                          CURLOPT_RETURNTRANSFER => true,
                          CURLOPT_ENCODING => '',
                          CURLOPT_MAXREDIRS => 10,
                          CURLOPT_TIMEOUT => 0,
                          CURLOPT_FOLLOWLOCATION => true,
                          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                          CURLOPT_CUSTOMREQUEST => 'POST',
                          CURLOPT_POSTFIELDS =>'<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
                           <soapenv:Header/>
                           <soapenv:Body>
                              <tem:getBranchByCompany>
                                 <!--Optional:-->
                                 <tem:version>'.$psn_version.'</tem:version>
                                 <!--Optional:-->
                                 <tem:password>'.$psn_password.'</tem:password>
                                 <tem:companyCode>'.$psn_company_code.'</tem:companyCode>
                                 <!--Optional:-->
                                 <tem:myisvbranchcode></tem:myisvbranchcode>
                              </tem:getBranchByCompany>
                           </soapenv:Body>
                        </soapenv:Envelope>',
                          CURLOPT_HTTPHEADER => array(
                            'Content-Type: text/xml',
                            'charset: utf-8'
                          ),
                        ));
                        
                        $response = curl_exec($curl);
                        curl_close($curl);
                        
                        $xml = file_get_contents($response);
                        $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
                        $xml = simplexml_load_string($xml);
                        $json = json_encode($xml);
                        $responseArray = json_decode($json,true);

                        $slno = 0;
                        foreach($responseArray as $r1){
                            foreach($r1 as $r2){
                                foreach($r2 as $r3){
                                    foreach($r3 as $r4){
                                        foreach($r4 as $r5){
                                            $d1_isv_branchcode = $r5['isv_branchcode'];
                                            $d2_companycode = $r5['companycode'];
                                            $d3_branchname = $r5['branchname'];
                                            $d4_returncode = $r5['ReturnCode'];
                                            
                                            $value = "";
                                            $value = $d1_isv_branchcode."$&@".$d2_companycode."$&@".$d3_branchname."$&@".$d4_returncode;
                                            $flag = 0;
                                            if(!empty($exist_pullval[$value])){
                                                $flag = 1;
                                            }
                                            else{
                                                $sql = "INSERT INTO `broiler_nisan_getbranches` (isv_branchcode,companycode,branchname,returncode,addedemp,addedtime,updatedemp) 
                                                VALUES ('$d1_isv_branchcode','$d2_companycode','$d3_branchname','$d4_returncode','$addedemp','$addedtime','$addedtime')";
                                                if(!mysqli_query($conn, $sql)){ $flag = 2; echo "<br/>".mysqli_error($conn); } else{ $flag = 0; }
                                            }
                                            if(!empty($brh_name[$d3_branchname])){
                                                $sqln = "UPDATE `location_branch` SET `nisan_aflag` = '1' WHERE `description` = '$d3_branchname' AND `active` = '1' AND `dflag` = '0'";
                                                mysqli_query($conn,$sqln);
                                            }
                                            $slno++;
                                            ?>
                                            <tr>
                                                <td title="Sl.No."><?php echo $slno; ?></td>
                                                <td title="Farm Code"><?php echo $d1_isv_branchcode; ?></td>
                                                <td title="Site Code"><?php echo $d2_companycode; ?></td>
                                                <td title="Site Name"><?php echo $d3_branchname; ?></td>
                                                <td title="Company Code"><?php echo $d4_returncode; ?></td>
                                                <?php
                                                if($flag == 1){
                                                    echo '<td style="color:blue;font-weight:bold;">Data Exist</td>';
                                                }
                                                else if($flag == 0){
                                                    echo '<td style="color:green;font-weight:bold;">Data Inserted</td>';
                                                }
                                                else if($flag == 2){
                                                    echo '<td style="color:red;font-weight:bold;">Error Inserting</td>';
                                                }
                                                else{ }
                                                ?>
                                            </tr>
                                            <?php
                                        }
                                    }
                                }
                            }
                        }
                    }
                    else if($sync_type == "getSites"){
                        $date1 = date("y-m-d", strtotime($tdate)); $date2 = date("Y-m-d", strtotime($tdate));
                        //'.$psn_version.' = "4.0"; '.$psn_password.' = "srfNx@1"; '.$psn_company_code.' = "1209"; '.$status.' = "A";
                    ?>
                    <thead class="thead3" align="center">
                        <tr align="center">
                            <th id="order_num">Sl.No.</th>
                            <th id="order">Farm Code</th>
                            <th id="order">Site Code</th>
                            <th id="order">Site Name</th>
                            <th id="order">Company Code</th>
                            <th id="order">Status</th>
                            <th id="order">GPS Location</th>
                            <th id="order">Return Code</th>
                            <th id="order">Status</th>
                        </tr>
                    </thead>
                    <?php
                        $sql='SHOW COLUMNS FROM `broiler_nisan_getsites`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
                        while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
                        if(in_array("isv_farmercode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getsites` ADD `isv_farmercode` VARCHAR(200) NULL DEFAULT NULL AFTER `id`"; mysqli_query($conn,$sql); }
                        if(in_array("isv_sitecode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getsites` ADD `isv_sitecode` VARCHAR(200) NULL DEFAULT NULL AFTER `isv_farmercode`"; mysqli_query($conn,$sql); }
                        if(in_array("sitename", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getsites` ADD `sitename` VARCHAR(200) NULL DEFAULT NULL AFTER `isv_sitecode`"; mysqli_query($conn,$sql); }
                        if(in_array("companycode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getsites` ADD `companycode` VARCHAR(200) NULL DEFAULT NULL AFTER `sitename`"; mysqli_query($conn,$sql); }
                        if(in_array("status", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getsites` ADD `status` VARCHAR(200) NULL DEFAULT NULL AFTER `companycode`"; mysqli_query($conn,$sql); }
                        if(in_array("gpslocation", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getsites` ADD `gpslocation` VARCHAR(200) NULL DEFAULT NULL AFTER `status`"; mysqli_query($conn,$sql); }
                        if(in_array("returncode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_getsites` ADD `returncode` VARCHAR(200) NULL DEFAULT NULL AFTER `gpslocation`"; mysqli_query($conn,$sql); }
                       
                        $exist_pullval = array();
                        $sql = "SELECT * FROM `broiler_nisan_getsites`"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){
                            $a1_isv_farmercode = $row['isv_farmercode'];
                            $a2_isv_sitecode = $row['isv_sitecode'];
                            $a3_sitename = $row['sitename'];
                            $a4_companycode = $row['companycode'];
                            $a5_status = $row['status'];
                            $a6_gpslocation = $row['gpslocation'];
                            $a7_returncode = $row['returncode'];

                            $exist_pullval[$a1_isv_farmercode."$&@".$a2_isv_sitecode."$&@".$a3_sitename."$&@".$a4_companycode."$&@".$a5_status."$&@".$a6_gpslocation."$&@".$a7_returncode] = $a1_isv_farmercode."$&@".$a2_isv_sitecode."$&@".$a3_sitename."$&@".$a4_companycode."$&@".$a5_status."$&@".$a6_gpslocation."$&@".$a7_returncode;

                        }
                        $sql='SHOW COLUMNS FROM `location_line`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
                        while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
                        if(in_array("nisan_aflag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `location_line` ADD `nisan_aflag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Nisan synchronization Flag' AFTER `dflag`"; mysqli_query($conn,$sql); }
                        
                        $line_farms = $lne_name = array();
                        $sql = "SELECT * FROM `location_line` WHERE `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql); $c = 0;
                        while($row = mysqli_fetch_assoc($query)){ $lne_name[$row['code']] = $row['description']; }
                        
                        $sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql); $c = 0;
                        while($row = mysqli_fetch_assoc($query)){ $line_farms[$lne_name[$row['line_code']]."@".$row['farm_code']] = $lne_name[$row['line_code']]."@".$row['farm_code']; }
                        
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'nisanapps.com/npickweb/nxinfoservice.asmx?wsdl=null',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS =>'<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
                            <soapenv:Header/>
                            <soapenv:Body>
                                <tem:getSites>
                                <!--Optional:-->
                                <tem:version>'.$psn_version.'</tem:version>
                                <!--Optional:-->
                                <tem:password>'.$psn_password.'</tem:password>
                                <tem:companyCode>'.$psn_company_code.'</tem:companyCode>
                                <!--Optional:-->
                                <tem:status>'.$status.'</tem:status>
                                </tem:getSites>
                            </soapenv:Body>
                            </soapenv:Envelope>',
                            CURLOPT_HTTPHEADER => array(
                                'Content-Type: text/xml',
                                'charset: utf-8'
                            ),
                        ));

                        $response = curl_exec($curl);
                        curl_close($curl);
                        
                        $xml = file_get_contents($response);
                        $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
                        $xml = simplexml_load_string($xml);
                        $json = json_encode($xml);
                        $responseArray = json_decode($json,true);

                        $slno = 0;
                        foreach($responseArray as $r1){
                            foreach($r1 as $r2){
                                foreach($r2 as $r3){
                                    foreach($r3 as $r4){
                                        foreach($r4 as $r5){
                                            $d1_isv_farmercode = $r5['isv_farmercode'];
                                            $d2_isv_sitecode = $r5['isv_sitecode'];
                                            $d3_sitename = $r5['sitename'];
                                            $d4_companycode = $r5['companycode'];
                                            $d5_status = $r5['status'];
                                            $d6_gpslocation = $r5['gpslocation'];
                                            $d7_returncode = $r5['ReturnCode'];
                                            
                                            $value = "";
                                            $value = $d1_isv_farmercode."$&@".$d2_isv_sitecode."$&@".$d3_sitename."$&@".$d4_companycode."$&@".$d5_status."$&@".$d6_gpslocation."$&@".$d7_returncode;
                                            $flag = 0;
                                            if(!empty($exist_pullval[$value])){
                                                $flag = 1;
                                            }
                                            else{
                                                $sql = "INSERT INTO `broiler_nisan_getsites` (isv_farmercode,isv_sitecode,sitename,companycode,status,gpslocation,returncode,addedemp,addedtime,updatedtime) 
                                                VALUES ('$d1_isv_farmercode','$d2_isv_sitecode','$d3_sitename','$d4_companycode','$d5_status','$d6_gpslocation','$d7_returncode','$addedemp','$addedtime','$addedtime')";
                                                if(!mysqli_query($conn, $sql)){ $flag = 2; echo "<br/>".mysqli_error($conn); } else{ $flag = 0; }
                                            }
                                            if(!empty($line_farms[$d3_sitename."@".$d1_isv_farmercode])){
                                                $sqln = "UPDATE `location_line` SET `nisan_aflag` = '1' WHERE `description` = '$d3_sitename' AND `active` = '1' AND `dflag` = '0'";
                                                mysqli_query($conn,$sqln);
                                            }
                                            $slno++;
                                            ?>
                                            <tr>
                                                <td title="Sl.No."><?php echo $slno; ?></td>
                                                <td title="Farm Code"><?php echo $d1_isv_farmercode; ?></td>
                                                <td title="Site Code"><?php echo $d2_isv_sitecode; ?></td>
                                                <td title="Site Name"><?php echo $d3_sitename; ?></td>
                                                <td title="Company Code"><?php echo $d4_companycode; ?></td>
                                                <td title="Status"><?php echo $d5_status; ?></td>
                                                <td title="GPS Location"><?php echo $d6_gpslocation; ?></td>
                                                <td title="Return Code"><?php echo $d7_returncode; ?></td>
                                                <?php
                                                if($flag == 1){
                                                    echo '<td style="color:blue;font-weight:bold;">Data Exist</td>';
                                                }
                                                else if($flag == 0){
                                                    echo '<td style="color:green;font-weight:bold;">Data Inserted</td>';
                                                }
                                                else if($flag == 2){
                                                    echo '<td style="color:red;font-weight:bold;">Error Inserting</td>';
                                                }
                                                else{ }
                                                ?>
                                            </tr>
                                            <?php
                                        }
                                    }
                                }
                            }
                        }
                    }
                    else if($sync_type == "getTraders"){
                        $date1 = date("y-m-d", strtotime($tdate)); $date2 = date("Y-m-d", strtotime($tdate));
                        //'.$psn_version.' = "4.0"; '.$psn_password.' = "srfNx@1"; '.$psn_company_code.' = "1209"; '.$status.' = "A";
                    ?>
                    <thead class="thead3" align="center">
                        <tr align="center">
                            <th id="order_num">Sl.No.</th>
                            <th id="order">Trader Code</th>
                            <th id="order">Trader Name</th>
                            <th id="order">Mobile No</th>
                            <th id="order">Company Code</th>
                            <th id="order">IMEI</th>
                            <th id="order">Branch</th>
                            <th id="order">Status</th>
                            <th id="order">Address</th>
                            <th id="order">Place of Supply</th>
                            <th id="order">GST No</th>
                            <th id="order">GST Date</th>
                            <th id="order">Return Code</th>
                            <th id="order">Table Status</th>
                    </thead>
                    <?php
                        $sql='SHOW COLUMNS FROM `broiler_nisan_gettraders`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
                        while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
                        if(in_array("isv_tradercode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_gettraders` ADD `isv_tradercode` VARCHAR(200) NULL DEFAULT NULL AFTER `id`"; mysqli_query($conn,$sql); }
                        if(in_array("tradername", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_gettraders` ADD `tradername` VARCHAR(200) NULL DEFAULT NULL AFTER `isv_tradercode`"; mysqli_query($conn,$sql); }
                        if(in_array("mobileno", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_gettraders` ADD `mobileno` VARCHAR(200) NULL DEFAULT NULL AFTER `tradername`"; mysqli_query($conn,$sql); }
                        if(in_array("companycode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_gettraders` ADD `companycode` VARCHAR(200) NULL DEFAULT NULL AFTER `mobileno`"; mysqli_query($conn,$sql); }
                        if(in_array("imeinumber", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_gettraders` ADD `imeinumber` VARCHAR(200) NULL DEFAULT NULL AFTER `companycode`"; mysqli_query($conn,$sql); }
                        if(in_array("isv_branchcode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_gettraders` ADD `isv_branchcode` VARCHAR(200) NULL DEFAULT NULL AFTER `imeinumber`"; mysqli_query($conn,$sql); }
                        if(in_array("status", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_gettraders` ADD `status` VARCHAR(200) NULL DEFAULT NULL AFTER `isv_branchcode`"; mysqli_query($conn,$sql); }
                        if(in_array("address", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_gettraders` ADD `address` VARCHAR(200) NULL DEFAULT NULL AFTER `status`"; mysqli_query($conn,$sql); }
                        if(in_array("placeofsupply", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_gettraders` ADD `placeofsupply` VARCHAR(200) NULL DEFAULT NULL AFTER `address`"; mysqli_query($conn,$sql); }
                        if(in_array("gstno", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_gettraders` ADD `gstno` VARCHAR(200) NULL DEFAULT NULL AFTER `placeofsupply`"; mysqli_query($conn,$sql); }
                        if(in_array("gstdate", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_gettraders` ADD `gstdate` VARCHAR(200) NULL DEFAULT NULL AFTER `gstno`"; mysqli_query($conn,$sql); }
                        if(in_array("returncode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_gettraders` ADD `returncode` VARCHAR(200) NULL DEFAULT NULL AFTER `gstdate`"; mysqli_query($conn,$sql); }
                       
                        $exist_pullval = array();
                        $sql = "SELECT * FROM `broiler_nisan_gettraders`"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){
                            $a1 = $row['isv_tradercode'];
                            $a2 = $row['tradername'];
                            $a3 = $row['mobileno'];
                            $a4 = $row['companycode'];
                            $a5 = $row['imeinumber'];
                            $a6 = $row['isv_branchcode'];
                            $a7 = $row['status'];
                            $a8 = $row['address'];
                            $a9 = $row['placeofsupply'];
                            $a10 = $row['gstno'];
                            $a11 = $row['gstdate'];
                            $a12 = $row['returncode'];

                            $exist_pullval[$a1."$&@".$a2."$&@".$a3."$&@".$a4."$&@".$a5."$&@".$a6."$&@".$a7."$&@".$a8."$&@".$a9."$&@".$a10."$&@".$a11."$&@".$a12] = $a1."$&@".$a2."$&@".$a3."$&@".$a4."$&@".$a5."$&@".$a6."$&@".$a7."$&@".$a8."$&@".$a9."$&@".$a10."$&@".$a11."$&@".$a12;
                        }
                        $sql='SHOW COLUMNS FROM `main_contactdetails`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
                        while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
                        if(in_array("nisan_aflag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `nisan_aflag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Nisan synchronization Flag' AFTER `dflag`"; mysqli_query($conn,$sql); }
                        
                        $line_farms = $vendor_name = array();
                        $sql = "SELECT * FROM `main_contactdetails` WHERE `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql); $c = 0;
                        while($row = mysqli_fetch_assoc($query)){ $vendor_name[$row['code']] = $row['name']; }
                        
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                        CURLOPT_URL => 'nisanapps.com/npickweb/nxinfoservice.asmx?wsdl=null',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS =>'<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
                        <soapenv:Header/>
                        <soapenv:Body>
                            <tem:getTraders>
                                <!--Optional:-->
                                <tem:version>'.$psn_version.'</tem:version>
                                <!--Optional:-->
                                <tem:password>'.$psn_password.'</tem:password>
                                <tem:companyCode>'.$psn_company_code.'</tem:companyCode>
                                <!--Optional:-->
                                <tem:status>'.$status.'</tem:status>
                            </tem:getTraders>
                        </soapenv:Body>
                        </soapenv:Envelope>',
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type: text/xml',
                            'charset: utf-8'
                        ),
                        ));
                        $response = curl_exec($curl);
                        curl_close($curl);
                        
                        $xml = file_get_contents($response);
                        $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
                        $xml = simplexml_load_string($xml);
                        $json = json_encode($xml);
                        $responseArray = json_decode($json,true);

                        $slno = 0;
                        foreach($responseArray as $r1){
                            foreach($r1 as $r2){
                                foreach($r2 as $r3){
                                    foreach($r3 as $r4){
                                        foreach($r4 as $r5){
                                            $d1 = $r5['isv_tradercode'];
                                            $d2 = $r5['tradername'];
                                            $d3 = $r5['mobileno'];
                                            $d4 = $r5['companycode'];
                                            $d5 = $r5['imeinumber']; if(is_array($r5['imeinumber'])){ $d5 = implode("@",$r5['imeinumber']); } else{ $d5 = $r5['imeinumber']; }
                                            $d6 = $r5['isv_branchcode'];
                                            $d7 = $r5['status'];
                                            $d8 = $r5['address']; if(is_array($r5['address'])){ $d8 = implode("@",$r5['address']); } else{ $d8 = $r5['address']; }
                                            $d9 = $r5['placeofsupply']; if(is_array($r5['placeofsupply'])){ $d9 = implode("@",$r5['placeofsupply']); } else{ $d9 = $r5['placeofsupply']; }
                                            $d10 = $r5['gstno']; if(is_array($r5['gstno'])){ $d10 = implode("@",$r5['gstno']); } else{ $d10 = $r5['gstno']; }
                                            $d11 = $r5['gstdate']; if(is_array($r5['gstdate'])){ $d11 = implode("@",$r5['gstdate']); } else{ $d11 = $r5['gstdate']; }
                                            $d12 = $r5['ReturnCode'];
                                            
                                            $value = "";
                                            $value = $d1."$&@".$d2."$&@".$d3."$&@".$d4."$&@".$d5."$&@".$d6."$&@".$d7."$&@".$d8."$&@".$d9."$&@".$d10."$&@".$d11."$&@".$d12;
                                            $flag = 0;
                                            if(!empty($exist_pullval[$value])){
                                                $flag = 1;
                                            }
                                            else{
                                                $sql = "INSERT INTO `broiler_nisan_gettraders` (isv_tradercode,tradername,mobileno,companycode,imeinumber,isv_branchcode,`status`,`address`,placeofsupply,gstno,gstdate,returncode,addedemp,addedtime,updatedtime) 
                                                VALUES ('$d1','$d2','$d3','$d4','$d5','$d6','$d7','$d8','$d9','$d10','$d11','$d12','$addedemp','$addedtime','$addedtime')";
                                                if(!mysqli_query($conn, $sql)){ $flag = 2; echo "<br/>".mysqli_error($conn); } else{ $flag = 0; }
                                            }
                                            $sqln = "UPDATE `main_contactdetails` SET `nisan_aflag` = '1' WHERE `name` = '$d2' AND `active` = '1' AND `dflag` = '0'"; mysqli_query($conn,$sqln);
                                            $slno++;
                                            ?>
                                            <tr>
                                                <td title="Sl.No."><?php echo $slno; ?></td>
                                                <td title="Trader Code"><?php echo $d1; ?></td>
                                                <td title="Trader Name"><?php echo $d2; ?></td>
                                                <td title="Mobile No"><?php echo $d3; ?></td>
                                                <td title="Company Code"><?php echo $d4; ?></td>
                                                <td title="IMEI"><?php echo $d5; ?></td>
                                                <td title="Branch"><?php echo $d6; ?></td>
                                                <td title="Status"><?php echo $d7; ?></td>
                                                <td title="Address"><?php echo $d8; ?></td>
                                                <td title="Place of Supply"><?php echo $d9; ?></td>
                                                <td title="GST No"><?php echo $d10; ?></td>
                                                <td title="GST Date"><?php echo $d11; ?></td>
                                                <td title="Return Code"><?php echo $d12; ?></td>
                                                <?php
                                                if($flag == 1){
                                                    echo '<td style="color:blue;font-weight:bold;">Data Exist</td>';
                                                }
                                                else if($flag == 0){
                                                    echo '<td style="color:green;font-weight:bold;">Data Inserted</td>';
                                                }
                                                else if($flag == 2){
                                                    echo '<td style="color:red;font-weight:bold;">Error Inserting</td>';
                                                }
                                                else{ }
                                                ?>
                                            </tr>
                                            <?php
                                        }
                                    }
                                }
                            }
                        }
                    }
                    else if($sync_type == "pullweighments"){
                        $date1 = date("y-m-d", strtotime($tdate)); $date2 = date("Y-m-d", strtotime($tdate));
                        //'.$psn_version.' = "4.0"; '.$psn_password.' = "srfNx@1"; '.$psn_company_code.' = "1209";
                    ?>
                        <thead class="thead3" align="center">
                            <tr>
                                <th id="order">Sl.No.</th>
                                <th id="order">companycode</th>
                                <th id="order">schedulecode</th>
                                <th id="order">isv_schedulecode</th>
                                <th id="order">dc_no</th>
                                <th id="order">mort_wt</th>
                                <th id="order">repl_wt</th>
                                <th id="order">totalweight</th>
                                <th id="order">noofbirds</th>
                                <th id="order">rate</th>
                                <th id="order">amount</th>
                                <th id="order">startdate</th>
                                <th id="order">starttime</th>
                                <th id="order">enddate</th>
                                <th id="order">endtime</th>
                                <th id="order">noofweighments</th>
                                <th id="order">vehicle</th>
                                <th id="order">scaleno</th>
                                <th id="order">slipno</th>
                                <th id="order">lotno</th>
                                <th id="order">remark</th>
                                <th id="order">drivername</th>
                                <th id="order">vehsnap</th>
                                <th id="order">errorcount</th>
                                <th id="order">oknob</th>
                                <th id="order">lamenob</th>
                                <th id="order">feedstock</th>
                                <th id="order">vehintime</th>
                                <th id="order">vehouttime</th>
                                <th id="order">abortcount</th>
                                <th id="order">isv_supervisorcode</th>
                                <th id="order">isv_tradercode</th>
                                <th id="order">isv_farmercode</th>
                                <th id="order">isv_sitecode</th>
                                <th id="order">isv_schedulecode2</th>
                                <th id="order">qty</th>
                                <th id="order">rate2</th>
                                <th id="order">amount2</th>
                                <th id="order">companycode2</th>
                                <th id="order">isv_tdepocode</th>
                                <th id="order">isv_flockcode</th>
                                <th id="order">isv_vechiclecode</th>
                                <th id="order">isv_branchcode</th>
                                <th id="order">returncode</th>
                                <th id="order">companycode3</th>
                                <th id="order">schedulecode2</th>
                                <th id="order">isv_schedulecode3</th>
                                <th id="order">dc_no2</th>
                                <th id="order">srno</th>
                                <th id="order">weight</th>
                                <th id="order">noofbirds2</th>
                                <th id="order">mortweight</th>
                                <th id="order">mortnoofbirds</th>
                                <th id="order">replweight</th>
                                <th id="order">replnoofbirds</th>
                                <th id="order">cageweight</th>
                                <th id="order">netweight</th>
                                <th id="order">noc</th>
                                <th id="order">Status</th>
                            </tr>
                        </thead>
                    <?php
                        $sql='SHOW COLUMNS FROM `broiler_nisan_pullweightments_detailed`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
                        while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
                        if(in_array("companycode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `companycode` VARCHAR(200) NULL DEFAULT NULL AFTER `id`"; mysqli_query($conn,$sql); }
                        if(in_array("schedulecode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `schedulecode` VARCHAR(200) NULL DEFAULT NULL AFTER `companycode`"; mysqli_query($conn,$sql); }
                        if(in_array("isv_schedulecode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `isv_schedulecode` VARCHAR(200) NULL DEFAULT NULL AFTER `schedulecode`"; mysqli_query($conn,$sql); }
                        if(in_array("dc_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `dc_no` VARCHAR(200) NULL DEFAULT NULL AFTER `isv_schedulecode`"; mysqli_query($conn,$sql); }
                        if(in_array("mort_wt", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `mort_wt` VARCHAR(200) NULL DEFAULT NULL AFTER `dc_no`"; mysqli_query($conn,$sql); }
                        if(in_array("repl_wt", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `repl_wt` VARCHAR(200) NULL DEFAULT NULL AFTER `mort_wt`"; mysqli_query($conn,$sql); }
                        if(in_array("totalweight", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `totalweight` VARCHAR(200) NULL DEFAULT NULL AFTER `repl_wt`"; mysqli_query($conn,$sql); }
                        if(in_array("noofbirds", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `noofbirds` VARCHAR(200) NULL DEFAULT NULL AFTER `totalweight`"; mysqli_query($conn,$sql); }
                        if(in_array("rate", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `rate` VARCHAR(200) NULL DEFAULT NULL AFTER `noofbirds`"; mysqli_query($conn,$sql); }
                        if(in_array("amount", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `amount` VARCHAR(200) NULL DEFAULT NULL AFTER `rate`"; mysqli_query($conn,$sql); }
                        if(in_array("startdate", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `startdate` VARCHAR(200) NULL DEFAULT NULL AFTER `amount`"; mysqli_query($conn,$sql); }
                        if(in_array("starttime", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `starttime` VARCHAR(200) NULL DEFAULT NULL AFTER `startdate`"; mysqli_query($conn,$sql); }
                        if(in_array("enddate", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `enddate` VARCHAR(200) NULL DEFAULT NULL AFTER `starttime`"; mysqli_query($conn,$sql); }
                        if(in_array("endtime", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `endtime` VARCHAR(200) NULL DEFAULT NULL AFTER `enddate`"; mysqli_query($conn,$sql); }
                        if(in_array("noofweighments", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `noofweighments` VARCHAR(200) NULL DEFAULT NULL AFTER `endtime`"; mysqli_query($conn,$sql); }
                        if(in_array("vehicle", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `vehicle` VARCHAR(200) NULL DEFAULT NULL AFTER `noofweighments`"; mysqli_query($conn,$sql); }
                        if(in_array("scaleno", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `scaleno` VARCHAR(200) NULL DEFAULT NULL AFTER `vehicle`"; mysqli_query($conn,$sql); }
                        if(in_array("slipno", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `slipno` VARCHAR(200) NULL DEFAULT NULL AFTER `scaleno`"; mysqli_query($conn,$sql); }
                        if(in_array("lotno", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `lotno` VARCHAR(200) NULL DEFAULT NULL AFTER `slipno`"; mysqli_query($conn,$sql); }
                        if(in_array("remark", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `remark` VARCHAR(200) NULL DEFAULT NULL AFTER `lotno`"; mysqli_query($conn,$sql); }
                        if(in_array("drivername", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `drivername` VARCHAR(200) NULL DEFAULT NULL AFTER `remark`"; mysqli_query($conn,$sql); }
                        if(in_array("vehsnap", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `vehsnap` VARCHAR(200) NULL DEFAULT NULL AFTER `drivername`"; mysqli_query($conn,$sql); }
                        if(in_array("errorcount", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `errorcount` VARCHAR(200) NULL DEFAULT NULL AFTER `vehsnap`"; mysqli_query($conn,$sql); }
                        if(in_array("oknob", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `oknob` VARCHAR(200) NULL DEFAULT NULL AFTER `errorcount`"; mysqli_query($conn,$sql); }
                        if(in_array("lamenob", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `lamenob` VARCHAR(200) NULL DEFAULT NULL AFTER `oknob`"; mysqli_query($conn,$sql); }
                        if(in_array("feedstock", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `feedstock` VARCHAR(200) NULL DEFAULT NULL AFTER `lamenob`"; mysqli_query($conn,$sql); }
                        if(in_array("vehintime", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `vehintime` VARCHAR(200) NULL DEFAULT NULL AFTER `feedstock`"; mysqli_query($conn,$sql); }
                        if(in_array("vehouttime", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `vehouttime` VARCHAR(200) NULL DEFAULT NULL AFTER `vehintime`"; mysqli_query($conn,$sql); }
                        if(in_array("abortcount", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `abortcount` VARCHAR(200) NULL DEFAULT NULL AFTER `vehouttime`"; mysqli_query($conn,$sql); }
                        if(in_array("isv_supervisorcode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `isv_supervisorcode` VARCHAR(200) NULL DEFAULT NULL AFTER `abortcount`"; mysqli_query($conn,$sql); }
                        if(in_array("isv_tradercode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `isv_tradercode` VARCHAR(200) NULL DEFAULT NULL AFTER `isv_supervisorcode`"; mysqli_query($conn,$sql); }
                        if(in_array("isv_farmercode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `isv_farmercode` VARCHAR(200) NULL DEFAULT NULL AFTER `isv_tradercode`"; mysqli_query($conn,$sql); }
                        if(in_array("isv_sitecode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `isv_sitecode` VARCHAR(200) NULL DEFAULT NULL AFTER `isv_farmercode`"; mysqli_query($conn,$sql); }
                        if(in_array("isv_schedulecode2", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `isv_schedulecode2` VARCHAR(200) NULL DEFAULT NULL AFTER `isv_sitecode`"; mysqli_query($conn,$sql); }
                        if(in_array("qty", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `qty` VARCHAR(200) NULL DEFAULT NULL AFTER `isv_schedulecode2`"; mysqli_query($conn,$sql); }
                        if(in_array("rate2", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `rate2` VARCHAR(200) NULL DEFAULT NULL AFTER `qty`"; mysqli_query($conn,$sql); }
                        if(in_array("amount2", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `amount2` VARCHAR(200) NULL DEFAULT NULL AFTER `rate2`"; mysqli_query($conn,$sql); }
                        if(in_array("companycode2", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `companycode2` VARCHAR(200) NULL DEFAULT NULL AFTER `amount2`"; mysqli_query($conn,$sql); }
                        if(in_array("isv_tdepocode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `isv_tdepocode` VARCHAR(200) NULL DEFAULT NULL AFTER `companycode2`"; mysqli_query($conn,$sql); }
                        if(in_array("isv_flockcode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `isv_flockcode` VARCHAR(200) NULL DEFAULT NULL AFTER `isv_tdepocode`"; mysqli_query($conn,$sql); }
                        if(in_array("isv_vechiclecode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `isv_vechiclecode` VARCHAR(200) NULL DEFAULT NULL AFTER `isv_flockcode`"; mysqli_query($conn,$sql); }
                        if(in_array("isv_branchcode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `isv_branchcode` VARCHAR(200) NULL DEFAULT NULL AFTER `isv_vechiclecode`"; mysqli_query($conn,$sql); }
                        if(in_array("returncode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `returncode` VARCHAR(200) NULL DEFAULT NULL AFTER `isv_branchcode`"; mysqli_query($conn,$sql); }
                        if(in_array("companycode3", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `companycode3` VARCHAR(200) NULL DEFAULT NULL AFTER `returncode`"; mysqli_query($conn,$sql); }
                        if(in_array("schedulecode2", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `schedulecode2` VARCHAR(200) NULL DEFAULT NULL AFTER `companycode3`"; mysqli_query($conn,$sql); }
                        if(in_array("isv_schedulecode3", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `isv_schedulecode3` VARCHAR(200) NULL DEFAULT NULL AFTER `schedulecode2`"; mysqli_query($conn,$sql); }
                        if(in_array("dc_no2", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `dc_no2` VARCHAR(200) NULL DEFAULT NULL AFTER `isv_schedulecode3`"; mysqli_query($conn,$sql); }
                        if(in_array("srno", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `srno` VARCHAR(200) NULL DEFAULT NULL AFTER `dc_no2`"; mysqli_query($conn,$sql); }
                        if(in_array("weight", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `weight` VARCHAR(200) NULL DEFAULT NULL AFTER `srno`"; mysqli_query($conn,$sql); }
                        if(in_array("noofbirds2", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `noofbirds2` VARCHAR(200) NULL DEFAULT NULL AFTER `weight`"; mysqli_query($conn,$sql); }
                        if(in_array("mortweight", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `mortweight` VARCHAR(200) NULL DEFAULT NULL AFTER `noofbirds2`"; mysqli_query($conn,$sql); }
                        if(in_array("mortnoofbirds", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `mortnoofbirds` VARCHAR(200) NULL DEFAULT NULL AFTER `mortweight`"; mysqli_query($conn,$sql); }
                        if(in_array("replweight", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `replweight` VARCHAR(200) NULL DEFAULT NULL AFTER `mortnoofbirds`"; mysqli_query($conn,$sql); }
                        if(in_array("replnoofbirds", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `replnoofbirds` VARCHAR(200) NULL DEFAULT NULL AFTER `replweight`"; mysqli_query($conn,$sql); }
                        if(in_array("cageweight", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `cageweight` VARCHAR(200) NULL DEFAULT NULL AFTER `replnoofbirds`"; mysqli_query($conn,$sql); }
                        if(in_array("netweight", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `netweight` VARCHAR(200) NULL DEFAULT NULL AFTER `cageweight`"; mysqli_query($conn,$sql); }
                        if(in_array("noc", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_nisan_pullweightments_detailed` ADD `noc` VARCHAR(200) NULL DEFAULT NULL AFTER `netweight`"; mysqli_query($conn,$sql); }

                        $exist_pullval = array();
                        $sql = "SELECT * FROM `broiler_nisan_pullweightments_detailed` WHERE `startdate` = '$date2' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){
                            $a1_companycode = $row['companycode'];
                            $a2_schedulecode = $row['schedulecode'];
                            $a3_isv_schedulecode = $row['isv_schedulecode'];
                            $a4_dc_no = $row['dc_no'];
                            $a5_mort_wt = $row['mort_wt'];
                            $a6_repl_wt = $row['repl_wt'];
                            $a7_totalweight = $row['totalweight'];
                            $a8_noofbirds = $row['noofbirds'];
                            $a9_rate = $row['rate'];
                            $a10_amount = $row['amount'];
                            $a11_startdate = $row['startdate'];
                            $a12_starttime = $row['starttime'];
                            $a13_enddate = $row['enddate'];
                            $a14_endtime = $row['endtime'];
                            $a15_noofweighments = $row['noofweighments'];
                            $a16_vehicle = $row['vehicle'];
                            $a17_scaleno = $row['scaleno'];
                            $a18_slipno = $row['slipno'];
                            $a19_lotno = $row['lotno'];
                            $a20_remark = $row['remark'];
                            $a21_drivername = $row['drivername'];
                            $a22_vehsnap = $row['vehsnap'];
                            $a23_errorcount = $row['errorcount'];
                            $a24_oknob = $row['oknob'];
                            $a25_lamenob = $row['lamenob'];
                            $a26_feedstock = $row['feedstock'];
                            $a27_vehintime = $row['vehintime'];
                            $a28_vehouttime = $row['vehouttime'];
                            $a29_abortcount = $row['abortcount'];
                            $a30_isv_supervisorcode = $row['isv_supervisorcode'];
                            $a31_isv_tradercode = $row['isv_tradercode'];
                            $a32_isv_farmercode = $row['isv_farmercode'];
                            $a33_isv_sitecode = $row['isv_sitecode'];
                            $a34_isv_schedulecode2 = $row['isv_schedulecode2'];
                            $a35_qty = $row['qty'];
                            $a36_rate2 = $row['rate2'];
                            $a37_amount2 = $row['amount2'];
                            $a38_companycode2 = $row['companycode2'];
                            $a39_isv_tdepocode = $row['isv_tdepocode'];
                            $a40_isv_flockcode = $row['isv_flockcode'];
                            $a41_isv_vechiclecode = $row['isv_vechiclecode'];
                            $a42_isv_branchcode = $row['isv_branchcode'];
                            $a43_returncode = $row['returncode'];
                            $a44_companycode3 = $row['companycode3'];
                            $a45_schedulecode2 = $row['schedulecode2'];
                            $a46_isv_schedulecode3 = $row['isv_schedulecode3'];
                            $a47_dc_no2 = $row['dc_no2'];
                            $a48_srno = $row['srno'];
                            $a49_weight = $row['weight'];
                            $a50_noofbirds2 = $row['noofbirds2'];
                            $a51_mortweight = $row['mortweight'];
                            $a52_mortnoofbirds = $row['mortnoofbirds'];
                            $a53_replweight = $row['replweight'];
                            $a54_replnoofbirds = $row['replnoofbirds'];
                            $a55_cageweight = $row['cageweight'];
                            $a56_netweight = $row['netweight'];
                            $a57_noc = $row['noc'];

                            $exist_pullval[$a1_companycode."$&@".$a2_schedulecode."$&@".$a3_isv_schedulecode."$&@".$a4_dc_no."$&@".$a5_mort_wt."$&@".$a6_repl_wt."$&@".$a7_totalweight."$&@".$a8_noofbirds."$&@".$a9_rate."$&@".$a10_amount."$&@".$a11_startdate."$&@".$a12_starttime."$&@".$a13_enddate."$&@".$a14_endtime."$&@".$a15_noofweighments."$&@".$a16_vehicle."$&@".$a17_scaleno."$&@".$a18_slipno."$&@".$a19_lotno."$&@".$a20_remark."$&@".$a21_drivername."$&@".$a22_vehsnap."$&@".$a23_errorcount."$&@".$a24_oknob."$&@".$a25_lamenob."$&@".$a26_feedstock."$&@".$a27_vehintime."$&@".$a28_vehouttime."$&@".$a29_abortcount."$&@".$a30_isv_supervisorcode."$&@".$a31_isv_tradercode."$&@".$a32_isv_farmercode."$&@".$a33_isv_sitecode."$&@".$a34_isv_schedulecode2."$&@".$a35_qty."$&@".$a36_rate2."$&@".$a37_amount2."$&@".$a38_companycode2."$&@".$a39_isv_tdepocode."$&@".$a40_isv_flockcode."$&@".$a41_isv_vechiclecode."$&@".$a42_isv_branchcode."$&@".$a43_returncode."$&@".$a44_companycode3."$&@".$a45_schedulecode2."$&@".$a46_isv_schedulecode3."$&@".$a47_dc_no2."$&@".$a48_srno."$&@".$a49_weight."$&@".$a50_noofbirds2."$&@".$a51_mortweight."$&@".$a52_mortnoofbirds."$&@".$a53_replweight."$&@".$a54_replnoofbirds."$&@".$a55_cageweight."$&@".$a56_netweight."$&@".$a57_noc] = $a1_companycode."$&@".$a2_schedulecode."$&@".$a3_isv_schedulecode."$&@".$a4_dc_no."$&@".$a5_mort_wt."$&@".$a6_repl_wt."$&@".$a7_totalweight."$&@".$a8_noofbirds."$&@".$a9_rate."$&@".$a10_amount."$&@".$a11_startdate."$&@".$a12_starttime."$&@".$a13_enddate."$&@".$a14_endtime."$&@".$a15_noofweighments."$&@".$a16_vehicle."$&@".$a17_scaleno."$&@".$a18_slipno."$&@".$a19_lotno."$&@".$a20_remark."$&@".$a21_drivername."$&@".$a22_vehsnap."$&@".$a23_errorcount."$&@".$a24_oknob."$&@".$a25_lamenob."$&@".$a26_feedstock."$&@".$a27_vehintime."$&@".$a28_vehouttime."$&@".$a29_abortcount."$&@".$a30_isv_supervisorcode."$&@".$a31_isv_tradercode."$&@".$a32_isv_farmercode."$&@".$a33_isv_sitecode."$&@".$a34_isv_schedulecode2."$&@".$a35_qty."$&@".$a36_rate2."$&@".$a37_amount2."$&@".$a38_companycode2."$&@".$a39_isv_tdepocode."$&@".$a40_isv_flockcode."$&@".$a41_isv_vechiclecode."$&@".$a42_isv_branchcode."$&@".$a43_returncode."$&@".$a44_companycode3."$&@".$a45_schedulecode2."$&@".$a46_isv_schedulecode3."$&@".$a47_dc_no2."$&@".$a48_srno."$&@".$a49_weight."$&@".$a50_noofbirds2."$&@".$a51_mortweight."$&@".$a52_mortnoofbirds."$&@".$a53_replweight."$&@".$a54_replnoofbirds."$&@".$a55_cageweight."$&@".$a56_netweight."$&@".$a57_noc;
                        }
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'nisanapps.com/npickweb/nxinfoservice.asmx?wsdl=null',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS =>'<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
                            <soapenv:Header/>
                            <soapenv:Body>
                                <tem:pullWeightments>
                                    <!--Optional:-->
                                    <tem:version>'.$psn_version.'</tem:version>
                                    <!--Optional:-->
                                    <tem:password>'.$psn_password.'</tem:password>
                                    <tem:companycode>'.$psn_company_code.'</tem:companycode>
                                    <!--Optional:-->
                                    <tem:fromdate>'.$date1.'</tem:fromdate>
                                    <!--Optional:-->
                                    <tem:todate>'.$date1.'</tem:todate>
                                </tem:pullWeightments>
                            </soapenv:Body>
                            </soapenv:Envelope>',
                            CURLOPT_HTTPHEADER => array(
                                'Content-Type: text/xml',
                                'charset: utf-8'
                            ),
                        ));

                        $response = curl_exec($curl);

                        curl_close($curl);
                        
                        $xml = file_get_contents($response);
                        $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
                        $xml = simplexml_load_string($xml);
                        $json = json_encode($xml);
                        if($json != ""){
                            $responseArray = json_decode($json,true);

                            $slno = 0;
                            foreach($responseArray as $r1){
                                foreach($r1 as $r2){
                                    foreach($r2 as $r3){
                                        foreach($r3 as $r4){
                                            foreach($r4 as $r5){
                                                $d1_companycode = $r5['companycode'];
                                                $d2_schedulecode = $r5['schedulecode'];
                                                if(is_array($r5['isv_schedulecode'])){ $d3_isv_schedulecode = implode("@",$r5['isv_schedulecode']); } else{ $d3_isv_schedulecode = $r5['isv_schedulecode']; }
                                                $d4_dc_no = $r5['dc_no'];
                                                $d5_mort_wt = $r5['mort_wt'];
                                                $d6_repl_wt = $r5['repl_wt'];
                                                $d7_totalweight = $r5['totalweight'];
                                                $d8_noofbirds = $r5['noofbirds'];
                                                $d9_rate = $r5['rate'];
                                                $d10_amount = $r5['amount'];
                                                $d11_startdate = date("Y-m-d",strtotime($r5['startdate']));
                                                $d12_starttime = $r5['starttime'];
                                                $d13_enddate = date("Y-m-d",strtotime($r5['enddate']));
                                                $d14_endtime = $r5['endtime'];
                                                $d15_noofweighments = $r5['noofweighments'];
                                                $d16_vehicle = $r5['vehicle'];
                                                $d17_scaleno = $r5['scaleno'];
                                                if(is_array($r5['slipno'])){ $d18_slipno = implode("@",$r5['slipno']); } else{ $d18_slipno = $r5['slipno']; }
                                                if(is_array($r5['lotno'])){ $d19_lotno = implode("@",$r5['lotno']); } else{ $d19_lotno = $r5['lotno']; }

                                                $d20_remark = $r5['remark'];
                                                $d21_drivername = $r5['drivername'];
                                                $d22_vehsnap = $r5['vehsnap'];
                                                $d23_errorcount = $r5['errorcount'];
                                                $d24_oknob = $r5['oknob'];
                                                $d25_lamenob = $r5['lamenob'];
                                                $d26_feedstock = $r5['feedstock'];
                                                $d27_vehintime = $r5['vehintime'];
                                                $d28_vehouttime = $r5['vehouttime'];
                                                $d29_abortcount = $r5['abortcount'];

                                                $d30_isv_supervisorcode = $r5['trsc_schedule']['isv_supervisorcode'];
                                                $d31_isv_tradercode = $r5['trsc_schedule']['isv_tradercode'];
                                                $d32_isv_farmercode = $r5['trsc_schedule']['isv_farmercode'];
                                                $d33_isv_sitecode = $r5['trsc_schedule']['isv_sitecode'];
                                                $d34_isv_schedulecode2 = $r5['trsc_schedule']['isv_schedulecode'];
                                                $d35_qty = $r5['trsc_schedule']['qty'];
                                                $d36_rate2 = $r5['trsc_schedule']['rate'];
                                                $d37_amount2 = $r5['trsc_schedule']['amount'];
                                                $d38_companycode2 = $r5['trsc_schedule']['companycode'];

                                                if(is_array($r5['trsc_schedule']['isv_tdepocode'])){ $d39_isv_tdepocode = implode("@",$r5['trsc_schedule']['isv_tdepocode']); } else{ $d39_isv_tdepocode = $r5['trsc_schedule']['isv_tdepocode']; }
                                                if(is_array($r5['trsc_schedule']['isv_flockcode'])){ $d40_isv_flockcode = implode("@",$r5['trsc_schedule']['isv_flockcode']); } else{ $d40_isv_flockcode = $r5['trsc_schedule']['isv_flockcode']; }
                                                if(is_array($r5['trsc_schedule']['isv_vechiclecode'])){ $d41_isv_vechiclecode = implode("@",$r5['trsc_schedule']['isv_vechiclecode']); } else{ $d41_isv_vechiclecode = $r5['trsc_schedule']['isv_vechiclecode']; }

                                                $d42_isv_branchcode = $r5['trsc_schedule']['isv_branchcode'];
                                                $d43_returncode = $r5['trsc_schedule']['ReturnCode'];

                                                foreach($r5['tdinfo'] as $r6){
                                                    foreach($r6 as $r7){
                                                        //foreach($r7 as $r8){ echo "<br/>".$r8; }
                                                        $d44_companycode3 = $r7['companycode'];
                                                        $d45_schedulecode2 = $r7['schedulecode'];
                                                        if(is_array($r7['isv_schedulecode'])){ $d46_isv_schedulecode3 = implode("@",$r7['isv_schedulecode']); } else{ $d46_isv_schedulecode3 = $r7['isv_schedulecode']; }

                                                        $d47_dc_no2 = $r7['dc_no'];
                                                        $d48_srno = $r7['srno'];
                                                        $d49_weight = $r7['weight'];
                                                        $d50_noofbirds2 = $r7['noofbirds'];
                                                        $d51_mortweight = $r7['mortweight'];
                                                        $d52_mortnoofbirds = $r7['mortnoofbirds'];
                                                        $d53_replweight = $r7['replweight'];
                                                        $d54_replnoofbirds = $r7['replnoofbirds'];
                                                        $d55_cageweight = $r7['cageweight'];
                                                        $d56_netweight = $r7['netweight'];
                                                        $d57_noc = $r7['NOC'];

                                                        $value = "";
                                                        $value = $d1_companycode."$&@".$d2_schedulecode."$&@".$d3_isv_schedulecode."$&@".$d4_dc_no."$&@".$d5_mort_wt."$&@".$d6_repl_wt."$&@".$d7_totalweight."$&@".$d8_noofbirds."$&@".$d9_rate."$&@".$d10_amount."$&@".$d11_startdate."$&@".$d12_starttime."$&@".$d13_enddate."$&@".$d14_endtime."$&@".$d15_noofweighments."$&@".$d16_vehicle."$&@".$d17_scaleno."$&@".$d18_slipno."$&@".$d19_lotno."$&@".$d20_remark."$&@".$d21_drivername."$&@".$d22_vehsnap."$&@".$d23_errorcount."$&@".$d24_oknob."$&@".$d25_lamenob."$&@".$d26_feedstock."$&@".$d27_vehintime."$&@".$d28_vehouttime."$&@".$d29_abortcount."$&@".$d30_isv_supervisorcode."$&@".$d31_isv_tradercode."$&@".$d32_isv_farmercode."$&@".$d33_isv_sitecode."$&@".$d34_isv_schedulecode2."$&@".$d35_qty."$&@".$d36_rate2."$&@".$d37_amount2."$&@".$d38_companycode2."$&@".$d39_isv_tdepocode."$&@".$d40_isv_flockcode."$&@".$d41_isv_vechiclecode."$&@".$d42_isv_branchcode."$&@".$d43_returncode."$&@".$d44_companycode3."$&@".$d45_schedulecode2."$&@".$d46_isv_schedulecode3."$&@".$d47_dc_no2."$&@".$d48_srno."$&@".$d49_weight."$&@".$d50_noofbirds2."$&@".$d51_mortweight."$&@".$d52_mortnoofbirds."$&@".$d53_replweight."$&@".$d54_replnoofbirds."$&@".$d55_cageweight."$&@".$d56_netweight."$&@".$d57_noc;
                                                        $flag = 0;
                                                        if(!empty($exist_pullval[$value])){
                                                            $flag = 1;
                                                        }
                                                        else{
                                                            $sql = "INSERT INTO `broiler_nisan_pullweightments_detailed` (companycode,schedulecode,isv_schedulecode,dc_no,mort_wt,repl_wt,totalweight,noofbirds,rate,amount,startdate,starttime,enddate,endtime,noofweighments,vehicle,scaleno,slipno,lotno,remark,drivername,vehsnap,errorcount,oknob,lamenob,feedstock,vehintime,vehouttime,abortcount,isv_supervisorcode,isv_tradercode,isv_farmercode,isv_sitecode,isv_schedulecode2,qty,rate2,amount2,companycode2,isv_tdepocode,isv_flockcode,isv_vechiclecode,isv_branchcode,returncode,companycode3,schedulecode2,isv_schedulecode3,dc_no2,srno,weight,noofbirds2,mortweight,mortnoofbirds,replweight,replnoofbirds,cageweight,netweight,noc,addedemp,addedtime,updatedemp) 
                                                            VALUES ('$d1_companycode','$d2_schedulecode','$d3_isv_schedulecode','$d4_dc_no','$d5_mort_wt','$d6_repl_wt','$d7_totalweight','$d8_noofbirds','$d9_rate','$d10_amount','$d11_startdate','$d12_starttime','$d13_enddate','$d14_endtime','$d15_noofweighments','$d16_vehicle','$d17_scaleno','$d18_slipno','$d19_lotno','$d20_remark','$d21_drivername','$d22_vehsnap','$d23_errorcount','$d24_oknob','$d25_lamenob','$d26_feedstock','$d27_vehintime','$d28_vehouttime','$d29_abortcount','$d30_isv_supervisorcode','$d31_isv_tradercode','$d32_isv_farmercode','$d33_isv_sitecode','$d34_isv_schedulecode2','$d35_qty','$d36_rate2','$d37_amount2','$d38_companycode2','$d39_isv_tdepocode','$d40_isv_flockcode','$d41_isv_vechiclecode','$d42_isv_branchcode','$d43_returncode','$d44_companycode3','$d45_schedulecode2','$d46_isv_schedulecode3','$d47_dc_no2','$d48_srno','$d49_weight','$d50_noofbirds2','$d51_mortweight','$d52_mortnoofbirds','$d53_replweight','$d54_replnoofbirds','$d55_cageweight','$d56_netweight','$d57_noc','$addedemp','$addedtime','$addedtime')";
                                                            if(!mysqli_query($conn, $sql)){ $flag = 2; echo "<br/>".mysqli_error($conn); } else{ $flag = 0; }
                                                        }
                                                        $slno++;
                                                        ?>
                                                        <tr>
                                                            <td title="Sl.No."><?php echo $slno; ?></td>
                                                            <td title="companycode"><?php echo $d1_companycode; ?></td>
                                                            <td title="schedulecode"><?php echo $d2_schedulecode; ?></td>
                                                            <td title="isv_schedulecode"><?php echo $d3_isv_schedulecode; ?></td>
                                                            <td title="dc_no"><?php echo $d4_dc_no; ?></td>
                                                            <td title="mort_wt"><?php echo $d5_mort_wt; ?></td>
                                                            <td title="repl_wt"><?php echo $d6_repl_wt; ?></td>
                                                            <td title="totalweight"><?php echo $d7_totalweight; ?></td>
                                                            <td title="noofbirds"><?php echo $d8_noofbirds; ?></td>
                                                            <td title="rate"><?php echo $d9_rate; ?></td>
                                                            <td title="amount"><?php echo $d10_amount; ?></td>
                                                            <td title="startdate"><?php echo $d11_startdate; ?></td>
                                                            <td title="starttime"><?php echo $d12_starttime; ?></td>
                                                            <td title="enddate"><?php echo $d13_enddate; ?></td>
                                                            <td title="endtime"><?php echo $d14_endtime; ?></td>
                                                            <td title="noofweighments"><?php echo $d15_noofweighments; ?></td>
                                                            <td title="vehicle"><?php echo $d16_vehicle; ?></td>
                                                            <td title="scaleno"><?php echo $d17_scaleno; ?></td>
                                                            <td title="slipno"><?php echo $d18_slipno; ?></td>
                                                            <td title="lotno"><?php echo $d19_lotno; ?></td>
                                                            <td title="remark"><?php echo $d20_remark; ?></td>
                                                            <td title="drivername"><?php echo $d21_drivername; ?></td>
                                                            <td title="vehsnap"><?php echo $d22_vehsnap; ?></td>
                                                            <td title="errorcount"><?php echo $d23_errorcount; ?></td>
                                                            <td title="oknob"><?php echo $d24_oknob; ?></td>
                                                            <td title="lamenob"><?php echo $d25_lamenob; ?></td>
                                                            <td title="feedstock"><?php echo $d26_feedstock; ?></td>
                                                            <td title="vehintime"><?php echo $d27_vehintime; ?></td>
                                                            <td title="vehouttime"><?php echo $d28_vehouttime; ?></td>
                                                            <td title="abortcount"><?php echo $d29_abortcount; ?></td>
                                                            <td title="isv_supervisorcode"><?php echo $d30_isv_supervisorcode; ?></td>
                                                            <td title="isv_tradercode"><?php echo $d31_isv_tradercode; ?></td>
                                                            <td title="isv_farmercode"><?php echo $d32_isv_farmercode; ?></td>
                                                            <td title="isv_sitecode"><?php echo $d33_isv_sitecode; ?></td>
                                                            <td title="isv_schedulecode2"><?php echo $d34_isv_schedulecode2; ?></td>
                                                            <td title="qty"><?php echo $d35_qty; ?></td>
                                                            <td title="rate2"><?php echo $d36_rate2; ?></td>
                                                            <td title="amount2"><?php echo $d37_amount2; ?></td>
                                                            <td title="companycode2"><?php echo $d38_companycode2; ?></td>
                                                            <td title="isv_tdepocode"><?php echo $d39_isv_tdepocode; ?></td>
                                                            <td title="isv_flockcode"><?php echo $d40_isv_flockcode; ?></td>
                                                            <td title="isv_vechiclecode"><?php echo $d41_isv_vechiclecode; ?></td>
                                                            <td title="isv_branchcode"><?php echo $d42_isv_branchcode; ?></td>
                                                            <td title="returncode"><?php echo $d43_returncode; ?></td>
                                                            <td title="companycode3"><?php echo $d44_companycode3; ?></td>
                                                            <td title="schedulecode2"><?php echo $d45_schedulecode2; ?></td>
                                                            <td title="isv_schedulecode3"><?php echo $d46_isv_schedulecode3; ?></td>
                                                            <td title="dc_no2"><?php echo $d47_dc_no2; ?></td>
                                                            <td title="srno"><?php echo $d48_srno; ?></td>
                                                            <td title="weight"><?php echo $d49_weight; ?></td>
                                                            <td title="noofbirds2"><?php echo $d50_noofbirds2; ?></td>
                                                            <td title="mortweight"><?php echo $d51_mortweight; ?></td>
                                                            <td title="mortnoofbirds"><?php echo $d52_mortnoofbirds; ?></td>
                                                            <td title="replweight"><?php echo $d53_replweight; ?></td>
                                                            <td title="replnoofbirds"><?php echo $d54_replnoofbirds; ?></td>
                                                            <td title="cageweight"><?php echo $d55_cageweight; ?></td>
                                                            <td title="netweight"><?php echo $d56_netweight; ?></td>
                                                            <td title="noc"><?php echo $d57_noc; ?></td>
                                                            <?php
                                                            if($flag == 1){
                                                                echo '<td style="color:blue;font-weight:bold;">Data Exist</td>';
                                                            }
                                                            else if($flag == 0){
                                                                echo '<td style="color:green;font-weight:bold;">Data Inserted</td>';
                                                            }
                                                            else if($flag == 2){
                                                                echo '<td style="color:red;font-weight:bold;">Error Inserting</td>';
                                                            }
                                                            else{ }
                                                            ?>
                                                        </tr>
                                                        <?php
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        else{
                            echo '<tr><td colspan="58">No Records Found</td></tr>';
                        }
                    }
                }
            ?>
        </table>
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
                    span_elem.style = "font-size:0.6rem; margin-left:0.5rem";
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
                    asc = !asc;
                    })
                });
            }

            table_sort();
            table_sort2();
            table_sort3();
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>