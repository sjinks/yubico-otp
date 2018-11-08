<?php

class BasicTest extends \PHPUnit\Framework\TestCase
{
    private $yubi;

    protected function setUp()
    {
        $this->yubi = new \WildWolf\Yubico\OTP(27655, '9Tt8Gg51VG/dthDKgopt0n8IXVI=');
    }

    public function testEndpoints()
    {
        $orig  = $this->yubi->getEndpoints();
        $empty = [];

        $this->yubi->setEndpoints($empty);
        $this->assertEquals($empty, $this->yubi->getEndpoints());

        $this->yubi->setEndpoints($orig);
        $this->assertEquals($orig, $this->yubi->getEndpoints());
    }

    public function testTimestamps()
    {
        $orig = $this->yubi->getUseTimestamp();
        $new  = !$orig;

        $this->yubi->setUseTimestamp($new);
        $this->assertNotEquals($orig, $this->yubi->getUseTimestamp());
    }

    public function testSyncLevel()
    {
        $orig = $this->yubi->getSyncLevel();
        $new  = 100;

        $this->yubi->setSyncLevel($new);
        $this->assertNotEquals($orig, $this->yubi->getSyncLevel());
    }

    public function testVerifyDvorak()
    {
        $otp = 'kkcbjp.ecxn.iubjdbcgiyejxpn..d.b.ydpnxcdechk';
        $res = \WildWolf\Yubico\OTP::parsePasswordOTP($otp);
        $this->assertTrue(is_array($res));
    }

    public function testBadOTP()
    {
        $otp = 'kkcbjpaecxn.iubjdbcgiyejxpn..d.b.ydpnxcdechk';
        $this->expectException(\InvalidArgumentException::class);
        \WildWolf\Yubico\OTP::parsePasswordOTP($otp);
    }

    public function testTransport()
    {
        $class = $this->yubi->getTransport();
        $this->assertInstanceOf(\WildWolf\Yubico\OTP\Transport::class, $class);
    }

    public function testOKResponse()
    {
        $this->yubi->setTransport(new class implements \WildWolf\Yubico\OTP\TransportInterface {
            public function request(string $key, array $endpoints, array $params) : bool
            {
                return true;
            }
        });

        $this->assertTrue($this->yubi->verify('vvincrediblegfnchniugtdcbrleehenethrlbihdijv'));
    }
}
