<?php
//chicken_add_customerreceipt1_nb_t.php
include "newConfig.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path); $href = "chicken_add_customerreceipt1_nb.php";
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $date = date("Y-m-d");
    $today = date("d.m.Y");
    global $trns_dtype; $trns_dtype = "Receipt"; include "chicken_fetch_daterangemaster.php"; if($rng_mdate == ""){ $rng_mdate = $today; }

    $sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$date' AND `tdate` >= '$date' AND `type` = 'TDS' AND `active` = '1' AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql); $tcds_per = 0;
    while($row = mysqli_fetch_assoc($query)){ $tcds_per = $row['tcds']; }
    
    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
    while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }

    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array(); $st_code = "";
    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; if($sector_name[$row['code']] == "StockPoint"){$st_code = $row['code'];} }

    $sql = "SELECT * FROM `acc_modes` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $mode_code = $mode_name = array();
    while($row = mysqli_fetch_assoc($query)){ $mode_code[$row['code']] = $row['code']; $mode_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `acc_coa` WHERE `ctype` LIKE 'Cash' AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $method_code = $method_name = array();
    while($row = mysqli_fetch_assoc($query)){ $method_code[$row['code']] = $row['code']; $method_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Customer Receipt' AND `field_function` = 'Display TCDS Calculations' AND `user_access` = 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $dtcds_flag = mysqli_num_rows($query);

    $sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Customer Receipt' AND `field_function` = 'Hide DocNo' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $hdcno_flag = mysqli_num_rows($query);

     //check and fetch date range
    global $drng_cday; $drng_cday = 0; global $drng_furl; $drng_furl = str_replace("_add_","_display_",basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
    include "poulsoft_fetch_daterange_master.php";

?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
        </head>
        <body>
            <div class="card border-secondary mb-3">
                <div class="card-header">Add Receipt</div>
                <form action="chicken_save_customerreceipt1_nb.php" method="post" onsubmit="return checkval();">
                    <div class="card-body">
                        <div class="row">
                            <table align="center">
                                <thead>
                                    <tr>
                                        <th>Date<b style="color:red;">&nbsp;*</b></th>
                                        <th>Customer<b style="color:red;">&nbsp;*</b></th>
                                        <th>Amount<b style="color:red;">&nbsp;*</b></th>
                                        <th>Mode<b style="color:red;">&nbsp;*</b></th>
                                        <th>Cash / Bank<b style="color:red;">&nbsp;*</b></th>
                                        
                                        <th <?php if((int)$dtcds_flag == 1){ echo 'style="visibility:visible;"'; } else{ echo 'style="visibility:hidden;"'; } ?>>TDS</th>
                                        <!-- <th>Doc No</th> -->
                                        
                                        <th>Remarks</th>
                                        <th>Action</th>
                                        <th style="visibility:hidden;">Sector<b >&nbsp;*</b></th>
                                        <th style="visibility:hidden;">T%</th>
                                        <th style="visibility:hidden;">TA</th>
                                        <th style="visibility:hidden;">FA</th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                    <tr style="margin:5px 0px 5px 0px;">
                                        <td><input type="text" name="date[]" id="date[0]" class="form-control range_picker" value="<?php echo date("d.m.Y",strtotime($date)); ?>" style="width:100px;" onchange="fetch_tcds_per(this.id);" readonly /></td>
                                        <td><select name="ccode[]" id="ccode[0]" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($cus_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $cus_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><input type="text" name="amount1[]" id="amount1[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_row_amt(this.id);get_final_total();" onchange="validate_amount(this.id);" /></td>
                                        <td><select name="mode[]" id="mode[0]" class="form-control select2" style="width:180px;" onchange="update_coa_method(this.id);"><option value="select">-select-</option><?php foreach($mode_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $mode_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><select name="code[]" id="code[0]" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($method_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $method_name[$scode]; ?></option><?php } ?></select></td>
                                      
                                        <td <?php if((int)$dtcds_flag == 1){ echo 'style="visibility:visible;text-align:center;"'; } else{ echo 'style="visibility:hidden;text-align:center;"'; } ?>><input type="checkbox" name="tcds_chk[]" id="tcds_chk[0]" onchange="calculate_row_amt(this.id);" /></td>
                                        <!-- <td><input type="text" name="dcno[]" id="dcno[0]" class="form-control" style="width:90px;" onkeyup="validate_name(this.id);" /></td> -->
                                      
                                        <td><textarea name="remarks[]" id="remarks[0]" class="form-control" style="height: 23px;"></textarea></td>
                                        <td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                          <td style="visibility:hidden;"><select name="sector[]" id="sector[0]" class="form-control select2" style="width:180px;"><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php echo ($st_code == $scode) ? 'selected' : ''; ?>><?php echo $sector_name[$scode]; ?></option><?php } ?></select></td>
                                        <td style="visibility:hidden;"><input type="text" name="tcds_per[]" id="tcds_per[0]" class="form-control text-right" value="<?php echo $tcds_per; ?>" style="width:20px;" readonly /></td>
                                        <td style="visibility:hidden;"><input type="text" name="tcds_amt[]" id="tcds_amt[0]" class="form-control text-right" style="width:20px;" readonly /></td>
                                        <td style="visibility:hidden;"><input type="text" name="amount[]" id="amount[0]" class="form-control text-right" style="width:20px;" readonly /></td>
                                    </tr>
                                </tbody>
                                 <tfoot>
                                    <tr>
                                        <th colspan="2" style="text-align:right;">Total</th>
                                        <!-- <th></th>
                                        <th></th> -->
                                        <th><input type="text" name="item_final_total" id="item_final_total" class="form-control text-right" style="width:90px;" readonly /></th>
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
                    window.location.href = "chicken_display_customerreceipt1_nb.php";
                }
                function get_final_total(){
                    var incr = document.getElementById("incr").value;
                    var total = 0;
                    for(var d = 0;d <= incr;d++){
                         var amt = parseFloat(document.getElementById("amount1["+d+"]").value);
                         total += amt;
                    }
                   // console.log(total);
                    document.getElementById("item_final_total").value = parseFloat(total).toFixed(2);
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true;

                    var date = ccode = mode = code = sector = ""; var c = amount1 = amount = 0;
                    var incr = document.getElementById("incr").value;
                    for(var d = 0;d <= incr;d++){
                        if(l == true){
                            c = d + 1;
                            date = document.getElementById("date["+d+"]").value;
                            ccode = document.getElementById("ccode["+d+"]").value;
                            mode = document.getElementById("mode["+d+"]").value;
                            code = document.getElementById("code["+d+"]").value;
                            amount1 = document.getElementById("amount1["+d+"]").value; if(amount1 == ""){ amount1 = 0; }
                            sector = document.getElementById("sector["+d+"]").value;
                            amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }

                            if(date == ""){
                                alert("Please select Date in row: "+c);
                                document.getElementById("date["+d+"]").focus();
                                l = false;
                            }
                            else if(ccode == "select"){
                                alert("Please select Customer in row: "+c);
                                document.getElementById("ccode["+d+"]").focus();
                                l = false;
                            }
                            else if(mode == "select"){
                                alert("Please select Mode in row: "+c);
                                document.getElementById("mode["+d+"]").focus();
                                l = false;
                            }
                            else if(code == "select"){
                                alert("Please select Code in row: "+c);
                                document.getElementById("code["+d+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(amount1) == 0){
                                alert("Please enter Amount in row: "+c);
                                document.getElementById("amount1["+d+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(amount) == 0){
                                alert("Please enter Amount in row: "+c);
                                document.getElementById("amount1["+d+"]").focus();
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
                function fetch_tcds_per(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var date = document.getElementById("date["+d+"]").value;
                    var tdsper = new XMLHttpRequest();
                    var method = "GET";
                    var url = "main_gettcdsvalue.php?type=TCS&cdate="+date;
                    //window.open(url);
                    var asynchronous = true;
                    tdsper.open(method, url, asynchronous);
                    tdsper.send();
                    tdsper.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var tcds_per = this.responseText;
                            if(tcds_per != ""){
                                document.getElementById("tcds_per["+d+"]").value = tcds_per;
                                calculate_row_amt(a);
                            }
                        }
                    }
                }
                function create_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("action["+d+"]").style.visibility = "hidden";

                    var pmode = document.getElementById('mode['+d+']').value;
                    var date = document.getElementById('date['+d+']').value;


                    d++; var html = '';
                    document.getElementById("incr").value = d;
                    var dtcds_flag = '<?php echo (int)$dtcds_flag; ?>';
                    html += '<tr id="row_no['+d+']">';
                    html += '<td><input type="text" name="date[]" id="date['+d+']" class="form-control datepicker" value="'+date+'" style="width:100px;" onchange="fetch_tcds_per(this.id);" readonly /></td>';
                    html += '<td><select name="ccode[]" id="ccode['+d+']" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($cus_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $cus_name[$scode]; ?></option><?php } ?></select></td>';
                    html += '<td><input type="text" name="amount1[]" id="amount1['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_row_amt(this.id);get_final_total();" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><select name="mode[]" id="mode['+d+']" class="form-control select2" style="width:180px;" onchange="update_coa_method(this.id);"><option value="select">-select-</option><?php foreach($mode_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $mode_name[$scode]; ?></option><?php } ?></select></td>';
                    html += '<td><select name="code[]" id="code['+d+']" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($method_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $method_name[$scode]; ?></option><?php } ?></select></td>';
                   
                    if(parseInt(dtcds_flag) == 1){ html += '<td style="visibility:visible;text-align:center;"><input type="checkbox" name="tcds_chk[]" id="tcds_chk['+d+']" onchange="calculate_row_amt(this.id);" /></td>'; }
                    else{ html += '<td style="visibility:hidden;text-align:center;"><input type="checkbox" name="tcds_chk[]" id="tcds_chk['+d+']" onchange="calculate_row_amt(this.id);" /></td>'; }
                    
                    //html += '<td><input type="text" name="dcno[]" id="dcno['+d+']" class="form-control" style="width:90px;" onkeyup="validate_name(this.id);" /></td>';
                    
                 
                    
                    
                    html += '<td><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="height: 23px;"></textarea></td>';
                    html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    html += '<td style="visibility:hidden;"><select name="sector[]" id="sector['+d+']" class="form-control select2" style="width:180px;"><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php echo ($st_code == $scode) ? 'selected' : ''; ?>><?php echo $sector_name[$scode]; ?></option><?php } ?></select></td>';
                    html += '<td style="visibility:hidden;"><input type="text" name="tcds_per[]" id="tcds_per['+d+']" class="form-control text-right" value="<?php echo $tcds_per; ?>" style="width:20px;" readonly /></td>';
                    html += '<td style="visibility:hidden;"><input type="text" name="tcds_amt[]" id="tcds_amt['+d+']" class="form-control text-right" style="width:20px;" readonly /></td>';
                    html += '<td style="visibility:hidden;"><input type="text" name="amount[]" id="amount['+d+']" class="form-control text-right" style="width:20px;" readonly /></td>';
                    html += '</tr>';
                    $('#row_body').append(html);
                    $('.select2').select2();
                    // var rng_mdate = '<?php echo $rng_mdate; ?>';
                    // var today = '<?php echo $today; ?>';
                    // $('.rct_datepickers').datepicker({ dateFormat:'dd.mm.yy',changeMonth:true,changeYear:true,minDate: rng_mdate,maxDate: today,autoclose: true });
                    $( ".datepicker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", minDate: s_date, maxDate: e_date, beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });

                    $('#mode\\[' + d + '\\]').select2(); document.getElementById('mode['+d+']').value = pmode; $('#mode\\[' + d + '\\]').select2();
                    var pfx = 'mode['+d+']';
                    update_coa_method2(pfx);
                }
                function destroy_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("row_no["+d+"]").remove();
                    d--;
                    document.getElementById("incr").value = d;
                    document.getElementById("action["+d+"]").style.visibility = "visible";
                    calculate_final_total_amount();
                }
                function calculate_row_amt(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var amount1 = document.getElementById("amount1["+d+"]").value; if(amount1 == ""){ amount1 = 0; }
                    var tcds_chk = document.getElementById("tcds_chk["+d+"]");
                    var tcds_per = tcds_amt = 0;
                    if(tcds_chk.checked == true){
                        tcds_per = document.getElementById("tcds_per["+d+"]").value; if(tcds_per == ""){ tcds_per = 0; }
                        tcds_amt = (parseFloat(amount1) * (parseFloat(tcds_per) / 100));
                    }
                    document.getElementById("tcds_amt["+d+"]").value = parseFloat(tcds_amt).toFixed(2);

                    var amount = parseFloat(amount1) - parseFloat(tcds_amt);
                    document.getElementById("amount["+d+"]").value = parseFloat(amount).toFixed(2);
                }
                function update_coa_method(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var b = document.getElementById("mode["+d+"]").value;

                    removeAllOptions(document.getElementById("code["+d+"]"));
                    myselect = document.getElementById("code["+d+"]"); //theOption1=document.createElement("OPTION"); theText1=document.createTextNode("select"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);

                    if(b.match("MOD-001")){
                        <?php
                        $sql="SELECT * FROM `acc_coa` WHERE `ctype` LIKE 'Cash' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){ ?> 
                            theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['description']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                        <?php } ?>
                    }
                    else {
                        <?php
                        $sql="SELECT * FROM `acc_coa` WHERE `ctype` LIKE 'Bank' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){ ?> 
                            theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['description']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                        <?php } ?>
                    }
                }
                function update_coa_method2(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var b = document.getElementById("mode["+d+"]").value;

                    removeAllOptions(document.getElementById("code["+d+"]"));
                    myselect = document.getElementById("code["+d+"]"); //theOption1=document.createElement("OPTION"); theText1=document.createTextNode("select"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);

                    if(b.match("MOD-001")){
                        <?php
                        $sql="SELECT * FROM `acc_coa` WHERE `ctype` LIKE 'Cash' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){ ?> 
                            theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['description']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                        <?php } ?>
                    }
                    else {
                        <?php
                        $sql="SELECT * FROM `acc_coa` WHERE `ctype` LIKE 'Bank' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){ ?> 
                            theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['description']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                        <?php } ?>
                    }
                    if(parseInt(d) > 0){
                        var e = parseInt(d) - 1;
                        var code = document.getElementById("code["+e+"]").value;
                        $('#code\\[' + d + '\\]').select2();
                        document.getElementById('code['+d+']').value = code;
                        $('#code\\[' + d + '\\]').select2();
                    }
                }
			    function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
            <script src="handle_ebtn_as_tbtn.js"></script>
             <script>
            //Date Range selection
            var s_date = '<?php echo $rng_sdate; ?>'; var e_date = '<?php echo $rng_edate; ?>';
            $( ".range_picker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", minDate: s_date, maxDate: e_date, beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
        </script>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
