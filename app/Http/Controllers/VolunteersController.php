<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Arr;

use Illuminate\Http\Request;
use App\Models\Volunteer;
use App\Models\Ministry;
use Illuminate\Container\Attributes\Auth;

class VolunteersController extends Controller
{
    public function index(Request $request)
    {
        
        try {
            $user = auth()->user();

            // Build the base query, including eager-loaded relations
            $query = Volunteer::with(['detail.ministry', 'timelines', 'affiliations']);


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
            $ministries = Ministry::whereNull('parent_id')->with('children')->get();

            // Fetch distinct statuses from the volunteer_details table
            $statuses = \App\Models\VolunteerDetail::select('volunteer_status')
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
            return view('admin_volunteer', compact('volunteers', 'ministries', 'statuses','user'));
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

            // Normalize inputs
            $firstName = Str::title(trim($request->first_name));
            $lastName = Str::title(trim($request->last_name));
            $middleInitial = strtoupper(trim($request->middle_initial));
            $nickname = Str::title(trim($request->nickname));
            $email = strtolower(trim($request->email));
            $fullName = "{$firstName} {$middleInitial} {$lastName}";
            $birthDate = $request->dob;

            // Check for existing volunteer by name + birthdate OR email
            $duplicate = Volunteer::where('email_address', $email)
                ->whereHas('detail', function ($q) use ($fullName) {
                    $q->where('full_name', $fullName);
                })
                ->whereDate('date_of_birth', $birthDate)
                ->exists();


            if ($duplicate) {
                return response()->json([
                    'message' => 'Volunteer already exists with the same name and birthdate or email.',
                ], 409);
            }

            // Handle profile picture upload
            $profilePicturePath = null;
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $filename = time() . '_' . $file->getClientOriginalName();
                $profilePicturePath = $file->storeAs('profile_pictures', $filename, 'public');
            }

            // Normalize other fields
            $address = Str::title(trim($request->address));
            $occupation = Str::title(trim($request->occupation));
            $civilStatus = $request->civil_status === 'others'
                ? ($request->civil_status_other ?: 'Others')
                : $request->civil_status;
            $civilStatus = Str::title($civilStatus);

            // Create volunteer
            $volunteer = Volunteer::create([
                'nickname' => $nickname,
                'date_of_birth' => $birthDate,
                'sex' => strtolower($request->sex),
                'address' => $address,
                'mobile_number' => $request->phone,
                'email_address' => $email,
                'occupation' => $occupation,
                'civil_status' => $civilStatus,
                'sacraments_received' => $request->sacraments ?? [],
                'formations_received' => $request->formations ?? [],
                'profile_picture' => $profilePicturePath,


            ]);

            // Create detail
            $volunteer->detail()->create([
                'ministry_id' => $request->ministry_id,
                'line_group' => $request->ministry_id,
                'applied_month_year' => $request->applied_date,
                'regular_years_month' => $request->regular_duration,
                'full_name' => $fullName,
                'volunteer_status' => 'Active',
            ]);

            // Timeline entries - FIXED VERSION
            $timelineOrgs = $request->timeline_org ?? [];
            $timelineStartYears = $request->timeline_start_year ?? [];
            $timelineEndYears = $request->timeline_end_year ?? [];
            $timelineTotals = $request->timeline_total ?? [];
            $timelineActives = $request->timeline_active ?? [];

            foreach ($timelineOrgs as $i => $org) {
                if (!empty(trim($org))) {
                    $orgName = Str::title(trim($org));
                    $startYear = !empty($timelineStartYears[$i]) ? (int) $timelineStartYears[$i] : null;
                    $endYear = !empty($timelineEndYears[$i]) ? (int) $timelineEndYears[$i] : null;
                    $total = isset($timelineTotals[$i]) ? trim($timelineTotals[$i]) : '';
                    $totalYears = !empty($total) ? (int) filter_var($total, FILTER_SANITIZE_NUMBER_INT) : null;
                    $isActive = isset($timelineActives[$i]) && $timelineActives[$i] === 'Y';

                    $volunteer->timelines()->create([
                        'organization_name' => $orgName,
                        'year_started' => $startYear,
                        'year_ended' => $endYear,
                        'total_years' => $totalYears,
                        'is_active' => $isActive,
                    ]);
                }
            }

            // Affiliations - FIXED VERSION
            $affilOrgs = $request->affil_org ?? [];
            $affilStartYears = $request->affil_start_year ?? [];
            $affilEndYears = $request->affil_end_year ?? [];
            $affilActives = $request->affil_active ?? [];

            foreach ($affilOrgs as $i => $org) {
                if (!empty(trim($org))) {
                    $orgName = Str::title(trim($org));
                    $startYear = !empty($affilStartYears[$i]) ? (int) $affilStartYears[$i] : null;
                    $endYear = !empty($affilEndYears[$i]) ? (int) $affilEndYears[$i] : null;
                    $isActive = isset($affilActives[$i]) && $affilActives[$i] === 'Y';

                    $volunteer->affiliations()->create([
                        'organization_name' => $orgName,
                        'year_started' => $startYear,
                        'year_ended' => $endYear,
                        'is_active' => $isActive,
                    ]);
                }
            }

            return response()->json(['message' => 'Volunteer registered successfully.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Volunteer registration error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
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
            $volunteer = Volunteer::with(['detail.ministry', 'timelines', 'affiliations'])
                ->findOrFail($id);

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
                    if ($years) $parts[] = "$years year" . ($years > 1 ? 's' : '');
                    if ($remainingMonths) $parts[] = "$remainingMonths month" . ($remainingMonths > 1 ? 's' : '');
                    $volunteer->active_for = $parts ? implode(' ', $parts) : 'Less than a month';
                } catch (\Exception) {
                    $volunteer->active_for = 'Invalid date';
                }
            } else {
                $volunteer->active_for = 'Duration unknown';
            }

            // $ministries = Ministry::pluck('ministry_name')->toArray();
            $ministries = Ministry::select('id', 'ministry_name')->get(); 

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
        try {
            $volunteer = Volunteer::findOrFail($id);

            // Convert empty strings to null
            $request->merge(array_map(function ($value) {
                return $value === '' ? null : $value;
            }, $request->all()));

            $validated = $request->validate([
                'nickname' => 'sometimes|string|max:255',
                'date_of_birth' => 'sometimes|date',
                'sex' => 'sometimes|in:male,female,Male,Female',
                'address' => 'sometimes|string|max:255',
                'mobile_number' => 'sometimes|string|max:20',
                'email_address' => 'sometimes|email|max:255',
                'occupation' => 'sometimes|string|max:255',
                'civil_status' => 'sometimes|string|max:255',
                'full_name' => 'sometimes|string|max:255',
                'timelines' => 'sometimes|array',
                'affiliations' => 'sometimes|array',
                'profile_picture' => 'sometimes|image|mimes:jpg,jpeg,png,webp|max:2048',
                'volunteer_status' => 'sometimes|string|in:Active,Inactive',
                'ministry_name' => 'sometimes|nullable|string|exists:ministries,ministry_name',
            ]);

            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                $path = $request->file('profile_picture')->store('volunteer_avatars', 'public');
                $validated['profile_picture'] = $path;
            }

            // Capitalize specific fields
            foreach (['nickname', 'full_name', 'occupation', 'address', 'civil_status'] as $field) {
                if (isset($validated[$field])) {
                    $validated[$field] = Str::title($validated[$field]);
                }
            }

            // Normalize casing for 'sex'
            if (isset($validated['sex'])) {
                $validated['sex'] = ucfirst(strtolower($validated['sex']));
            }

            // Fill Volunteer (excluding detail fields)
            $volunteer->fill(Arr::except($validated, ['full_name', 'volunteer_status', 'ministry_name']));
            $volunteer->save();

            // Fill VolunteerDetail
            if ($volunteer->detail) {
                if (isset($validated['volunteer_status'])) {
                    $volunteer->detail->volunteer_status = $validated['volunteer_status'];
                }

                if (isset($validated['full_name'])) {
                    $volunteer->detail->full_name = $validated['full_name'];
                }

                // Convert ministry_name to ministry_id if present
                if (isset($validated['ministry_name'])) {
                    if ($validated['ministry_name'] === null) {
                        // If ministry_name is null, clear the ministry assignment
                        $volunteer->detail->ministry_id = null;
                    } else {
                        $ministry = Ministry::where('ministry_name', $validated['ministry_name'])->first();
                        if ($ministry) {
                            $volunteer->detail->ministry_id = $ministry->id;
                        }
                    }
                }

                $volunteer->detail->save();
            }

            return response()->json(['message' => 'Profile updated successfully.']);
        } catch (\Throwable $e) {
            Log::error('Update error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update volunteer.'], 500);
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
