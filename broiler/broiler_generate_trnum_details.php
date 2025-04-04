<?php
//broiler_generate_trnum_details.php
function generate_transaction_details($date,$col_name,$pfx_type,$gen_type,$chk_dbname){
    $gconn = mysqli_connect("213.165.245.128","poulso6_userlist123","XBiypkFG2TF!9UB",$chk_dbname);

    //Create Transaction No. Format
    $sql='SHOW COLUMNS FROM `master_generator`'; $query = mysqli_query($gconn,$sql); $egen_cname = array(); $i = 0;
    while($row = mysqli_fetch_assoc($query)){ $egen_cname[$i] = $row['Field']; $i++; }
    if(in_array("$col_name", $egen_cname, TRUE) == ""){ $sql = "ALTER TABLE `master_generator` ADD `$col_name` INT(100) NOT NULL DEFAULT '0' COMMENT '' AFTER `tdate`"; mysqli_query($gconn,$sql); }

    $sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` LIKE '$col_name' AND `active` = '1'"; $query = mysqli_query($gconn,$sql); $count = mysqli_num_rows($query);
    if($count > 0){ } else{ $sql = "INSERT INTO `prefix_master` (`format`, `transaction_type`, `prefix`, `incr_wspb_flag`, `sfin_year_flag`, `sfin_year_wsp_flag`, `efin_year_flag`, `efin_year_wsp_flag`, `day_flag`, `day_wsp_flag`, `month_flag`, `month_wsp_flag`, `year_flag`, `year_wsp_flag`, `hour_flag`, `hour_wsp_flag`, `minute_flag`, `minute_wsp_flag`, `second_flag`, `second_wsp_flag`, `active`) VALUES ('column:flag', '$col_name', '$pfx_type-', '0', '1:1', '1', '0', '2:1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1');"; mysqli_query($gconn,$sql); }

    //Generate Invoice transaction number format
    $sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date' AND `tdate` >= '$date'"; $query = mysqli_query($gconn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $fyear = $row['prefix']; }
    
    $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($gconn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $col_val = $row[$col_name]; } $incr = $col_val + 1;

    if($gen_type == "generate"){
        $sql = "UPDATE `master_generator` SET `$col_name` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
        if(!mysqli_query($gconn,$sql)){ die("Error:-".mysqli_error($gconn)); } else { }
    }

    $sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` LIKE '$col_name' AND `active` = '1'"; $query = mysqli_query($gconn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $prefix = $row['prefix']; $incr_wspb_flag = $row['incr_wspb_flag']; $inv_format[$row['sfin_year_flag']] = "sfin_year_flag"; $inv_format[$row['sfin_year_wsp_flag']] = "sfin_year_wsp_flag"; $inv_format[$row['efin_year_flag']] = "efin_year_flag"; $inv_format[$row['efin_year_wsp_flag']] = "efin_year_wsp_flag"; $inv_format[$row['day_flag']] = "day_flag"; $inv_format[$row['day_wsp_flag']] = "day_wsp_flag"; $inv_format[$row['month_flag']] = "month_flag"; $inv_format[$row['month_wsp_flag']] = "month_wsp_flag"; $inv_format[$row['year_flag']] = "year_flag"; $inv_format[$row['year_wsp_flag']] = "year_wsp_flag"; $inv_format[$row['hour_flag']] = "hour_flag"; $inv_format[$row['hour_wsp_flag']] = "hour_wsp_flag"; $inv_format[$row['minute_flag']] = "minute_flag"; $inv_format[$row['minute_wsp_flag']] = "minute_wsp_flag"; $inv_format[$row['second_flag']] = "second_flag"; $inv_format[$row['second_wsp_flag']] = "second_wsp_flag"; }
    $a = 1; $tr_code = $prefix;
    for($j = 0;$j <= 16;$j++){
        if(!empty($inv_format[$j.":".$a])){
            if($inv_format[$j.":".$a] == "sfin_year_flag"){ $tr_code = $tr_code."".mb_substr($fyear, 0, 2, 'UTF-8'); }
            else if($inv_format[$j.":".$a] == "sfin_year_wsp_flag"){ $tr_code = $tr_code."".mb_substr($fyear, 0, 2, 'UTF-8')."-"; }
            else if($inv_format[$j.":".$a] == "efin_year_flag"){ $tr_code = $tr_code."".mb_substr($fyear, 2, 2, 'UTF-8'); }
            else if($inv_format[$j.":".$a] == "efin_year_wsp_flag"){ $tr_code = $tr_code."".mb_substr($fyear, 2, 2, 'UTF-8')."-"; }
            else if($inv_format[$j.":".$a] == "day_flag"){ $tr_code = $tr_code."".date("d"); }
            else if($inv_format[$j.":".$a] == "day_wsp_flag"){ $tr_code = $tr_code."".date("d")."-"; }
            else if($inv_format[$j.":".$a] == "month_flag"){ $tr_code = $tr_code."".date("m"); }
            else if($inv_format[$j.":".$a] == "month_wsp_flag"){ $tr_code = $tr_code."".date("m")."-"; }
            else if($inv_format[$j.":".$a] == "year_flag"){ $tr_code = $tr_code."".date("Y"); }
            else if($inv_format[$j.":".$a] == "year_wsp_flag"){ $tr_code = $tr_code."".date("Y")."-"; }
            else if($inv_format[$j.":".$a] == "hour_flag"){ $tr_code = $tr_code."".date("H"); }
            else if($inv_format[$j.":".$a] == "hour_wsp_flag"){ $tr_code = $tr_code."".date("H")."-"; }
            else if($inv_format[$j.":".$a] == "minute_flag"){ $tr_code = $tr_code."".date("i"); }
            else if($inv_format[$j.":".$a] == "minute_wsp_flag"){ $tr_code = $tr_code."".date("i")."-"; }
            else if($inv_format[$j.":".$a] == "second_flag"){ $tr_code = $tr_code."".date("s"); }
            else if($inv_format[$j.":".$a] == "second_wsp_flag"){ $tr_code = $tr_code."".date("s")."-"; }
            else{ }
        }
    }
    if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
    $trnum = ""; if($incr_wspb_flag == 1|| $incr_wspb_flag == "1"){ $trnum = $tr_code."-".$incr; } else{ $trnum = $tr_code."".$incr; }
    return $incr."@".$prefix."@".$trnum."@".$fyear;
    if($gconn){ mysqli_close($gconn); }
    $egen_cname = $inv_format = array();
}