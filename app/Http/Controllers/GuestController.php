<?php

namespace App\Http\Controllers;

use App\Models\Art;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class GuestController extends Controller
{
    /**
     * @return Response
     */
    public function codes()
    {
        $codes = DB::table('codes_list')->get()->map(function ($row) {
            $art = explode(' ', $row->description);
            $art = end($art);

            if (preg_match('/[A-Z]\d{5}/', $art)) {
                $row->art = trim($art);
            }

            return $row;
        });

        return Inertia::render('Applications/Guest/CodesExternal', [
            'codes' => $codes,
        ]);
    }

    /**
     * @return Response
     */
    public function arts()
    {
        $arts = Art::orderBy('requerimiento', 'desc')
            ->get();

        return Inertia::render('Applications/Guest/Art', [
            'arts' => $arts,
        ]);
    }
}
