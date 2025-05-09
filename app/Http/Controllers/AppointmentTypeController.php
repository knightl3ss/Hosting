<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppointmentTypeController extends Controller
{
    public function index()
    {
        $types = [
            'job_order',
            'contractual',
            'regularPermanent',
            'temporary',
            'casual',
            'elected',
            'provisional',
            'coterminous',
            'coterminous-temporary',
            'substitute',
        ];

        $typeLabels = config('appointment_types');

        $displayTypes = array_map(fn($type) => $typeLabels[$type] ?? $type, $types);

        return view('Pages.appointments.appointment_types_controller_page', [
            'displayTypes' => $displayTypes,
        ]);
    }
}
