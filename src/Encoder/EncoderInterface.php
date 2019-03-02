<?php

namespace LiqPay\Encoder;

interface EncoderInterface
{
    public function encode (array $params): string;

    public function decode (string $params): array;

    public function generateSignature (string $data, string $privateKey);
}