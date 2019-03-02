<?php

namespace Tests;

use LiqPay\Action\Payment;
use LiqPay\Encoder\EncoderInterface;
use LiqPay\LiqPay;
use PHPUnit\Framework\TestCase;

class LiqPayTest extends TestCase
{
    /** @var LiqPay */
    private $liqPay;

    protected function setUp ()
    {
        $encoder = $this->getMockBuilder(EncoderInterface::class)
            ->setMethods(['encode', 'decode', 'generateSignature'])
            ->getMock();

        $encoder
            ->method('encode')
            ->withAnyParameters()
            ->willReturn('data');

        $encoder
            ->method('generateSignature')
            ->withAnyParameters()
            ->willReturn('sign');

        $this->liqPay = new LiqPay('1', '2', '3', $encoder);
    }

    public function testGenerateCheckoutUrl ()
    {
        $payment = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->setMethods(['toParams'])
            ->getMock();

        $payment->expects($this->once())
            ->method('toParams')
            ->willReturn(['params']);

        $expected = 'www.liqpay.ua/api/3/checkout?data=data&signature=sign';
        $got = $this->liqPay->generateCheckoutUrl($payment);

        $this->assertEquals($expected, $got);
    }
}