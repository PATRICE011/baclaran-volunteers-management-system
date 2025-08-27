<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\Volunteer;
use App\Models\VolunteerDetail;
use App\Models\Ministry;

use App\Exports\VolunteersExport;
use Maatwebsite\Excel\Facades\Excel;

class VolunteersController extends Controller
{
    public function index(Request $request)
    {

        try {
            $user = auth()->user();

            // Build the base query, including eager-loaded relations
            $query = Volunteer::with(['detail.ministry', 'timelines', 'affiliations'])
                ->where('is_archived', false);

            // If user is staff, filter by their ministry
            if ($user->isStaff() && $user->ministry_id) {
                $query->whereHas('detail', function ($q) use ($user) {
                    $q->where('ministry_id', $user->ministry_id);
                });
            }


            // Search filter
            if ($request->filled('search')) {
                $searchTerm = trim($request->input('search'));
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('nickname', 'like', "%{$searchTerm}%")
                        ->orWhere('email_address', 'like', "%{$searchTerm}%")
                        ->orWhereHas('detail', function ($q) use ($searchTerm) {
                            $q->where('full_name', 'like', "%{$searchTerm}%");
                        })
                        ->orWhereHas('detail.ministry', function ($q) use ($searchTerm) {
                            $q->where('ministry_name', 'like', "%{$searchTerm}%");
                        });
                });
            }

            // Ministry filter
            if ($request->filled('ministry')) {
                $ministryId = $request->ministry;
                if (is_numeric($ministryId)) {
                    $query->whereHas('detail.ministry', function ($q) use ($ministryId) {
                        $q->where('id', $ministryId);
                    });
                }
            }

            // Status filter
            if ($request->filled('status')) {
                $status = $request->status;
                if (in_array($status, ['Active', 'Inactive'])) {
                    $query->whereHas('detail', function ($q) use ($status) {
                        $q->where('volunteer_status', $status);
                    });
                }
            }

            // Run the query, newest first
            $volunteers = $query->latest()
                ->paginate(12)
                ->appends($request->only(['search', 'ministry', 'status', 'view']));

            foreach ($volunteers as $volunteer) {
                $applied = $volunteer->detail?->applied_month_year;

                if ($applied) {
                    try {
                        $start = Carbon::createFromFormat('Y-m', $applied);
                        $endDate = ($volunteer->detail?->volunteer_status === 'Inactive' && $volunteer->detail?->updated_at)
                            ? Carbon::parse($volunteer->detail->updated_at)
                            : now();

                        $totalMonths = $start->diffInMonths($endDate);
                        $years = floor($totalMonths / 12);
                        $months = $totalMonths % 12;

                        $parts = [];
                        if ($years) {
                            $parts[] = "$years year" . ($years > 1 ? 's' : '');
                        }
                        if ($months) {
                            $parts[] = "$months month" . ($months > 1 ? 's' : '');
                        }

                        $volunteer->active_for = count($parts) > 0 ? implode(' ', $parts) : 'Less than a month';
                    } catch (\Exception $e) {
                        $volunteer->active_for = 'Invalid date';
                    }
                } else {
                    $volunteer->active_for = 'Duration unknown';
                }
            }


            // Fetch ministries and statuses
            $ministries = Ministry::whereNull('parent_id')
                ->with(['children.children']) // includes sub-groups
                ->get();


            // Fetch distinct statuses from the volunteer_details table
            $statuses = VolunteerDetail::select('volunteer_status')
                ->distinct()
                ->pluck('volunteer_status');

            // Handle AJAX or full page load
            if ($request->ajax() || $request->wantsJson()) {
                $viewType = $request->get('view', 'grid');
                $viewName = $viewType === 'list'
                    ? 'partials.volunteer_list_row'
                    : 'partials.volunteer_card';

                return response()->json([
                    'success' => true,
                    'html' => view($viewName, compact('volunteers'))->render(),
                    'view' => $viewType,
                ]);
            }

