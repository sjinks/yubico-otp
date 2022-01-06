<?php

use PHPUnit\Framework\TestCase;
use WildWolf\Yubico\OTP;
use WildWolf\Yubico\OTPBadResponseException;
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
		self::assertEquals('REPLAYED_OTP', $response->getStatus());
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
		self::assertEquals('REPLAYED_OTP', $response->getStatus());
	}

	public function testBadOTP(): void
	{
		$otp      = 'vvincrediblegfnchniugtdcbrleehenethrlbihdijc';
		$response = null;
		$result   = $this->yubi->verify($otp, null, $response);

		self::assertFalse($result);
		self::assertInstanceOf(OTPResponse::class, $response);
		self::assertEquals('BAD_OTP', $response->getStatus());
	}

	public function testBadResponse(): void
	{
		$otp      = 'vvincrediblegfnchniugtdcbrleehenethrlbihdijd';
		$response = null;

		try {
			$this->yubi->verify($otp, null, $response);
			self::assertTrue(false);
		}
		catch (Throwable $e) {
			self::assertNotNull($response);
			self::assertInstanceOf(OTPBadResponseException::class, $e);
			self::assertInstanceOf(OTPResponse::class, $e->getResponse());
		}
	}
}
