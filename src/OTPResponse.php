<?php
namespace WildWolf\Yubico;

final class OTPResponse
{
    private $otp            = null;
    private $nonce          = null;
    private $h              = null;
    private $t              = null;
    private $status         = null;
    private $timestamp      = null;
    private $sessioncounter = null;
    private $sessionuse     = null;
    private $sl             = null;

    public function __construct(string $s)
    {
        $rows     = \explode("\r\n", \trim($s));
        foreach ($rows as $val) {
            $row = explode('=', $val, 2);
            $this->{$row[0]} = $row[1];
        }
    }

    public function otp()
    {
        return $this->otp;
    }

    public function nonce()
    {
        return $this->nonce;
    }

    public function signature()
    {
        return $this->h;
    }

    public function timestamp()
    {
        return $this->t;
    }

    public function status()
    {
        return $this->status;
    }

    public function internalTimestamp()
    {
        return $this->timestamp;
    }

    public function sessionCounter()
    {
        return $this->sessioncounter;
    }

    public function sessionUse()
    {
        return $this->sessionuse;
    }

    public function syncLevel()
    {
        return $this->sl;
    }

    public function isValid(string $orig_otp, string $orig_nonce) : bool
    {
        return
               $this->status !== null
            && !\strcmp($orig_nonce, $this->nonce)
            && !\strcmp($orig_otp, $this->otp)
        ;
    }

    public function verifySignature($key)
    {
        if ($key) {
            static $keys = ['nonce', 'otp', 'sessioncounter', 'sessionuse', 'sl', 'status', 't', 'timeout', 'timestamp'];
            $s          = '';
            foreach ($keys as $k) {
                if (isset($this->$k)) {
                    $s .= $k . '=' . $this->$k . '&';
                }
            }

            return
                !\strcmp($this->h, \base64_encode(\hash_hmac('sha1', \substr($s, 0, -1), $key, true)))
            ;
        }

        return true;
    }
}
