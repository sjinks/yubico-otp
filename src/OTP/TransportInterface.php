<?php

namespace WildWolf\Yubico\OTP;

interface TransportInterface
{
	/**
	 * @psalm-param array<string,string> $params
	 */
	public function request(string $key, string $endpoint, array $params): string;
}
