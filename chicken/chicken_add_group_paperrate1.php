<?php
//chicken_add_group_paperrate1.php
include "newConfig.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $date = date("Y-m-d"); $today = date("d.m.Y");
    global $trns_dtype; $trns_dtype = "Paper Rate"; include "chicken_fetch_daterangemaster.php"; if($rng_mdate == ""){ $rng_mdate = $today; }
    
    $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%bird%' AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
    while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `main_groups` WHERE `active` = '1' AND `gvpr_flag` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $group_code = $group_name = array();
    while($row = mysqli_fetch_assoc($query)){ $group_code[$row['code']] = $row['code']; $group_name[$row['code']] = $row['description']; }

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
                <div class="card-header">Add Paper Rate</div>
                <form action="chicken_save_group_paperrate1.php" method="post" onsubmit="return checkval();">
                    <div class="card-body">
                        <div class="row">
                            <table align="center">
                                <thead>
                                    <tr>
                                        <th>Date<b style="color:red;">&nbsp;*</b></th>
                                        <th>Customer Group<b style="color:red;">&nbsp;*</b></th>
                                        <?php foreach($item_code as $scode){ echo '<th>'.$item_name[$scode].'</th>'; } ?>
                                        <th style="width:70px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                    <tr style="margin:5px 0px 5px 0px;">
                                        <td><input type="text" name="date[]" id="date[0]" class="form-control range_picker" value="<?php echo date("d.m.Y",strtotime($date)); ?>" style="width:100px;" onchange="fetch_tcds_per(this.id);" readonly /></td>
                                        <td><select name="cgroup[]" id="cgroup[0]" class="form-control select2" style="width:180px;"><option value="all">-All-</option><?php foreach($group_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $group_name[$scode]; ?></option><?php } ?></select></td>
                                        <?php
                                        foreach($item_code as $scode){
                                            $ikey = ""; $ikey = "rate_".$scode;
                                        ?>
                                        <td><input type="text" name="<?php echo $ikey; ?>[]" id="<?php echo $ikey; ?>[0]" title="<?php echo $ikey; ?>[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);" onchange="validate_amount(this.id);" /></td>
                                        <?php } ?>
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
                    window.location.href = "chicken_display_group_paperrate1.php";
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true;

                    var date = cgroup = r_key = ""; var c = r_val = r_nemp = 0;
                    var incr = document.getElementById("incr").value;
                    for(var d = 0;d <= incr;d++){
                        if(l == true){
                            c = d + 1; r_nemp = 0;
                            date = document.getElementById("date["+d+"]").value;
                            cgroup = document.getElementById("cgroup["+d+"]").value;
                            if(date == ""){
                                alert("Please select/enter Date in row: "+c);
                                document.getElementById("date["+d+"]").focus();
                                l = false;
                            }
                            else if(cgroup == "select"){
                                alert("Please select Customer Group in row: "+c);
                                document.getElementById("code["+d+"]").focus();
                                l = false;
                            }
                            else{
                                <?php
                                foreach($item_code as $scode){
                                    $ikey = ""; $ikey = "rate_".$scode;
                                ?>
                                r_key = '<?php echo $ikey; ?>['+d+']';
                                r_val = document.getElementById(r_key).value; if(r_val == ""){ r_val = 0; }
                                if(parseFloat(r_val) > 0){ r_nemp = 1; }
                                <?php
                                }
                                ?>
                                if(parseInt(r_nemp) == 0){
                                    alert("Please enter atleast one item Paper Rate in row: "+c);
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

                    html += '<tr id="row_no['+d+']">';
                    html += '<td><input type="text" name="date[]" id="date['+d+']" class="form-control range_picker" value="<?php echo date("d.m.Y",strtotime($date)); ?>" style="width:100px;" onchange="fetch_tcds_per(this.id);" readonly /></td>';
                    html += '<td><select name="cgroup[]" id="cgroup['+d+']" class="form-control select2" style="width:180px;"><option value="all">-All-</option><?php foreach($group_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $group_name[$scode]; ?></option><?php } ?></select></td>';
                    <?php
                    foreach($item_code as $scode){
                        $ikey = ""; $ikey = "rate_".$scode;
                    ?>
                        html += '<td><input type="text" name="<?php echo $ikey; ?>[]" id="<?php echo $ikey; ?>['+d+']" title="<?php echo $ikey; ?>['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);" onchange="validate_amount(this.id);" /></td>';
                    <?php
                    }
                    ?>
                    html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    html += '</tr>';
                    $('#row_body').append(html);
                    $('.select2').select2();
                    // var rng_mdate = '<?php echo $rng_mdate; ?>';
                    // var today = '<?php echo $today; ?>';
                    // $('.prate_datepickers').datepicker({ dateFormat:'dd.mm.yy',changeMonth:true,changeYear:true,minDate: rng_mdate,maxDate: today,autoclose: true });
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
