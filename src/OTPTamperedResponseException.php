<?php

namespace WildWolf\Yubico;

final class OTPTamperedResponseException extends OTPException
{
	use OTPResponseTrait;
}
