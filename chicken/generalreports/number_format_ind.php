<?php
	function number_format_ind($number){
		if($number == ''){
			$number = 0;
		}
		if($number < 0) {
			$negativeflag = 1;
			$number = abs($number);
		}
		else {
			$negativeflag = 0;
		}
		$nums = explode(".",$number);
		$decimal = $nums[1];
		if($decimal >= 10){ $decimal = ".".$decimal; } else if( $decimal == 0) { $decimal = ".00"; } else { $decimal = ".".$decimal."0"; }
		$money = $nums[0];
		$length = strlen($money);
		$delimiter = '';
		$money = strrev($money);

		for($i=0;$i<$length;$i++){
			if(( $i==3 || ($i>3 && ($i-1)%2==0) )&& $i!=$length){
		$delimiter .=',';
			}
			$delimiter .=$money[$i];
		}

		$result = strrev($delimiter);
		$decimal = preg_replace("/0\./i", ".", $decimal);
		$decimal = substr($decimal, 0, 3);

		if( $decimal != '0'){
			$result = $result.$decimal;
		}
		else {
			$result = $result.$decimal;
		}
		if($negativeflag == 1) {
			$result = "-".$result;
		}
		else { }
		if($result == "INF.00" || $result == "NAN.00" || $result == ".00"){ $result = "0.00"; }
		return $result;
	}
?>