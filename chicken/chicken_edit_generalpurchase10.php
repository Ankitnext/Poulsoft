<?php
//chicken_edit_generalpurchase10.php
include "newConfig.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $sql = "SELECT * FROM `item_category` WHERE (`description` LIKE '%BIRDS%' OR `description` LIKE '%Chicken%') AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $cat_code = array();
    while($row = mysqli_fetch_assoc($query)){ $cat_code[$row['code']] = $row['code']; }
    $cat_list = implode("','",$cat_code);

    $sql = "SELECT * FROM `item_details` WHERE `category` NOT IN ('$cat_list') AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
    while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
    while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }
    
?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
        </head>
        <body>
            <?php
            $ids = $_GET['trnum'];
            $sql = "SELECT * FROM `pur_purchase` WHERE `invoice` = '$ids' AND `flag` = '0' AND `tdflag` = '0' AND `pdflag` = '0' AND `trlink` = 'chicken_display_generalpurchase10.php'";
            $query = mysqli_query($conn,$sql); $c = 0;
            while($row = mysqli_fetch_assoc($query)){
                $date = $row['date'];
                $trnum = $row['invoice'];
                $vcode = $row['vendorcode'];
                $bookinvoice = $row['bookinvoice'];
                $itemcode[$c] = $row['itemcode'];
                $gst[$c] = $row['gst'];
                $netweight[$c] = round($row['netweight'],5);
                $warehouse[$c] = $row['warehouse'];
                $price[$c] = round($row['itemprice'],5);
                $amount[$c] = round($row['totalamt'],5);
                $amount1[$c] = round($row['amount1'],5);
                $roundoff = round($row['roundoff'],2);
                $finaltotal = round($row['finaltotal'],2);
                $remarks = $row['remarks'];
                $c++;
            } $c = $c - 1;

            ?>
            <div class="card border-secondary mb-3">
                <div class="card-header">Edit Purchase</div>
                <form action="chicken_modify_generalpurchase10.php" method="post" onsubmit="return checkval();">
                    <div class="card-body">
                        <div class="row justify-content-center align-items-center">
                            <div class="form-group" style="width:110px;">
                                <label for="date">Date<b style="color:red;">&nbsp;*</b></label>
                                <input type="text" name="date" id="date" class="form-control sale_datepickers" value="<?php echo date("d.m.Y",strtotime($date)); ?>" style="width:100px;" readonly />
                            </div>
                            <div class="form-group" style="width:130px;">
                                <label for="trnum">Invoice</label>
                                <input type="text" name="trnum" id="trnum" class="form-control" value="<?php echo $trnum; ?>" style="width:120px;" readonly />
                            </div>
                            <div class="form-group" style="width:290px;">
                                <label for="vcode">Supplier<b style="color:red;">&nbsp;*</b></label>
                                <select name="vcode" id="vcode" class="form-control select2" style="width:280px;">
                                    <option value="select">-select-</option>
                                    <?php foreach($cus_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($vcode == $scode){ echo "selected"; } ?>><?php echo $cus_name[$scode]; ?></option><?php } ?>
                                </select>
                            </div>
                            <div class="form-group" style="width:110px;">
                                <label for="bookinvoice">Dc. No.</label>
                                <input type="text" name="bookinvoice" id="bookinvoice" class="form-control" value="<?php echo $bookinvoice; ?>" style="width:100px;" />
                            </div>
                        </div>
                        <div class="row">
                            <table align="center">
                                <thead>
                                    <tr>
                                        <th>Item<b style="color:red;">&nbsp;*</b></th>
                                        <th>Quantity<b style="color:red;">&nbsp;*</b></th>
                                        <th>Price<b style="color:red;">&nbsp;*</b></th>
                                        <th>GST</th>
                                        <th>Amount</th>
                                        <th>Warehouse<b style="color:red;">&nbsp;*</b></th>
                                        <th style="width:70px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                <?php $incr = $c; for($c = 0;$c <= $incr;$c++){ ?>
                                    <tr style="margin:5px 0px 5px 0px;" id="row_no[<?php echo $c; ?>]">
                                        <td><select name="itemcode[]" id="itemcode[<?php echo $c; ?>]" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($itemcode[$c] == $scode){ echo "selected"; } ?>><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><input type="text" name="nweight[]" id="nweight[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $netweight[$c]; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="price[]" id="price[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $price[$c]; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" /></td>
                                        <td>
                                        <select name="gst[]" id="gst[<?= $c ?>]" class="form-control select2" style="width:100px;" onchange="calculate_final_totalamt(this.id);">
                                            <option value="select"<?= ($gst[$c] ?? '') === 'select' ? ' selected' : '' ?>>Select</option>
                                            <option value="5"<?= ($gst[$c] ?? '') === '5' ? ' selected' : '' ?>>GST@5%</option>
                                            <option value="12"<?= ($gst[$c] ?? '') === '12' ? ' selected' : '' ?>>GST@12%</option>
                                        </select>
                                        </td>
                                        <td><input type="text" name="amount[]" id="amount[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $amount[$c]; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" readonly /></td>
                                        <td><select name="warehouse[]" id="warehouse[<?php echo $c; ?>]" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($warehouse[$c] == $scode){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option><?php } ?></select></td>
                                        <?php
                                        if($c == $incr){ echo '<td id="action['.$c.']" style="padding-top: 5px;visibility:visible;">'; }
                                        else{ echo '<td id="action['.$c.']" style="padding-top: 5px;visibility:hidden;">'; }
                                        echo '<a href="javascript:void(0);" id="addrow['.$c.']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;';
                                        if($c > 0){ echo '<a href="javascript:void(0);" id="deductrow['.$c.']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a>'; }
                                        echo '</td>';
                                        ?>
                                        <td style="visibility:hidden;"><input type="text" name="amount1[]" id="amount1[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $amount1[$c]; ?>" style="width:90px;" readonly /></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th style="text-align:right;">Total</th>
                                        <th><input type="text" name="tot_nweight" id="tot_nweight" class="form-control text-right" style="width:90px;" readonly /></th>
                                        <th></th>
                                        <th><input type="text" name="tot_amount" id="tot_amount" class="form-control text-right" style="width:90px;" readonly /></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="3" style="text-align:right;">Round-Off</th>
                                        <th><input type="text" name="roundoff" id="roundoff" class="form-control text-right" value="<?php echo $roundoff;?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" readonly /></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="3" style="text-align:right;">Net Amount</th>
                                        <th><input type="text" name="finaltotal" id="finaltotal" class="form-control text-right" value="<?php echo $finaltotal;?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" readonly /></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div><br/>
                        <div class="row" style="margin-bottom:3px;">
                            <div class="col-md-4 form-group"></div>
                            <div class="col-md-4 form-group">
                                <label>Remarks</label>
                                <textarea name="remarks" id="remarks" class="form-control" style="height:75px;"><?php echo $remarks;?></textarea>
                            </div>
                            <div class="col-md-4 form-group"></div>
                        </div>
                        <div class="row" style="visibility:hidden;">
                            <div class="form-group" style="width:30px;">
                                <label>ID</label>
                                <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $ids; ?>" style="width:20px;" readonly />
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>IN</label>
                                <input type="text" name="incr" id="incr" class="form-control" value="<?php echo $incr; ?>" style="width:20px;" readonly />
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
                function return_back(){
                    window.location.href = "chicken_display_generalpurchase10.php";
                }
                function calculate_final_totalamt(){
                    // function recalcAll() {
                    var incr = +document.getElementById("incr").value;
                    var totW = totAmount  = 0;

                    for (var d = 0; d <= incr; d++) {
                        // 1. read inputs
                        var w    = parseFloat(document.getElementById("nweight["+d+"]").value) || 0;
                        var p    = parseFloat(document.getElementById("price["+d+"]").value) || 0;
                        var gstV    = parseFloat(document.getElementById("gst["+d+"]").value) || 0;
                           
                        // 2. compute base amount & store
                        var baseAmt = w * p;
                        document.getElementById("amount1["+d+"]").value = baseAmt.toFixed(2);

                        // 3. apply GST (if not “select”)
                        var finalAmt = gstV ? baseAmt * (1 + gstV/100) : baseAmt;

                        document.getElementById("amount["+d+"]").value = finalAmt.toFixed(2);

                        // 4. accumulate totals
                        totW      += w;
                        totAmount += finalAmt;
                    }

                    // 5. write out weight + amount totals
                    document.getElementById("tot_nweight").value = totW.toFixed(2);
                    document.getElementById("tot_amount").value  = totAmount.toFixed(2);

                    // 6. compute roundoff + finaltotal
                    var net     = Math.round(totAmount),
                        round   = net - totAmount;

                    document.getElementById("roundoff").value   = round.toFixed(2);
                    document.getElementById("finaltotal").value = net.toFixed(2);
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var date = document.getElementById("date").value;
                    var vcode = document.getElementById("vcode").value;
                    
                    var l = true;

                    if(date == ""){
                        alert("Please select date");
                        document.getElementById("date").focus();
                        l = false;
                    }
                    else if(vcode == "select"){
                        alert("Please select Customer");
                        document.getElementById("vcode").focus();
                        l = false;
                    }
                    else{
                        var itemcode = warehouse = ""; var c = nweight = price = amount = 0;
                        var incr = document.getElementById("incr").value;
                        for(var d = 0;d <= incr;d++){
                            if(l == true){
                                c = d + 1;
                                itemcode = document.getElementById("itemcode["+d+"]").value;
                                nweight = document.getElementById("nweight["+d+"]").value; if(nweight == ""){ nweight = 0; }
                                price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }
                                amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }
                                warehouse = document.getElementById("warehouse["+d+"]").value;

                                if(itemcode == "select"){
                                    alert("Please select Item in row: "+c);
                                    document.getElementById("itemcode["+d+"]").focus();
                                    l = false;
                                }
                                else if(parseFloat(nweight) == 0){
                                    alert("Please enter net weight in row: "+c);
                                    document.getElementById("nweight["+d+"]").focus();
                                    l = false;
                                }
                                else if(parseFloat(price) == 0){
                                    alert("Please enter price in row: "+c);
                                    document.getElementById("price["+d+"]").focus();
                                    l = false;
                                }
                                else if(parseFloat(amount) == 0){
                                    alert("Please enter price/Weight in row: "+c);
                                    document.getElementById("amount["+d+"]").focus();
                                    l = false;
                                }
                                else if(warehouse == "select"){
                                    alert("Please select Warehouse in row: "+c);
                                    document.getElementById("warehouse["+d+"]").focus();
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
                function create_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("action["+d+"]").style.visibility = "hidden";
                    d++; var html = '';
                    document.getElementById("incr").value = d;

                    var jals_flag = '<?php echo $jals_flag; ?>';
                    var birds_flag = '<?php echo $birds_flag; ?>';
                    var tweight_flag = '<?php echo $tweight_flag; ?>';
                    var eweight_flag = '<?php echo $eweight_flag; ?>';

                    html += '<tr id="row_no['+d+']">';
                    html += '<td><select name="itemcode[]" id="itemcode['+d+']" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>';
                    html += '<td><input type="text" name="nweight[]" id="nweight['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><input type="text" name="price[]" id="price['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><select name="gst[]" id="gst['+d+']" class="form-control select2" style="width:100px;" onchange="calculate_final_totalamt(this.id);"><option value="select">Select</option><option value="5">GST@5%</option><option value="12">GST@12%</option></select></td>';
                    html += '<td><input type="text" name="amount[]" id="amount['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" readonly /></td>';
                    html += '<td><select name="warehouse[]" id="warehouse['+d+']" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?></select></td>';
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
                // function calculate_final_totalamt(){
                //     var incr = document.getElementById("incr").value;

                //     var nweight = price = amount = 0;
                //     var tot_nweight = tot_amount = 0;
                //     var icode = iname = "";
                //     for(var d = 0;d <= incr;d++){
                //         nweight = price = amount = 0;
                //         nweight = document.getElementById("nweight["+d+"]").value; if(nweight == ""){ nweight = 0; }
                //         price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }
                //         amount = parseFloat(nweight) * parseFloat(price);
                //         document.getElementById("amount["+d+"]").value = parseFloat(amount).toFixed(2);
                //         document.getElementById("amount1["+d+"]").value = parseFloat(amount).toFixed(2); // store for restoration

                //         tot_nweight = parseFloat(tot_nweight) + parseFloat(nweight);
                //         tot_amount = parseFloat(tot_amount) + parseFloat(amount);
                //     }
                //     document.getElementById("tot_nweight").value = parseFloat(tot_nweight).toFixed(2);
                //     document.getElementById("tot_amount").value = parseFloat(tot_amount).toFixed(2);

                //     var net_amt = parseFloat(tot_amount).toFixed(0); if(net_amt == ""){ net_amt = 0; }
                //     var roundoff = parseFloat(net_amt) - parseFloat(tot_amount); if(roundoff == ""){ roundoff = 0; }
                //     document.getElementById("roundoff").value = parseFloat(roundoff).toFixed(2);
                //     document.getElementById("finaltotal").value = parseFloat(net_amt).toFixed(2);
                // }
                calculate_final_totalamt();
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
