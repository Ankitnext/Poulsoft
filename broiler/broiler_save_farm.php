<?php
//broiler_save_farm.php
session_start(); include "newConfig.php";
$dbname = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['farm'];

$sql='SHOW COLUMNS FROM `broiler_farm`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("security_cheque3", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_farm` ADD `security_cheque3` VARCHAR(300) NULL DEFAULT NULL AFTER `security_cheque2`"; mysqli_query($conn,$sql); }
if(in_array("security_cheque4", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_farm` ADD `security_cheque4` VARCHAR(300) NULL DEFAULT NULL AFTER `security_cheque3`"; mysqli_query($conn,$sql); }

mysqli_query($conn,"CREATE TABLE IF NOT EXISTS `broiler_farm_shed_details` (
  `id` INT NOT NULL AUTO_INCREMENT , PRIMARY KEY (id),
  `farm_code` varchar(300) DEFAULT NULL,
  `shed_no` varchar(300) DEFAULT NULL,
  `shed_dimentions` varchar(300) DEFAULT NULL,
  `shed_sqft` varchar(300) DEFAULT NULL,
  `updatedtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

//check folder exist or create a folder
$folder_path = "documents/".$dbname; if (!file_exists($folder_path)) { mkdir($folder_path, 0777, true); }

$sql ="SELECT MAX(incr) as incr FROM `broiler_farm`"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; } $incr = $incr + 1; } else { $incr = 1; }
$prefix = "FRM";

$bird_type = "Broiler";
$farm_code = $_POST['farm_code'];
$description = $_POST['description'];
$farm_pincode = $_POST['farm_pincode']; if($farm_pincode == ""){ $farm_pincode = 0; }
$region_code = $_POST['region_code'];
$branch_code = $_POST['branch_code'];
$line_code = $_POST['line_code'];
$supervisor_code = $_POST['supervisor_code'];
$farmer_code = $_POST['farmer_code'];
$farm_capacity = $_POST['farm_capacity'];
$farm_type = $_POST['farm_type'];

$farm_type2 = ""; if($_POST['farm_type'] == "int"){ $farm_type2 = $_POST['farm_types']; }

$state_code = $_POST['state_code'];
$district_name = $_POST['district_name'];
$area_name = $_POST['area_name'];
$farm_address = $_POST['farm_address'];
$agreement_months = $_POST['agreement_months'];

$i = 0; foreach($_POST['shed_no'] as $shed_nos){ $shed_no[$i] = $shed_nos; $i++; }
$i = 0; foreach($_POST['shed_dimentions'] as $shed_dimentionss){ $shed_dimentions[$i] = $shed_dimentionss; $i++; }
$i = 0; foreach($_POST['shed_sqft'] as $shed_sqfts){ $shed_sqft[$i] = $shed_sqfts; $i++; }

if(!empty($_FILES["agreement_copy"]["name"])) {
    //Get File Extension
    $filename = basename($_FILES["agreement_copy"]["name"]);
    $filetype = pathinfo($filename, PATHINFO_EXTENSION);

    //check file count in a directory
    $directory = $folder_path."/";
    $filecount = count(glob($directory . "*")); $filecount++;

    //Create new file name
    $file_name = $dbname."_".$filecount.".".$filetype;

    $filetmp = $_FILES['agreement_copy']['tmp_name'];
    $agreement_copy_name = $_FILES['agreement_copy']['name'];
    $agreement_copy_path = $folder_path."/".$file_name;
    move_uploaded_file($filetmp,$agreement_copy_path);
}
else{
    $agreement_copy_name = $agreement_copy_path = "";
}
$security_cheque1 = $_POST['security_cheque1'];
$security_cheque2 = $_POST['security_cheque2'];
$security_cheque3 = $_POST['security_cheque3'];
$security_cheque4 = $_POST['security_cheque4'];

if(!empty($_FILES["other_doc"]["name"])) {
    //Get File Extension
    $other_filename = basename($_FILES["other_doc"]["name"]);
    $other_filetype = pathinfo($other_filename, PATHINFO_EXTENSION);

    //check file count in a directory
    $directory = $folder_path."/";
    $filecount = count(glob($directory . "*")); $filecount++;

    //Create new file name
    $file_name = $dbname."_".$filecount.".".$other_filetype;

    $filetmp = $_FILES['other_doc']['tmp_name'];
    $other_doc_name = $_FILES['other_doc']['name'];
    $other_doc_path = $folder_path."/".$file_name;
    move_uploaded_file($filetmp,$other_doc_path);
}
else{
    $other_doc_name = $other_doc_path = "";
}
$remarks = $_POST['remarks'];

