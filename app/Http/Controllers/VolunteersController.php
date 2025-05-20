<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Volunteer;
use App\Models\Ministry;

class VolunteersController extends Controller
{
    public function index()
    {
        $ministries = Ministry::whereNull('parent_id')->with('children')->get();
        return view('admin_volunteer', compact('ministries'));
    }

    public function store(Request $request)
    {
        try {
            // Normalize civil status
            $civilStatus = $request->civil_status === 'others'
                ? ($request->civil_status_other ?: 'Others')
                : $request->civil_status;

            // Create main volunteer
            $volunteer = Volunteer::create([
                'nickname' => $request->nickname,
                'date_of_birth' => $request->dob,
                'sex' => $request->sex,
                'address' => $request->address,
                'mobile_number' => $request->phone,
                'email_address' => $request->email,
                'occupation' => $request->occupation,
                'civil_status' => $civilStatus,
                'sacraments_received' => $request->sacraments ?? [],
                'formations_received' => $request->formations ?? [],
            ]);

            // Create detail
            $volunteer->detail()->create([
                'ministry_id' => $request->ministry_id ?: null,
                'line_group' => $request->ministry_id, // optional: adjust if line_group differs
                'applied_month_year' => $request->applied_date,
                'regular_years_month' => $request->regular_duration,
                'full_name' => trim("{$request->last_name} {$request->first_name} {$request->middle_initial}"),
            ]);

            // Timeline entries
            foreach ($request->timeline_org ?? [] as $i => $org) {
                if (!empty($org)) {
                    $total = $request->timeline_total[$i] ?? '';
                    $totalYears = (int) filter_var($total, FILTER_SANITIZE_NUMBER_INT);

                    $volunteer->timelines()->create([
                        'organization_name' => $org,
                        'year_started' => $request->timeline_start_year[$i] ?? null,
                        'year_ended' => $request->timeline_end_year[$i] ?? null,
                        'total_years' => $totalYears ?: null,
                        'is_active' => ($request->timeline_active[$i] ?? '') === 'Y',
                    ]);
                }
            }

            // Affiliations
            foreach ($request->affil_org ?? [] as $i => $org) {
                if (!empty($org)) {
                    $volunteer->affiliations()->create([
                        'organization_name' => $org,
                        'year_started' => $request->affil_start_year[$i] ?? null,
                        'year_ended' => $request->affil_end_year[$i] ?? null,
                        'is_active' => ($request->affil_active[$i] ?? '') === 'Y',
                    ]);
                }
            }

            return response()->json(['message' => 'Volunteer registered successfully.']);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error registering volunteer',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
