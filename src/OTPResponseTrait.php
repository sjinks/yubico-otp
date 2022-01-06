<?php

namespace WildWolf\Yubico;

trait OTPResponseTrait
{
	private OTPResponse $response;

	public function __construct(OTPResponse $response)
	{
		$this->response = $response;
	}

	public function getResponse(): OTPResponse
	{
		return $this->response;
	}
}
