<?php

namespace WildWolf\Yubico\OTP;

use WildWolf\Yubico\OTPException;

class Transport implements TransportInterface
{
	public function request(string $key, string $endpoint, array $params): string
	{
		$qs = Helper::buildQueryString($params, $key);
		return $this->sendRequest($endpoint . '?' . $qs);
	}

	protected function sendRequest(string $endpoint): string
	{
		$s = file_get_contents($endpoint);
		if (false === $s) {
			throw new OTPException();
		}

		return $s;
	}
}
