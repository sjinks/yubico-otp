<?php
namespace WildWolf\Yubico\OTP;

class Transport implements TransportInterface
{
	protected $key;
	protected $endpoints = [];
	protected $curlmulti;
	protected $curl;

	public function __construct(\WildWolf\CurlMultiWrapperInterface $cm, \WildWolf\CurlWrapperInterface $c)
	{
		$this->curlmulti = $cm;
		$this->curl      = $c;
	}

	public function request(string $key, array $endpoints, array $params) : bool
	{
		$this->key       = $key;
		$this->endpoints = $endpoints;

		$qs              = $this->buildQueryString($params);
		$result          = $this->sendRequests($qs, $params);

		$this->key       = null;
		$this->endpoints = [];

		return $result;
	}

	protected function buildQueryString(array $params) : string
	{
		\ksort($params);
		$qs = '';
		foreach ($params as $k => $v) {
			$qs .= $k . '=' . $v . '&';
		}

		$qs = \substr($qs, 0, -1);
		if ($this->key) {
			$sig = \str_replace('+', '%2B', \base64_encode(\hash_hmac('sha1', $qs, $this->key, true)));
			$qs .= '&h=' . $sig;
		}

		return $qs;
	}

	protected function sendRequests(string $qs, array $params)
	{
		$handles = $this->prepareRequests($qs, $params);
		$result	 = null;

		$still_running = null;
		do {
			while (($mrc = $this->curlmulti->execute($still_running)) == \CURLM_CALL_MULTI_PERFORM) {
			}
		} while ($mrc == \CURLM_OK && ($result = $this->readResponses($params, $handles)) && $still_running);

		if (false === $result) {
			return true;
		}

		throw new \WildWolf\Yubico\OTPNoValidAnswerException();
	}

	protected function prepareRequests(string $qs, array $params) : array
	{
		$options = [
			\CURLOPT_USERAGENT      => 'WW Yubico Auth',
			\CURLOPT_RETURNTRANSFER => 1,
			\CURLOPT_FAILONERROR    => 1,
		];

		if ($params['timeout']) {
			$options[\CURLOPT_TIMEOUT] = $params['timeout'];
		}

		$handles = [];
		foreach ($this->endpoints as $ep) {
			$h = $this->curl->create($ep . '?' . $qs);
			$h->setOptions($options);
			$this->curlmulti->addHandle($h);
			$handles[(int)$h->handle()] = $h;
		}

		return $handles;
	}

	protected function readResponses(array $params, array $handles) : bool
	{
		while (($info = $this->curlmulti->readInfo())) {
			if ($info['result'] == \CURLE_OK) {
				$str = $this->curlmulti->getContent($handles[(int)$info['handle']]);
				if ($this->processResponse($str, $params, $handles)) {
					return false;
				}
			}

			$this->curlmulti->select();
		}

		return true;
	}

	protected function processResponse(string $s, array $params, array $handles) : bool
	{
		$response = new \WildWolf\Yubico\OTPResponse($s);

		if ($response->isValid($params['otp'], $params['nonce']) && $response->verifySignature($this->key)) {
			if ('REPLAYED_OTP' === $response->status()) {
				$this->abortRequests($handles);
				throw new \WildWolf\Yubico\OTPReplayException();
			}

			if ('OK' === $response->status()) {
				$this->abortRequests($handles);
				return true;
			}
		}

		return false;
	}

	protected function abortRequests(array $handles)
	{
		foreach ($handles as $h) {
			$this->curlmulti->removeHandle($h);
			$h->close();
		}
	}
}
