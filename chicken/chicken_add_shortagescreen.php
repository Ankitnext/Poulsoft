<?php
//chicken_add_shortagescreen.php
include "newConfig.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $date = $today = date("d.m.Y");
    global $trns_dtype; $trns_dtype = "Stock Adjustment"; include "chicken_fetch_daterangemaster.php"; if($rng_mdate == ""){ $rng_mdate = $today; }

    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Birds' AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
    while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
    
    $sql = "SELECT * FROM `main_transactionfields` WHERE `field` LIKE 'Stock Adjustment' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $jals_flag = $row['jals_flag']; $birds_flag = $row['birds_flag']; }
    if($jals_flag == "" || $jals_flag == NULL){ $jals_flag = 0; } if($birds_flag == "" || $birds_flag == NULL){ $birds_flag = 0; }
    
?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
        </head>
        <body>
            <div class="card border-secondary mb-3">
                <div class="card-header">Add Shoratge Screen</div>
                <form action="chicken_save_shortagescreen.php" method="post" onsubmit="return checkval();">
                    <div class="card-body">
                        <div class="row">
                            <table align="center">
                                <thead>
                                    <tr>
                                        <th>Date<b style="color:red;">&nbsp;*</b></th>
                                        <th>Warehouse<b style="color:red;">&nbsp;*</b></th>
                                        <!-- <th>Type<b style="color:red;">&nbsp;*</b></th> -->
                                        <th>Item<b style="color:red;">&nbsp;*</b></th>
                                        <th>Quantity</th>
                                        <th>Price<b style="color:red;">&nbsp;*</b></th>
                                        <th>Amount<b style="color:red;">&nbsp;*</b></th>
                                        <th>Remarks</th>
                                        <th style="visibility:hidden;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                    <tr style="margin:5px 0px 5px 0px;">
                                        <td><input type="text" name="date[]" id="date[0]" class="form-control stka_datepickers" value="<?php echo $date; ?>" style="min-width:100px;" onchange="fetch_latest_stkin_prc(this.id);"></td>
                                        <td><select name="warehouse[]" id="warehouse[0]" class="form-control select2" style="width:180px;" onchange="fetch_latest_stkin_prc(this.id);"><option value="select">-select-</option><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?></select></td>
                                        <td style="display:none;"><select name="a_type[]" id="a_type[0]" class="form-control select2" style="width:180px;"><option value="deduct" selected>-Deduct-</option></select></td>
                                        <td><select name="itemcode[]" id="itemcode[0]" class="form-control select2" style="width:180px;" onchange="fetch_latest_stkin_prc(this.id);"><option value="select">-select-</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><input type="text" name="quantity[]" id="quantity[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_row_amt(this.id);" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="price[]" id="price[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_row_amt(this.id);" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="amount[]" id="amount[0]" class="form-control text-right" style="width:90px;" readonly /></td>
                                        <td><textarea name="remarks[]" id="remarks[0]" class="form-control" style="height: 23px;"></textarea></td>
                                        <td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th><input type="text" name="tot_weight" id="tot_weight" class="form-control text-right" style="width:90px;" readonly /></th>
                                        <th></th>
                                        <th><input type="text" name="tot_amount" id="tot_amount" class="form-control text-right" style="width:90px;" readonly /></th>
                                        <th></th>
                                        <th></th>
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
                    window.location.href = "chicken_display_shortagescreen.php";
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true;

                    var date = a_type = warehouse = itemcode = ""; var quantity = price = amount = c = 0;
                    var incr = document.getElementById("incr").value;
                    for(var d = 0;d <= incr;d++){
                        if(l == true){
                            c = d + 1;
                            date = document.getElementById("date["+d+"]").value;
                            warehouse = document.getElementById("warehouse["+d+"]").value;
                            a_type = document.getElementById("a_type["+d+"]").value;
                            itemcode = document.getElementById("itemcode["+d+"]").value;
                            quantity = document.getElementById("quantity["+d+"]").value; if(quantity == ""){ quantity = 0; }
                            price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }
                            amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }
                            
                            if(date == ""){
                                alert("Please select Date in row: "+c);
                                document.getElementById("date["+d+"]").focus();
                                l = false;
                            }
                            else if(warehouse == "" || warehouse == "select"){
                                alert("Please select Warehouse in row: "+c);
                                document.getElementById("warehouse["+d+"]").focus();
                                l = false;
                            }
                            else if(a_type == "" || a_type == "select"){
                                alert("Please select Type in row: "+c);
                                document.getElementById("a_type["+d+"]").focus();
                                l = false;
                            }
                            else if(itemcode == "" || itemcode == "select"){
                                alert("Please select Item in row: "+c);
                                document.getElementById("itemcode["+d+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(quantity) == 0){
                                alert("Please enter Quantity in row: "+c);
                                document.getElementById("quantity["+d+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(price) == 0){
                                alert("Please enter Price in row: "+c);
                                document.getElementById("price["+d+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(amount) == 0){
                                alert("Please enter Quantity/Price in row: "+c);
                                document.getElementById("amount["+d+"]").focus();
                                l = false;
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
                    var jals_flag = '<?php echo (int)$jals_flag; ?>';
                    var birds_flag = '<?php echo (int)$birds_flag; ?>';
                    html += '<tr id="row_no['+d+']">';
                    html += '<td><input type="text" name="date[]" id="date['+d+']" class="form-control stka_datepickers" value="<?php echo $date; ?>" style="min-width:100px;" onchange="fetch_latest_stkin_prc(this.id);"></td>';
                    html += '<td><select name="warehouse[]" id="warehouse['+d+']" class="form-control select2" style="width:180px;" onchange="fetch_latest_stkin_prc(this.id);"><option value="select">-select-</option><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?></select></td>';
                    html += '<td style="display:none;"><select name="a_type[]" id="a_type['+d+']" class="form-control select2" style="width:180px;"><option value="deduct" selected>-Deduct-</option></select></td>';
                    html += '<td><select name="itemcode[]" id="itemcode['+d+']" class="form-control select2" style="width:180px;" onchange="fetch_latest_stkin_prc(this.id);"><option value="select">-select-</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>';
                    html += '<td><input type="text" name="quantity[]" id="quantity['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_row_amt(this.id);" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><input type="text" name="price[]" id="price['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_row_amt(this.id);" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><input type="text" name="amount[]" id="amount['+d+']" class="form-control text-right" style="width:90px;" readonly /></td>';
                    html += '<td><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="height: 23px;"></textarea></td>';
                    html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    html += '</tr>';
                    $('#row_body').append(html);
                    $('.select2').select2();
                    var rng_mdate = '<?php echo $rng_mdate; ?>';
                    var today = '<?php echo $today; ?>';
                    $('.stka_datepickers').datepicker({ dateFormat:'dd.mm.yy',changeMonth:true,changeYear:true,minDate: rng_mdate,maxDate: today,autoclose: true });
                }
                function destroy_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("row_no["+d+"]").remove();
                    d--;
                    document.getElementById("incr").value = d;
                    document.getElementById("action["+d+"]").style.visibility = "visible";
                    calculate_final_total_amount();
                }
                function calculate_row_amt(){
                    var incr = document.getElementById("incr").value;
                    var jals = birds = quantity = price = amount = tot_jals = tot_birds = tot_weight = tot_amount = 0;
                    for(var d = 0;d <= incr;d++){
                        jals = birds = quantity = price = amount = 0;
                        quantity = document.getElementById("quantity["+d+"]").value; if(quantity == ""){ quantity = 0; }
                        price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }

                        amount = parseFloat(quantity) * parseFloat(price); if(amount == ""){ amount = 0; }
                        document.getElementById("amount["+d+"]").value = parseFloat(amount).toFixed(0);

                        tot_weight = parseFloat(tot_weight) + parseFloat(quantity);
                        tot_amount = parseFloat(tot_amount) + parseFloat(amount);
                    }
                    document.getElementById("tot_weight").value = parseFloat(tot_weight).toFixed(2);
                    document.getElementById("tot_amount").value = parseFloat(tot_amount).toFixed(2);
                }
                function fetch_latest_stkin_prc(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var date = document.getElementById("date["+d+"]").value;
                    var sector = document.getElementById("warehouse["+d+"]").value;
                    var icode = document.getElementById("itemcode["+d+"]").value;
                    document.getElementById("price["+d+"]").value = "";

                    if(date != "" && sector != "select" && icode != "select"){
                        var ven_bals = new XMLHttpRequest();
                        var method = "GET";
                        var url = "fetch_item_prc_master1.php?date="+date+"&sector="+sector+"&icode="+icode+"&r_cnt="+d;
                        //window.open(url);
                        var asynchronous = true;
                        ven_bals.open(method, url, asynchronous);
                        ven_bals.send();
                        ven_bals.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var pur_dt1 = this.responseText;
                                var pur_dt2 = pur_dt1.split("[@$&]");
                                var rows = pur_dt2[0];
                                var p_prc = pur_dt2[1]; if(p_prc == ""){ p_prc = 0; }
                                document.getElementById("price["+rows+"]").value = parseFloat(p_prc).toFixed(2);
                                calculate_row_amt();
                            }
                        }
                    }
                    else{
                        calculate_row_amt();
                    }
                }
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
