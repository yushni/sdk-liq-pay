<?php
declare(strict_types = 1);

namespace LiqPay;

use LiqPay\Action\Action;
use LiqPay\Action\Payment;
use LiqPay\Encoder\EncoderInterface;

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
		$privateKey,
		$defaultParams,
        $actions,
        $encoder;

    public function __construct(
		string $publicKey,
		string $privateKey,
		bool $sandbox,
        EncoderInterface $encoder
	)
	{
		$this->privateKey = $privateKey;
        $this->encoder = $encoder;

		$this->defaultParams = [
			'public_key' => $publicKey,
			'sandbox'    => $sandbox,
			'version'    => self::VERSION,
		];

        $this->addAction(Payment::class, [Payment::ACTION_PAY]);
    }

	public function generateCheckoutUrl(Action $action): string
	{
		$params = $action->toParams() + $this->defaultParams;

		$data = $this->encoder->encode($params);
		$signature = $this->encoder->generateSignature($data, $this->privateKey);

		return $this->generateUrl('api/3/checkout', $data, $signature);
	}

	public function obtainCallback(string $data, string $signature): Action
	{
	    if ($signature !== $this->encoder->generateSignature($data, $this->privateKey)) {
			throw new \RuntimeException('Invalid response');
		}

        $data = $this->encoder->decode($data);

        foreach ($this->actions as $class => $actions) {
            if (\in_array($data['action'], $actions, true)) {
                /** @var $class Action */
                return $class::fromData($data);
            }
        }

		throw new \LogicException('Unsupported action');
	}

    public function addAction(string $actionClassName, array $actions): void
    {
        $this->actions[$actionClassName] = $actions;
	}

	private function generateUrl(string $path, string $data, string $signature): string
	{
		$queryParams = http_build_query([
			'data'      => $data,
			'signature' => $signature,
		]);

		return sprintf('%s/%s?%s', self::HOST, $path, $queryParams);
	}
}