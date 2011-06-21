<?PHP

class tCrypt {
	
	var $ckey = 'kjdhfljs7836428//sd+++saas####';

	function encrypt($string) {
		
		$result = NULL;
		
		for($i=0; $i<strlen($string); $i++) {
		
			$char = substr($string, $i, 1);
			$keychar = substr($this->ckey, ($i % strlen($this->ckey))-1, 1);
			$char = chr(ord($char)+ord($keychar));
			$result.=$char;
		}
		$code = base64_encode($result);
		return str_replace(array('+','/','='), array('-','_',''), $code);
	}
	function decrypt($string) {
		
		$result = NULL;
		
		$data = str_replace(array('-','_'),array('+','/'), $string);
		$mod4 = strlen($data) % 4;
	
		if ($mod4) {
			$data .= substr('====', $mod4);
		}
		
		$string = base64_decode($data);
		
		for($i=0; $i<strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($this->ckey, ($i % strlen($this->ckey))-1, 1);
			$char = chr(ord($char)-ord($keychar));
			$result.=$char;
		}
		return $result;
	}
}

?>