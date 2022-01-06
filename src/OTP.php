<?php

namespace WildWolf\Yubico;

use InvalidArgumentException;
use WildWolf\Yubico\OTP\TransportInterface;

class OTP
{
	private string $id;
	private string $key;
	private string $endpoint = 'https://api.yubico.com/wsapi/2.0/verify';
	private bool $usets = false;
	private string $synclevel = '';
	private ?TransportInterface $transport = null;

	/**
	 * @param string|int $id
	 * @psalm-param int|numeric-string $id
	 */
	public function __construct($id, string $key = '')
	{
		$this->id  = (string) $id;
		$this->key = base64_decode($key);
	}

	public function getEndpoint(): string
	{
		return $this->endpoint;
	}

	public function setEndpoint(string $endpoint): void
	{
		$this->endpoint = $endpoint;
	}

	public function getUseTimestamp(): bool
	{
		return $this->usets;
	}

	public function setUseTimestamp(bool $use): void
	{
		$this->usets = $use;
	}

	public function getSyncLevel(): string
	{
		return $this->synclevel;
	}

	/**
	 * @param string|int $v
	 * @return void 
	 */
	public function setSyncLevel($v): void
	{
		$this->synclevel = (string) $v;
	}

	public function setTransport(OTP\TransportInterface $v): void
	{
		$this->transport = $v;
	}

	public function getTransport() : OTP\TransportInterface
	{
		return $this->transport ?? new OTP\Transport();
	}

	/**
	 * @psalm-return array{otp: string, password: string, prefix: string, ciphertext: string}
	 */
	public static function parsePasswordOTP(string $s, string $delim = '[:]'): array
	{
		$re_qwerty = '/^(?:(.*)' . $delim . ')?(([cbdefghijklnrtuv]{0,16})([cbdefghijklnrtuv]{32}))$/i';
		$re_dvorak = '/^(?:(.*)' . $delim . ')?(([jxe.uidchtnbpygk]{0,16})([jxe.uidchtnbpygk]{32}))$/i';
		$m         = [];
		$result    = [];

		if (preg_match($re_qwerty, $s, $m)) {
			$result['otp'] = $m[2];
		}
		elseif (preg_match($re_dvorak, $s, $m)) {
			$result['otp'] = strtr($m[2], 'jxe.uidchtnbpygk', 'cbdefghijklnrtuv');
		}
		else {
			throw new InvalidArgumentException();
		}

		$result['password']   = $m[1] ?? '';
		$result['prefix']     = $m[3];
		$result['ciphertext'] = $m[4];
		return $result;
	}

	public function verify(string $token, int $timeout = null, ?OTPResponse &$response = null): bool
	{
		$ret    = self::parsePasswordOTP($token);
		$params = [
			'id'        => $this->id,
			'otp'       => $ret['otp'],
			'nonce'     => md5(openssl_random_pseudo_bytes(32)),
			'timestamp' => $this->usets ? '1' : '0',
			'sl'        => $this->synclevel,
			'timeout'   => (string) ($timeout ?? ''),
		];

		$params = array_filter($params, fn (string $s): bool => $s !== '');

		$s = $this->getTransport()->request($this->key, $this->endpoint, $params);
		$response = new OTPResponse($s);

		if (!$response->isValid($params['otp'], $params['nonce'])) {
			throw new OTPBadResponseException($response);
		}

		if (!$response->verifySignature($this->key)) {
			throw new OTPTamperedResponseException($response);
		}

		return 'OK' === $response->getStatus();
	}
}
