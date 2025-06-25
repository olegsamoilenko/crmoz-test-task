<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ZohoService
{
    public function getAccessToken()
    {
        $cacheKey = 'zoho_access_token';
        return Cache::remember($cacheKey, 3500, function () {
            $refreshToken = env('ZOHO_CURRENT_REFRESH_TOKEN');
            $clientId = env('ZOHO_CLIENT_ID');
            $clientSecret = env('ZOHO_CLIENT_SECRET');
            $region = env('ZOHO_REGION', 'eu');

            $response = Http::asForm()->post("https://accounts.zoho.{$region}/oauth/v2/token", [
                'refresh_token' => $refreshToken,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'grant_type' => 'refresh_token',
            ]);

            if (!$response->ok()) {
                throw new \Exception('Zoho access token refresh failed: ' . $response->body());
            }

            return $response->json('access_token');
        });
    }

    public function getDealStages()
    {
        $accessToken = $this->getAccessToken();
        $apiDomain = env('ZOHO_API_DOMAIN', "https://www.zohoapis.eu");
        $url = "{$apiDomain}/crm/v2/settings/fields?module=Deals";

        $response = Http::withToken($accessToken)->get($url);
        if (!$response->ok()) {
            throw new \Exception('Zoho get fields error: ' . $response->body());
        }

        $fields = $response->json('fields');
        $stageField = collect($fields)->firstWhere('api_name', 'Stage');
        $picklistValues = $stageField['pick_list_values'] ?? [];

        return collect($picklistValues)
            ->filter(fn($v) => !($v['hidden'] ?? false))
            ->pluck('display_value')
            ->values()
            ->all();
    }

    public function createAccount(array $data)
    {
        $accessToken = $this->getAccessToken();
        $apiDomain = env('ZOHO_API_DOMAIN', "https://www.zohoapis.eu");
        $url = "{$apiDomain}/crm/v2/Accounts";

        $response = \Http::withToken($accessToken)->post($url, [
            'data' => [$data]
        ]);

        if (!in_array($response->status(), [200, 201])) {
            throw new \Exception('Create Account error.');
        }

        $created = $response->json('data')[0] ?? [];
        return $created['details']['id'] ?? null;
    }

    public function createDeal(array $data)
    {
        $accessToken = $this->getAccessToken();
        $apiDomain = env('ZOHO_API_DOMAIN', "https://www.zohoapis.eu");
        $url = "{$apiDomain}/crm/v2/Deals";

        $response = \Http::withToken($accessToken)->post($url, [
            'data' => [$data]
        ]);

        if (!in_array($response->status(), [200, 201])) {
            throw new \Exception('Create Deal error.');
        }

        $created = $response->json('data')[0] ?? [];
        return $created['details']['id'] ?? null;
    }
}
