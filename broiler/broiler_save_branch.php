<?php
//broiler_save_branch.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['loc_branch'];

//Check Nisan APIs Flag
$psn_count = $psnc_count = $push_psn_flag = 0;
$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Nisan Push Branch Master' AND `field_function` = 'Auto:Send Details via API' AND `user_access` = 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $psn_count = mysqli_num_rows($query);
if($psn_count > 0){
    $sql='SHOW COLUMNS FROM `location_branch`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
    while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
    if(in_array("nisan_aflag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `location_branch` ADD `nisan_aflag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Nisan synchronization Flag' AFTER `dflag`"; mysqli_query($conn,$sql); }

    $sql = "SELECT * FROM `broiler_nisan_credentials` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $psnc_count = mysqli_num_rows($query);
    if($psnc_count > 0){
        $push_psn_flag = 1; while($row = mysqli_fetch_array($query)){ $psn_version = $row['version']; $psn_company_code = $row['company_code']; $psn_password = $row['password']; }
    }
}

$region_code = $_POST['region'];

$sql ="SELECT MAX(incr) as incr FROM `location_branch`"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; } $incr = $incr + 1; } else { $incr = 1; }
$prefix = "BRH";
// $i = 0; foreach($_POST['region'] as $region){ $region_code[$i] = $region; $i++; }
$i = 0; foreach($_POST['branch'] as $branch){ $description[$i] = $branch; $i++; }
$i = 0; foreach($_POST['flk_prefix'] as $fprx){ $flk_prefix[$i] = $fprx; $i++; }

$ssize = sizeof($description);
for($i = 0;$i < $ssize;$i++){
    if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
    $code = $prefix."-".$incr;
    $sql = "INSERT INTO `location_branch` (incr,prefix,code,description,region_code,flk_prefix,addedemp,addedtime,updatedtime) VALUES ('$incr','$prefix','$code','$description[$i]','$region_code','$flk_prefix[$i]','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else {
        if($push_psn_flag == 1){
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
                     <tem:myisvbranchcode>'.$description[$i].'</tem:myisvbranchcode>
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
            $xml = $json = ""; $responseArray = array();
            $xml = file_get_contents($response);
            $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
            $xml = simplexml_load_string($xml);
            $json = json_encode($xml);
            $responseArray = json_decode($json,true);

            $dup_flag = 0;
            foreach($responseArray as $r1){
                foreach($r1 as $r2){ foreach($r2 as $r3){ foreach($r3 as $r4){ 
                    if(gettype($r4) == "array"){
                        $dup_flag = 1;
                        $sql = "UPDATE `location_branch` SET `nisan_aflag` = '1' WHERE `code` = '$code'";
                        mysqli_query($conn,$sql);
                    }
                    else{
                        $dup_flag = 0;
                    }
                } } }
            }
            
            if($dup_flag == 0){
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
                        <tem:pushBranchMaster>
                            <tem:version>'.$psn_version.'</tem:version>
                            <tem:password>'.$psn_password.'</tem:password>
                            <tem:binfo>
                                <tem:BranchMaster>
                                <tem:isv_branchcode>'.$description[$i].'</tem:isv_branchcode>
                                <tem:companycode>'.$psn_company_code.'</tem:companycode>
                                <tem:branchname>'.$description[$i].'</tem:branchname>
                                <tem:mobileno></tem:mobileno>
                                <tem:branchaddress>'.$psn_version.'</tem:branchaddress>
                                <tem:mortality>N</tem:mortality>
                                <tem:brpass></tem:brpass>
                                <tem:imeinumber></tem:imeinumber>
                                <tem:operatingstyle>NORMAL</tem:operatingstyle>
                                <tem:user_category>S</tem:user_category>
                                <tem:qtytype>W</tem:qtytype>
                                <tem:manualschedule>Y</tem:manualschedule>
                                <tem:branchvattinno></tem:branchvattinno>
                                <tem:branchvattindate></tem:branchvattindate>
                                <tem:novershoot></tem:novershoot>
                                <tem:wovershoot></tem:wovershoot>
                                <tem:schlimit></tem:schlimit>
                                <tem:ReturnCode>1</tem:ReturnCode>
                                <tem:ReturnMessage>S</tem:ReturnMessage>
                                </tem:BranchMaster>
                            </tem:binfo>
                        </tem:pushBranchMaster>
                    </soapenv:Body>
                    </soapenv:Envelope>',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: text/xml',
                        'charset: utf-8'
                    ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);
                $xml = $json = ""; $responseArray = array();
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
                                $status = $r4['ReturnMessage'];
                                if($status == "Branch Record Inserted"){
                                    $sql = "UPDATE `location_branch` SET `nisan_aflag` = '1' WHERE `code` = '$code'";
                                    mysqli_query($conn,$sql); 
                                }
                                $sql2 = "INSERT INTO `broiler_nisan_pushdetail_status` (`type`,`code`,`name`,`status`) VALUES ('Create Branch','$code','$description[$i]','$status')";
                                mysqli_query($conn,$sql2);
                            }
                        }
                    }
                }
            }
        }
        $incr++;
    }
}
header('location:broiler_display_branch.php?ccid='.$ccid);
?>