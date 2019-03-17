<?php

class OTPResponseTest extends \PHPUnit\Framework\TestCase
{
	public function testParse()
	{
		$s =  "h=dGsA2NWThFaCHW5Za3lN0hZD3sg=\r\n"
			. "t=2018-10-27T14:41:39Z0174\r\n"
			. "otp=ccccccjknjjnhntdujfhkvhivjnkcrgeibjhfviflkhv\r\n"
			. "nonce=06adbf3280721ab6fc6148ff3cb89516\r\n"
			. "sl=25\r\n"
			. "status=OK\r\n"
		;

		$response = new \WildWolf\Yubico\OTPResponse($s);

		$this->assertEquals('2018-10-27T14:41:39Z0174', $response->timestamp());
		$this->assertEquals('ccccccjknjjnhntdujfhkvhivjnkcrgeibjhfviflkhv', $response->otp());
		$this->assertEquals('06adbf3280721ab6fc6148ff3cb89516', $response->nonce());
		$this->assertEquals('OK', $response->status());
		$this->assertEquals('25', $response->syncLevel());
		$this->assertEquals('dGsA2NWThFaCHW5Za3lN0hZD3sg=', $response->signature());
	}

	public function testParseFull()
	{
		$s =  "h=hm0rj+viUpgzyls8PUfDQyoIwmQ=\r\n"
			. "t=2018-10-27T14:54:59Z0744\r\n"
			. "otp=ccccccjknjjnkhctftrughtnunlvthdddigekhtiijeb\r\n"
			. "nonce=9234d78c2ce3d83cb8901b41dc15e26c\r\n"
			. "sl=25\r\n"
			. "timestamp=16184983\r\n"
			. "sessioncounter=41\r\n"
			. "sessionuse=2\r\n"
			. "status=OK\r\n"
		;

		$response = new \WildWolf\Yubico\OTPResponse($s);

		$this->assertEquals('2018-10-27T14:54:59Z0744', $response->timestamp());
		$this->assertEquals('ccccccjknjjnkhctftrughtnunlvthdddigekhtiijeb', $response->otp());
		$this->assertEquals('9234d78c2ce3d83cb8901b41dc15e26c', $response->nonce());
		$this->assertEquals('OK', $response->status());
		$this->assertEquals('25', $response->syncLevel());
		$this->assertEquals('hm0rj+viUpgzyls8PUfDQyoIwmQ=', $response->signature());
		$this->assertEquals('16184983', $response->internalTimestamp());
		$this->assertEquals('41', $response->sessionCounter());
		$this->assertEquals('2', $response->sessionUse());
	}
}
