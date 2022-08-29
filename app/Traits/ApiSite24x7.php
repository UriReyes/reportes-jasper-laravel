<?php
//make a trait for api site 24x7
namespace App\Traits;

use App\Events\ReloadProcessOnErrorAPI;
use Error;
use Illuminate\Support\Facades\Http;

trait ApiSite24x7
{
    public function getRefreshToken()
    {
        try {
            $url = env('ZOHO_API_URL');
            $response = Http::connectTimeout(10)->timeout(5000)->asForm()->retry(3, 5000)->post("{$url}/token", data: [
                'client_id' => env('ZOHO_CLIENT_ID'),
                'client_secret' => env('ZOHO_CLIENT_SECRET'),
                'refresh_token' => env('ZOHO_REFRESH_TOKEN'),
                'grant_type' => "refresh_token"
            ]);
            $response = $response->object();
            return $response->access_token;
        } catch (\Throwable | \GuzzleHttp\Exception\GuzzleException $th) {
            return $response->error;
        }
    }

    public function getCustomers($url, $refresh_token)
    {
        try {
            $authorization = "Zoho-oauthtoken {$refresh_token}";
            $response = Http::connectTimeout(10)->timeout(5000)->withHeaders([
                'Content-Type' => 'application/json;charset=UTF-8',
                'Accept' => 'application/json; version=2.0',
                'Authorization' => $authorization
            ])->retry(3, 10000)->get("{$url}/short/msp/customers");
            $response = $response->json();
            return $response['data'];
        } catch (\Throwable | \GuzzleHttp\Exception\GuzzleException $th) {
            return $th->getCode();
        }
    }

    public function getMonitors($url, $zaaid, $refresh_token)
    {
        try {
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
        } catch (\Throwable $th) {
            // dd('1');
            if (str_contains($th->getMessage(), '"message":"Invalid data provided."')) {
                return 'error';
            } else {
                // die();
                event(new ReloadProcessOnErrorAPI());
            }
        }
    }

    public function getMonitorGroups($url, $zaaid, $refresh_token, $monitor_group)
    {
        try {
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
        } catch (\Throwable | \GuzzleHttp\Exception\GuzzleException $th) {
            // dd('2');
            if (str_contains($th->getMessage(), '"message":"Invalid data provided."')) {
                return [
                    'data' => [],
                    'chart_data' => [],
                ];
            } else {
                // die();
                event(new ReloadProcessOnErrorAPI());
            }
        }
    }

    public function getMonitor($url, $zaaid, $refresh_token, $monitor_id)
    {
        try {
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
        } catch (\Throwable | \GuzzleHttp\Exception\GuzzleException $th) {
            // dd('3');
            if (str_contains($th->getMessage(), '"message":"Invalid data provided."')) {
                return [
                    'data' => [],
                    'chart_data' => [],
                ];
            } else {
                // die();
                event(new ReloadProcessOnErrorAPI());
            }
        }
    }

    public function getCurrentStatus($url, $zaaid, $refresh_token, $monitor_id)
    {
        try {
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
        } catch (\Throwable | \GuzzleHttp\Exception\GuzzleException $th) {
            // dd('4');
            if (str_contains($th->getMessage(), '"message":"Invalid data provided."')) {
                return [
                    'data' => [],
                    'chart_data' => [],
                ];
            } else {
                // die();
                event(new ReloadProcessOnErrorAPI());
            }
        }
    }

    public function getAvailabilityReport($url, $monitor_id, $zaaid, $refresh_token, $atributos)
    {
        try {
            $authorization = "Zoho-oauthtoken {$refresh_token}";
            $cookie = "zaaid={$zaaid}";
            $response = Http::connectTimeout(10)->timeout(5000)->withHeaders([
                'Accept' => 'application/json; version=2.0',
                'Authorization' => $authorization,
                'Cookie' => $cookie
            ])->retry(3, 10000)->get("{$url}/reports/availability_summary/${monitor_id}${atributos}");
            $response = $response->json();
            return $response;
        } catch (\Throwable | \GuzzleHttp\Exception\GuzzleException $th) {
            // dd('5');
            if (str_contains($th->getMessage(), '"message":"Invalid data provided."')) {
                return [
                    'data' => [],
                    'chart_data' => [],
                ];
            } else {
                // die();
                event(new ReloadProcessOnErrorAPI());
            }
        }
    }

    public function getPerformance($url, $monitor_id, $zaaid, $refresh_token, $attributos)
    {
        try {
            $authorization = "Zoho-oauthtoken {$refresh_token}";
            $cookie = "zaaid={$zaaid}";
            $response = Http::connectTimeout(10)->timeout(5000)->withHeaders([
                'Accept' => 'application/json; version=2.0',
                'Authorization' => $authorization,
                'Cookie' => $cookie
            ])->retry(3, 10000)->get("{$url}/reports/performance/{$monitor_id}{$attributos}");
            $response = $response->json();
            return $response;
        } catch (\Throwable | \GuzzleHttp\Exception\GuzzleException $th) {
            if (str_contains($th->getMessage(), '"message":"Invalid data provided."')) {
                return [
                    'data' => [],
                    'chart_data' => [],
                ];
            } else {
                // die();
                event(new ReloadProcessOnErrorAPI());
            }
        }
    }

    public function getPerformanceCharts($url, $monitor_id, $zaaid, $refresh_token, $attributos)
    {
        try {
            $authorization = "Zoho-oauthtoken {$refresh_token}";
            $cookie = "zaaid={$zaaid}";
            $response = Http::connectTimeout(10)->timeout(5000)->withHeaders([
                'Accept' => 'application/json; version=2.0',
                'Authorization' => $authorization,
                'Cookie' => $cookie
            ])->retry(3, 10000)->get("{$url}/charts/performance/{$monitor_id}{$attributos}");
            $response = $response->json();
            return $response;
        } catch (\Throwable | \GuzzleHttp\Exception\GuzzleException $th) {
            // dd('7');
            if (str_contains($th->getMessage(), '"message":"Invalid data provided."')) {
                return [
                    'data' => [],
                    'chart_data' => [],
                ];
            } else {
                // die();
                event(new ReloadProcessOnErrorAPI());
            }
        }
    }

    public function getPerformanceDiskWidget($url, $monitor_id, $zaaid, $refresh_token, $attributos)
    {
        try {
            $authorization = "Zoho-oauthtoken {$refresh_token}";
            $cookie = "zaaid={$zaaid}";
            $response = Http::connectTimeout(10)->timeout(5000)->withHeaders([
                'Accept' => 'application/json; version=2.0',
                'Authorization' => $authorization,
                'Cookie' => $cookie
            ])->retry(3, 10000)->get("{$url}/monitors/widget_details/{$monitor_id}{$attributos}");
            $response = $response->json();
            return $response;
        } catch (\Throwable | \GuzzleHttp\Exception\GuzzleException $th) {
            // dd('8');
            if (str_contains($th->getMessage(), '"message":"Invalid data provided."')) {
                return [
                    'data' => [],
                    'chart_data' => [],
                ];
            } else {
                // die();
                event(new ReloadProcessOnErrorAPI());
            }
        }
    }
}
