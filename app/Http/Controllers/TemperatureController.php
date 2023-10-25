<?php

namespace App\Http\Controllers;

use App\Models\TemperatureControl;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TemperatureController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Applications/TemperatureControl/Index');
    }

    public function create_temperature(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = TemperatureControl::create([
            'employee_document' => $request->employee_document,
            'temperature' => $request->temperature,
            'fever' => $request->fever,
            'cough' => $request->cough,
            'throat_pain' => $request->throat_pain,
            'respiratory_distress' => $request->respiratory_distress,
            'loss_of_taste' => $request->loss_of_taste,
            'contact_infected_person' => $request->contact_infected_person,
            'observations' => $request->observations,
            'time_of_entry' => Carbon::now()->format("Y-m-d {$request->time_of_entry}"),
            'created_by' => auth()->user()->id,
        ]);

        return response()->json($data, 200);
    }
}
