<?php
//chicken_edit_credit.php
include "newConfig.php";
include "chicken_generate_trnum_details.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
   
    $sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
    while($row = mysqli_fetch_assoc($query)){  $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
    while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }
    
    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $sup_code[$row['code']] = $row['code'];
        $sup_name[$row['code']] = $row['name'];
    }
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
                $sql = "SELECT * FROM `main_mortality` WHERE `code` = '$ids' AND `dflag` = '0' ORDER BY `date`,`code` ASC";
                $query = mysqli_query($conn,$sql); $tcdsamt = array(); $c = 0;
                while($row = mysqli_fetch_assoc($query)){
                   $fdate = date("d.m.Y",strtotime($row['date']));
                    $trnum = $row['code'];
                    $ccode = $row['ccode'];
                    $itemcode = $row['itemcode'];
                    $quantity = round($row['quantity'],2);
                    $price = round($row['price'],2);
                    $amount = round($row['amount'],2);
                    // $remarks = $row['remarks'];
                    $c++;
                } $c = $c - 1;
            }
            ?>
            <div class="card border-secondary mb-3">
                <div class="card-header">Edit Credit Note</div>
                <form action="chicken_modify_credit.php" method="post" onsubmit="return checkval();">
                    <div class="ml-5 card-body">
                        <div class="row">
                            <table>
                                <thead>
                                    <tr>
                                        <th colspan="<?php echo $colspan; ?>" style="background-color:#d1ffe4;color:#00722f;text-align:center;">Credit Note Details</th>
                                    </tr>
                                    <tr>
                                         <th style="text-align:center;"><label>Date<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Company<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Item<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Quantity<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Price<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Amount<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"></th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                <?php  ?>
                                    <tr id="">
                                        <td style="width:100px;"><input type="text" style="width:100px;" class="form-control range_picker" name="pdate" value="<?php echo $fdate; ?>" id="pdate" readonly></td>
                                        <td style="width: 250px;"><select name="company" id="company" class="form-control select2" style="width: 250px;"><option value="select">-select-</option><?php foreach($cus_code as $cscode){ ?><option value="<?php echo $cscode; ?>" <?php if($ccode == $cscode){ echo "selected"; } ?>><?php echo $cus_name[$cscode]; ?></option><?php } ?></select></td>
                                        <td style="width: 200px;"><select name="itemcode" id="itemcode" class="form-control select2" style="width: 200px;" onchange=""><option value="select">-select-</option><?php foreach($item_code as $cc){ ?><option value="<?php echo $item_code[$cc]; ?>" <?php if($itemcode == $cc){ echo "selected"; } ?>><?php echo $item_name[$cc]; ?></option><?php } ?></select></td>
                                        <td style="width: 80px;"><input type="text" name="quantity" id="quantity" value="<?php echo $quantity; ?>" style="width: 80px;" onkeyup="validatenum(this.id);calculate_amt(this.id);" onchange="validateamount(this.id)" class="form-control amount-format"></td>
                                        <td style="width: 80px;"><input type="text" name="price" id="price" value="<?php echo $price; ?>" style="width: 80px;" onkeyup="validatenum(this.id);calculate_amt(this.id);" onchange="validateamount(this.id)" class="form-control amount-format"></td>
                                        <td style="width: 80px;"><input type="text" name="amount" id="amount" value="<?php echo $amount; ?>" style="width: 80px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id)" class="form-control amount-format" readonly></td>
                                        <td style="width:20px;visibility:hidden;"><input type="text" name="trnum" id="trnum" class="form-control" value="<?php echo $trnum; ?>" style="width:20px;" readonly /></td>
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
                    window.location.href = "chicken_display_credit.php";
                }
                function checkval(){
                    var l = true;
                    var date = document.getElementById("pdate").value;
					var company = document.getElementById("company").value;
					var itemcode = document.getElementById("itemcode").value;
					var quantity = document.getElementById("quantity").value; if(quantity == ""){ quantity = 0; }
					var price = document.getElementById("price").value; if(price == ""){ price = 0; }
					var amount = document.getElementById("amount").value; if(amount == ""){ amount = 0; }
				
                    if(date == ""){
						alert("Please select Date in row: ");
						document.getElementById("date").focus();
						l = false;
					}
                    else if(company == "select"){
						alert("Please select Company names in row: ");
						document.getElementById("company").focus();
						l = false;
					}
                    else if(itemcode == "select"){
						alert("Please select Item names in row: ");
						document.getElementById("itemcode").focus();
						l = false;
					}
					else if(parseFloat(quantity) == 0){
						alert("Please enter Quantity in row: ");
						document.getElementById("quantity").focus();
						l = false;
					}
					else if(parseFloat(price) == 0){
						alert("Please enter Price in row: ");
						document.getElementById("price").focus();
						l = false;
					}
					else if(parseFloat(amount) == 0){
						alert("Please enter Amount in row: ");
						document.getElementById("amount").focus();
						l = false;
					}
					return l;
                }
                function calculate_amt(){
                    var qty = document.getElementById("quantity").value; if(qty == ""){ qty = 0; }
                    var prc = document.getElementById("price").value; if(prc == ""){ prc = 0; }

                    var amt = parseFloat(qty) * parseFloat(prc);
                    document.getElementById("amount").value = amt.toFixed(0);
                }
                document.addEventListener("keydown", (e) => { var key_search = document.activeElement.id.includes("["); if(key_search == true){ var b = document.activeElement.id.split("["); var c = b[1].split("]"); var d = c[0]; document.getElementById("incrs").value = d; } if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function () { $('#submittrans').click(); }); } } else{ } });
                function validate_count(x){ expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
                function validatenum(x){ expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
                function validateamount(x){ expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
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
