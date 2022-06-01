<?php
//make a trait for api site 24x7
namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use PHPJasper\PHPJasper;

trait GenerarReportesSite24x7
{
    use ReestructurarDatosAPISite24x7;

    public function getJasperReport($data, $folder_name, $file_name = "Resumen", $path_reports, $monitor_id, $type = "SERVER")
    {
        //$public = public_path("jasperreports/{$type}/");
        $xmlTmpfilePath = 'C:\Users\David Orozco\JaspersoftWorkspace\Informes KIO - Final'.DIRECTORY_SEPARATOR .$type.DIRECTORY_SEPARATOR .'Resumen.jrxml';
        // $textoRemplazar = "C:\\\Users\\\uriel.santiago\\\JaspersoftWorkspace\\\KIO-Jasper\\\\";
        // $contenido = file_get_contents("C:\\laragon\\www\\reportes-jasper-laravel\\public\\jasperreports\\Resumen.jrxml");
        // $templateRuta = str_replace($textoRemplazar, str_replace('\\', '\\\\', (string)$public), (string)$contenido);
        // file_put_contents($xmlTmpfilePath, $templateRuta);       

        $output_folder = storage_path('app/public') .
            '/InformesKIO' . DIRECTORY_SEPARATOR . $path_reports . DIRECTORY_SEPARATOR . $file_name;

        Storage::makeDirectory("public/jasper/{$folder_name}");
        $file_api_name = "{$folder_name}/{$monitor_id}.json";
        $jsonTmpfilePath = storage_path('app/public/jasper/') . $file_api_name;
        $jsonTmpfile = fopen($jsonTmpfilePath, 'w');
        fwrite($jsonTmpfile, json_encode($data));
        fclose($jsonTmpfile);
        $options = [
            'format' => ['pdf'],
            'params' => [],
            'db_connection' => [
                'driver' => 'json',
                'data_file' => $jsonTmpfilePath
            ]
        ];

        $jasper = new PHPJasper;

        $output = $jasper->process(
            $xmlTmpfilePath,
            $output_folder,
            $options
        )->output();
        //Compilando el reporte graficas.jrxml
        // shell_exec('jasperstarter compile "C:/Users/uriel.santiago/JaspersoftWorkspace/KIO-Jasper/graficas.jrxml"');
        //dd($output);
        shell_exec($output);
        //dd($output);
        // $pathToFile = base_path() .
        //     '/resources/reports/pdf/Resumen.pdf';
        // return response()->file($pathToFile);
    }

    public function createFolderToCustomer($last_month, $customer_name, $monitor = null)
    {
        $path_reports = Carbon::now()->format('Y') . DIRECTORY_SEPARATOR . str_replace(" ","_",$customer_name) . DIRECTORY_SEPARATOR . $last_month;
        if ($monitor) {
            $path_reports = Carbon::now()->format('Y') . DIRECTORY_SEPARATOR . str_replace(" ","_",$customer_name) . DIRECTORY_SEPARATOR . $last_month . DIRECTORY_SEPARATOR . $monitor['type'];
        }
        Storage::makeDirectory('public/InformesKIO' . DIRECTORY_SEPARATOR . $path_reports);
        return $path_reports;
    }

