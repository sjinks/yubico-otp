<?php

use PHPUnit\Framework\TestCase;
use WildWolf\Yubico\OTP;
use WildWolf\Yubico\OTP\Transport;
use WildWolf\Yubico\OTP\TransportInterface;
use WildWolf\Yubico\OTPResponse;

/**
 * @psalm-suppress MissingConstructor
 */
class BasicTest extends TestCase
{
	private OTP $yubi;

	protected function setUp(): void
	{
		$this->yubi = new OTP(27655, '9Tt8Gg51VG/dthDKgopt0n8IXVI=');
	}

	public function testEndpoints(): void
	{
		$orig  = $this->yubi->getEndpoint();
		$empty = '';

		$this->yubi->setEndpoint($empty);
		self::assertEquals($empty, $this->yubi->getEndpoint());

		$this->yubi->setEndpoint($orig);
		self::assertEquals($orig, $this->yubi->getEndpoint());
	}

	public function testTimestamps(): void
	{
		$orig = $this->yubi->getUseTimestamp();
		$new  = !$orig;

		$this->yubi->setUseTimestamp($new);
		self::assertNotEquals($orig, $this->yubi->getUseTimestamp());
	}

	public function testSyncLevel(): void
	{
		$orig = $this->yubi->getSyncLevel();
		$new  = 100;

		$this->yubi->setSyncLevel($new);
		self::assertNotEquals($orig, $this->yubi->getSyncLevel());
	}

	public function testVerifyDvorak(): void
	{
		$otp = 'kkcbjp.ecxn.iubjdbcgiyejxpn..d.b.ydpnxcdechk';
		$res = OTP::parsePasswordOTP($otp);
		/** @psalm-suppress RedundantCondition */
		self::assertIsArray($res);
	}

	public function testBadOTP(): void
	{
		$otp = 'kkcbjpaecxn.iubjdbcgiyejxpn..d.b.ydpnxcdechk';
		$this->expectException(InvalidArgumentException::class);
		OTP::parsePasswordOTP($otp);
	}

	public function testTransport(): void
	{
		$class = $this->yubi->getTransport();
		self::assertInstanceOf(Transport::class, $class);
	}

	public function testOKResponse(): void
	{
		$this->yubi->setTransport(new class implements TransportInterface {
			public function request(string $key, string $endpoint, array $params): string
			{
				$s   = sprintf("status=OK\r\notp=%s\r\nnonce=%s\r\nh=%s", $params['otp'], $params['nonce'], '');
				$r   = new OTPResponse($s);
				$sig = $r->calculateSignature($key);
				return sprintf("status=OK\r\notp=%s\r\nnonce=%s\r\nh=%s", $params['otp'], $params['nonce'], $sig);
			}
		});

		self::assertTrue($this->yubi->verify('vvincrediblegfnchniugtdcbrleehenethrlbihdijv'));
	}
}