            // Full page load
            return view('admin_volunteer', compact('volunteers', 'ministries', 'statuses', 'user'));
        } catch (\Exception $e) {
            Log::error('Volunteer filter error: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while filtering volunteers',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->withError('An error occurred while filtering volunteers');
        }
    }

    public function store(Request $request)
    {
        try {
            // Validate the profile picture if uploaded
            if ($request->hasFile('profile_picture')) {
                $request->validate([
                    'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
                ]);
            }

            // Log the incoming request data (excluding sensitive info)
            Log::info('Volunteer registration attempt', [
                'request_data' => $request->except(['profile_picture']),
                'formations_received' => $request->formations,
                'bos_year' => $request->bos_year,
                'diocesan_year' => $request->diocesan_year,
                'safeguarding_year' => $request->safeguarding_year,
                'other_formation_check' => $request->other_formation_check,
                'other_formation' => $request->other_formation,
                'other_formation_year' => $request->other_formation_year
            ]);

            // Normalize inputs
            $firstName = Str::title(trim($request->first_name));
            $lastName = Str::title(trim($request->last_name));
            $middleInitial = strtoupper(trim($request->middle_initial));
            $nickname = Str::title(trim($request->nickname));
            $email = strtolower(trim($request->email));
            $fullName = "{$firstName} {$middleInitial} {$lastName}";
            $birthDate = $request->dob;
            $volunteerId = $request->volunteer_id ?: 'VOL-' . strtoupper(Str::random(6));

            // Check for existing volunteer by name + birthdate OR email
            $duplicate = Volunteer::where('email_address', $email)
                ->orWhere('volunteer_id', $volunteerId)
                ->orWhere(function ($q) use ($fullName, $birthDate) {
                    $q->whereHas('detail', function ($q) use ($fullName) {
                        $q->where('full_name', $fullName);
                    })->whereDate('date_of_birth', $birthDate);
                })
                ->exists();

            if ($duplicate) {
                Log::warning('Duplicate volunteer registration attempt', [
                    'email' => $email,
                    'volunteer_id' => $volunteerId,
                    'full_name' => $fullName,
                    'date_of_birth' => $birthDate
                ]);

                return response()->json([
                    'message' => 'Volunteer already exists with the same name and birthdate or email.',
                ], 409);
            }

            // Handle profile picture upload
            $profilePicturePath = null;
            if ($request->hasFile('profile_picture')) {
                try {
                    $file = $request->file('profile_picture');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $profilePicturePath = $file->storeAs('profile_pictures', $filename, 'public');
                    Log::info('Profile picture uploaded successfully', ['path' => $profilePicturePath]);
                } catch (\Exception $e) {
                    Log::error('Profile picture upload failed', [
                        'error' => $e->getMessage(),
                        'filename' => $file->getClientOriginalName()
                    ]);
                    throw $e;
                }
            }

            // Normalize other fields
            $address = Str::title(trim($request->address));
            $occupation = Str::title(trim($request->occupation));
            $civilStatusMap = [
                'widower' => 'Widow/er',
                // Add other mappings if needed
            ];

            $civilStatus = $request->civil_status === 'others'
                ? ($request->civil_status_other ?: 'Others')
                : $request->civil_status;

            // Map to database values
            if (array_key_exists(strtolower($civilStatus), $civilStatusMap)) {
                $civilStatus = $civilStatusMap[strtolower($civilStatus)];
            } else {
                $civilStatus = Str::title($civilStatus);
            }

            // Handle formations with years - FIXED VERSION
            $formations = [];

            // Process predefined formations with years
            $formationMappings = [
                'BOS' => $request->bos_year,
                'Diocesan Basic Formation' => $request->diocesan_year,
                'Safeguarding Policy' => $request->safeguarding_year
            ];

            Log::info('Processing formations', [
                'request_formations' => $request->formations,
                'formation_mappings' => $formationMappings,
                'other_formation_check' => $request->other_formation_check,
                'other_formation' => $request->other_formation,
                'other_formation_year' => $request->other_formation_year
            ]);

            // Process standard formations
            if ($request->has('formations') && is_array($request->formations)) {
                foreach ($request->formations as $formation) {
                    // Skip "Other Formation" as it's handled separately
                    if ($formation === 'Other Formation') {
                        continue;
                    }

                    // Check if this formation has a corresponding year
                    if (array_key_exists($formation, $formationMappings)) {
                        $year = $formationMappings[$formation];
                        $formations[] = $year ? "{$formation} ({$year})" : $formation;
                    } else {
                        $formations[] = $formation;
                    }
                }
            }

            // Process other formation separately - FIXED
            if ($request->other_formation_check == '1' && !empty($request->other_formation)) {
                $otherFormation = trim($request->other_formation);
                $otherYear = $request->other_formation_year;

                if (!empty($otherFormation)) {
                    $formations[] = $otherYear ? "{$otherFormation} ({$otherYear})" : $otherFormation;
                }
            }

            Log::info('Formations processed', ['formations' => $formations]);

            // Create volunteer
            $volunteerData = [
                'volunteer_id' => $volunteerId,
                'nickname' => $nickname,
                'date_of_birth' => $birthDate,
                'sex' => strtolower($request->sex),
                'address' => $address,
                'mobile_number' => $request->phone,
                'email_address' => $email,
                'occupation' => $occupation,
                'civil_status' => $civilStatus,
                'profile_picture' => $profilePicturePath,
            ];

            Log::info('Creating volunteer with data', ['volunteer_data' => $volunteerData]);

            $volunteer = Volunteer::create($volunteerData);
            // Create sacraments
            if ($request->has('sacraments')) {
                foreach ($request->sacraments as $sacrament) {
                    $volunteer->sacraments()->create([
                        'sacrament_name' => $sacrament
                    ]);
                }
            }

            // Create formations
            foreach ($formations as $formation) {
                // Extract year if present (format: "Formation Name (Year)")
                $year = null;
                $formationName = $formation;

                if (preg_match('/(.*)\s\((\d{4})\)$/', $formation, $matches)) {
                    $formationName = trim($matches[1]);
                    $year = $matches[2];
                }

                $volunteer->formations()->create([
                    'formation_name' => $formationName,
                    'year' => $year
                ]);
            }
            // Create detail
            $detailData = [
                'ministry_id' => $request->ministry_id,
                'line_group' => $request->ministry_id,
                'applied_month_year' => $request->applied_date,
                'regular_years_month' => $request->regular_duration,
                'full_name' => $fullName,
                'volunteer_status' => 'Active',
            ];

            Log::info('Creating volunteer detail', ['detail_data' => $detailData]);
            $volunteer->detail()->create($detailData);

            // Timeline entries
            $timelineOrgs = $request->timeline_org ?? [];
            $timelineStartYears = $request->timeline_start_year ?? [];
            $timelineEndYears = $request->timeline_end_year ?? [];
            $timelineTotals = $request->timeline_total ?? [];

            foreach ($timelineOrgs as $i => $org) {
                if (!empty(trim($org))) {
                    $orgName = Str::title(trim($org));
                    $startYear = !empty($timelineStartYears[$i]) ? $timelineStartYears[$i] : null;
                    $endYear = !empty($timelineEndYears[$i]) ? $timelineEndYears[$i] : null;
                    $totalYears = isset($timelineTotals[$i]) ? $timelineTotals[$i] : null;
                    $isActive = $endYear === 'present';

                    $volunteer->timelines()->create([
                        'organization_name' => $orgName,
                        'year_started' => $startYear,
                        'year_ended' => $isActive ? null : $endYear,
                        'total_years' => $totalYears,
                        'is_active' => $isActive,
                    ]);
                }
            }

            // Affiliations
            $affilOrgs = $request->affil_org ?? [];
            $affilStartYears = $request->affil_start_year ?? [];
            $affilEndYears = $request->affil_end_year ?? [];
            $affilTotals = $request->affil_total ?? [];

            foreach ($affilOrgs as $i => $org) {
                if (!empty(trim($org))) {
                    $orgName = Str::title(trim($org));
                    $startYear = !empty($affilStartYears[$i]) ? $affilStartYears[$i] : null;
                    $endYear = !empty($affilEndYears[$i]) ? $affilEndYears[$i] : null;
                    $totalYears = isset($affilTotals[$i]) ? $affilTotals[$i] : null;
                    $isActive = $endYear === 'present';

                    $volunteer->affiliations()->create([
                        'organization_name' => $orgName,
                        'year_started' => $startYear,
                        'year_ended' => $isActive ? null : $endYear,
                        'is_active' => $isActive,
                    ]);
                }
            }

            Log::info('Volunteer registered successfully', [
                'volunteer_id' => $volunteer->id,
                'volunteer_number' => $volunteer->volunteer_id
            ]);

            return response()->json(['message' => 'Volunteer registered successfully.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Volunteer registration validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->except(['profile_picture'])
            ]);

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Volunteer registration error: ' . $e->getMessage(), [
                'request_data' => $request->except(['profile_picture']),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'message' => 'Error registering volunteer',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function show($id)
    {
        try {
            $volunteer = Volunteer::with([
                'detail.ministry',
                'timelines',
                'affiliations',
                'sacraments',
                'formations'
            ])->findOrFail($id);

            $volunteer->volunteer_id = $volunteer->volunteer_id ?? 'N/A';
            $volunteer->has_complete_profile = $volunteer->hasCompleteProfile();

            $applied = $volunteer->detail?->applied_month_year;
            if ($applied) {
                try {
                    $start = Carbon::createFromFormat('Y-m', $applied);
                    $now = $volunteer->detail?->volunteer_status === 'Inactive' && $volunteer->detail?->updated_at
                        ? Carbon::parse($volunteer->detail->updated_at)
                        : now();

                    $months = $start->diffInMonths($now);
                    $years = floor($months / 12);
                    $remainingMonths = $months % 12;

                    $parts = [];
                    if ($years)
                        $parts[] = "$years year" . ($years > 1 ? 's' : '');
                    if ($remainingMonths)
                        $parts[] = "$remainingMonths month" . ($remainingMonths > 1 ? 's' : '');
                    $volunteer->active_for = $parts ? implode(' ', $parts) : 'Less than a month';
                } catch (\Exception) {
                    $volunteer->active_for = 'Invalid date';
                }
            } else {
                $volunteer->active_for = 'Duration unknown';
            }

            $ministries = Ministry::whereNull('parent_id')
                ->with('children.children') // Load grandchildren
                ->get();

            return response()->json([
                'volunteer' => $volunteer,
                'ministries' => $ministries,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Volunteer not found'
            ], 404);
        }
    }

    public function edit($id)
    {
        $volunteer = Volunteer::with(['detail.ministry', 'timelines', 'affiliations'])->findOrFail($id);
        $ministries = Ministry::whereNull('parent_id')->with('children')->get();
        return view('admin_edit_profile', compact('volunteer', 'ministries'));
    }

    public function update(Request $request, $id)
    {
        $volunteer = Volunteer::findOrFail($id);

        // Update basic info
        $volunteer->update($request->only([
            'nickname',
            'date_of_birth',
            'sex',
            'address',
            'mobile_number',
            'email_address',
            'occupation',
            'civil_status'
        ]));

        // Update sacraments
        if ($request->has('sacraments')) {
            $volunteer->sacraments()->delete(); // Remove existing
            foreach ($request->sacraments as $sacramentData) {
                if (!empty($sacramentData['sacrament_name'])) {
                    $volunteer->sacraments()->create([
                        'sacrament_name' => $sacramentData['sacrament_name'],
                        'year' => $sacramentData['year'] ?? null
                    ]);
                }
            }
        }

        // Update formations
        if ($request->has('formations')) {
            $volunteer->formations()->delete(); // Remove existing
            foreach ($request->formations as $formationData) {
                if (!empty($formationData['formation_name'])) {
                    $volunteer->formations()->create([
                        'formation_name' => $formationData['formation_name'],
                        'year' => $formationData['year'] ?? null
                    ]);
                }
            }
        }

        // Update timelines and affiliations similarly
        if ($request->has('timelines')) {
            $volunteer->timelines()->delete(); // Remove existing
            foreach ($request->timelines as $timelineData) {
                $volunteer->timelines()->create([
                    'organization_name' => $timelineData['organization_name'],
                    'year_started' => $timelineData['year_started'],
                    'year_ended' => $timelineData['year_ended'],
                    'total_years' => $timelineData['total_years'] ?? null,
                ]);
            }
        }

        if ($request->has('affiliations')) {
            $volunteer->affiliations()->delete(); // Remove existing
            foreach ($request->affiliations as $affiliationData) {
                $volunteer->affiliations()->create([
                    'organization_name' => $affiliationData['organization_name'],
                    'year_started' => $affiliationData['year_started'],
                    'year_ended' => $affiliationData['year_ended'],
                    'total_years' => $affiliationData['total_years'] ?? null,
                ]);
            }
        }

        return response()->json(['success' => true]);
    }


    public function updateProfilePicture(Request $request, $id)
    {
        try {
            $request->validate([
                'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $volunteer = Volunteer::findOrFail($id);

            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('profile_pictures', $filename, 'public');

                // Delete old picture if exists
                if ($volunteer->profile_picture) {
                    Storage::disk('public')->delete($volunteer->profile_picture);
                }

                $volunteer->profile_picture = $path;
                $volunteer->save();
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Profile picture update error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update profile picture'], 500);
        }
    }

    public function updateTimeline(Request $request, $id)
    {
        try {
            $volunteer = Volunteer::findOrFail($id);
            $data = $request->validate([
                'index' => 'required|integer',
                'data.organization_name' => 'required|string|max:255',
                'data.year_started' => 'nullable|integer',
                'data.year_ended' => 'nullable|string',
                'data.total_years' => 'nullable|integer',
            ]);

            $timeline = $volunteer->timelines[$data['index']] ?? null;

            if ($timeline) {
                $timeline->update($data['data']);
            } else {
                $volunteer->timelines()->create($data['data']);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Timeline update error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update timeline'], 500);
        }
    }

    public function updateAffiliation(Request $request, $id)
    {
        try {
            $volunteer = Volunteer::findOrFail($id);
            $data = $request->validate([
                'index' => 'required|integer',
                'data.organization_name' => 'required|string|max:255',
                'data.year_started' => 'nullable|integer',
                'data.year_ended' => 'nullable|string',
                'data.is_active' => 'nullable|boolean',
            ]);

            $affiliation = $volunteer->affiliations[$data['index']] ?? null;

            if ($affiliation) {
                $affiliation->update($data['data']);
            } else {
                $volunteer->affiliations()->create($data['data']);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Affiliation update error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update affiliation'], 500);
        }
    }

    public function updateSacraments(Request $request, $id)
    {
        try {
            $volunteer = Volunteer::findOrFail($id);
            $volunteer->sacraments_received = $request->sacraments;
            $volunteer->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Update sacraments error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update sacraments'], 500);
        }
    }

    public function updateFormations(Request $request, $id)
    {
        try {
            $volunteer = Volunteer::findOrFail($id);
            $volunteer->formations_received = $request->formations;
            $volunteer->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Update formations error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update formations'], 500);
        }
    }

    public function completeUpdate(Request $request, $id)
    {
        try {
            $volunteer = Volunteer::findOrFail($id);

            // Update basic info
            if (!empty($request->all())) {
                // Update volunteer details if they exist in the request
                if ($volunteer->detail) {
                    if ($request->has('volunteer_status')) {
                        $volunteer->detail->volunteer_status = $request->volunteer_status;
                    }
                    if ($request->has('ministry_id')) {
                        $volunteer->detail->ministry_id = $request->ministry_id;
                    }
                    if ($request->has('full_name')) {
                        $volunteer->detail->full_name = $request->full_name;
                    }
                    $volunteer->detail->save();
                }

                // Update volunteer fields (excluding detail fields)
                $volunteer->fill($request->except(['volunteer_status', 'ministry_id', 'full_name']));
                $volunteer->save();
            }

            // Update timelines
            if ($request->has('timelines')) {
                $volunteer->timelines()->delete();
                foreach ($request->timelines as $timelineData) {
                    $volunteer->timelines()->create($timelineData);
                }
            }

            // Update affiliations
            if ($request->has('affiliations')) {
                $volunteer->affiliations()->delete();
                foreach ($request->affiliations as $affiliationData) {
                    $volunteer->affiliations()->create($affiliationData);
                }
            }

            // Update sacraments
            if ($request->has('sacraments')) {
                $volunteer->sacraments()->delete();
                foreach ($request->sacraments as $sacrament) {
                    if (!empty($sacrament)) {
                        $volunteer->sacraments()->create([
                            'sacrament_name' => $sacrament
                        ]);
                    }
                }
            }

            // Update formations
            if ($request->has('formations')) {
                $volunteer->formations()->delete();
                foreach ($request->formations as $formation) {
                    if (!empty($formation['formation_name'])) {
                        $volunteer->formations()->create([
                            'formation_name' => $formation['formation_name'],
                            'year' => $formation['year'] ?? null
                        ]);
                    }
                }
            }

            return response()->json(['message' => 'Volunteer updated successfully.']);
        } catch (\Exception $e) {
            Log::error('Complete update error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update volunteer.'], 500);
        }
    }
    public function archive(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:255']);

        $volunteer = Volunteer::findOrFail($id);

        $volunteer->update([
            'is_archived' => true,
            'archived_at' => now(),
            'archived_by' => auth()->id(),
            'archive_reason' => $request->reason,
        ]);

        Log::info("Volunteer #{$volunteer->id} archived by user #" . auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'Volunteer archived successfully'
        ]);
    }

    public function restore(Volunteer $volunteer)
    {
        try {
            $volunteer->update(['is_archived' => false]);
            return response()->json([
                'success' => true,
                'message' => 'Volunteer restored successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error restoring volunteer: ' . $e->getMessage()
            ]);
        }
    }

    public function forceDelete(Volunteer $volunteer)
    {
        try {
            $volunteer->forceDelete();
            return response()->json([
                'success' => true,
                'message' => 'User permanently deleted'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting user: ' . $e->getMessage()
            ]);
        }
    }


    public function bulkRestore(Request $request)
    {
        $ids = $request->input('ids');
        $count = Volunteer::where('is_archived', true)
            ->whereIn('id', $ids)
            ->update(['is_archived' => false]);

        return response()->json([
            'success' => true,
            'restored_count' => $count,
            'message' => "$count volunteer(s) restored successfully"
        ]);
    }

    public function bulkForceDelete(Request $request)
    {
        $ids = $request->input('ids');
        $count = Volunteer::where('is_archived', true)
            ->whereIn('id', $ids)
            ->forceDelete();

        return response()->json([
            'success' => true,
            'deleted_count' => $count,
            'message' => "$count volunteer(s) permanently deleted"
        ]);
    }

    public function exportExcel()
    {
        $fileName = 'volunteers_export_' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new VolunteersExport, $fileName);
    }
}
