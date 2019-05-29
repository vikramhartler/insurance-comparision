<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Excel;
use Illuminate\Support\Facades\Redis;
use App\User;
use App\Vehicle;

class InsuranceServices
{
    private $redis;

    public function __construct()
    {
        $this->redis = Redis::connection();
    }

    public function setCollectionData()
    {
        set_time_limit(0);
        $file = public_path('insurance.csv');
        $insurances = Excel::load($file, function ($reader) {

        })->get();

        $insurances = $insurances->toArray();
        $this->redis->set('collection', json_encode($insurances), 'EX', 40 * 60);
    }

    public function getAllData()
    {
        if($this->redis->exists('collection')) {
           $collection = $this->redis->get('collection');
           return json_decode($collection);
        }else {
            $this->setCollectionData();
            $collection = $this->redis->get('collection');
            return json_decode($collection);
        }
    }

    public function getYearsData()
    {
        if(!$this->redis->exists('collection')) {
            $this->setCollectionData();
        }
        $data = $this->redis->get('collection');
        $collection = collect(json_decode($data));
        $years = $collection->where('year', '>=', 1997)
            ->unique('year')
            ->sortByDesc('year');
        return $years->pluck('year')->toArray();
    }

    public function getMakeData()
    {
        if(!$this->redis->exists('collection')) {
            $this->setCollectionData();
        }
        $data = $this->redis->get('collection');
        $collection = collect(json_decode($data));
        $makes = $collection->unique('make')
            ->pluck('make')
            ->toArray();
        return $makes;
    }

    public function getModelData($post)
    {
        if(!$this->redis->exists('collection')) {
            $this->setCollectionData();
        }
        $data = $this->redis->get('collection');
        $collection = collect(json_decode($data));
        $models = $collection->where('make', '=', ucfirst(trim($post['make'])))
            ->where('year', $post['year'])
            ->unique('model')
            ->pluck('model')
            ->toArray();
        return $models;
    }

    public function getAutoInsurance()
    {
        $data = array('21st Century Insurance', 'AAA (Auto Club)', 'Allied Group', 'Allstate',
            'American Family Insurance', 'American National Property and Casualty', 'Auto Owners', 'Citizens',
            'Erie Insurance Group', 'Esurance', 'Farm Bureau', 'Farmers Insurance Group', 'Geico',
            'Liberty Mutual', 'Mercury Insurance Group', 'Nationwide Mutual Insurance Company',
            'Progressive Casualty', 'Safeco', 'State Farm Insurance', 'The Hartford', 'Travelers',
            'USAA', 'Other', 'Not Currently Insured');

        return $data;
    }

    public function saveData($data)
    {
        $status = false;
        $user = new User;
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->gender = $data['gender'];
        $user->email = $data['email'];
        $user->phone = $data['phone'];
        $user->birth_date = $data['birth_date'];
        $user->address = $data['address'];
        $user->married = $data['married'];
        $user->home_owner = $data['home_owner'];
        $user->zip_code = $data['zip_code'];
        $user->insurance = $data['insurance'];

        if($user->save()) {
            $status = $this->saveVehicleData($user->id, $data);
            $this->writeCSV($user->id);
        }
        return $status;
    }

    public function saveVehicleData($id, $data)
    {
        $status = false;
        foreach ($data['vehicle'] as $vehicle) {
            $vehicleObj = new Vehicle();
            $vehicleObj->user_id = $id;
            $vehicleObj->year = $vehicle['year'];
            $vehicleObj->company = $vehicle['company'];
            $vehicleObj->model = $vehicle['model'];
            $status = $vehicleObj->save();
        }

        return $status;
    }

    public function writeCSV($id)
    {
        $user = User::find($id);
        $vehicles = $user->vehicle()->get();
        $path = public_path('search.csv');

        if (!file_exists($path)) {
            $header = ['First Name', 'Last Name', 'Email', 'Gender', 'Birth Date', 'Phone', 'Address', 'Zip', 'Married', 'House Owner', 'Company', 'Model', 'Year'];
            $file = fopen($path,'a');
            fputcsv($file, $header);
            fclose($file);
        }

        $file = fopen($path,'a');
        foreach ($vehicles as $vehicle)
        {
            $home = $user->home_owner == true ? 'Own' : 'Rent';
            $married = $user->married == true ? 'Yes' : 'No';
            $data = [$user->first_name, $user->last_name, $user->email, $user->gender, $user->birth_date,
                $user->phone, $user->address, $user->zip, $married, $home, $vehicle->company,
                $vehicle->model, $vehicle->year ];
            fputcsv($file, $data);
        }
        fclose($file);
    }
}