<?php
namespace WildWolf\Yubico\OTP;

interface TransportInterface
{
    public function request(string $key, array $endpoints, array $params) : bool;
}
