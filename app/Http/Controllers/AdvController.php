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

    public function get(Request $request)
    {
        $lang = $request->lang;

        return DB::table('advs')->where(function ($query) use ($lang) {
            $query->where('lang', $lang)
                ->orWhereNull('lang');
        })->where('disabled', '!=', true)->inRandomOrder()->first());


    }

}