$sql='SHOW COLUMNS FROM `broiler_farm`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("farm_pincode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_farm` ADD `farm_pincode` INT(100) NOT NULL DEFAULT '0' COMMENT 'Farm Pincode' AFTER `description`"; mysqli_query($conn,$sql); }


if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
$code = $prefix."-".$incr;
$sql = "INSERT INTO `broiler_farm` (farm_pincode,incr,prefix,bird_type,code,farm_code,description,region_code,branch_code,line_code,supervisor_code,farmer_code,farm_capacity,farm_type,farm_type2,state_code,district_name,area_name,farm_address,agreement_months,agreement_copy_path,agreement_copy_name,security_cheque1,security_cheque2,security_cheque3,security_cheque4,other_doc_path,other_doc_name,remarks,flag,active,dflag,addedemp,addedtime,updatedtime) VALUES ('$farm_pincode','$incr','$prefix','$bird_type','$code','$farm_code','$description','$region_code','$branch_code','$line_code','$supervisor_code','$farmer_code','$farm_capacity','$farm_type','$farm_type2','$state_code','$district_name','$area_name','$farm_address','$agreement_months','$agreement_copy_path','$agreement_copy_name','$security_cheque1','$security_cheque2','$security_cheque3','$security_cheque4','$other_doc_path','$other_doc_name','$remarks','0','1','0','$addedemp','$addedtime','$addedtime')";
if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
else {

    $dsize = sizeof($shed_no);
    for ($ab = 0; $ab < $dsize;$ab++){
        mysqli_query($conn,"INSERT INTO `broiler_farm_shed_details`( `farm_code`, `shed_no`, `shed_dimentions`, `shed_sqft`) VALUES ('$code','$shed_no[$ab]','$shed_dimentions[$ab]','$shed_sqft[$ab]')");
    }

    //Check Nisan APIs Flag
    $psn_count = $psnc_count = $push_psn_flag = 0;
    $sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Nisan Push Farm/Farmer Master' AND `field_function` = 'Auto:Send Details via API' AND `user_access` = 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $psn_count = mysqli_num_rows($query);
    if($psn_count > 0){
        $sql='SHOW COLUMNS FROM `broiler_farm`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
        while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
        if(in_array("nisan_aflag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_farm` ADD `nisan_aflag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Nisan synchronization Flag' AFTER `dflag`"; mysqli_query($conn,$sql); }
    
        $sql='SHOW COLUMNS FROM `location_line`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
        while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
        if(in_array("nisan_aflag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `location_line` ADD `nisan_aflag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Nisan synchronization Flag' AFTER `dflag`"; mysqli_query($conn,$sql); }
    
        $sql = "SELECT * FROM `broiler_nisan_credentials` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $psnc_count = mysqli_num_rows($query);
        if($psnc_count > 0){ $push_psn_flag = 1; while($row = mysqli_fetch_array($query)){ $psn_version = $row['version']; $psn_company_code = $row['company_code']; $psn_password = $row['password']; } }
    }
    if($push_psn_flag == 1){
        $sql = "SELECT * FROM `location_branch` WHERE `code` = '$branch_code'"; $query = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($query)){ $branch_name = $row['description']; }

        $sql = "SELECT * FROM `location_line` WHERE `code` = '$line_code'"; $query = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($query)){ $line_name = $row['description']; }

        $sql = "SELECT * FROM `broiler_farmer` WHERE `code` = '$farmer_code'"; $query = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($query)){ $mobile1 = $row['mobile1']; }
        
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
              <tem:getFarmerByCompany>
                 <!--Optional:-->
                 <tem:version>'.$psn_version.'</tem:version>
                 <!--Optional:-->
                 <tem:password>'.$psn_password.'</tem:password>
                 <tem:companyCode>'.$psn_company_code.'</tem:companyCode>
                 <!--Optional:-->
                 <tem:myisvfarmercode>'.$farm_code.'</tem:myisvfarmercode>
              </tem:getFarmerByCompany>
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
                    $sqlf = "UPDATE `broiler_farm` SET `nisan_aflag` = '1' WHERE `code` = '$code' AND `active` = '1' AND `dflag` = '0'";
                    mysqli_query($conn,$sqlf);
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
                    <tem:pushFarmerMaster>
                        <tem:version>'.$psn_version.'</tem:version>
                        <tem:password>'.$psn_password.'</tem:password>
                        <tem:finfo>
                            <tem:FarmerMaster>
                            <tem:isv_farmercode>'.$farm_code.'</tem:isv_farmercode>
                            <tem:farmername>'.$description.'</tem:farmername>
                            <tem:mobileno>'.$mobile1.'</tem:mobileno>
                            <tem:companycode>'.$psn_company_code.'</tem:companycode>
                            <tem:imeinumber></tem:imeinumber>
                            <tem:isv_branchcode>'.$branch_name.'</tem:isv_branchcode>
                            <tem:status>A</tem:status>
                            <tem:ReturnCode>1</tem:ReturnCode>
                            <tem:ReturnMessage>S</tem:ReturnMessage>
                            </tem:FarmerMaster>
                        </tem:finfo>
                    </tem:pushFarmerMaster>
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
            
            foreach($responseArray as $r1){
                foreach($r1 as $r2){
                    foreach($r2 as $r3){
                        foreach($r3 as $r4){
                            $status = $r4['ReturnMessage'];
                            $sql2 = "INSERT INTO `broiler_nisan_pushdetail_status` (`type`,`code`,`name`,`status`) VALUES ('Create Farm','$farm_code','$description','$status')";
                            mysqli_query($conn,$sql2);

                            if($status == "Farmer record inserted"){
                                $sqlf = "UPDATE `broiler_farm` SET `nisan_aflag` = '1' WHERE `code` = '$code' AND `active` = '1' AND `dflag` = '0'";
                                mysqli_query($conn,$sqlf);                  
                            }
                        }
                    }
                }
            }
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
                <tem:getSites>
                <!--Optional:-->
                <tem:version>'.$psn_version.'</tem:version>
                <!--Optional:-->
                <tem:password>'.$psn_password.'</tem:password>
                <tem:companyCode>'.$psn_company_code.'</tem:companyCode>
                <!--Optional:-->
                <tem:status></tem:status>
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
        
        $xml = $json = ""; $responseArray = array();
        $xml = file_get_contents($response);
        $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
        $xml = simplexml_load_string($xml);
        $json = json_encode($xml);
        $responseArray = json_decode($json,true);

        $dup_flag = 0;
        foreach($responseArray as $r1){
            foreach($r1 as $r2){
                foreach($r2 as $r3){
                    foreach($r3 as $r4){
                        if(is_array($r4) && $r4['ReturnMessage'] == "No record found"){ }
                        else{
                            foreach($r4 as $r5){
                                if(is_array($r5) && $r5['isv_farmercode'] == $farm_code && $r5['isv_sitecode'] == $line_name && $r5['sitename'] == $line_name && $r5['status'] == "A"){
                                    $dup_flag = 1;
                                    $sqlf = "UPDATE `location_line` SET `nisan_aflag` = '1' WHERE `description` = '$line_name' AND `active` = '1' AND `dflag` = '0'";
                                    mysqli_query($conn,$sqlf);
                                }
                            }
                        }
                    }
                }
            }
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
                    <tem:pushSiteMaster>
                        <tem:version>'.$psn_version.'</tem:version>
                        <tem:password>'.$psn_password.'</tem:password>
                        <tem:sinfo>
                            <tem:SiteMaster>
                            <tem:isv_farmercode>'.$farm_code.'</tem:isv_farmercode>
                            <tem:isv_sitecode>'.$line_name.'</tem:isv_sitecode>
                            <tem:sitename>'.$line_name.'</tem:sitename>
                            <tem:companycode>'.$psn_company_code.'</tem:companycode>
                            <tem:status>A</tem:status>
                            <tem:gpslocation>0,0</tem:gpslocation>
                            <tem:ReturnCode>1</tem:ReturnCode>
                            <tem:ReturnMessage>S</tem:ReturnMessage>
                            </tem:SiteMaster>
                        </tem:sinfo>
                    </tem:pushSiteMaster>
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

            foreach($responseArray as $r1){
                foreach($r1 as $r2){
                    foreach($r2 as $r3){
                        foreach($r3 as $r4){
                            $status = $r4['ReturnMessage'];
                            $sql2 = "INSERT INTO `broiler_nisan_pushdetail_status` (`type`,`code`,`name`,`status`) VALUES ('Create Site','$farm_code','$line_name','$status')";
                            mysqli_query($conn,$sql2);
                            $sqlf = "UPDATE `location_line` SET `nisan_aflag` = '1' WHERE `description` = '$line_name' AND `active` = '1' AND `dflag` = '0'";
                            mysqli_query($conn,$sqlf);
                        }
                    }
                }
            }
        }
        header('location:broiler_display_farm.php?ccid='.$ccid);
    }
    else{ header('location:broiler_display_farm.php?ccid='.$ccid); }
}

?>