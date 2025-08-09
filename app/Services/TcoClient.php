<?php
namespace App\Services;

use Tco\TwocheckoutFacade;

class TcoClient
{
    public function client(): TwocheckoutFacade
    {
        return new TwocheckoutFacade([
            'sellerId'          => env('TCO_SELLER_ID'),
            'secretKey'         => env('TCO_SECRET_KEY'),
            'buyLinkSecretWord' => env('TCO_BUYLINK_SECRET'),
            'jwtExpireTime'     => 30,
            'curlVerifySsl'     => 1,
        ]);
    }

    public function signBuyLink(array $params): string
    {
        return $this->client()->getBuyLinkSignature($params);
    }

    public function validateIpn(array $payload): bool
    {
        return $this->client()->validateIpnResponse($payload);
    }

    public function ipnResponseToken(array $payload): string
    {
        return $this->client()->generateIpnResponse($payload);
    }
}
