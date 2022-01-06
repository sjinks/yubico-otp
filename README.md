# yubico-otp

[![CI](https://github.com/sjinks/yubico-otp/actions/workflows/ci.yaml/badge.svg)](https://github.com/sjinks/yubico-otp/actions/workflows/ci.yaml)
[![Static Code Analysis](https://github.com/sjinks/yubico-otp/actions/workflows/static-code-analysis.yml/badge.svg)](https://github.com/sjinks/yubico-otp/actions/workflows/static-code-analysis.yml)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=sjinks_yubico-otp&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=sjinks_yubico-otp)

PHP 7/8-friendly alternative to the official [php-yubico](https://github.com/Yubico/php-yubico) client.

## Installation

```bash
composer require wildwolf/yubico-otp
```

## Usage

```php
$otp      = new WildWolf\Yubico\OTP($id, $secret);
$response = null;
$result   = $otp->verify($code, null, &$response);
```

Where:
  * `$id`, `$secret` are the Client ID and the secret key; you will need to [sign up](https://upgrade.yubico.com/getapikey/) for them;
  * `$code` is the OTP code to verify (it will look something like `ccccccjknjjnfffttntuknrfnkednknkfjegcrhhkuut`; see `OTP::parsePasswordOTP()` for its format);
  * `$result` is the verification result (`true` for success, `false` for failure);
  * `$response` is the raw response from Yubico ([details](https://developers.yubico.com/OTP/Specifications/OTP_validation_protocol.html)).

`verify()` can throw `OTPBadResponseException` if the response fails the basic sanity checks, `OTPTamperedResponseException` if the response signature fails to validate, `OTPTransportException` in case of the issues talking to the OTP server.
