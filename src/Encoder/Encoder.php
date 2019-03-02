<?php

namespace LiqPay\Encoder;

class Encoder implements EncoderInterface
{
    public function encode(array $params): string
    {
        $data = stripcslashes(json_encode($params));

        return base64_encode($data);
    }

    public function decode(string $params): array
    {
        return json_decode(base64_decode($params), true);
    }

    public function generateSignature(string $data, string $privateKey): string
    {
        return base64_encode(
            sha1($privateKey . $data . $privateKey, true)
        );
    }
}