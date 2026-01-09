<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreMemberRequest;
use App\Http\Requests\Member\UpdateMemberRequest;
use App\Http\Resources\MemberResource;
use App\Services\MemberService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
     * Display a listing of members.
     *
     * GET /api/members
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Optional: Filter by status
        $status = $request->query('status');

        $members = $status === 'active'
            ? $this->memberService->getActiveMembers()
            : $this->memberService->getAllMembers();

        return response()->json([
            'data' => MemberResource::collection($members),
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
     * Display the specified member.
     *
     * GET /api/members/{id}
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $memberProfile = $this->memberService->findMemberById($id);

        return response()->json([
            'data' => new MemberResource($memberProfile),
        ]);
    }

    /**
     * Update the specified member.
     *
     * PUT/PATCH /api/members/{id}
     *
     * @param UpdateMemberRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateMemberRequest $request, int $id): JsonResponse
    {
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
     * Remove the specified member (soft delete).
     *
     * DELETE /api/members/{id}
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $this->memberService->deleteMemberProfile($id);

        return response()->json([
            'message' => 'Member profile deleted successfully.',
        ]);
    }
}
