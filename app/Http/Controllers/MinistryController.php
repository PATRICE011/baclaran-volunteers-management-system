<?php

namespace App\Http\Controllers;

use App\Models\Ministry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MinistryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Build query with filters
            $query = Ministry::with(['children', 'parent', 'volunteerDetails.volunteer'])
                ->withCount(['volunteerDetails', 'children']);
            
            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('ministry_name', 'LIKE', "%{$search}%")
                      ->orWhere('ministry_code', 'LIKE', "%{$search}%");
                });
            }
            
            // Apply category filter
            if ($request->filled('category') && $request->get('category') !== 'All') {
                $query->where('ministry_type', $request->get('category'));
            }
            
            // Get ministries with pagination
            $ministries = $query->orderBy('ministry_name', 'asc')
                               ->paginate(12);
            
            // Get distinct categories for filter dropdown
            $categories = Ministry::select('ministry_type')
                                 ->distinct()
                                 ->orderBy('ministry_type')
                                 ->get();
            
            // If it's an AJAX request (for search/filter), return JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'ministries' => $ministries->items(),
                    'pagination' => [
                        'current_page' => $ministries->currentPage(),
                        'last_page' => $ministries->lastPage(),
                        'total' => $ministries->total(),
                        'per_page' => $ministries->perPage(),
                    ]
                ]);
            }
            
            return view('admin_ministries', compact('user', 'ministries', 'categories'));
            
        } catch (\Exception $e) {
            Log::error('Error in MinistryController@index: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while loading ministries.'
                ], 500);
            }
            
            return back()->with('error', 'An error occurred while loading ministries.');
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $validator = Validator::make($request->all(), [
                'ministry_name' => 'required|string|max:255|unique:ministries,ministry_name',
                'ministry_code' => 'nullable|string|max:20|unique:ministries,ministry_code',
                'ministry_type' => 'required|in:LITURGICAL,PASTORAL,SOCIAL_MISSION,SUB_GROUP',
                'parent_id' => 'nullable|exists:ministries,id',
            ], [
                'ministry_name.required' => 'Ministry name is required.',
                'ministry_name.unique' => 'A ministry with this name already exists.',
                'ministry_code.unique' => 'A ministry with this code already exists.',
                'ministry_type.required' => 'Ministry type is required.',
                'ministry_type.in' => 'Invalid ministry type selected.',
                'parent_id.exists' => 'Selected parent ministry does not exist.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check for circular reference if parent_id is provided
            if ($request->filled('parent_id')) {
                $parentId = $request->get('parent_id');
                if ($this->wouldCreateCircularReference(null, $parentId)) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['parent_id' => ['This selection would create a circular reference.']]
                    ], 422);
                }
            }

            $ministry = Ministry::create([
                'ministry_name' => $request->get('ministry_name'),
                'ministry_code' => $request->get('ministry_code'),
                'ministry_type' => $request->get('ministry_type'),
                'parent_id' => $request->get('parent_id'),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ministry created successfully.',
                'ministry' => $this->formatMinistryData($ministry->load(['children', 'parent', 'volunteerDetails']))
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating ministry: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the ministry.'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $ministry = Ministry::with(['children', 'parent', 'volunteerDetails.volunteer'])
                ->findOrFail($id);

            $volunteers = $ministry->volunteerDetails->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'name' => $detail->full_name ?? 'N/A',
                    'email' => $detail->volunteer->email_address ?? 'N/A',
                    'phone' => $detail->volunteer->mobile_number ?? 'N/A',
                    'status' => $detail->volunteer_status ?? 'Unknown',
                    'line_group' => $detail->line_group ?? null,
                    'applied_date' => $detail->applied_month_year ?? null,
                    'regular_date' => $detail->regular_years_month ?? null,
                ];
            });

            return response()->json([
                'success' => true,
                'ministry' => $this->formatMinistryData($ministry),
                'volunteers' => $volunteers
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ministry not found.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error showing ministry: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while loading ministry details.'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $ministry = Ministry::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'ministry_name' => 'required|string|max:255|unique:ministries,ministry_name,' . $id,
                'ministry_code' => 'nullable|string|max:20|unique:ministries,ministry_code,' . $id,
                'ministry_type' => 'required|in:LITURGICAL,PASTORAL,SOCIAL_MISSION,SUB_GROUP',
                'parent_id' => 'nullable|exists:ministries,id',
            ], [
                'ministry_name.required' => 'Ministry name is required.',
                'ministry_name.unique' => 'A ministry with this name already exists.',
                'ministry_code.unique' => 'A ministry with this code already exists.',
                'ministry_type.required' => 'Ministry type is required.',
                'ministry_type.in' => 'Invalid ministry type selected.',
                'parent_id.exists' => 'Selected parent ministry does not exist.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check for circular reference if parent_id is being changed
            if ($request->filled('parent_id')) {
                $parentId = $request->get('parent_id');
                if ($parentId != $ministry->parent_id && $this->wouldCreateCircularReference($id, $parentId)) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['parent_id' => ['This selection would create a circular reference.']]
                    ], 422);
                }
            }

            $ministry->update([
                'ministry_name' => $request->get('ministry_name'),
                'ministry_code' => $request->get('ministry_code'),
                'ministry_type' => $request->get('ministry_type'),
                'parent_id' => $request->get('parent_id'),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ministry updated successfully.',
                'ministry' => $this->formatMinistryData($ministry->load(['children', 'parent', 'volunteerDetails']))
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ministry not found.'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating ministry: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the ministry.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $ministry = Ministry::findOrFail($id);

            // Check if ministry has children
            $childrenCount = $ministry->children()->count();
            if ($childrenCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete ministry with {$childrenCount} sub-ministries. Please delete or reassign sub-ministries first."
                ], 422);
            }

            // Check if ministry has volunteers
            $volunteersCount = $ministry->volunteerDetails()->count();
            if ($volunteersCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete ministry with {$volunteersCount} assigned volunteers. Please reassign volunteers first."
                ], 422);
            }

            $ministryName = $ministry->ministry_name;
            $ministry->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Ministry '{$ministryName}' deleted successfully."
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ministry not found.'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting ministry: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the ministry.'
            ], 500);
        }
    }

    public function getParentMinistries()
    {
        try {
            $parents = Ministry::whereNull('parent_id')
                ->select('id', 'ministry_name', 'ministry_type')
                ->orderBy('ministry_name')
                ->get();

            return response()->json([
                'success' => true,
                'parents' => $parents
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting parent ministries: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while loading parent ministries.'
            ], 500);
        }
    }

    /**
     * Get ministry statistics
     */
    public function getStats()
    {
        try {
            $stats = [
                'total_ministries' => Ministry::count(),
                'by_type' => Ministry::select('ministry_type', DB::raw('count(*) as count'))
                    ->groupBy('ministry_type')
                    ->get()
                    ->mapWithKeys(function ($item) {
                        return [$this->mapMinistryTypeToCategory($item->ministry_type) => $item->count];
                    }),
                'with_volunteers' => Ministry::has('volunteerDetails')->count(),
                'without_volunteers' => Ministry::doesntHave('volunteerDetails')->count(),
                'parent_ministries' => Ministry::whereNull('parent_id')->count(),
                'sub_ministries' => Ministry::whereNotNull('parent_id')->count(),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting ministry stats: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while loading statistics.'
            ], 500);
        }
    }

    /**
     * Check if setting a parent would create a circular reference
     */
    private function wouldCreateCircularReference($ministryId, $parentId)
    {
        if (!$parentId || $ministryId === $parentId) {
            return $ministryId === $parentId; // Self-reference
        }

        // If we're creating a new ministry, no circular reference possible
        if (!$ministryId) {
            return false;
        }

        // Check if the proposed parent is actually a descendant of this ministry
        $parent = Ministry::find($parentId);
        while ($parent) {
            if ($parent->id == $ministryId) {
                return true; // Circular reference found
            }
            $parent = $parent->parent;
        }

        return false;
    }

    /**
     * Map ministry type to readable category
     */
    private function mapMinistryTypeToCategory($type)
    {
        $mapping = [
            'LITURGICAL' => 'Liturgical',
            'PASTORAL' => 'Pastoral',
            'SOCIAL_MISSION' => 'Social Mission',
            'SUB_GROUP' => 'Sub Group'
        ];

        return $mapping[$type] ?? 'Other';
    }

    /**
     * Format ministry data for API responses
     */
    private function formatMinistryData($ministry)
    {
        return [
            'id' => $ministry->id,
            'name' => $ministry->ministry_name,
            'code' => $ministry->ministry_code,
            'category' => $this->mapMinistryTypeToCategory($ministry->ministry_type),
            'type' => $ministry->ministry_type,
            'volunteers' => $ministry->volunteerDetails ? $ministry->volunteerDetails->count() : 0,
            'parent_id' => $ministry->parent_id,
            'parent_name' => $ministry->parent ? $ministry->parent->ministry_name : null,
            'has_children' => $ministry->children ? $ministry->children->count() > 0 : false,
            'children_count' => $ministry->children ? $ministry->children->count() : 0,
            'full_path' => $this->buildFullPath($ministry),
            'created_at' => $ministry->created_at ? $ministry->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $ministry->updated_at ? $ministry->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }

    /**
     * Build full hierarchical path for ministry
     */
    private function buildFullPath($ministry)
    {
        $path = [];
        $current = $ministry;
        
        while ($current) {
            array_unshift($path, $current->ministry_name);
            $current = $current->parent;
        }
        
        return implode(' > ', $path);
    }
}