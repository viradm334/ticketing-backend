<?php

namespace App\Http\Controllers\Comment;

use App\Helpers\ApiResponse;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{
    public function show(string $id)
    {
        $comment = Comment::findOrFail($id);

        $comment->load(['user', 'ticket']);

        return ApiResponse::resource(new CommentResource($comment));
    }

    public function store(StoreCommentRequest $request)
    {
        $data = $request->validated();

        $data['user_id'] = Auth::id();

        $comment = Comment::create($data);

        return ApiResponse::resource(new CommentResource($comment), "Successfully created new comment");
    }

    public function update(UpdateCommentRequest $request, string $id)
    {
        $data = $request->validated();

        $comment = Comment::findOrFail($id);

        $this->authorize('update', $comment);

        $comment->update($data);

        $comment->load(['user', 'ticket']);

        return ApiResponse::resource(new CommentResource($comment), "Successfully updated comment");
    }

    public function destroy(string $id)
    {
        $comment = Comment::findOrFail($id);

        $this->authorize('delete', $comment);

        $comment->delete();

        return ApiResponse::success("Successfully deleted comment");
    }
}
