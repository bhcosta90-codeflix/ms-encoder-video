<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function get(Request $request)
    {
        $obj = Video::where('name', $request->file)
            ->with('logs')
            ->firstOrFail();

        return response()->json($obj);
    }
}
