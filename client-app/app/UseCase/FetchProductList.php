<?php

namespace App\UseCase;

use Illuminate\Support\Facades\Http;

class FetchProductList
{
    /**
     * @param string $accessToken
     * @return array|null
     */
    public function handle(string $accessToken): ?array
    {
        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->get(config('services.resource.product_url'));

        if (!$response->ok()) {
            return null;
        }

        return $response->json();
    }
}
