<?php
//chicken_add_mortality1.php
include "newConfig.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $date = $today = date("d.m.Y");
    global $trns_dtype; $trns_dtype = "Sales"; include "chicken_fetch_daterangemaster.php"; if($rng_mdate == ""){ $rng_mdate = $today; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
    while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $sup_code = $sup_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; }

    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Birds' AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
    while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
        </head>
        <body>
            <div class="card border-secondary mb-3">
                <div class="card-header">Add Mortality</div>
                <form action="chicken_save_mortality1.php" method="post" onsubmit="return checkval();">
                    <div class="card-body">
                        <div class="row">
                            <table align="center">
                                <thead>
                                    <tr>
                                        <th>Date<b style="color:red;">&nbsp;*</b></th>
                                        <th>Item<b style="color:red;">&nbsp;*</b></th>
                                        <th>Birds<b style="color:red;">&nbsp;*</b></th>
                                        <th>Weight</th>
                                        <th>Price<b style="color:red;">&nbsp;*</b></th>
                                        <th>Amount<b style="color:red;">&nbsp;*</b></th>
                                        <th>Mortality On<b style="color:red;">&nbsp;*</b></th>
                                        <th>Customer / Warehouse<b style="color:red;">&nbsp;*</b></th>
                                        <th>Remarks</th>
                                        <th style="visibility:hidden;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                    <tr style="margin:5px 0px 5px 0px;">
                                        <td><input type="text" name="date[]" id="date[0]" class="form-control sale_datepickers" value="<?php echo $date; ?>" style="min-width:100px;"></td>
                                        <td><select name="itemcode[]" id="itemcode[0]" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><input type="text" name="birds[]" id="birds[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);calculate_row_amt(this.id);" /></td>
                                        <td><input type="text" name="quantity[]" id="quantity[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_row_amt(this.id);" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="price[]" id="price[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_row_amt(this.id);" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="amount[]" id="amount[0]" class="form-control text-right" style="width:90px;" readonly /></td>
                                        <td><select name="mtype[]" id="mtype[0]" class="form-control select2" style="width: 180px;" onchange="setgroup(this.id);"><option value="select">select</option><option value="customer">Customer</option><option value="supplier">Supplier</option><option value="sector">Warehouse</option></select></td>
										<td><select name="ccode[]" id="ccode[0]" class="form-control select2"style="width:180px;"><option value="select">select</option></select></td>
                                        <td><textarea name="remarks[]" id="remarks[0]" class="form-control" style="height: 23px;"></textarea></td>
                                        <td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th><input type="text" name="tot_birds" id="tot_birds" class="form-control text-right" style="width:90px;" readonly /></th>
                                        <th><input type="text" name="tot_weight" id="tot_weight" class="form-control text-right" style="width:90px;" readonly /></th>
                                        <th></th>
                                        <th><input type="text" name="tot_amount" id="tot_amount" class="form-control text-right" style="width:90px;" readonly /></th>
                                        <th></th>
                                        <th></th>
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
                    window.location.href = "chicken_display_mortality1.php";
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true;

                    var date = itemcode = mtype = ccode = ""; var birds = price = amount = c = 0;
                    var incr = document.getElementById("incr").value;
                    for(var d = 0;d <= incr;d++){
                        if(l == true){
                            c = d + 1;
                            date = document.getElementById("date["+d+"]").value;
                            itemcode = document.getElementById("itemcode["+d+"]").value;
                            birds = document.getElementById("birds["+d+"]").value; if(birds == ""){ birds = 0; }
                            price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }
                            amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }
                            mtype = document.getElementById("mtype["+d+"]").value;
                            ccode = document.getElementById("ccode["+d+"]").value;
                            
                            if(date == ""){
                                alert("Please select Date in row: "+c);
                                document.getElementById("date["+d+"]").focus();
                                l = false;
                            }
                            else if(itemcode == "" || itemcode == "select"){
                                alert("Please select Item in row: "+c);
                                document.getElementById("itemcode["+d+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(birds) == 0){
                                alert("Please enter Birds in row: "+c);
                                document.getElementById("birds["+d+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(price) == 0){
                                alert("Please enter Price in row: "+c);
                                document.getElementById("price["+d+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(amount) == 0){
                                alert("Please enter Birds/Price in row: "+c); 
                                document.getElementById("amount["+d+"]").focus();
                                l = false;
                            }
                            else if(mtype == "" || mtype == "select"){
                                alert("Please select Mortality On in row: "+c);
                                document.getElementById("mtype["+d+"]").focus();
                                l = false;
                            }
                            else if(ccode == "" || ccode == "select"){
                                alert("Please select Customer / Warehouse in row: "+c);
                                document.getElementById("ccode["+d+"]").focus();
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
                    var rsncrdr_flag = '<?php echo (int)$rsncrdr_flag; ?>';
                    html += '<tr id="row_no['+d+']">';
                    html += '<td><input type="text" name="date[]" id="date['+d+']" class="form-control sale_datepickers" value="<?php echo $date; ?>" style="min-width:100px;"></td>';
                    html += '<td><select name="itemcode[]" id="itemcode['+d+']" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>';
                    html += '<td><input type="text" name="birds[]" id="birds['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);calculate_row_amt(this.id);" /></td>';
                    html += '<td><input type="text" name="quantity[]" id="quantity['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_row_amt(this.id);" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><input type="text" name="price[]" id="price['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_row_amt(this.id);" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><input type="text" name="amount[]" id="amount['+d+']" class="form-control text-right" style="width:90px;" readonly /></td>';
                    html += '<td><select name="mtype[]" id="mtype['+d+']" class="form-control select2" style="width: 180px;" onchange="setgroup(this.id);"><option value="select">select</option><option value="customer">Customer</option><option value="supplier">Supplier</option><option value="sector">Warehouse</option></select></td>';
                    html += '<td><select name="ccode[]" id="ccode['+d+']" class="form-control select2"style="width:180px;"><option value="select">select</option></select></td>';
                    html += '<td><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="height: 23px;"></textarea></td>';
                    html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    html += '</tr>';
                    $('#row_body').append(html);
                    $('.select2').select2();
                    var rng_mdate = '<?php echo $rng_mdate; ?>';
                    var today = '<?php echo $today; ?>';
                    $('.sale_datepickers').datepicker({ dateFormat:'dd.mm.yy',changeMonth:true,changeYear:true,minDate: rng_mdate,maxDate: today,autoclose: true });
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
                    var birds = quantity = price = amount = tot_birds = tot_weight = tot_amount = 0;
                    for(var d = 0;d <= incr;d++){
                        birds = quantity = price = amount = 0;
                        birds = document.getElementById("birds["+d+"]").value; if(birds == ""){ birds = 0; }
                        quantity = document.getElementById("quantity["+d+"]").value; if(quantity == ""){ quantity = 0; }
                        price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }

                        amount = parseFloat(birds) * parseFloat(price); if(amount == ""){ amount = 0; }
                        document.getElementById("amount["+d+"]").value = parseFloat(amount).toFixed(0);

                        tot_birds = parseFloat(tot_birds) + parseFloat(birds);
                        tot_weight = parseFloat(tot_weight) + parseFloat(quantity);
                        tot_amount = parseFloat(tot_amount) + parseFloat(amount);
                    }
                    document.getElementById("tot_birds").value = parseFloat(tot_birds).toFixed(0);
                    document.getElementById("tot_weight").value = parseFloat(tot_weight).toFixed(2);
                    document.getElementById("tot_amount").value = parseFloat(tot_amount).toFixed(2);
                }
                function setgroup(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var mtype = document.getElementById("mtype["+d+"]").value;

                    removeAllOptions(document.getElementById("ccode["+d+"]"));

                    var select1 = document.getElementById("ccode["+d+"]"); 
                    var option1 = document.createElement("OPTION"); 
                    var text1 = document.createTextNode("select"); 
                    option1.value = "select"; 
                    option1.appendChild(text1); 
                    select1.appendChild(option1);

                    if(mtype == "customer"){
                        <?php
                            foreach($cus_code as $icode){
                        ?>
                        option1 = document.createElement("OPTION");
                        text1 = document.createTextNode("<?php echo $cus_name[$icode]; ?>");
                        option1.value = "<?php echo $icode; ?>";
                        option1.appendChild(text1);
                        select1.appendChild(option1);
                        <?php
                            }
                        ?>
                    }
                    else if(mtype == "supplier"){
                        <?php
                            foreach($sup_code as $icode){
                        ?>
                        option1 = document.createElement("OPTION");
                        text1 = document.createTextNode("<?php echo $sup_name[$icode]; ?>");
                        option1.value = "<?php echo $icode; ?>";
                        option1.appendChild(text1);
                        select1.appendChild(option1);
                        <?php
                            }
                        ?>
                    }
                    else if(mtype == "sector"){
                        <?php
                            foreach($sector_code as $icode){
                        ?>
                        option1 = document.createElement("OPTION");
                        text1 = document.createTextNode("<?php echo $sector_name[$icode]; ?>");
                        option1.value = "<?php echo $icode; ?>";
                        option1.appendChild(text1);
                        select1.appendChild(option1);
                        <?php
                            }
                        ?>
                    }
                    else{ }
                }
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
