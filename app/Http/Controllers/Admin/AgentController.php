<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\StoreAgentRequest;
use App\Http\Requests\Agent\UpdateAgentRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function index()
    {
        $users = User::role('agent')->paginate(10);

        return ApiResponse::resource(UserResource::collection($users), "Successfully get agents");
    }

    public function store(StoreAgentRequest $request)
    {
        $data = $request->validated();

        $user = User::create($data);

        $user->assignRole('agent');

        return ApiResponse::resource(new UserResource($user), "Successfully created new agent");
    }

    public function update(UpdateAgentRequest $request, string $id)
    {
        $data = $request->validated();

        $user = User::findOrFail($id);

        $user->update($data);

        return ApiResponse::resource(new UserResource($user), "Successfully updated agent data");
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return ApiResponse::success("Successfully deleted agent");
    }

    public function resetPassword(ResetPasswordRequest $request, string $id)
    {
        $data = $request->validated();

        $user = User::findOrFail($id);

        $user->update($data);

        return ApiResponse::success("Successfully reset agent password");
    }
}
