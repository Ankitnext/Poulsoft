<?php
//broiler_edit_customer.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['customer'];
$uri = explode("/",$_SERVER['REQUEST_URI']); $url2 = explode("?",$uri[1]); $href = $url2[0];
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $link_childid = $row['childid']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $elink = array(); $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $elink = explode(",",$row['editaccess']);
        if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $user_type = "S"; }
        else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $user_type = "A"; }
        else{ $user_type = "N"; }
    }
    if($user_type == "S"){ $acount = 1; }
    else{
        foreach($elink as $edit_access_flag){
            if($edit_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){
        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Customer Master' AND `field_function` LIKE 'Bird Receiving:Add Prefix' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $brap_flag = mysqli_num_rows($query); if($brap_flag == ""){ $brap_flag = 0; }
        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Customer Creation Master' AND `field_function` LIKE 'Add Branch Selection' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $brchs_flag = mysqli_num_rows($query); if($brchs_flag == ""){ $brchs_flag = 0; }
        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Customer Master' AND `field_function` LIKE 'Upload documents' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $updoc_flag = mysqli_num_rows($query);

        $sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'E-Invoices' AND `field_function` = 'Generate Auto E-Invoices' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $einv_gflag = mysqli_num_rows($query);

        $sql = "SELECT * FROM `broiler_ebill_states` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $estate_code = $estate_name = array();
        while($row = mysqli_fetch_assoc($query)){ $estate_code[$row['code']] = $row['code']; $estate_name[$row['code']] = $row['description']; }

        $id = $_GET['id'];
		$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%C%'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){
			$gcode[$row['code']] = $row['code'];
			$gname[$row['code']] = $row['description'];
		}
        $sql = "SELECT * FROM `breeder_cus_lines` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){
			$clcode[$row['code']] = $row['code'];
			$clname[$row['code']] = $row['description'];
		}
		$sql = "SELECT * FROM `main_contactdetails` WHERE `id` = '$id'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){
            $code = $row['code'];
            $cus_ccode = $row['cus_ccode'];
            $cus_prefix = $row['cus_prefix'];
            $name = $row['name'];
            $mobile1 = $row['mobile1'];
            $mobile2 = $row['mobile2'];
            $emails = $row['emails'];
            $pan_no = $row['pan_no'];
            $aadhar_no = $row['aadhar_no'];
            $birth_date = $row['birth_date'];
            $contacttype = $row['contacttype'];
            $groupcode = $row['groupcode'];
            $gstinno = $row['gstinno'];
            $company_details = $row['company_details'];
            $state_code = $row['state_code'];
            $branch_code = $row['branch_code'];
            $baddress = $row['baddress'];
            $saddress = $row['saddress'];
            $obamt = $row['obamt'];
            $obtype = $row['obtype'];
            $obdate = $row['obdate'];
            $obremarks = $row['obremarks'];
            $creditdays = $row['creditdays'];
            $creditamt = $row['creditamt'];
            $country = $row['country'];
            $currency = $row['currency'];
            $bank_accno = $row['bank_accno'];
            $bank_ifsc_code = $row['bank_ifsc_code'];
            $bank = $row['bank'];
            $processing_flag = $row['processing_flag'];
            $clines = $row['cline_code'];
            $cterms = $row['cterms'];
		}
		$sql = "SELECT * FROM `broiler_ebill_shipping_details` WHERE `vcode` = '$code'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){
            if($row['addr_type'] == "billing_address"){
                $bgstin = $row['ven_gstin'];
                $blegal_name = $row['ven_legalname'];
                $btrade_name = $row['ven_tradename'];
                $baddr1 = $row['ven_addr1'];
                $baddr2 = $row['ven_addr2'];
                $blocation = $row['ven_loc'];
                $bpincode = $row['ven_pincode'];
                $bstate = $row['ven_statecode'];
                $bphone = $row['ven_phone'];
                $bemail = $row['ven_email'];
            }
            else if($row['addr_type'] == "shipping_address"){
                $sgstin = $row['ven_gstin'];
                $slegal_name = $row['ven_legalname'];
                $strade_name = $row['ven_tradename'];
                $saddr1 = $row['ven_addr1'];
                $saddr2 = $row['ven_addr2'];
                $slocation = $row['ven_loc'];
                $spincode = $row['ven_pincode'];
                $sstate = $row['ven_statecode'];
                $sphone = $row['ven_phone'];
                $semail = $row['ven_email'];
            }
		}
