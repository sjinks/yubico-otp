<?php

class TransportTest extends \PHPUnit\Framework\TestCase
{
    public function testOKResponse()
    {
        $stub = $this->createMock(Helpers\FakeMultiCurl::class);
        $stub->method('getContent')->willReturn(
            "t=2018-11-08T21:27:28Z0995\r\n" .
            "otp=ccccccjknjjnrltckbluuvlciulghunjfjbhniuikceb\r\n" .
            "nonce=58d704a7c0ee24784b661c643028e552\r\n" .
            "sl=25\r\n" .
            "status=OK\r\n"
        );

        $stub->method('readInfo')->willReturn([
            'result' => \CURLE_OK,
            'handle' => 2,
        ]);

        $c  = new Helpers\FakeCurl();
        $t  = new \WildWolf\Yubico\OTP\Transport($stub, $c);

        $p = [
            'id' => 41034,
            'nonce' => '58d704a7c0ee24784b661c643028e552',
            'otp' => 'ccccccjknjjnrltckbluuvlciulghunjfjbhniuikceb',
            'sl' => '',
            'timeout' => 10
        ];

        $this->assertTrue($t->request('', ["fake"], $p));
    }
}
