<?php

use PHPUnit\Framework\TestCase;
use WildWolf\Yubico\OTP\Helper;

class OTPHelperTest extends TestCase
{
	public function testBuildQueryString(): void
	{
		$params = [
			'b' => '2',
			'a' => '1',
		];

		$secret = 'secret';

		$expected = 'a=1&b=2&h=0Nrzo2wVaw8JsjmGesHcZl4eDk4=';
		$actual   = Helper::buildQueryString($params, $secret);
		self::assertEquals($expected, $actual);
	}
}