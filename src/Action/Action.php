<?php
declare(strict_types=1);

namespace LiqPay\Action;

use LiqPay\LiqPay;

abstract class Action
{
    protected
        $serverUrl,
        $resultUrl,
        $amount,
        $orderId,
        $currency,
        $action,
        $status,
        $description;

    public function __construct (
        float $amount,
        string $orderId,
        string $currency,
        string $action,
        string $description
    )
    {
        $this->amount = $amount;
        $this->orderId = $orderId;
        $this->currency = $currency;
        $this->description = $description;
        $this->action = $action;
    }

    public static function fromData (array $data): Action
    {
        $requiredFields = [
            'status',
            'amount',
            'order_id',
            'currency',
            'description',
            'action',
        ];

        foreach ($requiredFields as $requiredField) {
            if (empty($data[$requiredField])) {
                throw new \RuntimeException(sprintf('Field %s is not valid.', $requiredField));
            }
        }

        $action = new static(
            $data['amount'],
            $data['order_id'],
            $data['currency'],
            $data['action'],
            $data['description']
        );

        $action->status = $data['status'];

        return $action;
    }

    public function setResultUrl (string $resultUrl)
    {
        $this->resultUrl = $resultUrl;

        return $this;
    }

    public function setServerUrl (string $serverUrl)
    {
        $this->serverUrl = $serverUrl;

        return $this;
    }

    public function getAmount (): float
    {
        return $this->amount;
    }

    public function getOrderId (): string
    {
        return $this->orderId;
    }

    public function isSuccess (): bool
    {
        return in_array($this->status, [LiqPay::STATUS_SANDBOX, LiqPay::STATUS_SUCCESS], true);
    }

    public function getCurrency (): string
    {
        return $this->currency;
    }

    public function getDescription (): string
    {
        return $this->description;
    }

    public function getAction (): string
    {
        return $this->action;
    }

    public function toParams (): array
    {
        $params = [
            'amount'      => $this->amount,
            'currency'    => $this->currency,
            'description' => $this->description,
            'action'      => $this->action,
            'order_id'    => $this->orderId,
        ];

        if (!empty($this->resultUrl)) {
            $params['result_url'] = $this->resultUrl;
        }

        if (!empty($this->serverUrl)) {
            $params['server_url'] = $this->serverUrl;
        }

        return $params;
    }
}