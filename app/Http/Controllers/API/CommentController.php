<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\CommentResource;


class CommentController extends Controller
{
    
    public function getAllComments()
    {
        $comments = Comment::all();
        return response()->json([
            'status'=>200,
            'massage'=>'Retrieved success',
            'data'=>CommentResource::collection($comments)
        ]);
        
    }

    
    public function makeComment(Request $request)
    {
        $validator =Validator::make($request->all(),[
            'comment'=> 'required',
            'post_id'=> 'required|numeric|exists:posts,id',
            'user_id' => 'required|numeric|exists:users,id'
        ]);
        if($validator->fails()){
            return response()->json([ 
                'status' => 400,
                'error' => $validator->errors() 
            ]);
        }

        $comment=$request->comment;
        $userId=$request->user_id;
        $postId=$request->post_id;
        $comment = $this->createComment($comment,$userId,$postId);
        return response()->json([
            'status'=>200,
            'message'=>'User created',
            'data'=>new CommentResource($comment),  
        ]);
    }

    private function createComment($comment,$userId,$postId){
        return Comment::create([
            'comment'=>$comment,
            'user_id'=>$userId,
            'post_id'=>$postId
        ]);
    }

   
    
    public function getCommentByID(Request $request)
    {
        $comment= Comment::find($request->id);
        return response()->json([
            'status'=>200,
            'message'=>'Retrieved Success',
            'data'=>new CommentResource($comment)
        ]);
    }
    
    public function updateCommentByUserID(Request $request)
    {
        
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return response(['message' => 'Deleted']);
    }
}

