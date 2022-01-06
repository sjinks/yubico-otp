<?php

namespace WildWolf\Yubico;

final class OTPResponse
{
	private ?string $otp            = null;
	private ?string $nonce          = null;
	private ?string $h              = null;
	private ?string $t              = null;
	private ?string $status         = null;
	private ?string $timestamp      = null;
	private ?string $sessioncounter = null;
	private ?string $sessionuse     = null;
	private ?string $sl             = null;

	public function __construct(string $s)
	{
		$rows = explode("\r\n", trim($s));
		foreach ($rows as $val) {
			$row = explode('=', $val, 2);
			if (property_exists($this, $row[0])) {
				$this->{$row[0]} = $row[1];
			}
		}
	}

	public function getOTP(): string
	{
		return (string) $this->otp;
	}

	public function getNonce(): string
	{
		return (string) $this->nonce;
	}

	public function getSignature(): string
	{
		return (string) $this->h;
	}

	public function getTimestamp(): string
	{
		return (string) $this->t;
	}

	public function getStatus(): string
	{
		return (string) $this->status;
	}

	public function getInternalTimestamp(): ?string
	{
		return $this->timestamp;
	}

	public function getSessionCounter(): ?string
	{
		return $this->sessioncounter;
	}

	public function getSessionUse(): ?string
	{
		return $this->sessionuse;
	}

	public function getSyncLevel(): ?int
	{
		return $this->sl === null ? null : (int) $this->sl;
	}

	public function isValid(string $orig_otp, string $orig_nonce): bool
	{
		return $this->status !== null && $this->nonce !== null && $this->otp !== null && !strcmp($orig_nonce, $this->nonce) && !strcmp($orig_otp, $this->otp);
	}

	public function calculateSignature(string $key): string
	{
		/** @var string[] */
		static $keys = ['nonce', 'otp', 'sessioncounter', 'sessionuse', 'sl', 'status', 't', 'timeout', 'timestamp'];
		$s           = '';
		foreach ($keys as $k) {
			if (isset($this->$k)) {
				/** @var string */
				$v  = $this->$k;
				$s .= $k . '=' . $v . '&';
			}
		}

		return base64_encode(hash_hmac('sha1', substr($s, 0, -1), $key, true));
	}

	public function verifySignature(string $key): bool
	{
		if ($key) {
			$signature = $this->calculateSignature($key);
			return !strcmp((string) $this->h, $signature);
		}

		return true;
	}
}
