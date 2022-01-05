<?php

use PHPUnit\Framework\TestCase;
use WildWolf\Yubico\OTP;
use WildWolf\Yubico\OTPReplayException;

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
		$otp = 'vvincrediblegfnchniugtdcbrleehenethrlbihdijv';
		$this->expectException(OTPReplayException::class);
		$this->yubi->verify($otp);
	}

	public function testVerifyNoSig(): void
	{
		$otp = 'vvincrediblegfnchniugtdcbrleehenethrlbihdijv';
		$this->expectException(OTPReplayException::class);
		$yubi = new OTP(27655);
		$yubi->setTransport(new TestTransport());
		$yubi->verify($otp);
	}

	public function testBadOTP(): void
	{
		$otp = 'vvincrediblegfnchniugtdcbrleehenethrlbihdijc';
		self::assertFalse($this->yubi->verify($otp));
	}
}
