<?php

use PHPUnit\Framework\TestCase;
use WildWolf\Yubico\OTP;
use WildWolf\Yubico\OTPReplayException;
use WildWolf\Yubico\OTPResponse;

/**
 * @psalm-suppress MissingConstructor
 */
class YubicoTest extends TestCase
{
	private OTP $yubi;

	protected function setUp(): void
	{
		$this->yubi = new OTP(27655, '9Tt8Gg51VG/dthDKgopt0n8IXVI=');
		$this->yubi->setTransport(new TestTransport());
	}

	public function testVerify(): void
	{
		$otp      = 'vvincrediblegfnchniugtdcbrleehenethrlbihdijv';
		$response = null;
		$result   = $this->yubi->verify($otp, null, $response);

		self::assertFalse($result);
		self::assertInstanceOf(OTPResponse::class, $response);
		self::assertEquals('REPLAYED_OTP', $response->status());
	}

	public function testVerifyNoSig(): void
	{
		$otp  = 'vvincrediblegfnchniugtdcbrleehenethrlbihdijv';
		$yubi = new OTP(27655);
		$yubi->setTransport(new TestTransport());

		$response = null;
		$result   = $yubi->verify($otp, null, $response);

		self::assertFalse($result);
		self::assertInstanceOf(OTPResponse::class, $response);
		self::assertEquals('REPLAYED_OTP', $response->status());
	}

	public function testBadOTP(): void
	{
		$otp      = 'vvincrediblegfnchniugtdcbrleehenethrlbihdijc';
		$response = null;
		$result   = $this->yubi->verify($otp, null, $response);

		self::assertFalse($result);
		self::assertInstanceOf(OTPResponse::class, $response);
		self::assertEquals('BAD_OTP', $response->status());
	}
}
