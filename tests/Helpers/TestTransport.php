<?php

use WildWolf\Yubico\OTP\TransportInterface;
use WildWolf\Yubico\OTPReplayException;
use WildWolf\Yubico\OTPResponse;

class TestTransport implements TransportInterface
{
	public function request(string $key, string $endpoint, array $params): string
	{
		switch ($params['otp']) {
			case 'vvincrediblegfnchniugtdcbrleehenethrlbihdijv':
				$status = "REPLAYED_OTP";
				break;

			case 'vvincrediblegfnchniugtdcbrleehenethrlbihdijc':
				$status = "BAD_OTP";
				break;

			default:
				$status = "OK";
				break;
		}

		$s   = sprintf("status=%s\r\notp=%s\r\nnonce=%s\r\nh=%s", $status, $params['otp'], $params['nonce'], '');
		$r   = new OTPResponse($s);
		$sig = $r->getSignature($key);
		return sprintf("status=%s\r\notp=%s\r\nnonce=%s\r\nh=%s", $status, $params['otp'], $params['nonce'], $sig);
	}
}
