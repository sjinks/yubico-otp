<?php

namespace WildWolf\Yubico\OTP;

use WildWolf\Yubico\OTPTransportException;

final class Transport implements TransportInterface
{
	public function request(string $key, string $endpoint, array $params): string
	{
		$qs = Helper::buildQueryString($params, $key);
		return $this->sendRequest($endpoint . '?' . $qs);
	}

	protected function sendRequest(string $endpoint): string
	{
		$e = error_reporting();
		if ($e & E_WARNING) {
			error_reporting($e ^ E_WARNING);
		}

		$s = file_get_contents($endpoint);
		if ($e & E_WARNING) {
			error_reporting($e);
		}

		if (false === $s) {
			throw new OTPTransportException();
		}

		return $s;
	}
}
