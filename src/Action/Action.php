<?php
declare(strict_types = 1);

namespace LiqPay\Action;

abstract class Action
{
	protected
		$serverUrl,
		$resultUrl,
		$amount,
		$orderId,
		$currency,
		$description;

	abstract public static function getAction(): string;

	abstract public static function fromData(\stdClass $data): Action;

	public function __construct(
		float $amount,
		string $orderId,
		string $currency,
		string $description
	)
	{
		$this->amount = $amount;
		$this->orderId = $orderId;
		$this->currency = $currency;
		$this->description = $description;
	}

	public function setResultUrl(string $resultUrl)
	{
		$this->resultUrl = $resultUrl;

		return $this;
	}

	public function setServerUrl(string $serverUrl)
	{
		$this->serverUrl = $serverUrl;

		return $this;
	}

	public function toParams(): array
	{
		$params = [
			'amount'      => $this->amount,
			'currency'    => $this->currency,
			'description' => $this->description,
			'action'      => $this->getAction(),
			'order_id'    => $this->orderId,
		];

		if (!empty($this->resultUrl)) {
			$params['result_url']  = $this->resultUrl;
		}

		if (!empty($this->serverUrl)) {
			$params['server_url']  = $this->serverUrl;
		}

		return $params;
	}
}