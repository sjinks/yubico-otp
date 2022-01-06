<?php

use PHPUnit\Framework\TestCase;
use WildWolf\Yubico\OTPResponse;

class OTPResponseTest extends TestCase
{
	public function testParse(): void
	{
		$s =  "h=dGsA2NWThFaCHW5Za3lN0hZD3sg=\r\n"
			. "t=2018-10-27T14:41:39Z0174\r\n"
			. "otp=ccccccjknjjnhntdujfhkvhivjnkcrgeibjhfviflkhv\r\n"
			. "nonce=06adbf3280721ab6fc6148ff3cb89516\r\n"
			. "sl=25\r\n"
			. "status=OK\r\n"
		;

		$response = new OTPResponse($s);

		self::assertEquals('2018-10-27T14:41:39Z0174', $response->getTimestamp());
		self::assertEquals('ccccccjknjjnhntdujfhkvhivjnkcrgeibjhfviflkhv', $response->getOTP());
		self::assertEquals('06adbf3280721ab6fc6148ff3cb89516', $response->getNonce());
		self::assertEquals('OK', $response->getStatus());
		self::assertEquals('25', $response->getSyncLevel());
		self::assertEquals('dGsA2NWThFaCHW5Za3lN0hZD3sg=', $response->getSignature());
		self::assertTrue($response->isValid($response->getOTP(), $response->getNonce()));
	}

	public function testParseFull(): void
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

		$response = new OTPResponse($s);

		self::assertEquals('2018-10-27T14:54:59Z0744', $response->getTimestamp());
		self::assertEquals('ccccccjknjjnkhctftrughtnunlvthdddigekhtiijeb', $response->getOTP());
		self::assertEquals('9234d78c2ce3d83cb8901b41dc15e26c', $response->getNonce());
		self::assertEquals('OK', $response->getStatus());
		self::assertEquals('25', $response->getSyncLevel());
		self::assertEquals('hm0rj+viUpgzyls8PUfDQyoIwmQ=', $response->getSignature());
		self::assertEquals('16184983', $response->getInternalTimestamp());
		self::assertEquals('41', $response->getSessionCounter());
		self::assertEquals('2', $response->getSessionUse());
	}

	public function testVerifySignatureNoKey(): void
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

		$response = new OTPResponse($s);
		self::assertTrue($response->verifySignature(''));
	}

	public function testVerifySignature(): void
	{
		$s =  "h=Y6q7vF9H7F+hgfI/ekf4bNsTnjA=\r\n"
			. "t=2018-10-27T14:54:59Z0744\r\n"
			. "otp=ccccccjknjjnkhctftrughtnunlvthdddigekhtiijeb\r\n"
			. "nonce=9234d78c2ce3d83cb8901b41dc15e26c\r\n"
			. "sl=25\r\n"
			. "timestamp=16184983\r\n"
			. "sessioncounter=41\r\n"
			. "sessionuse=2\r\n"
			. "status=OK\r\n"
		;

		$key      = 'somekey';
		$response = new OTPResponse($s);
		self::assertTrue($response->verifySignature($key));
	}
}
