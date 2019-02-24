<?php
declare(strict_types=1);

namespace LiqPay;

use LiqPay\Action\Action;
use LiqPay\Action\Payment;

class LiqPay
{
	private const
		HOST = 'www.liqpay.ua',

		VERSION = 3;

	public const
		CURRENCY_USD = 'USD',
		CURRENCY_UAH = 'UAH',
		CURRENCY_RUB = 'RUB',
		CURRENCY_EUR = 'EUR',

		STATUS_SUCCESS = 'success',
		STATUS_SANDBOX = 'sandbox';

	private
		$publicKey,
		$privateKey,
		$sandbox,
	    $defaultParams;

	public function __construct(
		string $publicKey,
		string $privateKey,
		bool $sandbox
	)
	{
		$this->privateKey = $privateKey;
		$this->publicKey = $publicKey;
		$this->sandbox = $sandbox;

		$this->defaultParams = [
			'public_key' => $publicKey,
			'sandbox' => $sandbox,
			'version' => self::VERSION,
		];
	}

	public function generateCheckoutUrl(Action $action): string
	{
		$params = $action->toParams() + $this->defaultParams;

		$data = $this->encode($params);
		$signature = $this->toSignature($data);

		return $this->generateUrl('api/3/checkout', $data, $signature);
	}

	private function encode(array $params): string
	{
		return base64_encode(stripcslashes(json_encode($params)));
	}

	private function toSignature(string $data): string
	{
		return base64_encode(
			sha1($this->privateKey . $data . $this->privateKey, true)
		);
	}

	public function obtainCallback(string $data, string $signature): Action
	{
		if ($signature !== $this->toSignature($data)) {
			throw new \RuntimeException('Invalid response');
		}

		$data = $this->decode($data);

		switch ($data->action) {
			case Payment::ACTION_PAY:
				return Payment::fromData($data);
		}

		throw new \LogicException('Unsupported action');
	}

	private function decode(string $params): \stdClass
	{
		return json_decode(base64_decode($params));
	}

	private function generateUrl(string $path, string $data, string $signature): string
	{
		$queryParams = http_build_query([
			'data' => $data,
			'signature' => $signature,
		]);

		return sprintf('%s/%s?%s', self::HOST, $path, $queryParams);
	}
}