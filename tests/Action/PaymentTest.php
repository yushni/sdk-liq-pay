<?php

namespace Tests\Action;

use LiqPay\Action\Payment;
use LiqPay\LiqPay;
use PHPUnit\Framework\TestCase;

class PaymentTest extends TestCase
{

    public function testFromData(): void
    {
        $data = [
            'amount'      => 1.24,
            'currency'    => 'USD',
            'description' => 'Something very important',
            'action'      => Payment::ACTION_PAY,
            'status'      => LiqPay::STATUS_SANDBOX,
            'order_id'    => '0498aa12-a891-4963-9841-968849d74cc9',
        ];

        $this->assertInstanceOf(Payment::class, Payment::fromData($data));
    }

    public function testPay(): void
    {
        $params = [
            'amount'      => 1.24,
            'currency'    => 'USD',
            'description' => 'Something very important',
            'action'      => Payment::ACTION_PAY,
            'order_id'    => '0498aa12-a891-4963-9841-968849d74cc9',
            'result_url'  => 'https://easy-visual.com',
            'server_url'  => 'https://easy-visual.com',
        ];

        $pay = Payment::pay($params['amount'], $params['order_id'], $params['currency'], $params['description']);
        $pay->setResultUrl($params['result_url']);
        $pay->setServerUrl($params['server_url']);

        $this->assertEquals($params, $pay->toParams());
    }
}
