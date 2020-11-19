<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Calculator;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class PbCalculatorController extends Controller
{
    //
    public function add($a, $b){
        $result = $a + $b;
        return view('builder::modules.calculator.add', compact('result'));
    }

    public function substract($a, $b){
        $result = $a - $b;
        return view('builder::modules.calculator.substract', compact('result'));
    }
}