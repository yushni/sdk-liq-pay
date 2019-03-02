<?php

namespace tests\Encoder;

use LiqPay\Encoder\Encoder;
use PHPUnit\Framework\TestCase;

class EncoderTest extends TestCase
{
    private
        $encoder;

    public function __construct (?string $name = null, array $data = [], string $dataName = '')
    {
        $this->encoder = new Encoder();
        parent::__construct($name, $data, $dataName);
    }

    public function testEncode (): void
    {
        $expectedString = '{"public":"\some \hard \key","private":"\/fdafdsa"}';

        $params = [
            'public'  => '\\some \\hard \\key',
            'private' => '\/fdafdsa',
        ];

        $got = $this->encoder->encode($params);

        $this->assertEquals(base64_encode($expectedString), $got);
    }

    public function testDecode (): void
    {
        $expected = [
            'public'  => 'publicKey',
            'private' => '\/fdafdsa',
        ];

        $got = $this->encoder
            ->decode($this->encoder->encode($expected));

        $expected['private'] = '/fdafdsa';

        $this->assertEquals($expected, $got);
    }

    public function testGenerateSignature (): void
    {
        $params = [
            'public'  => 'publicKey',
            'private' => '\/fdafdsa',
        ];

        $data = $this->encoder->encode($params);

        $got = $this->encoder->generateSignature($data, '12345');

        $this->assertEquals('Wy98txiU4TkYGH9A54cnu0ynZ1M=', $got);
    }
}
