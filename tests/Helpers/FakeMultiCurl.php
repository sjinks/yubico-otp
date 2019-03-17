<?php
namespace Helpers;

class FakeMultiCurl implements \WildWolf\CurlMultiWrapperInterface
{
	private $handles;

	public function addHandle(\WildWolf\CurlWrapperInterface $h)
	{
		$this->handles[(int)$h->handle()] = $h->handle();
	}

	public function removeHandle(\WildWolf\CurlWrapperInterface $h)
	{
		unset($this->handles[(int)$h->handle()]);
	}

	public function setOption($key, $value)
	{
	}

	public function error($error)
	{
	}

	public function errno()
	{
	}

	public function execute(&$still_running)
	{
		$still_running = false;
		return \CURLM_OK;
	}

	public function select($timeout = 1.0)
	{
	}

	public function getContent(\WildWolf\CurlWrapperInterface $h)
	{
		return '';
	}

	public function readInfo(int &$msgs_in_queue = null)
	{
		if (!$this->handles) {
			return false;
		}

		return [
			'msg'    => \CURLMSG_DONE,
			'result' => \CURLE_OK,
			'handle' => key($this->handles),
		];
	}
}
