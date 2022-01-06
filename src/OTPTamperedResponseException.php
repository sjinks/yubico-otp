<?php

namespace WildWolf\Yubico;

class OTPTamperedResponseException extends OTPException
{
	use OTPResponseTrait;
}
