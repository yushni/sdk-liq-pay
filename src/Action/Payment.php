<?php
declare(strict_types = 1);

namespace LiqPay\Action;

class Payment extends Action
{
	public static function getAction(): string
	{
		return 'pay';
	}

	public static function fromData(\stdClass $data): Action
	{

		// TODO: Implement fromData() method.
	}
}