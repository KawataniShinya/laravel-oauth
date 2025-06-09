<?php

namespace App\UseCase;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class FetchCustomerList
{
    /**
     * @param string $accessToken
     * @return array|string|null
     */
    public function handle(string $accessToken): array|string|null
    {
        try {
            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->get(config('services.resource.customer_url'));

            if ($response->status() === 403) {
                return $response->body();
            }

            if (!$response->ok()) {
                return null;
            }

            return $response->json();
        } catch (ConnectionException $e) {
            return null;
        }
    }
}
