<?php

namespace Anibalealvarezs\Projectbuilder\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class PbController extends Controller
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