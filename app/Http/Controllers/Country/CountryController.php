<?php

namespace App\Http\Controllers\Country;

use App\Http\Controllers\Controller;
use App\Http\Resources\Country\CountryResource;
use App\Models\Country\Country;
use Illuminate\Http\Response;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::all();
        return CountryResource::collection($countries)->response()->setStatusCode(Response::HTTP_OK);
    }
}