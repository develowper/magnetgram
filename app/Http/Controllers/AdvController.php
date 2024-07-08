<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;

use App\Models\Adv;

use Illuminate\Http\Request;

use Log;

class AdvController extends Controller
{

    public function click(Request $request)
    {
        return Adv::findOrNew($request->id)->increment('clicks');
    }


}
