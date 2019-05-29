<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Excel;
use App\Services\InsuranceServices;
use App\Http\Requests\InsuranceRequest;
use App\Http\Requests\InsuranceSearchRequest;

class InsuranceApiController extends Controller
{
    public function getYears()
    {
        $insuranceService = new InsuranceServices();
        $data = $insuranceService->getYearsData();
        $response = [
            'success' => true,
            'years' => $data
        ];
        return response()->json($response);
    }

    public function getMakes()
    {
        $insuranceService = new InsuranceServices();
        $data = $insuranceService->getMakeData();
        $response = [
            'success' => true,
            'makes' => $data
        ];
        return response()->json($response);
    }

    public function getModels(InsuranceRequest $request)
    {
        $data = $request->all();
        $insuranceService = new InsuranceServices();
        $data = $insuranceService->getModelData($data);
        $response = [
            'success' => true,
            'models' => $data
        ];
        return response()->json($response);
    }

    public function autoInsurance()
    {
        $insuranceService = new InsuranceServices();
        $data = $insuranceService->getAutoInsurance();
        $response = [
            'success' => true,
            'insurance' => $data
        ];
        return response()->json($response);
    }

    public function searchQuotes(InsuranceSearchRequest $request)
    {
        $post = $request->all();
        $insuranceService = new InsuranceServices();
        $status = $insuranceService->saveData($post);

        if($status) {
            $response = [
                'success' => true
            ];
            return response()->json($response);
        }

        $response = [
            'success' => false
        ];
        return response()->json($response);
    }
}