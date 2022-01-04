<?php

namespace App\Traits;

use GuzzleHttp\Client;

trait VerifyRecaptchaTrait
{
    /**
     * Verify recaptcha token.
     *
     * @param $token
     * @param $ip
     * @return mixed
     */
    public function checkRecaptcha($token, $ip)
    {
        $response = (new Client())->post(config('recaptcha.verify_uri'), [
            'form_params' => [
                'secret'   => config('recaptcha.secret'),
                'response' => $token,
                'remoteip' => $ip,
            ],
        ]);
        $response = json_decode((string)$response->getBody(), true);

        return $response['success'];
    }
}