<?php

namespace Ljozwiak\Gus\Http\Controllers;

use Ljozwiak\Gus\Gus;
use Illuminate\Http\Request;

class GusController extends Controller
{
    public function index()
    {
        return view('gus::gus.index');
    }

    public function searchGus(Request $request)
    {
        $request->validate([
            'number' => 'required|numeric|digits_between:9,10',
        ]);

        $number = $request->input('number');
        $gus = new Gus();
        return $gus->getGusInfo($number);
    }
}
