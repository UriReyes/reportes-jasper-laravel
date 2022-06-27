<?php
//make a trait for api site 24x7
namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait ApiSite24x7
{
    public function getRefreshToken()
    {
        $url = env('ZOHO_API_URL');
        $response = Http::connectTimeout(10)->timeout(5000)->asForm()->retry(3, 5000)->post("{$url}/token", data: [
            'client_id' => env('ZOHO_CLIENT_ID'),
            'client_secret' => env('ZOHO_CLIENT_SECRET'),
            'refresh_token' => env('ZOHO_REFRESH_TOKEN'),
            'grant_type' => "refresh_token"
        ]);
        $response = $response->object();
        return $response->access_token;
    }

    public function getCustomers($url, $refresh_token)
    {
        $authorization = "Zoho-oauthtoken {$refresh_token}";
        $response = Http::connectTimeout(10)->timeout(5000)->withHeaders([
            'Content-Type' => 'application/json;charset=UTF-8',
            'Accept' => 'application/json; version=2.0',
            'Authorization' => $authorization
        ])->retry(3, 10000)->get("{$url}/short/msp/customers");
        $response = $response->json();
        return $response['data'];
    }

    public function getMonitors($url, $zaaid, $refresh_token)
    {
        $authorization = "Zoho-oauthtoken {$refresh_token}";
        $cookie = "zaaid={$zaaid}";
        $response = Http::connectTimeout(10)->timeout(5000)->withHeaders([
            'Content-Type' => 'application/json;charset=UTF-8',
            'Accept' => 'application/json; version=2.0',
            'Authorization' => $authorization,
            'Cookie' => $cookie
        ])->retry(3, 10000)->get("{$url}/monitors");
        $response = $response->json();
        return $response['data'];
    }

    public function getMonitorGroups($url, $zaaid, $refresh_token, $monitor_group)
    {
        $authorization = "Zoho-oauthtoken {$refresh_token}";
        $cookie = "zaaid={$zaaid}";
        $response = Http::connectTimeout(10)->timeout(5000)->withHeaders([
            'Content-Type' => 'application/json;charset=UTF-8',
            'Accept' => 'application/json; version=2.0',
            'Authorization' => $authorization,
            'Cookie' => $cookie
        ])->retry(3, 10000)->get("{$url}/monitor_groups/{$monitor_group}");
        $response = $response->json();
        return $response['data'];
    }

    public function getMonitor($url, $zaaid, $refresh_token, $monitor_id)
    {
        $authorization = "Zoho-oauthtoken {$refresh_token}";
        $cookie = "zaaid={$zaaid}";
        $response = Http::connectTimeout(10)->timeout(5000)->withHeaders([
            'Content-Type' => 'application/json;charset=UTF-8',
            'Accept' => 'application/json; version=2.0',
            'Authorization' => $authorization,
            'Cookie' => $cookie
        ])->retry(3, 10000)->get("{$url}/monitors/{$monitor_id}");
        $response = $response->json();
        return $response['data'];
    }

    public function getCurrentStatus($url, $zaaid, $refresh_token, $monitor_id)
    {
        try{
            $authorization = "Zoho-oauthtoken {$refresh_token}";
            $cookie = "zaaid={$zaaid}";
            $response = Http::connectTimeout(10)->withHeaders([
                'Content-Type' => 'application/json;charset=UTF-8',
                'Accept' => 'application/json; version=2.0',
                'Authorization' => $authorization,
                'Cookie' => $cookie
            ])->timeout(5000)->retry(5, 10000)->get("{$url}/current_status/{$monitor_id}");
            $response = $response->json();
            return $response['data'];
        } catch (\Exception $e) {
            return [
                'data' => [],
            ];
        }
    }

    public function getAvailabilityReport($url, $monitor_id, $zaaid, $refresh_token, $atributos)
    {
        $authorization = "Zoho-oauthtoken {$refresh_token}";
        $cookie = "zaaid={$zaaid}";
        $response = Http::connectTimeout(10)->timeout(5000)->withHeaders([
            'Accept' => 'application/json; version=2.0',
            'Authorization' => $authorization,
            'Cookie' => $cookie
        ])->retry(3, 10000)->get("{$url}/reports/availability_summary/${monitor_id}${atributos}");
        $response = $response->json();
        return $response;
    }

    public function getPerformance($url, $monitor_id, $zaaid, $refresh_token, $attributos)
    {
        try{   
            $authorization = "Zoho-oauthtoken {$refresh_token}";
            $cookie = "zaaid={$zaaid}";
            $response = Http::connectTimeout(10)->timeout(5000)->withHeaders([
                'Accept' => 'application/json; version=2.0',
                'Authorization' => $authorization,
                'Cookie' => $cookie
            ])->retry(3, 10000)->get("{$url}/reports/performance/{$monitor_id}{$attributos}");
            $response = $response->json();
            return $response;
        } catch (\Exception $e) {
            return [
                'data' => [],
                'chart_data' => [],
            ];
        }
    }
    
    public function getPerformanceCharts($url, $monitor_id, $zaaid, $refresh_token, $attributos)
    {
        try{   
            $authorization = "Zoho-oauthtoken {$refresh_token}";
            $cookie = "zaaid={$zaaid}";
            $response = Http::connectTimeout(10)->timeout(5000)->withHeaders([
                'Accept' => 'application/json; version=2.0',
                'Authorization' => $authorization,
                'Cookie' => $cookie
            ])->retry(3, 10000)->get("{$url}/charts/performance/{$monitor_id}{$attributos}");
            $response = $response->json();
            return $response;
        } catch (\Exception $e) {
            return [
                'data' => [],
                'chart_data' => [],
            ];
        }
    }

    public function getPerformanceDiskWidget($url, $monitor_id, $zaaid, $refresh_token, $attributos)
    {
        try{
            $authorization = "Zoho-oauthtoken {$refresh_token}";
            $cookie = "zaaid={$zaaid}";
            $response = Http::connectTimeout(10)->timeout(5000)->withHeaders([
                'Accept' => 'application/json; version=2.0',
                'Authorization' => $authorization,
                'Cookie' => $cookie
            ])->retry(3, 10000)->get("{$url}/monitors/widget_details/{$monitor_id}{$attributos}");
            $response = $response->json();
            return $response;
        } catch (\Exception $e) {
            return [
                'data' => [],
                'chart_data' => [],
            ];
        }
    }
}
