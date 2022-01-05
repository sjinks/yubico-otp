<?php

namespace WildWolf\Yubico\OTP;

abstract class Helper
{
	/**
	 * @param array<string,string> $params 
	 */
	public static function buildQueryString(array $params, string $key): string
	{
		ksort($params);
		$qs = '';
		foreach ($params as $k => $v) {
			$qs .= $k . '=' . $v . '&';
		}

		$qs = substr($qs, 0, -1);
		if ($key) {
			$sig = str_replace('+', '%2B', base64_encode(hash_hmac('sha1', $qs, $key, true)));
			$qs .= '&h=' . $sig;
		}

		return $qs;
	}
}
