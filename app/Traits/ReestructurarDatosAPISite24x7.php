<?php
//make a trait for api site 24x7
namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

trait ReestructurarDatosAPISite24x7
{

    public function getUptimeDownTimeAndMaintenance($data)
    {
        if (array_key_exists('data', $data)) {
            $charts = array_map(function ($item) {
                if ($item['key'] == 'percentage_chart') {
                    $item['data']['uptimes'] = array_map(function ($item) {
                        return [
                            'date' => Carbon::parse($item[0])->format('Y-m-d H:i:s'),
                            'uptime' => $item[1],
                            'downtime' => $item[2],
                            'maintenance' => $item[3],
                        ];
                    }, $item['data']);
                }
                return $item;
            }, $data['data']['charts']);
            $data['data']['charts'] = $charts;

            return $data;
        }
    }

    public function getOverallCPUChart($OverallCPUChart)
    {
        if (array_key_exists('chart_data', $OverallCPUChart)) {

            $OverallCPUChart['chart_data'] = array_map(function ($item) {
                return [
                    'date' => array_key_exists(0, $item) ? Carbon::parse($item[0])->format('Y-m-d H:i:s') : null,
                    'value' => array_key_exists(1, $item) ? $item[1] : null,
                ];
            },  $OverallCPUChart['chart_data']);

            return $OverallCPUChart;
        }
    }

    public function getOverallMemoryChart($OverallMemoryChart)
    {
        if (array_key_exists('chart_data', $OverallMemoryChart)) {
            $OverallMemoryChart['chart_data'] = array_map(function ($item) {
                return [
                    'date' => array_key_exists(0, $item) ? Carbon::parse($item[0])->format('Y-m-d H:i:s') : null,
                    'value' => array_key_exists(1, $item) ? $item[1] : null,
                ];
            }, $OverallMemoryChart['chart_data']);
            return $OverallMemoryChart;
        }
    }

    public function getOverallDiskUtilization($OverallDiskUtilization)
    {
        if (array_key_exists('chart_data', $OverallDiskUtilization)) {
            $OverallDiskUtilization['chart_data'] = array_map(function ($item) {
                return [
                    'date' => array_key_exists(0, $item) ? Carbon::parse($item[0])->format('Y-m-d H:i:s') : null,
                    'performance1' => array_key_exists(1, $item) ? $item[1] : null,
                    'performance2' => array_key_exists(2, $item) ? $item[2] : null,
                ];
            }, $OverallDiskUtilization['chart_data']);
            return $OverallDiskUtilization;
        }
    }
    public function getFormattedResponse($item)
    {
        if (array_key_exists('chart_data', $item)) {
            $item['chart_data'] = array_map(function ($item) {
                return [
                    'date' => array_key_exists(0, $item) ? Carbon::parse($item[0])->format('Y-m-d H:i:s') : null,
                    'value1' => array_key_exists(1, $item) ? $item[1] : null,
                ];
            }, $item['chart_data']);
            return $item;
        }
    }
    public function getFormattedResponseWithTwoValues($item)
    {
        if (array_key_exists('chart_data', $item)) {
            $item['chart_data'] = array_map(function ($item) {
                return [
                    'date' => array_key_exists(0, $item) ? Carbon::parse($item[0])->format('Y-m-d H:i:s') : null,
                    'value1' => array_key_exists(1, $item) ? $item[1] : null,
                    'value2' => array_key_exists(2, $item) ? $item[2] : null,
                ];
            }, $item['chart_data']);
            return $item;
        }
    }

    public function getIndividualDiskUtilization($IndividualDiskUtilization)
    {
        if (array_key_exists('chart_data', $IndividualDiskUtilization)) {
            $IndividualDiskUtilization['chart_data'] = array_map(function ($item) {
                return [
                    'disk' => array_key_exists(0, $item) ? $item[0] : null,
                    'value1' => array_key_exists(1, $item) ? $item[1] : null,
                    'value2' => array_key_exists(2, $item) ? $item[2] : null,
                ];
            }, $IndividualDiskUtilization['chart_data']);
            return $IndividualDiskUtilization;
        }
    }

    public function getIndividualDiskUtilizationTimeChart($IndividualDiskUtilizationTimeChart)
    {
        // pass IndividualDiskUtilizationTimeChart to array_map

        $IndividualDiskUtilizationTimeChart = array_map(function ($items) {
            foreach ($items as $item) {
                if (array_key_exists('chart_data', $item)) {
                    $item['chart_data'] = array_map(function ($item) {
                        return [
                            'disk' => array_key_exists(0, $item) ? $item[0] : null,
                            'value1' => array_key_exists(1, $item) ? $item[1] : null,
                            'value2' => array_key_exists(2, $item) ? $item[2] : null,
                        ];
                    }, $item['chart_data']);
                    return $item;
                }
            }
        }, $IndividualDiskUtilizationTimeChart);

        return $IndividualDiskUtilizationTimeChart;
    }

    public function getOverallDiskUsedChart($OverallDiskUsedChart)
    {
        if (array_key_exists('chart_data', $OverallDiskUsedChart)) {
            $OverallDiskUsedChart['chart_data'] = array_map(function ($item) {
                return [
                    'date' => array_key_exists(0, $item) ? Carbon::parse($item[0])->format('Y-m-d H:i:s') : null,
                    'value1' => array_key_exists(1, $item) ? $item[1] : null,
                    'value2' => array_key_exists(2, $item) ? $item[2] : null,
                ];
            }, $OverallDiskUsedChart['chart_data']);
            return $OverallDiskUsedChart;
        }
    }

    public function getDiskIO($DiskIO)
    {
        if (array_key_exists('chart_data', $DiskIO)) {
            $DiskIO['chart_data'] = array_map(function ($item) {
                return [
                    'date' => array_key_exists(0, $item) ? Carbon::parse($item[0])->format('Y-m-d H:i:s') : null,
                    'value1' => array_key_exists(1, $item) ? $item[1] : null,
                    'value2' => array_key_exists(2, $item) ? $item[2] : null,
                ];
            }, $DiskIO['chart_data']);
            return $DiskIO;
        }
    }
}
