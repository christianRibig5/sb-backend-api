<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    
    public function getAllPostFeeds()
    {
        $posts = Post::all();
        $rowCount=$posts->count();
        foreach($posts as $post){
            $user=$this->getUser($post);
            $comment=$this->getPostComments($post);
        }
        
        return response()->json([
            'status'=>200,
            'massage'=>'Retrieved success',
            'count'=>$rowCount,
            'data'=>PostResource::collection($posts)
        ]);
    }

    private function getUser($post){
        $user=User::where('id',$post->user_id)->first();
        $post->posterUserDetails=$user;
        return $post->posterUserdetails;
    }
    private function getPostComments($post){
        $comments=Comment::where('post_id',$post->id)->get();
        $post->postComments=$comments;
        foreach($post->postComments as $comment){
            $user=$this->getCommentUser($comments);
        }
        return $post->postComments;
    }
    private function getCommentUser($comments){
        foreach($comments as $comment){
            $user=User::where('id',$comment->user_id)->first();
            $comment->commenterUserDetails=$user;
        return $comment->commenterUserDetails;
        }
    }
    
    public function makePost(Request $request)
    {
        $validator =Validator::make($request->all(),[
            'post_text'=> 'required',
            'user_id'=> 'required|numeric|exists:users,id',
            'file_path'=>'sometimes|required',
            'file_path'=>'max:10240'
        ]);
        if($validator->fails()){
            return response()->json([ 
                'status' => 400,
                'error' => $validator->errors() 
            ]);
        }
        $postTex=$request->post_text;
        $userId=$request->user_id;
        $file=$request->file_path;
        $post = $this->createPost($postTex,$userId,$file);
        return response()->json([
            'status'=>200,
            'message'=>'Post created',
            'data'=>new PostResource($post),
        ]);
    }
    private function resize($file) {
        ini_set('memory_limit', '256M');
        $file->resize(2000,null);
    }

   private function createPost($postText,$userId,$file){
       if($file===null){
        return Post::create([
            'post_text'=>$postText,
            'user_id'=>$userId,
        ]);
       }
        $mimeType = $file->getMimeType();
        $imageMimes = array('image/jpg','image/jpeg','image/png');
        $videoMimes = array('video/mp4','video/x-ms-wmv','video/quicktime');
        
        if(in_array($mimeType, $imageMimes)){
            $renamedFile = time().'.'.$file->getClientOriginalExtension();
            $destination = public_path('/uploads/images');
            $file->move($destination,$renamedFile);
            return Post::create([
                'post_text'=>$postText,
                'user_id'=>$userId,
                'image_path'=>$renamedFile
            ]);
        
        }else if(in_array($mimeType, $videoMimes)){
           
            $renamedFile = time().'.'.$file->getClientOriginalExtension();
            $destination = public_path('/uploads/videos');
            $file->move($destination,$renamedFile);
            return Post::create([
                'post_text'=>$postText,
                'user_id'=>$userId,
                'video_path'=>$renamedFile
            ]);
        }
   }
    public function getPostByID(Request $request)
    {
        $post = Post::find($request->id);
        return response()->json([
            'status'=>200,
            'message'=>'Retrieved Success',
            'data'=>new PostResource($post)
        ]);
    }

   

    
    public function updatePostByID(Request $request)
    {
        $post = Post::findOrFail($request->id);
        if($post->image_path!==null){
            $post->post_text=$request->post_text;
            Storage::disk('public')->delete($post->image_path);
            $renamedFile = time().'.'.$file->getClientOriginalExtension();
            $destination = public_path('/uploads/images');
            $file->move($destination,$renamedFile);
            $post->image_path=$renamedFile;
        }else if($post->video_path !==null){

            $post->post_text = $request->post_text;
            Storage::disk('public')->delete($post->video_path);
            $renamedFile = time().'.'.$file->getClientOriginalExtension();
            $destination = public_path('/uploads/videos');
            $file->move($destination,$renamedFile);
            $post->video_path = $renamedFile;

        }else if($post->image_path === null && $post->video_path===null){
            $post->post_text = $request->post_text;
        }
        
        $post->save();
        
        return response()->json([
            'status'=>200,
            'message'=>'Updated successfully',
            'data'=>new UserResource($post)
        ]);
        
    }
    public function updatePostByUserID($id){
        $post = Post::where('user_id',$id)->get();
        if($post->image_path!==null){
            $post->post_text=$request->post_text;
            Storage::disk('public')->delete($post->image_path);
            $renamedFile = time().'.'.$file->getClientOriginalExtension();
            $destination = public_path('/uploads/images');
            $file->move($destination,$renamedFile);
            $post->image_path=$renamedFile;
        }else if($post->video_path !==null){

            $post->post_text = $request->post_text;
            Storage::disk('public')->delete($post->video_path);
            $renamedFile = time().'.'.$file->getClientOriginalExtension();
            $destination = public_path('/uploads/videos');
            $file->move($destination,$renamedFile);
            $post->video_path = $renamedFile;

        }else if($post->image_path === null && $post->video_path===null){
            $post->post_text = $request->post_text;
        }
        
        $post->save();
        
        return response()->json([
            'status'=>200,
            'message'=>'Updated successfully',
            'data'=>new UserResource($post)
        ]);
    }

   
    public function destroy(Post $post)
    {
        $post->delete();

        return response(['message' => 'Deleted']);
    }
}
