<?php
namespace Helpers;

class FakeCurl implements \WildWolf\CurlWrapperInterface
{
	private static $ctr = 1;
	private $h;

	public static function create(string $url = null)
	{
		return new self();
	}

	public function __construct()
	{
		$this->h = self::$ctr++;
	}

	public function setOption($key, $value)
	{
	}

	public function setOptions(array $opts)
	{
	}

	public function execute()
	{
	}

	public function reset()
	{
	}

	public function info(int $key = null)
	{
	}

	public function error()
	{
	}

	public function errno()
	{
	}

	public function handle()
	{
		return $this->h;
	}

	public function close()
	{
	}
}
