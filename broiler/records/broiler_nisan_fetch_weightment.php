<?php
//broiler_nisan_fetch_weightment.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

include "header_head.php";

$sql='SHOW COLUMNS FROM `broiler_sales`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("scale_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `scale_no` VARCHAR(300) NULL DEFAULT NULL AFTER `driver_code`"; mysqli_query($conn,$sql); }
if(in_array("nisan_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `nisan_flag` int(100) NOT NULL DEFAULT '0' AFTER `dflag`"; mysqli_query($conn,$sql); }

$fdate = $tdate = date("Y-m-d");
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_REQUEST['fdate']));
    $tdate = date("Y-m-d",strtotime($_REQUEST['tdate']));
    $status = $_POST['status'];
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
        <link rel="stylesheet" type="text/css" href="../loading_screen.css">
        <?php
            if($excel_type == "print"){
                echo '<style>body { padding:10px;text-align:center; }
                .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .thead1 { background-image: linear-gradient(#D5D8DC,#D5D8DC); box-shadow: 0px 0px 10px #EAECEE; }
                .thead2 { display:none;background-image: linear-gradient(#D5D8DC,#D5D8DC); }
                .thead2_empty_row { display:none; }
                .thead3 { background-image: linear-gradient(#ABB2B9,#ABB2B9); }
                .thead4 { background-image: linear-gradient(#D5D8DC,#D5D8DC); }
                .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
                .report_head { background-image: linear-gradient(#ABB2B9,#ABB2B9); }
                .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); font-weight:bold; }</style>';
            }
            else{
                echo '<style>body { left:0;width:auto;overflow:auto; } table { white-space: nowrap; }
                table.tbl { left:0;margin-right: auto;visibility:visible; }
                table.tbl2 { left:0;margin-right: auto; }
                .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .thead1 { background-image: linear-gradient(#D5D8DC,#D5D8DC); box-shadow: 0px 0px 10px #EAECEE; }
                .thead2 { background-image: linear-gradient(#D5D8DC,#D5D8DC); }
                .thead3 { background-image: linear-gradient(#ABB2B9,#ABB2B9); }
                .thead4 { background-image: linear-gradient(#D5D8DC,#D5D8DC); }
                .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
                .report_head { background-image: linear-gradient(#ABB2B9,#ABB2B9); }
                .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); }</style>';
                
            }
        ?>
        <style>
            td .form-control{
                padding: 0;
                padding-left:2px;
                text-decoration: none;
                border: none;
                //background: inherit;
            }
        </style>
    </head>
    <body align="center">
        <table class="tbl" align="center">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'purchases Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" style="width:1212px;">
                <tr align="center">
                    <td colspan="7" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="10" align="center"><?php echo $row['cdetails']; ?><h5>Synchronize Nisan Sales</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_nisan_fetch_weightment.php" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:1212px;">
                    <tr>
                        <th colspan="17">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" readonly />
                                </div>
                                <div class="m-2 form-group">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" readonly />
                                </div>
                                <div class="m-2 form-group">
                                    <br/>
                                    <button type="submit" name="submit_report" id="submit_report" class="btn btn-sm btn-success">Fetch</button>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
            </form>
            <?php
            if(isset($_POST['submit_report']) == true){
                $sql = "SELECT * FROM `broiler_nisan_credentials` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $psnc_count = mysqli_num_rows($query);
                if($psnc_count > 0){
                    while($row = mysqli_fetch_array($query)){ $psn_version = $row['version']; $psn_company_code = $row['company_code']; $psn_password = $row['password']; }
                    //$psn_version = "4.0"; $psn_company_code = "1209"; $psn_password = "srfNx@1";

                    $sql = "SELECT * FROM `broiler_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`billno` ASC"; $exist_dt = $exist_customer = $exist_supervisor = array();
                    $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $key = ""; $key = $row['date']."@".$row['billno']."@".round($row['birds'],5)."@".round($row['rcd_qty'],5); $exist_dt[$key] = $key; $exist_customer[$key] = $row['vcode']; $exist_farm[$key] = $row['warehouse']; $exist_rate[$key] = $row['rate']; $exist_amount[$key] = $row['item_tamt']; $exist_supervisor[$key] = $row['supervisor_code']; }

                    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `dflag` = '0' ORDER BY `name` ASC"; $cus_code = $cus_name = $cus_name1 = array();
                    $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; $cus_name1[ucwords(strtolower($row['name']))] = $row['code']; }

                    $sql = "SELECT * FROM `broiler_employee` WHERE `dflag` = '0' ORDER BY `name` ASC"; $emp_code = $emp_name = $emp_name1 = array();
                    $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; $emp_name1[ucwords(strtolower($row['name']))] = $row['code']; }

                    $sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0' ORDER BY `description` ASC"; $farm_code = $farm_name = $farm_name1 = array();
                    $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $farm_code[$row['code']] = $row['code']; $farm_name[$row['code']] = $row['description']; $farm_name1[ucwords(strtolower($row['description']))] = $row['code']; }
                    
                    //getTraders
                    //echo "<b>Traders</b>";
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
                             <tem:status></tem:status>
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
                    
                    $responseArray = array();
                    $xml = file_get_contents($response);
                    $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
                    $xml = simplexml_load_string($xml);
                    $json = json_encode($xml);
                    $responseArray = json_decode($json,true);

                    $psn_cus_code = $psn_cus_name = array();
                    foreach($responseArray as $r1){
                        foreach($r1 as $r2){
                            foreach($r2 as $r3){
                                foreach($r3 as $r4){
                                    foreach($r4 as $r5){
                                        $psn_cus_code[$r5['isv_tradercode']] = $r5['isv_tradercode'];
                                        $psn_cus_name[$r5['isv_tradercode']] = $r5['tradername'];
                                        //echo "<br/>".$r5['isv_tradercode']."@".$r5['tradername'];
                                    }
                                }
                            }
                        }
                    }

                    //Get Farmer
                    //echo "<br/><b>Farmer</b>";
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
                            <tem:status></tem:status>
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
                    //echo $response;

                    $responseArray = array();
                    $xml = file_get_contents($response);
                    $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
                    $xml = simplexml_load_string($xml);
                    $json = json_encode($xml);
                    $responseArray = json_decode($json,true);

                    $psn_farm_code = $psn_farm_name = array();
                    foreach($responseArray as $r1){
                        foreach($r1 as $r2){
                            foreach($r2 as $r3){
                                foreach($r3 as $r4){
                                    foreach($r4 as $r5){
                                        $psn_farm_code[$r5['isv_farmercode']] = $r5['isv_farmercode'];
                                        $psn_farm_name[$r5['isv_farmercode']] = $r5['farmername'];
                                        //echo "<br/>".$r5['isv_farmercode']."@".$r5['farmername'];
                                    }
                                }
                            }
                        }
                    }

                    //Pull Weightment By Dates
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
                            <tem:fromdate>'.date("y-m-d",strtotime($fdate)).'</tem:fromdate>
                            <!--Optional:-->
                            <tem:todate>'.date("y-m-d",strtotime($tdate)).'</tem:todate>
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

                    $responseArray = array();
                    $xml = file_get_contents($response);
                    $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
                    $xml = simplexml_load_string($xml);
                    $json = json_encode($xml);
                    $responseArray = json_decode($json,true);

                    $i = 0; $html = '';
                    foreach($responseArray as $r1){
                        foreach($r1 as $r2){
                            foreach($r2 as $r3){
                                foreach($r3 as $r4){
                                    foreach($r4 as $r5){
                                        $companycode = $r5['companycode'];
                                        $schedulecode = $r5['schedulecode'];
                                        $isv_schedulecode = $r5['isv_schedulecode'];
                                        if(is_array($r5['dc_no'])){ $dc_no = ""; } else{ $dc_no = $r5['dc_no']; }
                                        $mort_wt = $r5['mort_wt'];
                                        $repl_wt = $r5['repl_wt'];
                                        $totalweight = $r5['totalweight'];
                                        $noofbirds = $r5['noofbirds'];
                                        $rate = $r5['rate'];
                                        $amount = $r5['amount'];
                                        $startdate = $r5['startdate'];
                                        $starttime = $r5['starttime'];
                                        $enddate = $r5['enddate'];
                                        $endtime = $r5['endtime'];
                                        $noofweighments = $r5['noofweighments'];
                                        $vehicle = $r5['vehicle'];
                                        $scaleno = $r5['scaleno'];
                                        if(is_array($r5['slipno'])){ $slipno = ""; } else{ $slipno = $r5['slipno']; }
                                        $lotno = $r5['lotno'];
                                        $remark = $r5['remark'];
                                        $drivername = $r5['drivername'];
                                        $vehsnap = $r5['vehsnap'];
                                        $errorcount = $r5['errorcount'];
                                        $oknob = $r5['oknob'];
                                        $lamenob = $r5['lamenob'];
                                        $feedstock = $r5['feedstock'];
                                        $vehintime = $r5['vehintime'];
                                        $vehouttime = $r5['vehouttime'];
                                        $abortcount = $r5['abortcount'];

                                        $isv_supervisorcode = $r5['trsc_schedule']['isv_supervisorcode'];
                                        $isv_tradercode = $r5['trsc_schedule']['isv_tradercode'];
                                        $isv_farmercode = $r5['trsc_schedule']['isv_farmercode'];
                                        $isv_sitecode = $r5['trsc_schedule']['isv_sitecode'];
                                        $psnl2_isv_schedulecode = $r5['trsc_schedule']['isv_schedulecode'];
                                        $qty = $r5['trsc_schedule']['qty'];
                                        $psnl2_rate = $r5['trsc_schedule']['rate'];
                                        $psnl2_amount = $r5['trsc_schedule']['amount'];
                                        $psnl2_companycode = $r5['trsc_schedule']['companycode'];
                                        $isv_tdepocode = $r5['trsc_schedule']['isv_tdepocode'];
                                        $isv_flockcode = $r5['trsc_schedule']['isv_flockcode'];
                                        $isv_vechiclecode = $r5['trsc_schedule']['isv_vechiclecode'];
                                        $isv_branchcode = $r5['trsc_schedule']['isv_branchcode'];
                                        $ReturnCode = $r5['trsc_schedule']['ReturnCode'];

                                        $key = ""; $key = date("Y-m-d",strtotime($startdate))."@".$slipno."@".$noofbirds."@".$totalweight;

                                        $i++;
                                        if(!empty($exist_rate[$key]) && $exist_rate[$key] != ""){ $rate = $exist_rate[$key]; $amount = $exist_amount[$key]; }

                                        $html .= '<tr>';
                                        $html .= '<td style="text-align:center;">'.$i.'</td>';
                                        if(!empty($exist_dt[$key]) && $exist_dt[$key] == $key){
                                            $html .= '<td style="text-align:center;visibility:hidden;"><input type="checkbox" name="slno[]" id="slno['.$i.']" value="'.$i.'" /></td>';
                                        }
                                        else{
                                            $html .= '<td style="text-align:center;"><input type="checkbox" name="slno[]" id="slno['.$i.']" value="'.$i.'" checked /></td>';
                                        }
                                        $html .= '<td><input type="text" name="date[]" id="date['.$i.']" class="form-control" value="'.date("d.m.Y",strtotime($startdate)).'" style="width:85px;" readonly /></td>';

                                        $vname = ""; $vname = ucwords(strtolower($psn_cus_name[$isv_tradercode]));
                                        if(!empty($cus_name1[$vname]) && $cus_name1[$vname] != ""){
                                            $html .= '<td><select name="vcode[]" id="vcode['.$i.']" class="form-control select2" style="width:210px;">';
                                            $html .= '<option value="'.$cus_name1[$vname].'">'.$vname.' ('.$cus_name1[$vname].')</option>';
                                            $html .= '</select></td>';
                                        }
                                        else if(!empty($exist_dt[$key]) && $exist_dt[$key] == $key){
                                            echo "<br/>".$i.") Customer Name Not Matching with Nisan: ".$vname;
                                            $html .= '<td><select name="vcode[]" id="vcode['.$i.']" class="form-control select2" style="width:210px;">';
                                            $html .= '<option value="'.$exist_customer[$key].'">'.$cus_name[$exist_customer[$key]].'</option>';
                                            $html .= '</select></td>';
                                        }
                                        else{
                                            echo "<br/>".$i.") Customer Name Not Matching with Nisan: ".$vname;
                                            $html .= '<td><select name="vcode[]" id="vcode['.$i.']" class="form-control select2" style="width:210px;"><option value="select">-select-</option>';
                                            foreach($cus_code as $vcode){ $html .= '<option value="'.$vcode.'">'.$cus_name[$vcode].'</option>'; }
                                            $html .= '</select></td>';
                                        }
                                        
                                        $html .= '<td><input type="text" name="billno[]" id="billno['.$i.']" class="form-control" value="'.$slipno.'" style="width:90px;" readonly /></td>';
                                        $html .= '<td><input type="text" name="birds[]" id="birds['.$i.']" class="form-control text-right" value="'.round($noofbirds,5).'" style="width:90px;" readonly /></td>';
                                        $html .= '<td><input type="text" name="weight[]" id="weight['.$i.']" class="form-control text-right" value="'.round($totalweight,5).'" style="width:90px;" readonly /></td>';
                                        if(!empty($exist_dt[$key]) && $exist_dt[$key] == $key){
                                            $html .= '<td><input type="text" name="rate[]" id="rate['.$i.']" class="form-control text-right" value="'.round($rate,5).'" style="width:90px;" onkeyup="calculate_amt(this.id);" readonly /></td>';
                                        }
                                        else{
                                            $html .= '<td><input type="text" name="rate[]" id="rate['.$i.']" class="form-control text-right" value="'.round($rate,5).'" style="width:90px;" onkeyup="validatenums(this.id);calculate_amt(this.id);" /></td>';
                                        }
                                        $html .= '<td><input type="text" name="amount[]" id="amount['.$i.']" class="form-control text-right" value="'.round($amount,5).'" style="width:90px;" readonly /></td>';
                                        $html .= '<td><input type="text" name="vehicle[]" id="vehicle['.$i.']" class="form-control" value="'.$vehicle.'" style="width:90px;" readonly /></td>';
                                        $html .= '<td><input type="text" name="driver[]" id="driver['.$i.']" class="form-control" value="'.$drivername.'" style="width:90px;" readonly /></td>';
                                        $html .= '<td><input type="text" name="scale[]" id="scale['.$i.']" class="form-control" value="'.$scaleno.'" style="width:90px;" readonly /></td>';
                                        $html .= '<td><input type="text" name="branch[]" id="branch['.$i.']" class="form-control" value="'.$isv_branchcode.'" style="width:110px;" readonly /></td>';
                                        $html .= '<td><input type="text" name="line[]" id="line['.$i.']" class="form-control" value="'.$isv_sitecode.'" style="width:110px;" readonly /></td>';
                                                                                
                                        $sname = ""; $sname = ucwords(strtolower($isv_supervisorcode));
                                        if(!empty($emp_name1[$sname]) && $emp_name1[$sname] != ""){
                                            $html .= '<td><select name="supervisor[]" id="supervisor['.$i.']" class="form-control select2" style="width:210px;">';
                                            $html .= '<option value="'.$emp_name1[$sname].'">'.$sname.' ('.$emp_name1[$sname].')</option>';
                                            $html .= '</select></td>';
                                        }
                                        else if(!empty($exist_dt[$key]) && $exist_dt[$key] == $key){
                                            echo "<br/>".$i.") Supervisor Name Not Matching with Nisan: ".$sname;
                                            $html .= '<td><select name="supervisor[]" id="supervisor['.$i.']" class="form-control select2" style="width:210px;">';
                                            $html .= '<option value="'.$exist_supervisor[$key].'">'.$emp_name[$exist_supervisor[$key]].'</option>';
                                            $html .= '</select></td>';
                                        }
                                        else{
                                            echo "<br/>".$i.") Supervisor Name Not Matching with Nisan: ".$sname;
                                            $html .= '<td><select name="supervisor[]" id="supervisor['.$i.']" class="form-control select2" style="width:210px;"><option value="select">-select-</option>';
                                            foreach($emp_code as $ecode){ $html .= '<option value="'.$ecode.'">'.$emp_name[$ecode].'</option>'; }
                                            $html .= '</select></td>';
                                        }
                                        
                                        $fname = ""; $fname = ucwords(strtolower($psn_farm_name[$isv_farmercode]));
                                        if(!empty($farm_name1[$fname]) && $farm_name1[$fname] != ""){
                                            $html .= '<td><select name="farm[]" id="farm['.$i.']" class="form-control select2" style="width:210px;">';
                                            $html .= '<option value="'.$farm_name1[$fname].'">'.$fname.' ('.$farm_name1[$fname].')</option>';
                                            $html .= '</select></td>';
                                        }
                                        else if(!empty($exist_dt[$key]) && $exist_dt[$key] == $key){
                                            echo "<br/>".$i.") Farm Name Not Matching with Nisan: ".$fname;
                                            $html .= '<td><select name="farm[]" id="farm['.$i.']" class="form-control select2" style="width:210px;">';
                                            $html .= '<option value="'.$exist_farm[$key].'">'.$farm_name[$exist_farm[$key]].'</option>';
                                            $html .= '</select></td>';
                                        }
                                        else{
                                            echo "<br/>".$i.") Farm Name Not Matching with Nisan: ".$fname;
                                            $html .= '<td><select name="farm[]" id="farm['.$i.']" class="form-control select2" style="width:210px;"><option value="select">-select-</option>';
                                            foreach($farm_code as $fcode){ $html .= '<option value="'.$fcode.'">'.$farm_name[$fcode].'</option>'; }
                                            $html .= '</select></td>';
                                        }

                                        if(!empty($exist_dt[$key]) && $exist_dt[$key] == $key){
                                            $html .= '<td style="padding:0; text-align:center;"><input type="text" name="ext_status[]" id="ext_status['.$i.']" class="form-control" value="Data Exist" style="width:110px;color:green;" readonly /></td>';
                                        }
                                        else{
                                            $html .= '<td style="padding:0; text-align:center;"><input type="text" name="ext_status[]" id="ext_status['.$i.']" class="form-control" value="Data Not Exist" style="width:110px;color:red;" readonly /></td>';
                                        }
                                        $html .= '</tr>';

                                        $total_noofbirds += $noofbirds;
                                        $total_totalweight += $totalweight;
                                        $total_rate += $rate;
                                        $total_amount += $amount;

                                        /*if($i == 1){
                                            echo "<br/>".$i."<br/>Company Code:".$companycode."<br/>Schedule Code:".$schedulecode."<br/>Schedule Code-2:".$isv_schedulecode."<br/>DC NO:".$dc_no."<br/>Mort Wt:".$mort_wt."<br/>Repl Wt:".$repl_wt."<br/>Total Weight:".$totalweight."<br/>No. of Birds:".$noofbirds."<br/>Rate:".$rate."<br/>Amount:".$amount."<br/>Start Date:".$startdate."<br/>Start Time:".$starttime."<br/>End Date:".$enddate."<br/>End Time:".$endtime."<br/>Total Weightment:".$noofweighments."<br/>Vehicle:".$vehicle."<br/>Scale:".$scaleno."<br/>Slip No:".$slipno."<br/>Lot No:".$lotno."<br/>Remark:".$remark."<br/>Driver:".$drivername."<br/>Vehsnap:".$vehsnap."<br/>Error Count:".$errorcount."<br/>OkNob:".$oknob."<br/>Lamenob:".$lamenob."<br/>Feed Stock:".$feedstock."<br/>VehInTime:".$vehintime."<br/>VehOutTime:".$vehouttime."<br/>Abort Count:".$abortcount;
                                            echo "<br/>Supervisor: ".$isv_supervisorcode."<br/>Trader: ".$isv_tradercode."<br/>Farmer: ".$isv_farmercode."<br/>Site: ".$isv_sitecode."<br/>Schedule Code: ".$psnl2_isv_schedulecode."<br/>Quantity: ".$qty."<br/>Rate: ".$psnl2_rate."<br/>Amount: ".$psnl2_amount."<br/>Company Code: ".$psnl2_companycode."<br/>TDEPO Code: ".$isv_tdepocode."<br/>Flock: ".$isv_flockcode."<br/>Vehicle: ".$isv_vechiclecode."<br/>Branch: ".$isv_branchcode."<br/>Return Code: ".$ReturnCode;
                                        }
                                        $j = 0;
                                        foreach($r5['tdinfo'] as $r6){
                                            foreach($r6 as $r7){
                                                $j++;
                                                $key = $i."@$&".$j;
                                                $psnl3_companycode[$key] = $r7['companycode'];
                                                $psnl3_schedulecode[$key] = $r7['schedulecode'];
                                                $psnl3_isv_schedulecode[$key] = $r7['isv_schedulecode'];
                                                $psnl3_dc_no[$key] = $r7['dc_no'];
                                                $srno[$key] = $r7['srno'];
                                                $weight[$key] = $r7['weight'];
                                                $psnl3_noofbirds[$key] = $r7['noofbirds'];
                                                $mortweight[$key] = $r7['mortweight'];
                                                $mortnoofbirds[$key] = $r7['mortnoofbirds'];
                                                $replweight[$key] = $r7['replweight'];
                                                $replnoofbirds[$key] = $r7['replnoofbirds'];
                                                $cageweight[$key] = $r7['cageweight'];
                                                $netweight[$key] = $r7['netweight'];
                                                $NOC[$key] = $r7['NOC'];

                                                //echo "<br/>".$k."--".$psnl3_companycode[$key]."@".$psnl3_schedulecode[$key]."@".$psnl3_isv_schedulecode[$key]."@".$psnl3_dc_no[$key]."@".$srno[$key]."@".$weight[$key]."@".$psnl3_noofbirds[$key]."@".$mortweight[$key]."@".$mortnoofbirds[$key]."@".$replweight[$key]."@".$replnoofbirds[$key]."@".$cageweight[$key]."@".$netweight[$key]."@".$NOC[$key];
                                            }
                                        }*/
                                    }
                                }
                            }
                        }
                    }



                }
            ?>
            <tbody class="tbody1">
                <tr>
                    <th colspan="17">
                        <div class="p-2 row">
                            <div class="form-group">
                                <label for="mst_rate">Rate:</label>
                                <input type="text" name="mst_rate" id="mst_rate" class="form-control" value="" style="padding:0;padding-left:2px;width:100px;" onkeyup="update_rates();"/>
                            </div>
                            <div class="form-group">
                                <label for="mst_rate">Select All:</label>
                                <input type="checkbox" name="select_all" id="select_all" class="form-control" style="transform: scale(.5);" onclick="update_chkboxes();" />
                            </div>
                        </div>
                    </th>
                </tr>
                <tr>
                    <th>Sl.No.</th>
                    <th>Select</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Slip No.</th>
                    <th>Birds</th>
                    <th>Weight</th>
                    <th>Rate</th>
                    <th>Amount</th>
                    <th>Vehicle</th>
                    <th>Driver</th>
                    <th>Scale No</th>
                    <th>Branch</th>
                    <th>Line</th>
                    <th>Supervisor</th>
                    <th>Farm</th>
                    <th>Status</th>
                </tr>
                <?php echo $html; ?>
                <tr >
                    <th colspan="5" style="text-align:center;padding:0; font-size: medium;">Total</th>
                    <th style="text-align:right;font-size: medium;"><?php echo number_format_ind(round($total_noofbirds,2)); ?></th>
                    <th style="text-align:right;font-size: medium;"><?php echo number_format_ind(round($total_totalweight,2)); ?></th>
                    <th colspan="10" style="text-align:right;"></th>
                    
                </tr>
                <tr>
                    <td><input type="text" name="ebtncount" id="ebtncount" class="form-control" value="0" style="padding:0;padding-left:2px;width:50px;" readonly /></td>
                    <td colspan="15"><button type="button" name="submit" id="submit" class="btn btn-sm btn-success" onclick="checkval()">Submit</button></td>
                    <td><input type="text" name="incr" id="incr" class="form-control" value="<?php echo $i; ?>" style="padding:0;padding-left:2px;width:50px;" readonly /></td>
                </tr>
            </tbody>
            <?php
            }
            ?>
        </table><br/><br/><br/>
        <div class="ring">Loading<span></span></div>
		<div class="ring_status" id = "disp_val"></div>
        <script>
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var incr = document.getElementById("incr").value;
                if(parseInt(incr) > 0){
                    var slno = ext_status = date = vcode = billno = vehicle = driver = scale = supervisor = farm = ""; var d = birds = weight = rate = amount = 0; var l = true;
                    for(d = 1;d <= incr;d++){
                        if(l == true){
                            slno = document.getElementById("slno["+d+"]");
                            ext_status = document.getElementById("ext_status["+d+"]").value;

                            if(slno.checked == true && ext_status == "Data Not Exist"){
                                vcode = document.getElementById("vcode["+d+"]").value;
                                supervisor = document.getElementById("supervisor["+d+"]").value;
                                farm = document.getElementById("farm["+d+"]").value;
                                /*rate = document.getElementById("rate["+d+"]").value; if(rate == ""){ rate = 0; }
                                amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }*/

                                if(vcode == "" || vcode == "select"){
                                    alert("Please select Customer in row: "+d);
                                    document.getElementById("vcode["+d+"]").focus();
                                    l = false;
                                }
                                /*else if(parseFloat(rate) == 0){
                                    alert("Please enter Rate in row: "+d);
                                    document.getElementById("rate["+d+"]").focus();
                                    l = false;
                                }
                                else if(parseFloat(amount) == 0){
                                    alert("Please enter Rate in row: "+d);
                                    document.getElementById("rate["+d+"]").focus();
                                    l = false;
                                }*/
                                else if(supervisor == "" || supervisor == "select"){
                                    alert("Please select Supervisor in row: "+d);
                                    document.getElementById("supervisor["+d+"]").focus();
                                    l = false;
                                }
                                else if(farm == "" || farm == "select"){
                                    alert("Please select Farm in row: "+d);
                                    document.getElementById("farm["+d+"]").focus();
                                    l = false;
                                }
                                else{ }
                            }
                        }
                    }
                    if(l == true){
                        document.getElementsByClassName("ring")[0].style.display = "block";
                        document.getElementsByClassName("ring_status")[0].style.display = "block";
                        document.getElementById("disp_val").innerHTML = 'Getting Information';
                        rslt = 0;
                        for(d = 1;d <= incr;d++){
                            slno = document.getElementById("slno["+d+"]");
                            ext_status = document.getElementById("ext_status["+d+"]").value;

                            if(slno.checked == true && ext_status == "Data Not Exist"){
                                document.getElementById("disp_val").innerHTML = 'Getting Information from row: '+d;
                                date = document.getElementById("date["+d+"]").value;
                                vcode = document.getElementById("vcode["+d+"]").value;
                                billno = document.getElementById("billno["+d+"]").value;
                                birds = document.getElementById("birds["+d+"]").value;
                                weight = document.getElementById("weight["+d+"]").value;
                                rate = document.getElementById("rate["+d+"]").value; if(rate == ""){ rate = 0; }
                                amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }
                                vehicle = document.getElementById("vehicle["+d+"]").value;
                                driver = document.getElementById("driver["+d+"]").value;
                                scale = document.getElementById("scale["+d+"]").value;
                                supervisor = document.getElementById("supervisor["+d+"]").value;
                                farm = document.getElementById("farm["+d+"]").value;

                                document.getElementById("disp_val").innerHTML = 'Saving from row: '+d;
                                var insert_data = new XMLHttpRequest();
                                var method = "GET";
                                var url = "broiler_save_nisan_sales.php?date="+date+"&vcode="+vcode+"&billno="+billno+"&birds="+birds+"&weight="+weight+"&rate="+rate+"&amount="+amount+"&vehicle="+vehicle+"&driver="+driver+"&scale="+scale+"&supervisor="+supervisor+"&farm="+farm+"&row_no="+d;
                                //window.open(url);
                                var asynchronous = true;
                                insert_data.open(method, url, asynchronous);
                                insert_data.send();
                                insert_data.onreadystatechange = function(){
                                    if(this.readyState == 4 && this.status == 200){
                                        var data1 = this.responseText;
                                        var data2 = data1.split("@");
                                        var error_flag = data2[0];
                                        var error_msg = data2[1];
                                        var row_no = data2[2];
                                        if(error_flag > 0){
                                            document.getElementById("disp_val").innerHTML = 'Error Status: row-'+row_no+', '+error_msg;
                                        }
                                        else{
                                            document.getElementById("disp_val").innerHTML = 'Status: row-'+row_no+', Data Saved Successfully'; 
                                        }
                                    }
                                }
                            }
                        }
                        function checkFlag() {
                            if(parseInt(d) < parseInt(incr)) {
                                window.setTimeout(checkFlag, 100);
                            }
                            else {
                                document.getElementById("disp_val").innerHTML = 'Data Saved Successfully';
                                const myInterval = setInterval(myTimer, 2000);
                                function myTimer() {
                                    const date = new Date();
                                    document.getElementById("disp_val").innerHTML = 'Redirecting Page';
                                    clearInterval(myInterval);
                                    window.location.href = "broiler_nisan_fetch_weightment.php";
                                }
                            }
                        }
                        checkFlag();
                    }
                    else{
                        document.getElementById("submit").style.visibility = "visible";
                        document.getElementById("ebtncount").value = "0";
                        return true;
                    }
                }
                else{
                    alert("No Data Available to process entry");
                    document.getElementById("submit").style.visibility = "visible";
                    document.getElementById("ebtncount").value = "0";
                }
            }
            function calculate_amt(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var rate = document.getElementById("rate["+d+"]").value; if(rate == ""){ rate = 0; }
                var weight = document.getElementById("weight["+d+"]").value; if(weight == ""){ weight = 0; }
                var amount = parseFloat(rate) * parseFloat(weight);
                document.getElementById("amount["+d+"]").value = parseFloat(amount).toFixed(2);
            }
            function update_rates(){
                var incr = document.getElementById("incr").value;
                if(parseInt(incr) > 0){
                    var mst_rate = document.getElementById("mst_rate").value;
                    var slno = ext_status = ""; var weight = amount = 0;
                    for(var d = 1;d <= incr;d++){
                        slno = ext_status = ""; weight = amount = 0;
                        slno = document.getElementById("slno["+d+"]");
                        ext_status = document.getElementById("ext_status["+d+"]").value;

                        if(slno.checked == true && ext_status == "Data Not Exist"){
                            document.getElementById("rate["+d+"]").value = parseFloat(mst_rate).toFixed(2);
                            weight = document.getElementById("weight["+d+"]").value; if(weight == ""){ weight = 0; }
                            amount = parseFloat(mst_rate) * parseFloat(weight);
                            document.getElementById("amount["+d+"]").value = parseFloat(amount).toFixed(2);
                        }
                    }
                }
            }
            function update_chkboxes(){
                var incr = document.getElementById("incr").value;
                if(parseInt(incr) > 0){
                    var select_all = document.getElementById("select_all");
                    var chkbox = slno = ext_status = "";
                    if(select_all.checked == true){
                        for(var d = 1;d <= incr;d++){
                            slno = ext_status = "";
                            slno = document.getElementById("slno["+d+"]");
                            ext_status = document.getElementById("ext_status["+d+"]").value;

                            if(ext_status == "Data Not Exist"){
                                slno.checked = true;
                            }
                        }
                    }
                    else{
                        for(var d = 1;d <= incr;d++){
                            slno = ext_status = "";
                            slno = document.getElementById("slno["+d+"]");
                            ext_status = document.getElementById("ext_status["+d+"]").value;

                            if(ext_status == "Data Not Exist"){
                                slno.checked = false;
                            }
                        }
                    }
                }
            }
			document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatenums(x){ expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 100){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>