<?php
//chicken_add_item_return1.php
include "newConfig.php";
include "chicken_generate_trnum_details.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $date = date("Y-m-d"); $today = date("d.m.Y");
    global $trns_dtype; $trns_dtype = "Sales"; include "chicken_fetch_daterangemaster.php"; if($rng_mdate == ""){ $rng_mdate = $today; }
    //Generate Transaction No.
    $incr = 0; $prefix = $trnum = "";
    $trno_dt1 = generate_transaction_details($date,"item_return1","CSR","display",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fyear = $trno_dt2[3];

    $sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'";
    $query = mysqli_query($conn,$sql);$jals_flag = $birds_flag = $tweight_flag = $eweight_flag = 0;
    while($row = mysqli_fetch_assoc($query)){ $jals_flag = (int)$row['jals_flag']; $birds_flag = (int)$row['birds_flag']; $tweight_flag = (int)$row['tweight_flag']; $eweight_flag = (int)$row['eweight_flag']; }

    $sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
    while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
    while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }
    
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
                <div class="card-header">Add Sales Return</div>
                <form action="chicken_save_item_return1.php" method="post" onsubmit="return checkval();">
                    <div class="card-body">
                        <div class="row">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Date<b style="color:red;">&nbsp;*</b></th>
                                        <th>Customer<b style="color:red;">&nbsp;*</b></th>
                                        <th>Item<b style="color:red;">&nbsp;*</b></th>
                                        <?php if((int)$jals_flag == 1){ echo "<th>Jals</th>"; } ?>
                                        <?php if((int)$birds_flag == 1){ echo "<th>Birds</th>"; } ?>
                                        <th>Weight<b style="color:red;">&nbsp;*</b></th>
                                        <th>Price<b style="color:red;">&nbsp;*</b></th>
                                        <th>Amount</th>
                                        <th>Warehouse<b style="color:red;">&nbsp;*</b></th>
                                        <th>Remarks</th>
                                        <th style="width:70px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                    <tr style="margin:5px 0px 5px 0px;">
                                        <td><input type="text" name="date[]" id="date[0]" class="form-control sale_datepickers" value="<?php echo date("d.m.Y",strtotime($date)); ?>" style="width:100px;" readonly /></td>
                                        <td><select name="vcode[]" id="vcode[0]" class="form-control select2" style="width:350px;"><option value="select">-select-</option><?php foreach($cus_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $cus_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><select name="itemcode[]" id="itemcode[0]" class="form-control select2" style="width:180px;" onchange="update_row_fields();"><option value="select">select</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>
                                        <?php
                                        if((int)$jals_flag == 1){ echo '<td><input type="text" name="jals[]" id="jals[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);calculate_total_amt();" /></td>'; }
                                        if((int)$birds_flag == 1){ echo '<td><input type="text" name="birds[]" id="birds[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);calculate_total_amt();" /></td>'; }
                                        ?>
                                        <td><input type="text" name="quantity[]" id="quantity[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="price[]" id="price[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="amount[]" id="amount[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" readonly /></td>
                                        <td><select name="warehouse[]" id="warehouse[0]" class="form-control select2" style="width:180px;"><option value="select">select</option><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><textarea name="remarks[]" id="remarks[0]" class="form-control" style="width:150px;height:25px;"></textarea></td>
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
                function return_back(){
                    window.location.href = "chicken_display_item_return1.php";
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true;
                    var vcode = itemcode = ""; var c = quantity = price = amount = 0;
                    var incr = document.getElementById("incr").value;
                    for(var d = 0;d <= incr;d++){
                        if(l == true){
                            c = d + 1;
                            date = document.getElementById("date["+d+"]").value;
                            vcode = document.getElementById("vcode["+d+"]").value;
                            itemcode = document.getElementById("itemcode["+d+"]").value;
                            quantity = document.getElementById("quantity["+d+"]").value; if(quantity == ""){ quantity = 0; }
                            price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }
                            amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }
                            warehouse = document.getElementById("warehouse["+d+"]").value;

                            if(date == ""){
                                alert("Please select Date in row: "+c);
                                document.getElementById("date["+d+"]").focus();
                                l = false;
                            }
                            else if(vcode == "select"){
                                alert("Please select Customer in row: "+c);
                                document.getElementById("vcode["+d+"]").focus();
                                l = false;
                            }
                            else if(itemcode == "select"){
                                alert("Please select Item");
                                document.getElementById("itemcode["+d+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(quantity) == 0){
                                alert("Please enter weight in row: "+c);
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
                                document.getElementById("amount["+d+"]").focus();
                                l = false;
                            }
                            else if(warehouse == "select"){
                                alert("Please select Warehouse");
                                document.getElementById("warehouse["+d+"]").focus();
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

                    var picode = document.getElementById('itemcode['+d+']').value;
                    var date = document.getElementById('date['+d+']').value;


                    d++; var html = '';
                    document.getElementById("incr").value = d;

                    var jals_flag = '<?php echo $jals_flag; ?>';
                    var birds_flag = '<?php echo $birds_flag; ?>';

                    html += '<tr id="row_no['+d+']">';
                    html += '<td><input type="text" name="date[]" id="date['+d+']" class="form-control sale_datepickers" value="'+date+'" style="width:100px;" readonly /></td>';
                    html += '<td><select name="vcode[]" id="vcode['+d+']" class="form-control select2" style="width:350px;"><option value="select">-select-</option><?php foreach($cus_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $cus_name[$scode]; ?></option><?php } ?></select></td>';
                    html += '<td><select name="itemcode[]" id="itemcode['+d+']" class="form-control select2" style="width:180px;" onchange="update_row_fields();"><option value="select">select</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>';
                    if(parseInt(jals_flag) == 1){ html += '<td><input type="text" name="jals[]" id="jals['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);calculate_total_amt();" /></td>'; }
                    if(parseInt(birds_flag) == 1){ html += '<td><input type="text" name="birds[]" id="birds['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);calculate_total_amt();" /></td>'; }
                    html += '<td><input type="text" name="quantity[]" id="quantity['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><input type="text" name="price[]" id="price['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><input type="text" name="amount[]" id="amount['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" readonly /></td>';
                    html += '<td><select name="warehouse[]" id="warehouse['+d+']" class="form-control select2" style="width:180px;"><option value="select">select</option><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?></select></td>';
                    html += '<td><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="width:150px;height:25px;"></textarea></td>';
                    html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    html += '</tr>';

                    $('#row_body').append(html);
                    $('.select2').select2();

                    //document.getElementById("date["+d+"]").focus();
                    $('#itemcode\\[' + d + '\\]').select2(); document.getElementById('itemcode['+d+']').value = picode; $('#itemcode\\[' + d + '\\]').select2();

                    var rng_mdate = '<?php echo $rng_mdate; ?>';
                    var today = '<?php echo $today; ?>';
                    $('.sale_datepickers').datepicker({ dateFormat:'dd.mm.yy',changeMonth:true,changeYear:true,minDate: rng_mdate,maxDate: today,autoclose: true });

                    update_row_fields();
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
                    var quantity = price = amount = 0;

                    var icode = iname = "";
                    for(var d = 0;d <= incr;d++){
                        quantity = price = amount = 0;
                        quantity = document.getElementById("quantity["+d+"]").value; if(quantity == ""){ quantity = 0; }
                        price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }
                        amount = parseFloat(quantity) * parseFloat(price);
                        document.getElementById("amount["+d+"]").value = parseFloat(amount).toFixed(2);
                    }
                }
                function update_row_fields(){
                    var incr = document.getElementById("incr").value;
                    var icode = iname = "";
                    for(var d = 0;d <= incr;d++){
                        var jals_flag = '<?php echo $jals_flag; ?>';
                        var birds_flag = '<?php echo $birds_flag; ?>';

                        icode = iname = "";
                        icode = document.getElementById("itemcode["+d+"]");
                        iname = icode.options[icode.selectedIndex].text;
                        bird_flag = iname.search(/Birds/i);

                        if(parseInt(bird_flag) > 0){
                            if(parseInt(jals_flag) == 1){ document.getElementById("jals["+d+"]").style.visibility = "visible"; }
                            if(parseInt(birds_flag) == 1){ document.getElementById("birds["+d+"]").style.visibility = "visible"; }
                        }
                        else{
                            if(parseInt(jals_flag) == 1){ document.getElementById("jals["+d+"]").style.visibility = "hidden"; document.getElementById("jals["+d+"]").value = ""; }
                            if(parseInt(birds_flag) == 1){ document.getElementById("birds["+d+"]").style.visibility = "hidden"; document.getElementById("birds["+d+"]").value = ""; }
                        }
                    }
                    calculate_total_amt();
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
