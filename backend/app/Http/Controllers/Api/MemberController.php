<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreMemberRequest;
use App\Http\Requests\Member\UpdateMemberRequest;
use App\Http\Resources\MemberResource;
use App\Models\MemberProfile;
use App\Services\MemberService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

/**
 * MemberController
 *
 * Handles HTTP requests for member profile operations.
 * RESTful resource controller following Laravel conventions.
 * Delegates business logic to MemberService.
 */
class MemberController extends Controller
{
    public function __construct(
        private readonly MemberService $memberService
    ) {}

    /**
     * Display a listing of members (paginated).
     *
     * GET /api/members?page=1&per_page=15&search=john&status=active
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Check authorization to view member list
        Gate::authorize('viewAny', MemberProfile::class);

        // Get pagination and search parameters from request
        $perPage = (int) $request->query('per_page', 15);
        $perPage = min(max($perPage, 5), 100); // Clamp between 5-100

        // Optional: Filter by status
        $status = $request->query('status');

        // Optional: Search term (searches name, email, membership type)
        $search = $request->query('search');

        $members = $status === 'active'
            ? $this->memberService->getActiveMembers($perPage, $search)
            : $this->memberService->getAllMembers($perPage, $search);

        // Laravel's paginate() automatically handles ?page=N from request
        return response()->json([
            'data' => MemberResource::collection($members->items()),
            'meta' => [
                'total' => $members->total(),
                'per_page' => $members->perPage(),
                'current_page' => $members->currentPage(),
                'last_page' => $members->lastPage(),
                'from' => $members->firstItem(),
                'to' => $members->lastItem(),
            ],
        ]);
    }

    /**
     * Store a newly created member profile.
     *
     * POST /api/members
     *
     * @param StoreMemberRequest $request
     * @return JsonResponse
     */
    public function store(StoreMemberRequest $request): JsonResponse
    {
        $memberProfile = $this->memberService->createMemberProfile(
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'message' => 'Member profile created successfully.',
            'data' => new MemberResource($memberProfile),
        ], 201);
    }

    /**
     * Display specified member.
     *
     * GET /api/members/{id}
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $memberProfile = $this->memberService->findMemberById($id);

        // Check authorization - user can only view their own profile
        Gate::authorize('view', $memberProfile);

        return response()->json([
            'data' => new MemberResource($memberProfile),
        ]);
    }

    /**
     * Update specified member.
     *
     * PUT/PATCH /api/members/{id}
     *
     * @param UpdateMemberRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateMemberRequest $request, int $id): JsonResponse
    {
        // Fetch member first to check authorization
        $memberProfile = $this->memberService->findMemberById($id);

        // Check authorization - user can only update their own profile
        Gate::authorize('update', $memberProfile);

        $memberProfile = $this->memberService->updateMemberProfile(
            $id,
            $request->validated()
        );

        return response()->json([
            'message' => 'Member profile updated successfully.',
            'data' => new MemberResource($memberProfile),
        ]);
    }

    /**
     * Remove specified member (soft delete).
     *
     * DELETE /api/members/{id}
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        // Fetch member first to check authorization
        $memberProfile = $this->memberService->findMemberById($id);

        // Check authorization - user can only delete their own profile
        Gate::authorize('delete', $memberProfile);

        $this->memberService->deleteMemberProfile($id);

        return response()->noContent();
    }
}