?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
    <!-- Datepicker -->
    <link href="datepicker/jquery-ui.css" rel="stylesheet">
    <style>
        body{
            overflow: auto;
        }
        .form-control{
            font-size: 13px;
        }
    </style>
    </head>
    <body class="m-0 hold-transition">
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Customer</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_modify_customer.php" method="post" role="form" onsubmit="return checkval()" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label>Code</label>
							                    <input type="text" name="cus_ccode" id="cus_ccode" class="form-control" value="<?php echo $cus_ccode; ?>" placeholder="Enter Name..." onkeyup="validatename(this.id)" onchange="checkcontacts1()">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Name<b style="color:red;">&nbsp;*</b></label>
							                    <input type="text" name="cname" id="cname" class="form-control" value="<?php echo $name; ?>" placeholder="Enter Name..." onkeyup="validatename(this.id)" onchange="checkcontacts()">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>SMS/WhatsApp No<b style="color:red;">&nbsp;*</b></label>
							                    <input type="text" name="mobile" id="mobile" class="form-control" value="<?php echo $mobile1; ?>" placeholder="Enter Mobile..." onkeyup="validatenums(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label>Mobile No 2</label>
							                    <input type="text" name="mobile2" id="mobile2" class="form-control" value="<?php echo $mobile2; ?>" placeholder="Enter Mobile2..." onkeyup="validatenums(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label>Email</label>
							                    <input type="text" name="emails" id="emails" class="form-control" value="<?php echo $emails; ?>" placeholder="Enter E-mail..." onkeyup="validateemail(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>PAN</label>
							                    <input type="text" name="pan" id="pan" class="form-control" value="<?php echo $pan_no; ?>" placeholder="Enter PAN..." onkeyup="validatename(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Aadhar</label>
							                    <input type="text" name="aadhar_no" id="aadhar_no" class="form-control" value="<?php echo $aadhar_no; ?>" placeholder="Enter Aadhar..." onkeyup="validatename(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label>Birth Date</label>
							                    <input type="text" name="bday" id="bday" class="form-control datepicker" value="<?php echo date("d.m.Y",strtotime($birth_date)); ?>" onkeyup="validatename(this.id)">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Party Type<b style="color:red;">&nbsp;*</b></label>
                                                <select name="stype" id="stype" class="form-control select2" style="width: 100%;" onchange="setgroup();">
                                                    <option value="select">select</option>
                                                    <option value="C" <?php if($contacttype == "C"){ echo "selected"; } ?>>Customer</option>
                                                    <option value="S&C" <?php if($contacttype == "S&C"){ echo "selected"; } ?>>Supplier &amp; Customer</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Party Catgegory<b style="color:red;">&nbsp;*</b></label>
                                                <select name="sgrp" id="sgrp" class="form-control select2" style="width: 100%;">
                                                    <option value="select">select</option>
                                                    <?php
                                                    foreach($gcode as $gc){
                                                    ?>
                                                        <option value="<?php echo $gcode[$gc]; ?>" <?php if($groupcode == $gc){ echo 'selected'; } ?>><?php echo $gname[$gc]; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>GSTIN</label>
                                                <input type="text" name="cgstin" id="cgstin" class="form-control" value="<?php echo $gstinno; ?>" placeholder="Enter GSTIN..." onkeyup="validatename(this.id)" onchange="validate_company()">
                                            </div>
                                        </div>
                                        <?php if((int)$brap_flag == 1){ ?>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label>Plant Access</label>
                                                <input type="checkbox" name="processing_flag" id="processing_flag" class="form-control" style="transform: scale(.5);" <?php if($processing_flag == 1){ echo "checked"; } ?>></input>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Customer Line<b style="color:red;">&nbsp;*</b></label>
                                                <select name="cline_code" id="cline_code" class="form-control select2" style="width: 100%;">
                                                    <option value="select">select</option>
                                                    <?php
                                                    foreach($clcode as $gc){
                                                    ?>
                                                        <option value="<?php echo $clcode[$gc]; ?>" <?php if($clines == $gc){ echo 'selected'; } ?>><?php echo $clname[$gc]; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2" style="visibility:hidden;">
                                            <div class="form-group">
                                                <label>Verified Company</label>
                                                <textarea name="company" id="company" class="form-control"><?php echo $company_details; ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div <?php if((int)$brap_flag == 1){ echo 'class="col-md-2"'; } else{ echo 'class="col-md-4"'; } ?>>
                                            <div class="form-group">
                                                <label>State of Supply</label>
                                                <select name="state" id="state" class="form-control select2" style="width: 100%;">
                                                    <option value="select">select</option>
                                                    <?php
                                                    $sql = "SELECT * FROM `country_states` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
                                                    while($row = mysqli_fetch_assoc($query)){
                                                    ?>
                                                    <option value="<?php echo $row['code']; ?>" <?php if($state_code == $row['code']){ echo "selected"; } ?>><?php echo $row['name']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php if((int)$brchs_flag == 1){ ?>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Branch</label>
                                                <select name="branch_code" id="branch_code" class="form-control select2" style="width: 100%;">
                                                    <option value="select">select</option>
                                                    <?php
                                                    $sql = "SELECT * FROM `location_branch` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                                                    while($row = mysqli_fetch_assoc($query)){
                                                    ?>
                                                    <option value="<?php echo $row['code']; ?>" <?php if($branch_code == $row['code']){ echo "selected"; } ?>><?php echo $row['description']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div <?php if((int)$brchs_flag == 1){ echo 'class="col-md-2"'; } else{ echo 'class="col-md-4"'; } ?>>
                                            <div class="form-group">
                                                <label>Billing Address</label>
                                                <textarea name="baddress" id="baddress" class="form-control"><?php echo $baddress; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Shipping Address</label>
                                                <textarea name="saddress" id="saddress" class="form-control"><?php echo $saddress; ?></textarea>
                                            </div>
                                        </div>
                                        <?php if((int)$brap_flag == 1){ ?>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="cus_prefix">Batch Prefix</label>
                                                    <input type="text" name="cus_prefix" id="cus_prefix" class="form-control" value="<?php echo $cus_prefix; ?>" onkeyup="validateprefix(this.id);" onchange="validateprefix(this.id);" />
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Opening Balance</label>
                                                <input type="text" name="obamount" id="obamount" class="form-control" value="<?php echo $obamt; ?>" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>To Pay /To Receive</label>
                                                <select name="obtype" id="obtype" class="form-control select2" style="width: 100%;">
                                                    <option value="select">select</option>
                                                    <option value="Cr" <?php if($obtype == "Cr"){ echo "selected"; } ?>>To Pay</option>
                                                    <option value="Dr" <?php if($obtype == "Dr"){ echo "selected"; } ?>>To Receive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>As on Date</label>
                                                <input type="text" name="obdate" id="obdate" class="form-control datepicker" value="<?php echo date("d.m.Y",strtotime($obdate)); ?>" onkeyup="validatename(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Remarks</label>
                                                <textarea name="obremarks" id="obremarks" class="form-control"><?php echo $obremarks; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Credit Period</label>
                                                <input type="text" name="ctime" id="ctime" class="form-control" value="<?php echo $creditdays; ?>" placeholder="0" onkeyup="validatenum(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Credit Limit</label>
                                                <input type="text" name="camount" id="camount" class="form-control" value="<?php echo $creditamt; ?>" placeholder="0" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Country</label>
                                                <input type="text" name="country" id="country" class="form-control" value="<?php echo $country; ?>" onkeyup="validatenum(this.id)" />
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Currency</label>
                                                <input type="text" name="currency" id="currency" class="form-control" value="<?php echo $currency; ?>" onkeyup="validatenum(this.id)" />
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Account No</label>
                                                <input type="text" name="accno" id="accno" class="form-control" value="<?php echo $bank_accno; ?>" placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>IFSC Code</label>
                                                <input type="text" name="ifsc" id="ifsc" class="form-control" value="<?php echo $bank_ifsc_code; ?>" onchange="fetchbankdetails()">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Bank Details</label>
                                                <textarea name="bank_details" id="bank_details" class="form-control"><?php echo $bank; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Terms</label>
                                                <textarea name="cterms" id="cterms" class="form-control"><?php echo $cterms; ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if((int)$updoc_flag == 1){ ?>
                                    <div class="row justify-content-center align-items-center">
                                        <div class="form-group" style="width:110px;">
                                            <label>DOC-1</label>
                                            <input type="file" name="vdoc_link1" id="vdoc_link1" class="form-control" style="width:100px;">
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label>DOC-2</label>
                                            <input type="file" name="vdoc_link2" id="vdoc_link2" class="form-control" style="width:100px;">
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label>DOC-3</label>
                                            <input type="file" name="vdoc_link3" id="vdoc_link3" class="form-control" style="width:100px;">
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label>DOC-4</label>
                                            <input type="file" name="vdoc_link4" id="vdoc_link4" class="form-control" style="width:100px;">
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label>DOC-5</label>
                                            <input type="file" name="vdoc_link5" id="vdoc_link5" class="form-control" style="width:100px;">
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <?php if((int)$einv_gflag == 1){ ?>
                                    <div class="row justify-content-center align-items-center row_body2">
                                        <div class="card">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <div class="row justify-content-center align-items-center">
                                                        <table align="center">
                                                            <thead>
                                                                <tr><th colspan="10" style="color:#009f07;background-color:#dbf7f8;text-align:center;font-weight:bold;">E-Invoice Billing Details</th></tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <th>Legal Name<b style="color:red;">&nbsp;*</b></th>
                                                                    <th>Trade Name<b style="color:red;">&nbsp;*</b></th>
                                                                    <th>GSTIN<b style="color:red;">&nbsp;*</b></th>
                                                                    <th>Address-1<b style="color:red;">&nbsp;*</b></th>
                                                                    <th>Address-2</th>
                                                                    <th>Location<b style="color:red;">&nbsp;*</b></th>
                                                                    <th>Pincode<b style="color:red;">&nbsp;*</b></th>
                                                                    <th>State<b style="color:red;">&nbsp;*</b></th>
                                                                    <th>Phone</th>
                                                                    <th>E-Mail</th>
                                                                </tr>
                                                                <tr>
                                                                    <td><input type="text" name="blegal_name" id="blegal_name" class="form-control" value="<?php echo $blegal_name; ?>" style="width:110px;" onkeyup="validatename(this.id)" /></td>
                                                                    <td><input type="text" name="btrade_name" id="btrade_name" class="form-control" value="<?php echo $btrade_name; ?>" style="width:110px;" onkeyup="validatename(this.id)" /></td>
                                                                    <td><input type="text" name="bgstin" id="bgstin" class="form-control" value="<?php echo $bgstin; ?>" style="width:110px;" onkeyup="validatename(this.id)" /></td>
                                                                    <td><textarea name="baddr1" id="baddr1" class="form-control" style="width:210px;"><?php echo $baddr1; ?></textarea></td>
                                                                    <td><textarea name="baddr2" id="baddr2" class="form-control" style="width:210px;"><?php echo $baddr2; ?></textarea></td>
                                                                    <td><input type="text" name="blocation" id="blocation" class="form-control" value="<?php echo $blocation; ?>" style="width:110px;" onkeyup="validatename(this.id)" /></td>
                                                                    <td><input type="text" name="bpincode" id="bpincode" class="form-control" value="<?php echo $bpincode; ?>" style="width:110px;" onkeyup="validate_count(this.id)" /></td>
                                                                    <td><select name="bstate" id="bstate" class="form-control select2" style="width:210px;"><option value="select">-select-</option><?php foreach($estate_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($scode == $bstate){ echo "selected"; } ?>><?php echo $estate_name[$scode]; ?></option><?php } ?></select></td>
                                                                    <td><input type="text" name="bphone" id="bphone" class="form-control" value="<?php echo $bphone; ?>" style="width:110px;" onkeyup="validatename(this.id)" /></td>
                                                                    <td><input type="text" name="bemail" id="bemail" class="form-control" value="<?php echo $bemail; ?>" style="width:110px;" onkeyup="validatename(this.id)" /></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center align-items-center row_body2">
                                        <div class="card card-default">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <div class="row justify-content-center align-items-center">
                                                        <table align="center">
                                                            <thead>
                                                                <tr><th colspan="10" style="color:brown;background-color:#e5e5e5;text-align:center;font-weight:bold;">E-Invoice Shipping Details</th></tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <th>Legal Name<b style="color:red;">&nbsp;*</b></th>
                                                                    <th>Trade Name<b style="color:red;">&nbsp;*</b></th>
                                                                    <th>GSTIN<b style="color:red;">&nbsp;*</b></th>
                                                                    <th>Address-1<b style="color:red;">&nbsp;*</b></th>
                                                                    <th>Address-2</th>
                                                                    <th>Location<b style="color:red;">&nbsp;*</b></th>
                                                                    <th>Pincode<b style="color:red;">&nbsp;*</b></th>
                                                                    <th>State<b style="color:red;">&nbsp;*</b></th>
                                                                    <th>Phone</th>
                                                                    <th>E-Mail</th>
                                                                </tr>
                                                                <tr>
                                                                    <td><input type="text" name="slegal_name" id="slegal_name" class="form-control" value="<?php echo $slegal_name; ?>" style="width:110px;" onkeyup="validatename(this.id)" /></td>
                                                                    <td><input type="text" name="strade_name" id="strade_name" class="form-control" value="<?php echo $strade_name; ?>" style="width:110px;" onkeyup="validatename(this.id)" /></td>
                                                                    <td><input type="text" name="sgstin" id="sgstin" class="form-control" value="<?php echo $sgstin; ?>" style="width:110px;" onkeyup="validatename(this.id)" /></td>
                                                                    <td><textarea name="saddr1" id="saddr1" class="form-control" style="width:210px;"><?php echo $saddr1; ?></textarea></td>
                                                                    <td><textarea name="saddr2" id="saddr2" class="form-control" style="width:210px;"><?php echo $saddr2; ?></textarea></td>
                                                                    <td><input type="text" name="slocation" id="slocation" class="form-control" value="<?php echo $slocation; ?>" style="width:110px;" onkeyup="validatename(this.id)" /></td>
                                                                    <td><input type="text" name="spincode" id="spincode" class="form-control" value="<?php echo $spincode; ?>" style="width:110px;" onkeyup="validate_count(this.id)" /></td>
                                                                    <td><select name="sstate" id="sstate" class="form-control select2" style="width:210px;"><option value="select">-select-</option><?php foreach($estate_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($scode == $sstate){ echo "selected"; } ?>><?php echo $estate_name[$scode]; ?></option><?php } ?></select></td>
                                                                    <td><input type="text" name="sphone" id="sphone" class="form-control" value="<?php echo $sphone; ?>" style="width:110px;" onkeyup="validatename(this.id)" /></td>
                                                                    <td><input type="text" name="semail" id="semail" class="form-control" value="<?php echo $semail; ?>" style="width:110px;" onkeyup="validatename(this.id)" /></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <div class="row">
                                        <div class="form-group col-md-1" style="visibility:hidden;">
                                            <label>id<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $id; ?>" placeholder="Enter description..." onkeyup="validatename(this.id);colorchange(this.id);">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
                                            <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
                                            <label>Name Count<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="dupflag" id="dupflag" class="form-control text-right" value="0" style="width:20px;" readonly />
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
                                            <label>Code Count<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="dupflag2" id="dupflag2" class="form-control text-right" value="0" style="width:20px;" readonly />
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group" align="center">
                                            <button type="submit" name="submit" id="submit" class="btn btn-sm bg-purple">Update</button>&ensp;
                                            <button type="button" name="cancel" id="cancel" class="btn btn-sm bg-danger" onclick="return_back()">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- Datepicker -->
        <script src="datepicker/jquery/jquery.js"></script>
        <script src="datepicker/jquery-ui.js"></script>
        <script>
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_customer.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var cname = document.getElementById("cname").value;
                var stype = document.getElementById("stype").value;
                var sgrp = document.getElementById("sgrp").value;
                var obamount = document.getElementById("obamount").value;
                var obtype = document.getElementById("obtype").value;
                var dupflag = document.getElementById("dupflag").value; if(dupflag == ""){ dupflag = 0; }
                var dupflag2 = document.getElementById("dupflag2").value; if(dupflag2 == ""){ dupflag2 = 0; }

                var einv_gflag = '<?php echo $einv_gflag; ?>'; if(einv_gflag == ""){ einv_gflag = 0; }
                var blegal_name = btrade_name = bgstin = baddr1 = blocation = bpincode = bstate = "";
                var slegal_name = strade_name = sgstin = saddr1 = slocation = spincode = sstate = "";
                if(parseInt(einv_gflag) > 0){
                    blegal_name = document.getElementById("blegal_name").value;
                    btrade_name = document.getElementById("btrade_name").value;
                    bgstin = document.getElementById("bgstin").value;
                    baddr1 = document.getElementById("baddr1").value;
                    blocation = document.getElementById("blocation").value;
                    bpincode = document.getElementById("bpincode").value;
                    bstate = document.getElementById("bstate").value;

                    slegal_name = document.getElementById("slegal_name").value;
                    strade_name = document.getElementById("strade_name").value;
                    sgstin = document.getElementById("sgstin").value;
                    saddr1 = document.getElementById("saddr1").value;
                    slocation = document.getElementById("slocation").value;
                    spincode = document.getElementById("spincode").value;
                    sstate = document.getElementById("sstate").value;
                }

                var l = true;
                if(cname == ""){
                    alert("Please enter name");
                    document.getElementById("cname").focus();
                    l = false;
                }
                else if(stype == "select"){
                    alert("Please select Party Type");
                    document.getElementById("stype").focus();
                    l = false;
                }
                else if(sgrp == "select"){
                    alert("Please select Party Catgegory");
                    document.getElementById("sgrp").focus();
                    l = false;
                }
                else if(parseFloat(dupflag) > 0) {
                    alert("Customer/Supplier Details are available with the same name.\n Kindly change the name");
                    document.getElementById("cname").focus();
                    l = false;
                } 
                else if(parseFloat(dupflag2) > 0) {
                    alert("Customer/Supplier Details are available with the same code.\n Kindly change the code");
                    document.getElementById("cus_ccode").focus();
                    l = false;
                }
                else if(obamount != "" && parseFloat(obamount) > 0){
                    if(obtype == "select"){
                        alert("Please select To Pay /To Receive");
                        document.getElementById("obtype").focus();
                        l = false;
                    }
                    else{ }
                }
                else if(parseInt(einv_gflag) > 0 && blegal_name == ""){
                    alert("Please enter Legal Name in E-Invoice Billing Details");
                    document.getElementById("blegal_name").focus();
                    l = false;
                }
                else if(parseInt(einv_gflag) > 0 && bgstin == ""){
                    alert("Please enter GSTIN in E-Invoice Billing Details");
                    document.getElementById("bgstin").focus();
                    l = false;
                }
                else if(parseInt(einv_gflag) > 0 && btrade_name == ""){
                    alert("Please enter Trade Name in E-Invoice Billing Details");
                    document.getElementById("btrade_name").focus();
                    l = false;
                }
                else if(parseInt(einv_gflag) > 0 && baddr1 == ""){
                    alert("Please enter Address-1 in E-Invoice Billing Details");
                    document.getElementById("baddr1").focus();
                    l = false;
                }
                else if(parseInt(einv_gflag) > 0 && blocation == ""){
                    alert("Please enter Location in E-Invoice Billing Details");
                    document.getElementById("blocation").focus();
                    l = false;
                }
                else if(parseInt(einv_gflag) > 0 && bpincode == ""){
                    alert("Please enter Pincode in E-Invoice Billing Details");
                    document.getElementById("bpincode").focus();
                    l = false;
                }
                else if(parseInt(einv_gflag) > 0 && bstate == "select"){
                    alert("Please select State in E-Invoice Billing Details");
                    document.getElementById("bstate").focus();
                    l = false;
                }
                else if(parseInt(einv_gflag) > 0 && slegal_name == ""){
                    alert("Please enter Legal Name in E-Invoice Shipping Details");
                    document.getElementById("slegal_name").focus();
                    l = false;
                }
                else if(parseInt(einv_gflag) > 0 && strade_name == ""){
                    alert("Please enter Trade Name in E-Invoice Shipping Details");
                    document.getElementById("strade_name").focus();
                    l = false;
                }
                else if(parseInt(einv_gflag) > 0 && sgstin == ""){
                    alert("Please enter GSTIN in E-Invoice Shipping Details");
                    document.getElementById("sgstin").focus();
                    l = false;
                }
                else if(parseInt(einv_gflag) > 0 && saddr1 == ""){
                    alert("Please enter Address-1 in E-Invoice Shipping Details");
                    document.getElementById("saddr1").focus();
                    l = false;
                }
                else if(parseInt(einv_gflag) > 0 && slocation == ""){
                    alert("Please enter Location in E-Invoice Shipping Details");
                    document.getElementById("slocation").focus();
                    l = false;
                }
                else if(parseInt(einv_gflag) > 0 && spincode == ""){
                    alert("Please enter Pincode in E-Invoice Shipping Details");
                    document.getElementById("spincode").focus();
                    l = false;
                }
                else if(parseInt(einv_gflag) > 0 && sstate == "select"){
                    alert("Please select State in E-Invoice Shipping Details");
                    document.getElementById("sstate").focus();
                    l = false;
                }
                else{ }
                if(l == true){
                    return true;
                }
                else{
                    document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
                    return false;
                }
            }
			function checkcontacts(){
				var b = document.getElementById("cname").value;
				var c = "new";
				if(!b.length == 0){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "main_getcontactdetails.php?cname="+b+"&cid="+c;
					var asynchronous = true;
					oldqty.open(method, url, asynchronous);
					oldqty.send();
					oldqty.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var f = this.responseText;
							if(f.match("ok")){
								document.getElementById("dupflag"). value = 0;
							}
							else {
								alert("Customer/Supplier Details are available with the same name.\n Kindly change the name");
								document.getElementById("dupflag"). value = 1;
							}
						}
					}
				}
				else { }
			}
            function checkcontacts1(){
				var g = document.getElementById("cus_ccode").value;
				var h = "new";
				if(!g.length == 0){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "main_getcontactdetails1.php?cus_ccode="+g+"&cid="+h;
					var asynchronous = true;
					oldqty.open(method, url, asynchronous);
					oldqty.send();
					oldqty.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var k = this.responseText;
							if(k.match("ok")){
								document.getElementById("dupflag2").value = 0;
							}
							else {
								alert("Customer/Supplier Details are available with the same code.\n Kindly change the code");
								document.getElementById("dupflag2").value = 1;
							}
						}
					}
				}
				else { }
			}
            
            function fetchbankdetails(){
                var a = document.getElementById("ifsc").value;
                if(a.length == 11){
                    var ifsc_code = new XMLHttpRequest();
                    var method = "GET";
                    var url = "bank_fetchdetails_api.php?ifsc="+a;
                    var asynchronous = true;
                    ifsc_code.open(method, url, asynchronous);
                    ifsc_code.send();
                    ifsc_code.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var b = this.responseText;
                            if(b == 0 || b == ""){
                                alert("Invalid IFSC Code entered \n Kindly check and Try again");
                            }
                            else {
                                var bank_details = b;
                                document.getElementById("bank_details").value = bank_details;
                            }
                        }
                    }
                }
            }
            function validate_company(){
                var a = document.getElementById("cgstin").value;
                if(a.length == 15){
                    var cgstin = new XMLHttpRequest();
                    var method = "GET";
                    var url = "company_fetchdetails_with_gst.php?gstno="+a;
                    var asynchronous = true;
                    cgstin.open(method, url, asynchronous);
                    cgstin.send();
                    cgstin.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var b = this.responseText;
                            if(b == 0 || b == ""){
                                alert("Invalid GSTIN Code entered \n Kindly check and Try again");
                            }
                            else {
                                var bank_details = b;
                                document.getElementById("company").value = bank_details;
                            }
                        }
                    }
                }
            }
			function setgroup() {
				var a = document.getElementById("sgrp").value;
				var b = document.getElementById("stype").value;
				removeAllOptions(document.getElementById("sgrp"));
				
				myselect1 = document.getElementById("sgrp"); 
				theOption1=document.createElement("OPTION"); 
				theText1=document.createTextNode("Select"); 
				theOption1.value = "select"; 
				theOption1.appendChild(theText1); 
				myselect1.appendChild(theOption1);
				<?php
					$sql="SELECT * FROM `main_groups` WHERE `active` = '1' ORDER BY `description` ASC";
					$query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ echo "if(b == '$row[gtype]'){"; ?> 
						theOption1=document.createElement("OPTION");
						theText1=document.createTextNode("<?php echo $row['description']; ?>"); 
						theOption1.value = "<?php echo $row['code']; ?>"; 
						theOption1.appendChild(theText1); myselect1.appendChild(theOption1);	
					<?php echo "}"; } ?>
					
					
			}
			function showDetails(a,b) {
				var checkBox = document.getElementById(a);
				if(b.match("CD")){
					var text = document.getElementById("creditdetails");
					if (checkBox.checked == true){
						text.style.display = "block";
					}
					else {
						text.style.display = "none";
					}
				}
				else if(b.match("OB")){
					var text = document.getElementById("crdrdetails");
					if (checkBox.checked == true){
						text.style.display = "block";
					}
					else {
						text.style.display = "none";
					}
				}
				else if(b.match("BD")){
					var text = document.getElementById("bankdetails");
					if (checkBox.checked == true){
						text.style.display = "block";
					}
					else {
						text.style.display = "none";
					}
				}
				else {}
				
			}
            function validatename(x) {
                expr = /^[a-zA-Z0-9 (.&)_-]*$/;
                var a = document.getElementById(x).value;
                if(a.length > 50){
                    a = a.substr(0,a.length - 1);
                }
                if(!a.match(expr)){
                    a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, '');
                }
                document.getElementById(x).value = a;
            }
            function validateemail(x) {
                expr = /^[a-zA-Z0-9 (.&@)_-]*$/;
                var a = document.getElementById(x).value;
                if(a.length > 50){
                    a = a.substr(0,a.length - 1);
                }
                if(!a.match(expr)){
                    a = a.replace(/[^a-zA-Z0-9 (.&@)_-]/g, '');
                }
                document.getElementById(x).value = a;
            }
			function validatenum(x) {
				expr = /^[0-9.]*$/;
				var a = document.getElementById(x).value;
				if(a.length > 50){
					a = a.substr(0,a.length - 1);
				}
				if(!a.match(expr)){
					a = a.replace(/[^0-9.]/g, '');
				}
				document.getElementById(x).value = a;
			}
			function validatenums(x) {
				expr = /^[0-9.]*$/;
				var a = document.getElementById(x).value;
				if(a.length > 100){
					a = a.substr(0,a.length - 1);
				}
				if(!a.match(expr)){
					a = a.replace(/[^0-9.]/g, '');
				}
				document.getElementById(x).value = a;
			}
			function validateamount(x) {
				expr = /^[0-9.]*$/;
				var a = document.getElementById(x).value;
				if(a.length > 50){
					a = a.substr(0,a.length - 1);
				}
				while(!a.match(expr)){
					a = a.replace(/[^0-9.]/g, '');
				}
				if(a == ""){ a = 0; } else { }
				var b = parseFloat(a).toFixed(2);
				document.getElementById(x).value = b;
			}
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <?php include "header_foot.php"; ?>
    </body>
</html>

<?php
    }
    else{
        echo "You don't have access to this page \n Kindly contact your admin for more information"; 
    }
}
else{
    echo "You don't have access to this page \n Kindly contact your admin for more information";
}
?>