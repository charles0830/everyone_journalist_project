<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    use ApiResponse;


    /**
     * ApiController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }
}