    public function processSite24x7Monitors($monitor, $site24x7Url, $refresh_token, $zaaid, $customer_name, $last_month)
    {
        //and $monitor['monitor_id'] == "417536000001287157"
        $monitor_id = $monitor['monitor_id'];
        $current_status = $this->getCurrentStatus($site24x7Url,$zaaid,$refresh_token,$monitor_id);
        $current_status = array_key_exists('status',$current_status) ? $current_status['status'] : 10;
        if($this->isPermitedEstatus($current_status))
        {
        if ($monitor['type'] == 'SERVER' and $monitor['state'] == 0) {
            $availability = $this->getAvailabilityReport($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?period={$this->period_report}");
            $performance = $this->getPerformance($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?unit_of_time=3&period={$this->period_report}");
            $performance_disk = $this->getPerformance($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?unit_of_time=3&period={$this->period_report}&report_attribute=DISK");
            $ServerUptime = $this->getPerformanceCharts($site24x7Url, $monitor_id, $zaaid, $refresh_token, "/ServerUptimeChart?granularity=3&period={$this->period_report}");
            $performance_diskdetail = $this->getPerformanceDiskWidget($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?period={$this->period_report}&widget_name=DiskDetails");
            $customers = [
                'customer' => [
                    'name' => $customer_name,
                    'zaaid' => $zaaid,
                    'monitor' => $monitor,
                    'availability' => $this->getUptimeDownTimeAndMaintenance($availability),
                    'ServerUptime' =>  $this->applyFormatToPerformanceServerUpTime($ServerUptime),
                    'performance' =>  $this->applyFormatToPerformance($performance),
                    'performance_disk' => $this->applyFormatToPerformanceDisk($performance_disk),
                    'performance_diskdetail' => $performance_diskdetail,
                ]
            ];
            //dd($customers);
            $path_reports = $this->createFolderToCustomer($last_month, $customer_name, $monitor);
            $this->getJasperReport($customers,  str_replace(" ","_",$customer_name), $monitor['display_name'], $path_reports, $monitor_id, $monitor['type']);
        } 
        else if ($monitor['type'] == 'AGENTLESSSERVER' and $monitor['state'] == 0) {
            $availability = $this->getAvailabilityReport($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?period={$this->period_report}");
            $performance = $this->getPerformance($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?unit_of_time=3&period={$this->period_report}");
            $performance_disk = $this->getPerformanceCharts($site24x7Url, $monitor_id, $zaaid, $refresh_token, "/AllDiskUsedChart?granularity=3&period={$this->period_report}");
            $performance_diskdetail = $this->getPerformanceDiskWidget($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?period={$this->period_report}&widget_name=DiskDetails");
            $customers = [
                'customer' => [
                    'name' => $customer_name,
                    'zaaid' => $zaaid,
                    'monitor' => $monitor,
                    'availability' => $this->getUptimeDownTimeAndMaintenance($availability),
                    'performance' =>  $this->applyFormatToPerformance($performance),
                    'performance_disk' => $this->applyFormatToPerformanceDiskCharts($performance_disk),
                    'performance_diskdetail' => $performance_diskdetail,
                ]
            ];
            //dd($customers);
            $path_reports = $this->createFolderToCustomer($last_month, $customer_name, $monitor);
            $this->getJasperReport($customers,  str_replace(" ","_",$customer_name), $monitor['display_name'], $path_reports, $monitor_id, $monitor['type']);
        }
        else if ($monitor['type'] == 'VMWAREVM' and $monitor['state'] == 0) {
            $availability = $this->getAvailabilityReport($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?period={$this->period_report}");
            $performance = $this->getPerformance($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?unit_of_time=3&period={$this->period_report}");
            $performance_disk = $this->getPerformanceDiskWidget($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?period={$this->period_report}&widget_name=GUESTDISKIO");
            $customers = [
                'customer' => [
                    'name' => $customer_name,
                    'zaaid' => $zaaid,
                    'monitor' => $monitor,
                    'availability' => $this->getUptimeDownTimeAndMaintenance($availability),
                    'performance' =>  $this->applyFormatToPerformance($performance),
                    'performance_disk' => $performance_disk,
                ]
            ];
            $path_reports = $this->createFolderToCustomer($last_month, $customer_name, $monitor);
            $this->getJasperReport($customers,  str_replace(" ","_",$customer_name), $monitor['display_name'], $path_reports, $monitor_id, $monitor['type']);
        } 
        else if ($monitor['type'] == 'VMWAREESX' and $monitor['state'] == 0) {
            $availability = $this->getAvailabilityReport($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?period={$this->period_report}");
            $performance = $this->getPerformance($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?unit_of_time=3&period={$this->period_report}");
            //dd($performance);
            $customers = [
                'customer' => [
                    'name' => $customer_name,
                    'zaaid' => $zaaid,
                    'monitor' => $monitor,
                    'availability' => $this->getUptimeDownTimeAndMaintenance($availability),
                    'performance' =>  $this->applyFormatToPerformance($performance),
                ]
            ];
            $path_reports = $this->createFolderToCustomer($last_month, $customer_name, $monitor);
            $this->getJasperReport($customers,  str_replace(" ","_",$customer_name), $monitor['display_name'], $path_reports, $monitor_id, $monitor['type']);
        } 
        else if ($monitor['type'] == 'PLUGIN' and $monitor['state'] == 0) {
            $availability = $this->getAvailabilityReport($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?period={$this->period_report}");
            $performance = $this->getPerformance($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?unit_of_time=3&period={$this->period_report}");
            $customers = [
                'customer' => [
                    'name' => $customer_name,
                    'zaaid' => $zaaid,
                    'monitor' => $monitor,
                    'availability' => $this->getUptimeDownTimeAndMaintenance($availability),
                    'performance' =>  $this->applyFormatToPerformancePlugin($performance),
                ]
            ];
            //dd($customers);
            $path_reports = $this->createFolderToCustomer($last_month, $customer_name, $monitor);
            $this->getJasperReport($customers,  str_replace(" ","_",$customer_name), $monitor['display_name'], $path_reports, $monitor_id, $monitor['type']);
        } 
        else if ($monitor['type'] == 'SQLSERVER' and $monitor['state'] == 0) {
            $availability = $this->getAvailabilityReport($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?period={$this->period_report}");
            $performance = $this->getPerformance($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?unit_of_time=3&period={$this->period_report}");
            $performance_page = $this->getPerformance($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?period={$this->period_report}&report_attribute=PAGEREADS");
            $performance_db = $this->getPerformanceDiskWidget($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?period={$this->period_report}&widget_name=DBDETAILS");
            $customers = [
                'customer' => [
                    'name' => $customer_name,
                    'zaaid' => $zaaid,
                    'monitor' => $monitor,
                    'availability' => $this->getUptimeDownTimeAndMaintenance($availability),
                    'performance' =>  $this->applyFormatToPerformance($performance),
                    'performance_page' => $this->applyFormatToPerformance($performance_page),
                    'performance_db' => $performance_db,
                ]
            ];
            //dd($customers);
            $path_reports = $this->createFolderToCustomer($last_month, $customer_name, $monitor);
            $this->getJasperReport($customers,  str_replace(" ","_",$customer_name), $monitor['display_name'], $path_reports, $monitor_id, $monitor['type']);
        } 
        else if ($monitor['type'] == 'NETWORKDEVICE' and $monitor['state'] == 0) {
            $availability = $this->getAvailabilityReport($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?period={$this->period_report}");
            $performance = $this->getPerformance($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?unit_of_time=3&period={$this->period_report}");
            $performance_intraffic = $this->getPerformance($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?period={$this->period_report}&report_attribute=INTRAFFIC");
            $performance_outtraffic = $this->getPerformance($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?period={$this->period_report}&report_attribute=OUTTRAFFIC");
            $formated_performance_intraffic=$this->applyFormatToPerformanceInterface($performance_intraffic);
            $formated_performance_outtraffic=$this->applyFormatToPerformanceInterface($performance_outtraffic);
            $performance_traffic = $this->getPerformanceTraffic($formated_performance_intraffic,$formated_performance_outtraffic);
            $customers = [
                'customer' => [
                    'name' => $customer_name,
                    'zaaid' => $zaaid,
                    'monitor' => $monitor,
                    'availability' => $this->getUptimeDownTimeAndMaintenance($availability),
                    'performance' =>  $this->applyFormatToPerformance($performance),
                    'performance_traffic' => $performance_traffic,
                ]
            ];
            $path_reports = $this->createFolderToCustomer($last_month, $customer_name, $monitor);
            $this->getJasperReport($customers,  str_replace(" ","_",$customer_name), $monitor['display_name'], $path_reports, $monitor_id, $monitor['type']);
        }
        else if ($monitor['type'] == 'DATASTORE' and $monitor['state'] == 0) {
            $availability = $this->getAvailabilityReport($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?period={$this->period_report}");
            $performance = $this->getPerformance($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?unit_of_time=3&period={$this->period_report}");
            $performance_store = $this->getPerformanceDiskWidget($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?period={$this->period_report}&widget_name=VMSTORAGE");
            $customers = [
                'customer' => [
                    'name' => $customer_name,
                    'zaaid' => $zaaid,
                    'monitor' => $monitor,
                    'availability' => $this->getUptimeDownTimeAndMaintenance($availability),
                    'performance' =>  $this->applyFormatToPerformance($performance),
                    'performance_store' => $performance_store,
                ]
            ];
            $path_reports = $this->createFolderToCustomer($last_month, $customer_name, $monitor);
            $this->getJasperReport($customers,  str_replace(" ","_",$customer_name), $monitor['display_name'], $path_reports, $monitor_id, $monitor['type']);
        }  
        else if ($monitor['type'] == 'MSEXCHANGE' and $monitor['state'] == 0 and $monitor['monitor_id'] == "425201000000406079") {
            $availability = $this->getAvailabilityReport($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?period={$this->period_report}");
            $performance = $this->getPerformance($site24x7Url, $monitor_id, $zaaid, $refresh_token, "?unit_of_time=3&period={$this->period_report}");
            //dd($performance);
            $customers = [
                'customer' => [
                    'name' => $customer_name,
                    'zaaid' => $zaaid,
                    'monitor' => $monitor,
                    'availability' => $this->getUptimeDownTimeAndMaintenance($availability),
                    'performance' =>  $this->applyFormatToPerformance($performance),
                ]
            ];
            $path_reports = $this->createFolderToCustomer($last_month, $customer_name, $monitor);
            $this->getJasperReport($customers,  str_replace(" ","_",$customer_name), $monitor['display_name'], $path_reports, $monitor_id, $monitor['type']);
        } 
        }
    }

    public function applyFormatToPerformance($performance)
    {
        if (array_key_exists('data', $performance)) {
            $newPerformances = [];
            $newPerformancesTable = [];
            foreach ($performance['data']['chart_data'] as $pfms) {
                foreach ($pfms as $pfm) {
                    if (array_key_exists('OverallCPUChart', $pfm)) {
                        $newPerformances['OverallCPUChart'] = $this->getOverallCPUChart($pfm['OverallCPUChart']);
                    }
                    if (array_key_exists('OverallMemoryChart', $pfm)) {
                        $newPerformances['OverallMemoryChart'] = $this->getOverallMemoryChart($pfm['OverallMemoryChart']);
                    }
                    if (array_key_exists('OverallDiskUtilization', $pfm)) {
                        $newPerformances['OverallDiskUtilization'] = $this->getOverallDiskUtilization($pfm['OverallDiskUtilization']);
                    }
                    if (array_key_exists('ResponseTimeChart', $pfm)) {
                        $newPerformances['ResponseTimeChart'] = $this->getFormattedResponse($pfm['ResponseTimeChart']);
                    }
                    if (array_key_exists('CpuUtilChart', $pfm)) {
                        $newPerformances['CpuUtilChart'] = $this->getFormattedResponse($pfm['CpuUtilChart']);
                    }
                    if (array_key_exists('CpuUtilChart', $pfm)) {
                        $newPerformances['CpuUtilChart'] = $this->getFormattedResponse($pfm['CpuUtilChart']);
                    }
                    if (array_key_exists('MemoryUtilChart', $pfm)) {
                        $newPerformances['MemoryUtilChart'] = $this->getFormattedResponse($pfm['MemoryUtilChart']);
                    }
                    if (array_key_exists('DiskUtilChart', $pfm)) {
                        $newPerformances['DiskUtilChart'] = $this->getFormattedResponse($pfm['DiskUtilChart']);
                    }
                    if (array_key_exists('CPUUtilizationChart', $pfm)) {
                        $newPerformances['CPUUtilizationChart'] = $this->getFormattedResponse($pfm['CPUUtilizationChart']);
                    }
                    if (array_key_exists('MEMORYUtilizationChart', $pfm)) {
                        $newPerformances['MEMORYUtilizationChart'] = $this->getFormattedResponse($pfm['MEMORYUtilizationChart']);
                    }
                    if (array_key_exists('DISKSpacePercentageChart', $pfm)) {
                        $newPerformances['DISKSpacePercentageChart'] = $this->getFormattedResponse($pfm['DISKSpacePercentageChart']);
                    }
                    if (array_key_exists('NETUtilizationChart', $pfm)) {
                        $newPerformances['NETUtilizationChart'] = $this->getFormattedResponse($pfm['NETUtilizationChart']);
                    }
                    if (array_key_exists('NETWORKUsageChart', $pfm)) {
                        $newPerformances['NETWORKUsageChart'] = $this->getFormattedResponse($pfm['NETWORKUsageChart']);
                    }
                    if (array_key_exists('ConnectionChart', $pfm)) {
                        $newPerformances['ConnectionChart'] = $this->getFormattedResponse($pfm['ConnectionChart']);
                    }
                    if (array_key_exists('RequestChart', $pfm)) {
                        $newPerformances['RequestChart'] = $this->getFormattedResponse($pfm['RequestChart']);
                    }
                    if (array_key_exists('BufferCacheHitChart', $pfm)) {
                        $newPerformances['BufferCacheHitChart'] = $this->getFormattedResponse($pfm['BufferCacheHitChart']);
                    }
                    if (array_key_exists('PlanCacheHitChart', $pfm)) {
                        $newPerformances['PlanCacheHitChart'] = $this->getFormattedResponse($pfm['PlanCacheHitChart']);
                    }
                    if (array_key_exists('PageOperationChart', $pfm)) {
                        $newPerformances['PageOperationChart'] = $this->getFormattedResponseWithTwoValues($pfm['PageOperationChart']);
                    }
                    if (array_key_exists('MemoryChart', $pfm)) {
                        $newPerformances['MemoryChart'] = $this->getFormattedResponseMemorySQL($pfm['MemoryChart']);
                    }
                    if (array_key_exists('CpuUsageChart', $pfm)) {
                        $newPerformances['CpuUsageChart'] = $this->getFormattedResponse($pfm['CpuUsageChart']);
                    }
                    if (array_key_exists('MemoryUsageChart', $pfm)) {
                        $newPerformances['MemoryUsageChart'] = $this->getFormattedResponse($pfm['MemoryUsageChart']);
                    }
                    if (array_key_exists('ResponseTimeChart', $pfm)) {
                        $newPerformances['ResponseTimeChart'] = $this->getFormattedResponse($pfm['ResponseTimeChart']);
                    }
                    if (array_key_exists('PacketLossChart', $pfm)) {
                        $newPerformances['PacketLossChart'] = $this->getFormattedResponse($pfm['PacketLossChart']);
                    }
                    if (array_key_exists('DS-STORAGE', $pfm)) {
                        $newPerformances['DS-STORAGE'] = $this->getFormattedResponseDatastore($pfm['DS-STORAGE']);
                    }
                    if (array_key_exists('EdgeSMTPChart', $pfm)) {
                        $newPerformances['EdgeSMTPChart'] = $this->getFormattedResponseWithTwoValues($pfm['EdgeSMTPChart']);
                    }
                    if (array_key_exists('ISCASRPCResponseChart', $pfm)) {
                        $newPerformances['ISCASRPCResponseChart'] = $this->getFormattedResponseWithFourValues($pfm['ISCASRPCResponseChart']);
                    }
                    if (array_key_exists('HubSMTPChart', $pfm)) {
                        $newPerformances['HubSMTPChart'] = $this->getFormattedResponseWithTwoValues($pfm['HubSMTPChart']);
                    }
                    if (array_key_exists('CASRespChart', $pfm)) {
                        $newPerformances['CASRespChart'] = $this->getFormattedResponseWithTwoValues($pfm['CASRespChart']);
                    }
                    if (array_key_exists('UMAvailChart', $pfm)) {
                        $newPerformances['UMAvailChart'] = $this->getFormattedResponseWithThreeValues($pfm['UMAvailChart']);
                    }
                }
            }
            $performance['data']['chart_data'] = $newPerformances;
            foreach ($performance['data']['table_data'] as $pfm) {
                if (array_key_exists('CAPACITY', $pfm)) {
                    $newPerformancesTable['CAPACITY'] = $this->getFormattedResponse($pfm['CAPACITY']);
                }
            }
            $performance['data']['table_data'] = $newPerformancesTable;
            return $performance;
        }
    }

    public function applyFormatToPerformanceDisk($performance)
    {
        if (array_key_exists('data', $performance)) {
            $newPerformances = [];
            foreach ($performance['data']['chart_data'] as $pfms) {
                foreach ($pfms as $pfm) {
                    if (array_key_exists('OverallDiskUtilization', $pfm)) {
                        $newPerformances['OverallDiskUtilization'] = $this->getOverallDiskUtilization($pfm['OverallDiskUtilization']);
                    }
                    if (array_key_exists('OverallDiskUsedChart', $pfm)) {
                        $newPerformances['OverallDiskUsedChart'] = $this->getOverallDiskUsedChart($pfm['OverallDiskUsedChart']);
                    }
                    if (array_key_exists('IndividualDiskUtilization', $pfm)) {
                        $newPerformances['IndividualDiskUtilization'] = $this->getIndividualDiskUtilization($pfm['IndividualDiskUtilization']);
                    }
                    if (array_key_exists('DiskIO', $pfm)) {
                        $newPerformances['DiskIO'] = $this->getDiskIO($pfm['DiskIO']);
                    }
                }
                if (array_key_exists('IndividualDiskUtilizationTimeChart', $pfms)) {
                    $newPerformances['IndividualDiskUtilizationTimeChart'] = $this->getIndividualDiskUtilizationTimeChart($pfms['IndividualDiskUtilizationTimeChart']);
                }
            }
            $performance['data']['chart_data'] = $newPerformances;
            return $performance;
        }
    }

    public function applyFormatToPerformanceDiskCharts($performance)
    {
        if (array_key_exists('data', $performance)) {
            if (array_key_exists('AllDiskUsedChart', $performance['data'])) {
                $newPerformances = [];
                foreach ($performance['data']['AllDiskUsedChart'] as $disks) {
                    foreach ($disks as $disk) {
                        array_push($newPerformances, $this->getFormattedResponse($disk));
                    }
                }
                $performance['data']['AllDiskUsedChart'] = $newPerformances;
                return $performance;
            }
        }
        return [];
    }

    public function applyFormatToPerformanceServerUpTime($performance)
    {
        if (array_key_exists('data', $performance)) {
            $newPerformances = [];
            foreach ($performance['data'] as $pfms) {
                foreach ($pfms as $pfm) {
                    if (array_key_exists('chart_data', $pfm)) {
                        array_push($newPerformances, $this->getFormattedResponse($pfm));
                    }
                }
            }
            $performance['data']['chart_data'] = $newPerformances;
            return $performance;
        }
        return [];
    }

    public function applyFormatToPerformancePlugin($performance)
    {
        if (array_key_exists('data', $performance)) {
            if (array_key_exists('chart_data', $performance['data'])) {
                $newPerformances = [];
                foreach ($performance['data']['chart_data'] as $plugins) {
                    //dd($plugins);
                    foreach ($plugins as $plugin) {
                        foreach ($plugin as $key => $plugin_item) {
                            $newPerformances[] = $this->getFormattedResponsePlugin($plugin_item,$key);
                        }
                    }
                }
                $performance['data']['chart_data'] = $newPerformances;
                return $performance;
            }
        }
        return [];
    }

    public function applyFormatToPerformanceInterface($performance)
    {
        if (array_key_exists('data', $performance)) {
            if (array_key_exists('chart_data', $performance['data'])) {
            $newPerformances = [];
                foreach ($performance['data']['chart_data'] as $pfms) 
                {
                    if (array_key_exists('InterfaceTrafficInChart', $pfms)) {
                        $newPerformances['InterfaceTrafficInChart'] = $this->getFormattedResponseTraffic($pfms['InterfaceTrafficInChart']);
                    }
                    if (array_key_exists('InterfaceTrafficOutChart', $pfms)) {
                        $newPerformances['InterfaceTrafficOutChart'] = $this->getFormattedResponseTraffic($pfms['InterfaceTrafficOutChart']);
                    }
                }
            $performance['data']['chart_data'] = $newPerformances;
            return $performance;
            }
        }
    }

    function getPercentage($cantidad, $total)
    {
        $porcentaje = ($cantidad * 100) / $total; // Regla de tres
        // $porcentaje = round($porcentaje, 0);  // Quitar los decimalesreturn $porcentaje;
        return round($porcentaje);
    }

    function getPerformanceTraffic($intraffic,$outtraffic){
        $performance_traffic_array=[];
        foreach($intraffic['data']['chart_data']['InterfaceTrafficInChart'] as $intraffic_it){
            foreach($outtraffic['data']['chart_data']['InterfaceTrafficOutChart'] as $outtraffic_it){
                if($intraffic_it['label']==$outtraffic_it['label']){
                    array_push($performance_traffic_array,[
                    'label'=>$intraffic_it['label'],
                    'intraffic'=>$intraffic_it,
                    'outtraffic'=>$outtraffic_it,
                    ]);
                }
            }
        }
        sort($performance_traffic_array);
        return $performance_traffic_array;
    }

    function isPermitedEstatus($current_status){
        $permited_estatus=[0,1,2,3,7];
        if(in_array(intval($current_status),$permited_estatus))
        {
            return true;
        }
        return false;
    }
}
