<?php
//chicken_add_crdrnote1.php
include "newConfig.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $date = date("Y-m-d");
    $today = date("d.m.Y");
    global $trns_dtype; $trns_dtype = "CrDr Note"; include "chicken_fetch_daterangemaster.php"; if($rng_mdate == ""){ $rng_mdate = $today; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
    while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $sup_code = $sup_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; }

    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $method_code = $method_name = array();
    while($row = mysqli_fetch_assoc($query)){ $method_code[$row['code']] = $row['code']; $method_name[$row['code']] = $row['description']; }

	$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'CrDr-Note Transaction' AND `field_function` LIKE 'Display: Reason selection' AND `flag` = '1'";
	$query = mysqli_query($conn,$sql); $rsncrdr_flag = mysqli_num_rows($query);

	if((int)$rsncrdr_flag == 1){
		$sql = "SELECT * FROM `crdr_note_reasons` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC";
		$query = mysqli_query($conn,$sql); $reason_code = $reason_name = array();
		while($row = mysqli_fetch_assoc($query)){ $reason_code[$row['code']] = $row['code']; $reason_name[$row['code']] = $row['description']; }
	}
?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
        </head>
        <body>
            <div class="card border-secondary mb-3">
                <div class="card-header">Add Credit/Debit Note</div>
                <form action="chicken_save_crdrnote1.php" method="post" onsubmit="return checkval();">
                    <div class="card-body">
                        <div class="row">
                            <table align="center">
                                <thead>
                                    <tr>
                                        <th>S/C Type<b style="color:red;">&nbsp;*</b></th>
                                        <th>C/D Type<b style="color:red;">&nbsp;*</b></th>
                                        <th>Supplier/Customer<b style="color:red;">&nbsp;*</b></th>
                                        <th>Date<b style="color:red;">&nbsp;*</b></th>
                                        <th>Doc No</th>
                                        <th>Account<b style="color:red;">&nbsp;*</b></th>
                                        <th>Amount<b style="color:red;">&nbsp;*</b></th>
                                        <?php if((int)$rsncrdr_flag == 1){ echo '<th><label>Reason</label></th>'; } ?>
                                        <th>Sector<b style="color:red;">&nbsp;*</b></th>
                                        <th>Remarks</th>
                                        <th style="visibility:hidden;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                    <tr style="margin:5px 0px 5px 0px;">
                                        <td><select name="vtype[]" id="vtype[0]" class="form-control select2" style="width:180px;" onchange="fetch_vendors(this.id);"><option value="select">-select-</option><option value="S">Supplier</option><option value="C">Customer</option></select></td>
                                        <td><select name="cdtype[]" id="cdtype[0]" class="form-control select2" style="width:180px;"><option value="select">-select-</option><option value="CN">Credit Note</option><option value="DN">Debit Note</option></select></td>
                                        <td><select name="ccode[]" id="ccode[0]" class="form-control select2" style="width:180px;"><option value="select">-select-</option></select></td>
                                        <td><input type="text" name="date[]" id="date[0]" class="form-control crdr_datepickers" value="<?php echo date("d.m.Y",strtotime($date)); ?>" style="width:100px;" readonly /></td>
                                        <td><input type="text" name="dcno[]" id="dcno[0]" class="form-control" style="width:90px;" onkeyup="validate_name(this.id);" /></td>
                                        <td><select name="code[]" id="code[0]" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($method_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $method_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><input type="text" name="amount[]" id="amount[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_row_amt();" onchange="validate_amount(this.id);" /></td>
                                        <?php if((int)$rsncrdr_flag == 1){ ?><td><select name="reason_code[]" id="reason_code[0]" class="form-control select2" style="width:100%"> <?php foreach($reason_code as $rcode){ ?> <option value="<?php echo $rcode; ?>"><?php echo $reason_name[$rcode]; ?></option> <?php } ?> </select></td><?php } ?>
										<td><select name="sector[]" id="sector[0]" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><textarea name="remarks[]" id="remarks[0]" class="form-control" style="height: 23px;"></textarea></td>
                                        <td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Total Credit / Debit</th>
                                        <th>Credit / Debit Amount</th>
                                    </tr>
                                    <tr>
                                        <th><input type="text" name="tot_rows" id="tot_rows" class="form-control text-right" style="width:90px;" readonly /></th>
                                        <th><input type="text" name="tot_amt" id="tot_amt" class="form-control text-right" style="width:90px;" readonly /></th>
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
                    window.location.href = "chicken_display_crdrnote1.php";
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true;

                    var vtype = cdtype = ccode = date = code = amount = sector = ""; var c = amount1 = amount = 0;
                    var incr = document.getElementById("incr").value;
                    for(var d = 0;d <= incr;d++){
                        if(l == true){
                            c = d + 1;
                            vtype = document.getElementById("vtype["+d+"]").value;
                            cdtype = document.getElementById("cdtype["+d+"]").value;
                            ccode = document.getElementById("ccode["+d+"]").value;
                            date = document.getElementById("date["+d+"]").value;
                            code = document.getElementById("code["+d+"]").value;
                            amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }
                            sector = document.getElementById("sector["+d+"]").value;
                            
                            if(vtype == "select"){
                                alert("Please select S/C Type in row: "+c);
                                document.getElementById("vtype["+d+"]").focus();
                                l = false;
                            }
                            else if(cdtype == "select"){
                                alert("Please select C/D Type in row: "+c);
                                document.getElementById("cdtype["+d+"]").focus();
                                l = false;
                            }
                            else if(ccode == "select"){
                                alert("Please select Supplier/Customer in row: "+c);
                                document.getElementById("ccode["+d+"]").focus();
                                l = false;
                            }
                            else if(date == ""){
                                alert("Please select Date in row: "+c);
                                document.getElementById("date["+d+"]").focus();
                                l = false;
                            }
                            else if(code == "select"){
                                alert("Please select Account in row: "+c);
                                document.getElementById("code["+d+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(amount) == 0){
                                alert("Please enter Amount in row: "+c);
                                document.getElementById("amount1["+d+"]").focus();
                                l = false;
                            }
                            else if(sector == "select"){
                                alert("Please select Sector in row: "+c);
                                document.getElementById("sector["+d+"]").focus();
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
                    html += '<td><select name="vtype[]" id="vtype['+d+']" class="form-control select2" style="width:180px;" onchange="fetch_vendors(this.id);"><option value="select">-select-</option><option value="S">Supplier</option><option value="C">Customer</option></select></td>';
                    html += '<td><select name="cdtype[]" id="cdtype['+d+']" class="form-control select2" style="width:180px;"><option value="select">-select-</option><option value="CN">Credit Note</option><option value="DN">Debit Note</option></select></td>';
                    html += '<td><select name="ccode[]" id="ccode['+d+']" class="form-control select2" style="width:180px;"><option value="select">-select-</option></select></td>';
                    html += '<td><input type="text" name="date[]" id="date['+d+']" class="form-control crdr_datepickers" value="<?php echo date("d.m.Y",strtotime($date)); ?>" style="width:100px;" readonly /></td>';
                    html += '<td><input type="text" name="dcno[]" id="dcno['+d+']" class="form-control" style="width:90px;" onkeyup="validate_name(this.id);" /></td>';
                    html += '<td><select name="code[]" id="code['+d+']" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($method_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $method_name[$scode]; ?></option><?php } ?></select></td>';
                    html += '<td><input type="text" name="amount[]" id="amount['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_row_amt();" onchange="validate_amount(this.id);" /></td>';
                    if(parseInt(rsncrdr_flag) == 1){ html += '<td><select name="reason_code[]" id="reason_code['+d+']" class="form-control select2" style="width:100%"> <?php foreach($reason_code as $rcode){ ?> <option value="<?php echo $rcode; ?>"><?php echo $reason_name[$rcode]; ?></option> <?php } ?> </select></td>'; }
                    html += '<td><select name="sector[]" id="sector['+d+']" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?></select></td>';
                    html += '<td><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="height: 23px;"></textarea></td>';
                    html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    html += '</tr>';
                    $('#row_body').append(html);
                    $('.select2').select2();
                    var rng_mdate = '<?php echo $rng_mdate; ?>';
                    var today = '<?php echo $today; ?>';
                    $('.crdr_datepickers').datepicker({ dateFormat:'dd.mm.yy',changeMonth:true,changeYear:true,minDate: rng_mdate,maxDate: today,autoclose: true });
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
                    var tot_rows = amount = tot_amt = 0;
                    for(var d = 0;d <= incr;d++){
                        amount = 0; tot_rows++;
                        amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }
                        tot_amt = parseFloat(tot_amt) + parseFloat(amount);
                    }
                    document.getElementById("tot_rows").value = tot_rows;
                    document.getElementById("tot_amt").value = parseFloat(tot_amt).toFixed(2);
                }
                function fetch_vendors(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var vtype = document.getElementById("vtype["+d+"]").value;
                    removeAllOptions(document.getElementById("ccode["+d+"]"));
                    myselect = document.getElementById("ccode["+d+"]"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("select"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                    if(vtype.match("S")){
                        <?php
                        $sql="SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){ ?> 
                            theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['name']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                        <?php } ?>
                    }
                    else if(vtype.match("C")){
                        <?php
                        $sql="SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1'  ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){ ?> 
                            theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['name']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                        <?php } ?>
                    }
                }
			    function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
