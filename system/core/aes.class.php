<?php
/**
 * 一个AES算法的加密类
 *
 * @example
 *	 	$aes = new AES();
 * 		$aes->requirePkcs5();
 *		$aes->setKey(AUTH_KEY);
 *		$ret = $aes->encrypt($string);
 *		$string = $aes->decrypt($ret);
 */
class Aes
{
	protected $cipher = MCRYPT_RIJNDAEL_256;
	protected $mode = MCRYPT_MODE_CBC;
	protected $padMethod = NULL;
	protected $secretKey = 'cmstop';
	protected $iv = '';

	public function setCipher($cipher)
	{
		$this->cipher = $cipher;
	}

	public function setMode($mode)
	{
		$this->mode = $mode;
	}

	public function setIV($iv)
	{
		$this->iv = $iv;
	}

	public function setKey($key)
	{
		$this->secretKey = $key;
		$this->setIV(md5($key));
	}

	public function requirePkcs5()
	{
		$this->padMethod = 'pkcs5';
	}

	public function encrypt($str)
	{
		$str = $this->pad($str);
		$td = mcrypt_module_open($this->cipher, '', $this->mode, '');

		if (empty($this->iv)) {
			$iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		} else {
			$iv = $this->iv;
		}

		@mcrypt_generic_init($td, $this->secretKey, $iv);
		$cyper_text = mcrypt_generic($td, $str);
		$rt = base64_encode($cyper_text);
		//$rt = bin2hex($cyper_text);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);

		return $rt;
	}

	public function decrypt($str)
	{
		$td = mcrypt_module_open($this->cipher, '', $this->mode, '');

		if (empty($this->iv)) {
			$iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		} else {
			$iv = $this->iv;
		}

		@mcrypt_generic_init($td, $this->secretKey, $iv);
		//$decrypted_text = mdecrypt_generic($td, self::hex2bin($str));
		$decrypted_text = mdecrypt_generic($td, base64_decode($str));
		$rt = $decrypted_text;
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);

		return $this->unpad($rt);
	}

	public static function hex2bin($hexdata)
	{
		$bindata = '';
		$length = strlen($hexdata);
		for ($i = 0; $i < $length; $i += 2) {
			$bindata .= chr(hexdec(substr($hexdata, $i, 2)));
		}
		return $bindata;
	}

	public static function pkcs5Pad($text, $blocksize)
	{
		$pad = $blocksize - (strlen($text) % $blocksize);
		return $text . str_repeat(chr($pad), $pad);
	}

	public static function pkcs5Unpad($text)
	{
		$pad = ord($text{strlen($text) - 1});
		if ($pad > strlen($text)) return false;
		if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false;
		return substr($text, 0, -1 * $pad);
	}

	protected function padOrUnpad($str, $ext)
	{
		if (is_null($this->padMethod)) {
			return $str;
		} else {
			$func_name = __CLASS__ . '::' . $this->padMethod . ($ext ? ucfirst($ext) . 'pad' : 'Pad');
			if (is_callable($func_name)) {
				$size = mcrypt_get_block_size($this->cipher, $this->mode);
				return call_user_func($func_name, $str, $size);
			}
		}
		return $str;
	}

	protected function pad($str)
	{
		return $this->padOrUnpad($str, '');
	}

	protected function unpad($str)
	{
		return $this->padOrUnpad($str, 'un');
	}
}
