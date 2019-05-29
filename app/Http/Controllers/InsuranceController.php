<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Excel;
use App\Services\InsuranceServices;

class InsuranceController extends Controller
{
    public function setCollection()
    {
        $insuranceService = new InsuranceServices();
        $data = $insuranceService->setCollectionData();
        return $data;
    }

    public function create()
    {
        set_time_limit(0);
        $path = public_path('insurance.xlsx');
        $data = Excel::load($path, function ($reader) {
        })->get();

        $dataHeading = $data->getHeading();
        $data = $data->toArray();
        $time = 'insurance'.date('d_M_Y');

        return Excel::create($time, function($excel) use ($data) {
            $excel->sheet('mySheet', function($sheet) use ($data)
            {
                $exportData = array();

                for($i=0; $i<100; $i++)
                {
                    $exportData[] = [
                        'Id' => (int)$data[$i]['id'],
                        'Make' => $data[$i]['make'],
                        'Model' => $data[$i]['model'],
                        'Year' => (int)$data[$i]['year'],
                        'Description' => $data[$i]['description'],
                        'Old Trim' => $data[$i]['old_trim'],
                        'Old Description' => $data[$i]['old_description'],
                    ];
                }
                $sheet->fromArray($exportData);
            });
        })->download('csv');
    }

    public function getData()
    {
        $insuranceService = new InsuranceServices();
        $data = $insuranceService->getAllData();
        return $data;
    }
}