<?php
namespace WildWolf\Yubico;

class OTP
{
	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var string
	 */
	private $key;

	/**
	 * @var array
	 */
	private $endpoints = [
		'https://api.yubico.com/wsapi/2.0/verify',
		'https://api2.yubico.com/wsapi/2.0/verify',
		'https://api3.yubico.com/wsapi/2.0/verify',
		'https://api4.yubico.com/wsapi/2.0/verify',
		'https://api5.yubico.com/wsapi/2.0/verify',
	];

	/**
	 * @var bool
	 */
	private $usets = false;

	private $synclevel = '';

	private $transport = null;

	public function __construct(string $id, string $key = '')
	{
		$this->id  = $id;
		$this->key = \base64_decode($key);
	}

	public function getEndpoints() : array
	{
		return $this->endpoints;
	}

	public function setEndpoints(array $endpoints)
	{
		$this->endpoints = $endpoints;
	}

	public function getUseTimestamp() : bool
	{
		return $this->usets;
	}

	public function setUseTimestamp(bool $use)
	{
		$this->usets = $use;
	}

	public function getSyncLevel()
	{
		return $this->synclevel;
	}

	public function setSyncLevel($v)
	{
		$this->synclevel = $v;
	}

	public function setTransport(OTP\TransportInterface $v)
	{
		$this->transport = $v;
	}

	public function getTransport() : OTP\TransportInterface
	{
		return $this->transport ?? new OTP\Transport(new \WildWolf\CurlMultiWrapper(), new \WildWolf\CurlWrapper());
	}

	public static function parsePasswordOTP(string $s, string $delim = '[:]') : array
	{
		$re_qwerty = '/^(?:(.*)' . $delim . ')?(([cbdefghijklnrtuv]{0,16})([cbdefghijklnrtuv]{32}))$/i';
		$re_dvorak = '/^(?:(.*)' . $delim . ')?(([jxe.uidchtnbpygk]{0,16})([jxe.uidchtnbpygk]{32}))$/i';
		$m         = [];
		$result    = [];

		if (\preg_match($re_qwerty, $s, $m)) {
			$result['otp'] = $m[2];
		}
		elseif (\preg_match($re_dvorak, $s, $m)) {
			$result['otp'] = \strtr($m[2], 'jxe.uidchtnbpygk', 'cbdefghijklnrtuv');
		}
		else {
			throw new \InvalidArgumentException();
		}

		$result['password']   = $m[1] ?? '';
		$result['prefix']     = $m[3];
		$result['ciphertext'] = $m[4];
		return $result;
	}

	public function verify(string $token, int $timeout = null) : bool
	{
		$ret    = self::parsePasswordOTP($token);
		$params = [
			'id'        => $this->id,
			'otp'       => $ret['otp'],
			'nonce'     => \md5(\openssl_random_pseudo_bytes(32)),
			'timestamp' => (int)$this->usets,
			'sl'        => $this->synclevel,
			'timeout'   => $timeout ?? '',
		];

		$t = $this->getTransport();
		return $t->request($this->key, $this->endpoints, $params);
	}
}
