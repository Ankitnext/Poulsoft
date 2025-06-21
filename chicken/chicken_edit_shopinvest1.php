<?php
//chicken_edit_shopinvest1.php
include "newConfig.php";
include "chicken_generate_trnum_details.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $sql = "SELECT * FROM `item_category` WHERE (`description` LIKE '%MACHINE%' OR `description` LIKE '%SHOP INVESTMENT%' OR `description` LIKE '%SCALE%' OR `description` LIKE '%BOARD%' OR `description` LIKE '%CASH%' OR `description` LIKE '%OTHERS%') AND `active` = '1' ORDER BY `id`";
    $query = mysqli_query($conn,$sql); $cat_alist = array();
    while($row = mysqli_fetch_assoc($query)) { $cat_alist[$row['code']] = $row['code']; }
    $cat_list = implode("','",$cat_alist);

    $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$cat_list') AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
    while($row = mysqli_fetch_assoc($query)){  $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
    while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }
    
    //Fetch Column From CoA Table
    $sql='SHOW COLUMNS FROM `acc_coa`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
    while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
    if(in_array("mobile_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa` ADD `mobile_no` VARCHAR(300) NULL DEFAULT NULL AFTER `flag`"; mysqli_query($conn,$sql); }
    if(in_array("transport_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa` ADD `transport_flag` INT(100) NOT NULL DEFAULT '0' AFTER `mobile_no`"; mysqli_query($conn,$sql); }

    //check and fetch date range
    global $drng_cday; $drng_cday = 0; global $drng_furl; $drng_furl = str_replace("_add_","_display_",basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
    include "poulsoft_fetch_daterange_master.php";

    $colspan = 5;
?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
            <style>
                body{
                    overflow: auto;
                }
                /*table,tr,th,td {
                    border: 1px solid black;
                    border-collapse: collapse;
                }*/
                label{
                    font-weight:bold;
                }
            </style>
        </head>
        <body>
            <?php
            $ids = $_GET['trnum'];
            if($ids != ""){
                $sql = "SELECT * FROM `shop_machine_investment` WHERE `trnum` = '$ids' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql); $tcdsamt = array(); $c = 0;
                while($row = mysqli_fetch_assoc($query)){
                   $fdate = date("d.m.Y",strtotime($row['date']));
                    $trnum = $row['trnum'];
                    $vcode = $row['vcode'];
                    $itemcode = $row['itemcode'];
                    $amount = round($row['amount'],2);
                    $remarks = $row['remarks'];
                    $c++;
                } $c = $c - 1;
            }
            ?>
            <div class="card border-secondary mb-3">
                <div class="card-header">Edit Shop Investment</div>
                <form action="chicken_modify_shopinvest1.php" method="post" onsubmit="return checkval();">
                    <div class="ml-5 card-body">
                        <div class="row">
                            <table>
                                <thead>
                                    <tr>
                                        <th colspan="<?php echo $colspan; ?>" style="background-color:#d1ffe4;color:#00722f;text-align:center;">Shop Investment Details</th>
                                    </tr>
                                    <tr>
                                        <th style="width:100px;"><label>Date<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Customer Name<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Item Name<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Amount<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Remarks</label></th>
                                        <th style="text-align:center;"></th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                <?php  ?>
                                    <tr id="">
                                        <td style="width:100px;"><input type="text" style="width:100px;" class="form-control range_picker" name="pdate" value="<?php echo $fdate; ?>" id="pdate" readonly></td>
                                        <td style="width: 250px;"><select name="vcode" id="vcode" class="form-control select2" style="width: 250px;" onchange=""><option value="select">-select-</option><?php foreach($cus_code as $cc){ ?><option value="<?php echo $cus_code[$cc]; ?>" <?php if($vcode == $cc){ echo "selected"; } ?>><?php echo $cus_name[$cc]; ?></option><?php } ?></select></td>
                                        <td style="width: 200px;"><select name="itemcode" id="itemcode" class="form-control select2" style="width: 200px;" onchange=""><option value="select">-select-</option><?php foreach($item_code as $cc){ ?><option value="<?php echo $item_code[$cc]; ?>" <?php if($itemcode == $cc){ echo "selected"; } ?>><?php echo $item_name[$cc]; ?></option><?php } ?></select></td>
                                        <td style="width: 80px;"><input type="text" name="amount" id="amount" value="<?php echo $amount; ?>" style="width: 80px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id)" class="form-control amount-format"></td>
                                        <td style="width: auto;"><textarea name="remarks" id="remarks" class="form-control" ><?php echo $remarks; ?></textarea></td>
                                        <td style="width:20px;visibility:hidden;"><input type="text" name="trnum" id="trnum" class="form-control" value="<?php echo $invoice; ?>" style="width:20px;" readonly /></td>
                                    </tr>
                                    <?php  ?>
                                </tbody>
                            </table>
                        </div><br/>
                        <div class="row" style="visibility:hidden;">
                            <div class="form-group" style="width:30px;">
                                <label>ID</label>
                                <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $ids; ?>" style="width:20px;" readonly />
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>EB</label>
                                <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0" style="width:20px;" readonly />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group" align="center">
                                <button type="submit" name="submit" id="submit" class="btn btn-sm text-white bg-success">Update</button>&ensp;
                                <button type="button" name="cancel" id="cancel" class="btn btn-sm text-white bg-danger" onclick="return_back()">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <script>
                 //Date Range selection
                var s_date = '<?php echo $rng_sdate; ?>'; var e_date = '<?php echo $rng_edate; ?>';
                function return_back(){
                    window.location.href = "chicken_display_shopinvest1.php";
                }
                function checkval(){
                    var l = true;
                    var date = document.getElementById("pdate").value;
					var vcode = document.getElementById("vcode").value;
					var amount = document.getElementById("amount").value; if(amount == ""){ amount = 0; }
					var itemcode = document.getElementById("itemcode").value;
				
                    if(vcode == "select"){
						alert("Please select Customer names in row: ");
						document.getElementById("vcode").focus();
						l = false;
					}
                    else if(itemcode == "select"){
						alert("Please select Item names in row: ");
						document.getElementById("itemcode").focus();
						l = false;
					}
					else if(parseFloat(amount) == 0){
						alert("Please enter Amount in row: ");
						document.getElementById("amount").focus();
						l = false;
					}
					return l;
                }
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
		    <script src="handle_ebtn_as_tbtn.js"></script>
            <script>
                //Date Range selection
                $( ".range_picker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", minDate: s_date, maxDate: e_date, beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
            </script>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
