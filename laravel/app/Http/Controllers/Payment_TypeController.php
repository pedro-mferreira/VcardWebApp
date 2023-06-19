<?php

namespace App\Http\Controllers;

use App\Http\Resources\Payment_TypeResource;
use Illuminate\Http\Request;
use App\Models\Payment_type;

class Payment_TypeController extends Controller
{
    public function getAll() {
        return Payment_TypeResource::collection(Payment_type::all());
    }
}
