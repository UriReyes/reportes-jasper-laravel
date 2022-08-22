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
                            'date' => Carbon::parse($item[0])->format('d M Y'),
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
                    'date' => array_key_exists(0, $item) ? Carbon::parse($item[0])->format('d M Y') : null,
                    'value' => array_key_exists(1, $item) ? $item[1] : null,
                ];
            },  $OverallCPUChart['chart_data']);
            usort($OverallCPUChart['chart_data'], function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });
            return $OverallCPUChart;
        }
    }

    public function getOverallMemoryChart($OverallMemoryChart)
    {
        if (array_key_exists('chart_data', $OverallMemoryChart)) {
            $OverallMemoryChart['chart_data'] = array_map(function ($item) {
                return [
                    'date' => array_key_exists(0, $item) ? Carbon::parse($item[0])->format('d M Y') : null,
                    'value' => array_key_exists(1, $item) ? $item[1] : null,
                ];
            }, $OverallMemoryChart['chart_data']);
            usort($OverallMemoryChart['chart_data'], function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });
            return $OverallMemoryChart;
        }
    }

    public function getOverallDiskUtilization($OverallDiskUtilization)
    {
        if (array_key_exists('chart_data', $OverallDiskUtilization)) {
            $OverallDiskUtilization['chart_data'] = array_map(function ($item) {
                return [
                    'date' => array_key_exists(0, $item) ? Carbon::parse($item[0])->format('d M Y') : null,
                    'performance1' => array_key_exists(1, $item) ? $item[1] : null,
                    'performance2' => array_key_exists(2, $item) ? $item[2] : null,
                ];
            }, $OverallDiskUtilization['chart_data']);
            usort($OverallDiskUtilization['chart_data'], function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });
            return $OverallDiskUtilization;
        }
    }

    public function getFormattedResponse($item)
    {
        if (array_key_exists('chart_data', $item)) {
            $item['chart_data'] = array_map(function ($item) {
                return [
                    'date' => array_key_exists(0, $item) ? Carbon::parse($item[0])->format('d M Y') : null,
                    'value1' => array_key_exists(1, $item) ? $item[1] : null,
                ];
            }, $item['chart_data']);
            usort($item['chart_data'], function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });
            return $item;
        }
    }

    public function getFormattedResponseWithTwoValues($item)
    {
        if (array_key_exists('chart_data', $item)) {
            $item['chart_data'] = array_map(function ($item) {
                return [
                    'date' => array_key_exists(0, $item) ? Carbon::parse($item[0])->format('d M Y') : null,
                    'value1' => array_key_exists(1, $item) ? $item[1] : null,
                    'value2' => array_key_exists(2, $item) ? $item[2] : null,
                ];
            }, $item['chart_data']);
            usort($item['chart_data'], function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });
            return $item;
        }
    }

    public function getFormattedResponseWithThreeValues($item)
    {
        if (array_key_exists('chart_data', $item)) {
            $item['chart_data'] = array_map(function ($item) {
                return [
                    'date' => array_key_exists(0, $item) ? Carbon::parse($item[0])->format('d M Y') : null,
                    'value1' => array_key_exists(1, $item) ? $item[1] : null,
                    'value2' => array_key_exists(2, $item) ? $item[2] : null,
                    'value3' => array_key_exists(3, $item) ? $item[3] : null,
                ];
            }, $item['chart_data']);
            usort($item['chart_data'], function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });
            return $item;
        }
    }

    public function getFormattedResponseWithFourValues($item)
    {
        if (array_key_exists('chart_data', $item)) {
            $item['chart_data'] = array_map(function ($item) {
                return [
                    'date' => array_key_exists(0, $item) ? Carbon::parse($item[0])->format('d M Y') : null,
                    'value1' => array_key_exists(1, $item) ? $item[1] : null,
                    'value2' => array_key_exists(2, $item) ? $item[2] : null,
                    'value3' => array_key_exists(3, $item) ? $item[3] : null,
                    'value4' => array_key_exists(4, $item) ? $item[4] : null,
                ];
            }, $item['chart_data']);
            usort($item['chart_data'], function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });
            return $item;
        }
    }

    public function getFormattedResponseMemorySQL($item)
    {
        if (array_key_exists('chart_data', $item)) {
            $item['chart_data'] = array_map(function ($item) {
                return [
                    'date' => array_key_exists(0, $item) ? Carbon::parse($item[0])->format('d M Y') : null,
                    'TotalSM' => array_key_exists(1, $item) ? $item[1] : null,
                    'TargetSM' => array_key_exists(2, $item) ? $item[2] : null,
                    'SQLCache' => array_key_exists(3, $item) ? $item[3] : null,
                    'OptimizerM' => array_key_exists(4, $item) ? $item[4] : null,
                    'ConnectionM' => array_key_exists(5, $item) ? $item[5] : null,
                    'LockM' => array_key_exists(6, $item) ? $item[6] : null,
                    'GrantedWorkspaceM' => array_key_exists(7, $item) ? $item[7] : null,
                ];
            }, $item['chart_data']);
            usort($item['chart_data'], function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });
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
                            'date' => array_key_exists(0, $item) ? Carbon::parse($item[0])->format('d M Y') : null,
                            'value1' => array_key_exists(1, $item) ? $item[1] : null,
                            'value2' => array_key_exists(2, $item) ? $item[2] : null,
                        ];
                    }, $item['chart_data']);
                    usort($item['chart_data'], function ($a, $b) {
                        return strtotime($a['date']) - strtotime($b['date']);
                    });
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
                    'date' => array_key_exists(0, $item) ? Carbon::parse($item[0])->format('d M Y') : null,
                    'value1' => array_key_exists(1, $item) ? $item[1] : null,
                    'value2' => array_key_exists(2, $item) ? $item[2] : null,
                ];
            }, $OverallDiskUsedChart['chart_data']);
            usort($OverallDiskUsedChart['chart_data'], function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });
            return $OverallDiskUsedChart;
        }
    }

    public function getDiskIO($DiskIO)
    {
        if (array_key_exists('chart_data', $DiskIO)) {
            $DiskIO['chart_data'] = array_map(function ($item) {
                return [
                    'date' => array_key_exists(0, $item) ? Carbon::parse($item[0])->format('d M Y') : null,
                    'value1' => array_key_exists(1, $item) ? $item[1] : null,
                    'value2' => array_key_exists(2, $item) ? $item[2] : null,
                ];
            }, $DiskIO['chart_data']);
            usort($DiskIO['chart_data'], function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });
            return $DiskIO;
        }
    }

    public function getFormattedResponsePlugin($item, $key)
    {
        if (array_key_exists('chart_data', $item)) {
            $item['chart_data'] = array_map(function ($item) {
                return [
                    'date' => array_key_exists(0, $item) ? Carbon::parse($item[0])->format('d M Y') : null,
                    'value1' => array_key_exists(1, $item) ? $item[1] : null,
                ];
            }, $item['chart_data']);
            $item['metric'] = $key;
            usort($item['chart_data'], function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });
            return $item;
        }
    }

    public function getFormattedResponseTraffic($traffic)
    {
        $trafficArray = [];
        foreach ($traffic as $traffic_it) {
            foreach ($traffic_it as $item) {
                if (array_key_exists('chart_data', $item)) {
                    $item['chart_data'] = array_map(function ($item) {
                        return [
                            'date' => array_key_exists(0, $item) ? Carbon::parse($item[0])->format('d M Y') : null,
                            'value1' => array_key_exists(1, $item) ? $item[1] : null,
                        ];
                    }, $item['chart_data']);
                    usort($item['chart_data'], function ($a, $b) {
                        return strtotime($a['date']) - strtotime($b['date']);
                    });
                    array_push($trafficArray, $item);
                }
            }
        }
        return $trafficArray;
    }

    public function getFormattedResponseDatastore($item)
    {
        if (array_key_exists('chart_data', $item)) {
            $item['chart_data'] = array_map(function ($item) {
                return [
                    'date' => array_key_exists(0, $item) ? Carbon::parse($item[0])->format('d M Y') : null,
                    'Snapshots Size' => array_key_exists(1, $item) ? $item[1] : null,
                    'Swap File Size' => array_key_exists(2, $item) ? $item[2] : null,
                    'Disk Files Size' => array_key_exists(3, $item) ? $item[3] : null,
                    'Other VM Space' => array_key_exists(4, $item) ? $item[4] : null,
                    'Other Space' => array_key_exists(5, $item) ? $item[5] : null,
                    'Free Space' => array_key_exists(6, $item) ? $item[6] : null,
                ];
            }, $item['chart_data']);
            usort($item['chart_data'], function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });
            return $item;
        }
    }

    public function getFormattedResponseTimeReportURL($item)
    {
        if (array_key_exists('chart_data', $item)) {
            $item['chart_data'] = array_map(function ($item) {
                return [
                    'date' => array_key_exists(0, $item) ? Carbon::parse($item[0])->format('d M Y') : null,
                    'DNSTime' => array_key_exists(1, $item) ? $item[1] : null,
                    'ConnectionTime' => array_key_exists(2, $item) ? $item[2] : null,
                    'SSLHandshakeTime' => array_key_exists(3, $item) ? $item[3] : null,
                    'FirstByteTime' => array_key_exists(4, $item) ? $item[4] : null,
                    'DownloadTime' => array_key_exists(5, $item) ? $item[5] : null,
                ];
            }, $item['chart_data']);
            usort($item['chart_data'], function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });
            return $item;
        }
    }

    public function getFormattedLocationResponseTime($locationResponse)
    {
        $locationResponse = array_map(function ($items) {
            foreach ($items as $item) {
                if (array_key_exists('chart_data', $item)) {
                    $item['chart_data'] = array_map(function ($item) {
                        return [
                            'date' => array_key_exists(0, $item) ? Carbon::parse($item[0])->format('d M Y') : null,
                            'value1' => array_key_exists(1, $item) ? $item[1] : null,
                        ];
                    }, $item['chart_data']);
                    usort($item['chart_data'], function ($a, $b) {
                        return strtotime($a['date']) - strtotime($b['date']);
                    });
                    return $item;
                }
            }
        }, $locationResponse);

        return $locationResponse;
    }
}
