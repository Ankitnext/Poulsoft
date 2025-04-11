<?php
//chicken_add_generalsales3.php
include "newConfig.php";
include "chicken_generate_trnum_details.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $date = date("Y-m-d");
    //Generate Transaction No.
    $incr = 0; $prefix = $trnum = "";
    $trno_dt1 = generate_transaction_details($date,"generalsales3","RST","display",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fyear = $trno_dt2[3];

    $emp_code = $_SESSION['userid'];
    $sql = "SELECT * FROM `main_access` WHERE `empcode` = '$emp_code' AND `active` = '1' AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql); $loc_access = $cash_coa = $bank_coa = "";
    while($row = mysqli_fetch_assoc($query)){ $loc_access = $row['loc_access']; $cash_coa = $row['cash_coa']; $bank_coa = $row['bank_coa']; }
    
    //Sector Access Filter
    if($loc_access == "" || $loc_access == "all"){ $sec_fltr = ""; }
    else{
        $loc1 = explode(",",$loc_access); $loc_list = "";
        foreach($loc1 as $loc2){ if($loc_list = ""){ $loc_list = $loc2; } else{ $loc_list = $loc_list."','".$loc2; } }
        $sec_fltr = " AND `code` IN ('$loc_list')";
    }
    /*Cash CoA Filter*/
    if($cash_coa == "" || $cash_coa == "all"){ $cash_fltr = ""; } else{ $cash_fltr = " AND `code` IN ('$cash_coa')"; }
    /*bank CoA Filter*/
    if($bank_coa == "" || $bank_coa == "all"){ $bank_fltr = ""; } else{ $bank_fltr = " AND `code` IN ('$bank_coa')"; }

    $sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
    while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'".$sec_fltr." ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `name` LIKE '%Shop Sale%' AND `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
    while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }
    
    $sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Bank')".$bank_fltr." AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $bank_code = $bank_name = array();
    while($row = mysqli_fetch_assoc($query)){ $bank_code[$row['code']] = $row['code']; $bank_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash')".$cash_fltr." AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $cash_code = $cash_name = array();
    while($row = mysqli_fetch_assoc($query)){ $cash_code[$row['code']] = $row['code']; $cash_name[$row['code']] = $row['description']; }
?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
            <style>
                label{
                    font-weight:bold;
                }
            </style>
        </head>
        <body>
            <div class="card border-secondary mb-3">
                <div class="card-header">Add Multiple-Sales</div>
                <form action="chicken_save_generalsales3.php" method="post" onsubmit="return checkval();">
                    <div class="card-body justify-content-center align-items-center">
                        <div class="row justify-content-center align-items-center">
                            <div class="form-group" style="width:110px;">
                                <label for="date">Date<b style="color:red;">&nbsp;*</b></label>
                                <input type="text" name="date" id="date" class="form-control sale_datepickers" value="<?php echo date("d.m.Y",strtotime($date)); ?>" style="width:100px;" onchange="fetch_tcds_per(this.id);fetch_stockwise_sectors();" readonly />
                            </div>
                            <div class="form-group" style="width:190px;">
                                <label for="vcode">Customer<b style="color:red;">&nbsp;*</b></label>
                                <select name="vcode" id="vcode" class="form-control select2" style="width:180px;" onchange="">
                                    <?php foreach($cus_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $cus_name[$scode]; ?></option><?php } ?>
                                </select>
                            </div>
                            <div class="form-group" style="width:190px;">
                                <label for="warehouse">Warehouse<b style="color:red;">&nbsp;*</b></label>
                                <select name="warehouse" id="warehouse" class="form-control select2" style="width:180px;" onchange="">
                                    <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?>
                                </select>
                            </div>
                            <div class="form-group" id="item_wstk_details"></div>
                        </div>
                        <div class="row">
                            <table align="center">
                                <thead>
                                    <tr>
                                        <th>Item<b style="color:red;">&nbsp;*</b></th>
                                        <th>Quantity<b style="color:red;">&nbsp;*</b></th>
                                        <th>Price<b style="color:red;">&nbsp;*</b></th>
                                        <th>Amount</th>
                                        <th style="width:70px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                    <tr style="margin:5px 0px 5px 0px;">
                                        <td><select name="icode[]" id="icode[0]" class="form-control select2" style="width:280px;"><option value="select">select</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><input type="text" name="quantity[]" id="quantity[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="price[]" id="price[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="item_amt[]" id="item_amt[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" readonly /></td>
                                        <td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="1" style="text-align:right;">Total</th>
                                        <th><input type="text" name="tot_quantity" id="tot_quantity" class="form-control text-right" style="width:90px;" readonly /></th>
                                        <th></th>
                                        <th><input type="text" name="total_amt" id="total_amt" class="form-control text-right" style="width:90px;" readonly /></th>
                                    </tr>
                                    <tr>
                                        <th colspan="1" style="text-align:right;">Cash</th>
                                        <th colspan="2" style="text-align:right;"><select name="cash_code" id="cash_code" class="form-control select2" style="width:180px;"><?php foreach($cash_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $cash_name[$scode]; ?></option><?php } ?></select></th>
                                        <th><input type="text" name="cash_amt" id="cash_amt" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></th>
                                    </tr>
                                    <tr>
                                        <th colspan="1" style="text-align:right;">Bank</th>
                                        <th colspan="2" style="text-align:right;"><select name="bank_code" id="bank_code" class="form-control select2" style="width:180px;"><?php foreach($bank_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $bank_name[$scode]; ?></option><?php } ?></select></th>
                                        <th><input type="text" name="bank_amt" id="bank_amt" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></th>
                                    </tr>
                                    <tr>
                                        <th colspan="3" style="text-align:right;">Balance</th>
                                        <th><input type="text" name="balance_amt" id="balance_amt" class="form-control text-right" style="width:90px;" readonly /></th>
                                    </tr>
                                </tfoot>
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
                function return_back(){
                    window.location.href = "chicken_display_generalsales3.php";
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true;
                    var date = document.getElementById("date").value;
                    var vcode = document.getElementById("vcode").value;
                    var warehouse = document.getElementById("warehouse").value;
                    var cash_code = document.getElementById("cash_code").value;
                    var cash_amt = document.getElementById("cash_amt").value; if(cash_amt == ""){ cash_amt = 0; }
                    var bank_code = document.getElementById("bank_code").value;
                    var bank_amt = document.getElementById("bank_amt").value; if(bank_amt == ""){ bank_amt = 0; }

                    if(date == ""){
                        alert("Please select date");
                        document.getElementById("date").focus();
                        l = false;
                    }
                    else if(vcode == "select"){
                        alert("Please select Customer");
                        document.getElementById("Customer").focus();
                        l = false;
                    }
                    else if(warehouse == "select"){
                        alert("Please select Warehouse");
                        document.getElementById("warehouse").focus();
                        l = false;
                    }
                    else if(parseFloat(cash_amt) > 0 && (cash_code == "select" || cash_code == "")){
                        alert("Please select Cash Code");
                        document.getElementById("cash_code").focus();
                        l = false;
                    }
                    else if(parseFloat(bank_amt) > 0 && (bank_code == "select" || bank_code == "")){
                        alert("Please select bank Code");
                        document.getElementById("bank_code").focus();
                        l = false;
                    }
                    else{
                        var icode = ""; var c = quantity = price = item_amt = 0;
                        var incr = document.getElementById("incr").value;
                        for(var d = 0;d <= incr;d++){
                            if(l == true){
                                c = d + 1;
                                icode = document.getElementById("icode["+d+"]").value;
                                quantity = document.getElementById("quantity["+d+"]").value; if(quantity == ""){ quantity = 0; }
                                price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }
                                item_amt = document.getElementById("item_amt["+d+"]").value; if(item_amt == ""){ item_amt = 0; }

                                if(icode == "select"){
                                    alert("Please select Item");
                                    document.getElementById("icode["+d+"]").focus();
                                    l = false;
                                }
                                else if(parseFloat(quantity) == 0){
                                    alert("Please enter net weight in row: "+c);
                                    document.getElementById("quantity["+d+"]").focus();
                                    l = false;
                                }
                                else if(parseFloat(price) == 0){
                                    alert("Please enter price in row: "+c);
                                    document.getElementById("price["+d+"]").focus();
                                    l = false;
                                }
                                else if(parseFloat(item_amt) == 0){
                                    alert("Please enter price/Weight in row: "+c);
                                    document.getElementById("item_amt["+d+"]").focus();
                                    l = false;
                                }
                                else{ }
                            }
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
                function fetch_tcds_per(){
                    var date = document.getElementById("date").value;
                    var tdsper = new XMLHttpRequest();
                    var method = "GET";
                    var url = "main_gettcdsvalue.php?type=TDS&cdate="+date;
                    var asynchronous = true;
                    tdsper.open(method, url, asynchronous);
                    tdsper.send();
                    tdsper.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var tds = this.responseText;
                            if(tds != ""){
                                document.getElementById("tcds_per").value = tds;
                            }
                        }
                    }
                }
                function create_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("action["+d+"]").style.visibility = "hidden";
                    d++; var html = '';
                    document.getElementById("incr").value = d;

                    html += '<tr id="row_no['+d+']">';
                    html += '<td><select name="icode[]" id="icode['+d+']" class="form-control select2" style="width:280px;"><option value="select">select</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>';
                    html += '<td><input type="text" name="quantity[]" id="quantity['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><input type="text" name="price[]" id="price['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><input type="text" name="item_amt[]" id="item_amt['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" readonly /></td>';
                    html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    html += '</tr>';

                    $('#row_body').append(html);
                    $('.select2').select2();
                }
                function destroy_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("row_no["+d+"]").remove();
                    d--;
                    document.getElementById("incr").value = d;
                    document.getElementById("action["+d+"]").style.visibility = "visible";
                    calculate_final_total_amount();
                }
                function calculate_total_amt(){
                    /*Total Calculations*/
                    var incr = document.getElementById("incr").value;
                    var quantity = price = item_amt = 0;
                    var tot_quantity = total_amt = 0;

                    for(var d = 0;d <= incr;d++){
                        quantity = price = item_amt = 0;
                        quantity = document.getElementById("quantity["+d+"]").value; if(quantity == ""){ quantity = 0; }
                        price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }
                        item_amt = parseFloat(quantity) * parseFloat(price);
                        document.getElementById("item_amt["+d+"]").value = parseFloat(item_amt).toFixed(2);

                        tot_quantity = parseFloat(tot_quantity) + parseFloat(quantity);
                        total_amt = parseFloat(total_amt) + parseFloat(item_amt);
                    }
                    document.getElementById("tot_quantity").value = parseFloat(tot_quantity).toFixed(2);
                    document.getElementById("total_amt").value = parseFloat(total_amt).toFixed(2);

                    var cash_amt = document.getElementById("cash_amt").value; if(cash_amt == ""){ cash_amt = 0; }
                    var bank_amt = document.getElementById("bank_amt").value; if(bank_amt == ""){ bank_amt = 0; }
                    var balance_amt = (parseFloat(total_amt) - (parseFloat(cash_amt) + parseFloat(bank_amt)));
                    document.getElementById("balance_amt").value = parseFloat(balance_amt).toFixed(2);
                }
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
		    <script src="handle_ebtn_as_tbtn.js"></script>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
