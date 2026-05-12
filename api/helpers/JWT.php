<?php

function base64UrlEncode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64UrlDecode($data) {
    return base64_decode(strtr($data, '-_', '+/'));
}

function _jwtSecret(): string {
    return defined('JWT_SECRET') ? JWT_SECRET : 'dev-secret-change-me';
}

function generateJWT(array $payload, ?string $secret = null): string {
    $secret = $secret ?? _jwtSecret();
    $header = ['alg' => 'HS256', 'typ' => 'JWT'];

    $headerEncoded  = base64UrlEncode(json_encode($header));
    $payloadEncoded = base64UrlEncode(json_encode($payload));

    $signature        = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", $secret, true);
    $signatureEncoded = base64UrlEncode($signature);

    return "$headerEncoded.$payloadEncoded.$signatureEncoded";
}

function verifyJWT(string $jwt, ?string $secret = null) {
    $secret = $secret ?? _jwtSecret();
    $parts  = explode('.', $jwt);
    if (count($parts) !== 3) return false;

    [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;
    $validSig = base64UrlEncode(hash_hmac('sha256', "$headerEncoded.$payloadEncoded", $secret, true));
    if (!hash_equals($validSig, $signatureEncoded)) return false;

    $payload = json_decode(base64UrlDecode($payloadEncoded), true);
    if (!is_array($payload)) return false;
    if (isset($payload['exp']) && time() > $payload['exp']) return false;

    return $payload;
}
