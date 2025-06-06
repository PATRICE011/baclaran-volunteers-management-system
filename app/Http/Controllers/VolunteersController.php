<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Volunteer;
use App\Models\Ministry;

class VolunteersController extends Controller
{
    public function index(Request $request)
    {
        // Build the base query, including eager-loaded relations
        $query = Volunteer::with(['detail.ministry']);

        /* ---------- Search filter ---------- */
        if ($request->filled('search')) {
            $searchTerm = trim($request->input('search'));

            $query->where(function ($q) use ($searchTerm) {
                $q->where('nickname',      'like', "%{$searchTerm}%")
                    ->orWhere('email_address', 'like', "%{$searchTerm}%")

                    // full_name lives on the related detail model
                    ->orWhereHas('detail', function ($q) use ($searchTerm) {
                        $q->where('full_name', 'like', "%{$searchTerm}%");
                    })

                    // ministry_name lives on the grand-child relation
                    ->orWhereHas('detail.ministry', function ($q) use ($searchTerm) {
                        $q->where('ministry_name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        /* ---------- Run the query, newest first ---------- */
        $volunteers = $query->latest()
            ->paginate(12)
            ->appends($request->only(['search', 'view']));

        /* ---------- AJAX or full page ---------- */
        if ($request->ajax() || $request->wantsJson()) {
            $viewType = $request->get('view', 'grid');
            $viewName = $viewType === 'list'
                ? 'partials.volunteer_list_row'  // Changed to include pagination
                : 'partials.volunteer_card';  // Changed to include pagination

            return response()->json([
                'success' => true,
                'html'    => view($viewName, compact('volunteers'))->render(),
                'view'    => $viewType,
            ]);
        }

        // Full page load
        $ministries = Ministry::whereNull('parent_id')
            ->with('children')
            ->get();

        return view('admin_volunteer', compact('volunteers', 'ministries'));
    }

    public function store(Request $request)
{
    try {
        // Normalize civil status
        $civilStatus = $request->civil_status === 'others'
            ? ($request->civil_status_other ?: 'Others')
            : $request->civil_status;

        // Normalize name and other fields
        $nickname = Str::title(trim($request->nickname));
        $address = Str::title(trim($request->address));
        $occupation = Str::title(trim($request->occupation));
        $civilStatus = Str::title($civilStatus);

        $firstName = Str::title(trim($request->first_name));
        $lastName = Str::title(trim($request->last_name));
        $middleInitial = strtoupper(trim($request->middle_initial));

        // Create main volunteer
        $volunteer = Volunteer::create([
            'nickname' => $nickname,
            'date_of_birth' => $request->dob,
            'sex' => strtolower($request->sex),
            'address' => $address,
            'mobile_number' => $request->phone,
            'email_address' => strtolower($request->email),
            'occupation' => $occupation,
            'civil_status' => $civilStatus,
            'sacraments_received' => $request->sacraments ?? [],
            'formations_received' => $request->formations ?? [],
        ]);

        // Create detail
        $volunteer->detail()->create([
            'ministry_id' => $request->ministry_id ?: null,
            'line_group' => $request->ministry_id,
            'applied_month_year' => $request->applied_date,
            'regular_years_month' => $request->regular_duration,
            'full_name' => "{$lastName} {$firstName} {$middleInitial}",
            'volunteer_status' => 'Active',
        ]);

        // Timeline entries
        foreach ($request->timeline_org ?? [] as $i => $org) {
            if (!empty($org)) {
                $orgName = Str::title(trim($org));
                $total = $request->timeline_total[$i] ?? '';
                $totalYears = (int) filter_var($total, FILTER_SANITIZE_NUMBER_INT);

                $volunteer->timelines()->create([
                    'organization_name' => $orgName,
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
                $orgName = Str::title(trim($org));
                $volunteer->affiliations()->create([
                    'organization_name' => $orgName,
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
    public function show($id)
    {
        try {
            $volunteer = Volunteer::with(['detail.ministry'])
                ->findOrFail($id);

            // Add computed properties for the frontend
            $volunteer->has_complete_profile = $volunteer->hasCompleteProfile();

            return response()->json($volunteer);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Volunteer not found'
            ], 404);
        }
    }

    // You might also want to add a destroy method for the delete functionality
    public function destroy($id)
    {
        try {
            $volunteer = Volunteer::findOrFail($id);
            $volunteer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Volunteer deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete volunteer'
            ], 500);
        }
    }
}
