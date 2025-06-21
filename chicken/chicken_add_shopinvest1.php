<?php
//chicken_add_shopinvest1.php
include "newConfig.php";
include "chicken_generate_trnum_details.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $date = date("d.m.Y");
   
    $sql = "SELECT * FROM `item_category` WHERE (`description` LIKE '%MACHINE%' OR `description` LIKE '%SHOP INVESTMENT%' OR `description` LIKE '%SCALE%' OR `description` LIKE '%BOARD%' OR `description` LIKE '%CASH%' OR `description` LIKE '%OTHERS%') AND `active` = '1' ORDER BY `id`";
    $query = mysqli_query($conn,$sql); $cat_alist = array();
    while($row = mysqli_fetch_assoc($query)) { $cat_alist[$row['code']] = $row['code']; }
    $cat_list = implode("','",$cat_alist);

    $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$cat_list') AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
    while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

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
            <div class="card border-secondary mb-3">
                <div class="card-header">Add Shop Investment</div>
                <form action="chicken_save_shopinvest1.php" method="post" onsubmit="return checkval();">
                    <div class="ml-5 card-body">
                        <div class="row">
                            <table>
                                <thead>
                                    <tr>
                                        <th colspan="<?php echo $colspan; ?>" style="background-color:#d1ffe4;color:#00722f;text-align:center;">Shop Investment Details</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align:center;"><label>Date<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Customer Name<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Item Name<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Amount<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Remarks</label></th>
                                        <th style="text-align:center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                    <tr style="margin:5px 0px 5px 0px;">
                                        <td style="width:100px;"><input type="text" name="pdate[]" id="pdate[0]" style="width:100px;" class="form-control range_picker"  value="<?php echo $date; ?>" id="pdate" readonly></td>
                                        <td style="width: 250px;"><select name="vcode[]" id="vcode[0]" class="form-control select2" style="width: 250px;"><option value="select">-select-</option><?php foreach($cus_code as $cc){ ?><option value="<?php echo $cus_code[$cc]; ?>"><?php echo $cus_name[$cc]; ?></option><?php } ?></select></td>
                                        <td style="width: 200px;"><select name="itemcode[]" id="itemcode[0]" class="form-control select2" style="width: 200px;" ><option value="select">-select-</option><?php foreach($item_code as $cc){ ?><option value="<?php echo $item_code[$cc]; ?>"><?php echo $item_name[$cc]; ?></option><?php } ?></select></td>
                                        <td style="width: 80px;"><input type="text" name="amount[]" id="amount[0]" class="form-control amount-format" style="width: 80px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id)" ></td>
                                        <td style="width: auto;"><textarea name="remarks[]" id="remarks[0]" class="form-control" style="height:23px;"></textarea></td>
                                        <td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div><br/>
                        <div class="row" style="visibility:hidden;">
                            <div class="form-group" style="width:30px;">
                                <label>IN</label>
                                <input type="text" name="incr" id="incr" class="form-control" value="0" style="width:20px;" readonly />
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>EB</label>
                                <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0" style="width:20px;" readonly />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group" align="center">
                                <button type="submit" name="submit" id="submit" class="btn btn-sm text-white bg-success">Submit</button>&ensp;
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
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true;
                   
                    var vcode = itemcode = ""; var amount = 0;
                    var incr = parseInt(document.getElementById("incr").value);
                    for(var d = 0;d <= incr;d++){
                        if(l == true){
                            c = d + 1;
                            pdate = document.getElementById("pdate["+d+"]").value;
                            vcode = document.getElementById("vcode["+d+"]").value;
                            amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }
                            itemcode = document.getElementById("itemcode["+d+"]").value;
                           
                            if(pdate == ""){
                                alert("Please select Date in row: "+c);
                                document.getElementById("pdate["+d+"]").focus();
                                l = false;
                                break;
                            }
                            else if(vcode == "select"){
                                alert("Please select Customer names in row: "+c);
                                document.getElementById("vcode["+d+"]").focus();
                                l = false;
                                break;
                            }
                            else if(parseFloat(amount) == 0){
                                alert("Please enter Amount in row: "+c);
                                document.getElementById("amount["+d+"]").focus();
                                l = false;
                                break;
                            }
                             else if(itemcode == "select"){
                                alert("Please select Item names in row: "+c);
                                document.getElementById("itemcode["+d+"]").focus();
                                l = false;
                                break;
                            }
                            
                            else{ }
                        }
                    }
                    if(l == true){
                        return true;
                    }
                    else{
                        document.getElementById("submit").style.visibility = "visible";
                        document.getElementById("ebtncount").value = "0";
                        return false;
                    }
                }
                function create_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("action["+d+"]").style.visibility = "hidden";
                    d++; var html = '';
                    document.getElementById("incr").value = d;
                    html += '<tr id="row_no['+d+']">';
                    html+= '<td style="width: 100px;"><input type="text" name="pdate[]" id="pdate['+d+']" style="width:100px;" class="form-control range_picker"  value="<?php echo $date; ?>" id="pdate" readonly></td>';
                    html+= '<td style="width: 250px;padding-right:5px;"><select name="vcode[]" id="vcode['+d+']" class="form-control select2" style="width: 250px;"><option value="select">-select-</option><?php foreach($cus_code as $cc){ ?><option value="<?php echo $cus_code[$cc]; ?>"><?php echo $cus_name[$cc]; ?></option><?php } ?></select></td>';
                    html+= '<td style="width: 200px;padding-right:5px;"><select name="itemcode[]" id="itemcode['+d+']" class="form-control select2" style="width: 200px;"><option value="select">-select-</option><?php foreach($item_code as $cc){ ?><option value="<?php echo $item_code[$cc]; ?>"><?php echo $item_name[$cc]; ?></option><?php } ?></select></td>';
                    html+= '<td style="width: 80px;"><input type="text" name="amount[]" id="amount['+d+']" style="width: 80px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id)" class="form-control amount-format"></td>';
                    html+= '<td style="width: auto;"><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="height:23px;"></textarea></td>';
                    html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    html += '<td style="width:20px;visibility:hidden;text-align:center;"><input type="checkbox" name="rndoff_chk[]" id="rndoff_chk['+d+']" onchange="" checked /></td>';
                    html += '<td style="width:20px;visibility:hidden;"><input type="text" name="roundoff[]" id="roundoff['+d+']" class="form-control text-right" style="width:20px;" onkeyup="validate_num(this.id);" onchange="validate_amount(this.id);" readonly /></td>';
                    html += '</tr>';
                    $('#row_body').append(html);
                    $('.select2').select2();
                    document.getElementById("vcode["+d+"]").focus();
                    $( ".range_picker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", minDate: s_date, maxDate: e_date, beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
                }
                function destroy_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("row_no["+d+"]").remove();
                    d--;
                    document.getElementById("incr").value = d;
                    document.getElementById("action["+d+"]").style.visibility = "visible";
                    calculate_final_total_amount();
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
