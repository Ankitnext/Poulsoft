<?php
// poulsoft_convert_langmst1.php
function convert_language(mysqli $conns, string $from, string $to, array $words): array{
    $script_url = 'https://script.google.com/macros/s/AKfycbyKl_tOgHrU0wD7Kcaf4sCSYQt3q4P5IU3SPDJEY0tsSh9yEVgcckerlItU8jrBaexx/exec';
    //Step 1: Check Language Column exists
    $sql1 = "SELECT * FROM `master_langualge1` WHERE `sname` LIKE '$to' AND `active` = '1' AND `dflag` = '0'";
    $query1 = mysqli_query($conns,$sql1); $s_cnt = mysqli_num_rows($query1); $cname = ""; $ext_words = $req_words = $rtn_words = array();
    if((int)$s_cnt > 0){
        while($row1 = mysqli_fetch_assoc($query1)){ $cname = $row1['cname']; }

        if($cname != ""){
            /*Check for Column Availability*/
            $sql1 = 'SHOW COLUMNS FROM `Language_master2`'; $query = mysqli_query($conns,$sql1); $existing_col_names = array(); $i = 0;
            while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
            if(in_array("$cname", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `Language_master2` ADD `$cname` VARCHAR(500) NULL DEFAULT NULL COMMENT '' AFTER `default_word`"; mysqli_query($conns,$sql); }

            //Step 2: Check if words are already have converted values
            $w_list = ""; foreach($words as $w1){ if($w_list == ""){ $w_list = $w1; } else{ $w_list .= "','".$w1; } }
            if($w_list != ""){
                $sql1 = "SELECT `default_word`,`$cname` FROM `Language_master2` WHERE `default_word` IN ('$w_list') AND `active` = '1' AND `dflag` = '0'";
                $query1 = mysqli_query($conns,$sql1);
                while($row1 = mysqli_fetch_assoc($query1)){ $ext_words[strtolower($row1['default_word'])] = $row1[$cname]; }
            }
            //Step 3: Identify empty results
            if(sizeof($ext_words) > 0){
                foreach($words as $w1){
                    $w2 = strtolower($w1);
                    if(empty($ext_words[$w2]) || $ext_words[$w2] == ""){ $req_words[$w2] = $w2; } else{ $rtn_words[$w2] = $ext_words[$w2]; }
                }
            }
            else{ foreach($words as $w1){$w2 = strtolower($w1); $req_words[$w2] = $w2; } }
            //Step 4: Google API to fetch new words
            foreach($req_words as $w1){
                $w2 = strtolower($w1);
                if(empty($rtn_words[$w2]) || $rtn_words[$w2] == ""){
                    $new_word = translate_via_api($w2, $from, $to, $script_url);

                    if($new_word != ""){
                        $rtn_words[$w2] = $new_word;
                        //Step 5: Store back the new words
                        $sql1 = "INSERT INTO `Language_master2` (`default_word`, `$cname`) VALUES ('$w2', '$new_word') ON DUPLICATE KEY UPDATE `$cname` = VALUES(`$cname`)";
                        mysqli_query($conns,$sql1);
                    }
                }
            }
            return $rtn_words;
        }
    }
}

function translate_via_api(string $text, string $from, string $to, string $url): string{
    $url = $url . "/exec?source=" . urlencode($from) . "&target=" . urlencode($to);
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $text,
        CURLOPT_HTTPHEADER => ['Content-Type: text/plain'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
    ]);
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if($code === 200 && $response && !str_starts_with($response, 'ERROR:')){ return $response;; }
    return $text;
}
/*
$conns = mysqli_connect("213.165.245.128","poulso6_userlist123","XBiypkFG2TF!9UB","poulso6_userlist") or die('No Connection');
$from = "en";
$to = "te";
$words = ['Hello', 'Welcome', 'Thank You', 'Dear'];

$res_words = convert_language($conns, $from, $to, $words);

if(is_array($res_words)){
    echo print_r($res_words);
}
else{
    echo $res_words;
}
*/