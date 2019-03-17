<?php

class YubicoTest extends \PHPUnit\Framework\TestCase
{
	private $yubi;

	protected function setUp()
	{
		$this->yubi = new \WildWolf\Yubico\OTP(27655, '9Tt8Gg51VG/dthDKgopt0n8IXVI=');
	}

	public function testVerify()
	{
		$otp = 'vvincrediblegfnchniugtdcbrleehenethrlbihdijv';
		$this->expectException(WildWolf\Yubico\OTPReplayException::class);
		$this->yubi->verify($otp);
	}

	public function testVerifyNoSig()
	{
		$otp = 'vvincrediblegfnchniugtdcbrleehenethrlbihdijv';
		$this->expectException(WildWolf\Yubico\OTPReplayException::class);
		$yubi = new \WildWolf\Yubico\OTP(27655);
		$yubi->verify($otp);
	}

	public function testBadOTP()
	{
		$otp = 'vvincrediblegfnchniugtdcbrleehenethrlbihdijc';
		$this->expectException(WildWolf\Yubico\OTPNoValidAnswerException::class);
		$this->yubi->verify($otp);
	}
}
