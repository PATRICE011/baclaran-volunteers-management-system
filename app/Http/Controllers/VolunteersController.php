<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Volunteer;
use App\Models\Ministry;

class VolunteersController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Build the base query, including eager-loaded relations
            $query = Volunteer::with(['detail.ministry']);

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
            return view('admin_volunteer', compact('volunteers', 'ministries', 'statuses'));
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
            // Normalize inputs
            $firstName = Str::title(trim($request->first_name));
            $lastName = Str::title(trim($request->last_name));
            $middleInitial = strtoupper(trim($request->middle_initial));
            $nickname = Str::title(trim($request->nickname));
            $email = strtolower(trim($request->email));
            $fullName = "{$lastName} {$firstName} {$middleInitial}";
            $birthDate = $request->dob;

            // Check for existing volunteer by name + birthdate OR email
            $duplicate = Volunteer::where(function ($query) use ($email, $fullName, $birthDate) {
                $query->where('email_address', $email)
                    ->orWhereHas('detail', function ($q) use ($fullName, $birthDate) {
                        $q->where('full_name', $fullName)
                            ->whereHas('volunteer', function ($subQ) use ($birthDate) {
                                $subQ->whereDate('date_of_birth', $birthDate);
                            });
                    });
            })->exists();

            if ($duplicate) {
                return response()->json([
                    'message' => 'Volunteer already exists with the same name and birthdate or email.',
                ], 409);
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

            // [Timeline and Affiliation saving remains same...]
            // You can keep those sections as-is

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
