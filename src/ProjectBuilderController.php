<?php

namespace Anibalealvarezs\ProjectBuilder;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ProjectBuilderController extends Controller
{
    //
    public function add($a, $b){
        $result = $a + $b;
        return view('builder::add', compact('result'));
    }

    public function subtract($a, $b){
        $result = $a - $b;
        return view('builder::substract', compact('result'));
    }
}