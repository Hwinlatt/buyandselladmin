<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->limit;
        $total = Post::where('user_id', Auth::user()->id)->count();
        $posts = Post::where('user_id', Auth::user()->id)->when(request('status') != '', function ($q) {
            $q->where('status', request('status'));
        })
            ->when(request('search'), function ($q) {
                $q->where(function ($q) {
                    $q->where('posts.name', 'like', '%' . request('search') . '%');
                    $q->orWhere('posts.additional', 'like', '%' . request('search') . '%');
                    $q->orWhere('posts.description', 'like', '%' . request('search') . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->limit($limit)->get()->each(function ($post) {
            $post->images = json_decode($post->images);
            $post->post_like_count = Like::where('post_id',$post->id)->count();
            return $post;
        });
        return response()->json(['posts' => $posts, 'total' => $total], 200);
    }

    public function store(Request $request)
    {
        $validator = $this->postValidation($request, 'create');
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 200);
        }
        Post::create($this->savePost($request, null));
        return response()->json(['success' => 'Post Create Successful.'], 200);
    }

    public function show($id, Request $request)
    {
        $post = Post::where('id', $id)->get()->each(function ($post) {
            $post->images = json_decode($post->images);
            $post->post_like_count = Like::where('post_id',$post->id)->count();
            $like = Like::where('post_id', $post->id)->where('user_id', Auth::user()->id)->first();
            if ($like) {
                $post->like = true;
            } else {
                $post->like = false;
            }
            return $post;
        });
        if (Auth::user()->id != $post[0]->user_id) {
            $updatePost = Post::find($id);
            $updatePost->update([
                'view' => $updatePost->view + 1,
            ]);
        }
        $user = User::find($post[0]->user_id);
        if ($post && $user) {
            return response()->json(['post' => $post, 'user' => $user], 200);
        }
    }

    public function edit($id, Request $request)
    {
        $post = Post::where('id', $id)->where('user_id', Auth::user()->id)->get()->each(function ($post) {
            $post->images = json_decode($post->images);
            return $post;
        });
        if (count($post) > 0) {
            if ($post[0]->user_id == Auth::user()->id) {
                return response()->json($post, 200);
            } else {
                abort(403);
            }
        } else {
            abort(403);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = $this->postValidation($request, 'update');
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 200);
        }
        if (!$request->oldImages && !$request->images) {
            return response()->json(['error' => ['images field is required']], 200);
        }
        $oldImages = [];
        if ($request->oldImages) {
            $oldImages = $request->oldImages;
        }
        $post = Post::where('id', $id)->where('user_id', Auth::user()->id)->first();
        $dbOldImages = json_decode($post->images);
        foreach ($dbOldImages as $image) {
            if (!in_array($image, $oldImages)) {
                $this->deleteImage($image);
            }
        }
        $post->update($this->savePost($request, $post));
        if (count($oldImages) > 0) {
            $array = json_decode($post->images);
            foreach ($oldImages as $img) {
                array_push($array, $img);
            }
            $post->update([
                'images' => $array,
            ]);
        }

        return response()->json(['success' => 'Post updated success.'], 200);
    }

    public function destroy($id, Request $request)
    {
        $post = Post::where('user_id', $request->user()->id)->where('id', $id)->first();
        $images = json_decode($post->images);
        foreach ($images as $image) {
            $path = 'storage/images/' . $image;
            if (File::exists($path)) {
                File::delete($path);
            }
        }
        $post->delete();
        return response()->json(['success' => 'Post deleted.'], 200);
    }

    public function search($key, Request $request)
    {
        if (strlen($key) > 0) {
            $posts = Post::select('posts.*', 'users.region as region', 'users.city as city')->join('users', 'posts.user_id', 'users.id')
                ->where('posts.user_id', '!=', Auth::user()->id)
                ->where(function ($query) use ($key) {
                    $query->where('posts.name', 'like', '%' . $key . '%');
                    $query->orWhere('posts.additional', 'like', '%' . $key . '%');
                    $query->orWhere('posts.description', 'like', '%' . $key . '%');
                })
                ->get()->each(function ($post) {
                $post->images = json_decode($post->images);
                $like = Like::where('post_id', $post->id)->where('user_id', Auth::user()->id)->first();
                if ($like) {
                    $post->like = true;
                } else {
                    $post->like = false;
                }
                return $post;
            });
            return response()->json($posts, 200);
        }
    }

    public function post_by_user($id, $limit)
    {
        $posts = Post::where('user_id', $id)->orderBy('id', 'desc')->limit($limit)->get()->each(function ($post) {
            $post->images = json_decode($post->images);
            $like = Like::where('post_id', $post->id)->where('user_id', Auth::user()->id)->first();
            if ($like) {
                $post->like = true;
            } else {
                $post->like = false;
            }
            return $post;
        });

        return response()->json($posts, 200);
    }

    public function soldout($id, Request $request)
    {
        $post = Post::where('id', $id)->where('user_id', Auth::user()->id)->first();
        $post->update([
            'status' => '0',
        ]);
        return response()->json(['status' => '0'], 200);
    }

    public function resold($id, Request $request)
    {
        $post = Post::where('id', $id)->where('user_id', Auth::user()->id)->first();
        $post->update([
            'status' => '1',
        ]);
        return response()->json(['status' => '1'], 200);
    }

    //Select Post By Category
    public function post_by_category($id, Request $request)
    {
        $limit = $request->limit;
        $category = Category::find($id);
        $total = Post::where('category_id', $id)->where('status', '1')->count();
        $posts = Post::where('category_id', $id)->where('status', '1')
            ->limit($limit)->orderBy('view','desc')->get()->each(function ($post) {
            $post->images = json_decode($post->images);
            $like = Like::where('post_id', $post->id)->where('user_id', Auth::user()->id)->first();
            if ($like) {
                $post->like = true;
            } else {
                $post->like = false;
            }
            return $post;
        });
        return response()->json(['posts' => $posts, 'total' => $total,'category'=>$category], 200);
    }

    private function deleteImage($filename)
    {
        $path = 'storage/images/' . $filename;
        if (File::exists($path)) {
            File::delete($path);
        }
    }

    private function savePost($request, $db)
    {
        $return = [
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'price' => $request->price . ' ',
            'mmk' => $request->mmk,
            'adjust_price' => $request->adjust_price ? 1 : 0,
            'additional' => $request->additional,
            'description' => $request->description,
        ];
        if (!$db) {
            $return['view'] = 0;
        }
        if ($request->images) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $fileName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/images', $fileName);
                array_push($images, $fileName);
            };
            $return['images'] = json_encode($images);
        } else {
            $return['images'] = json_encode([]);
        }
        return $return;
    }
    private function postValidation($request, $type)
    {
        $rule = [
            'name' => 'required|string|max:255',
            'category_id' => 'required',
            'price' => 'required|string|max:10',
            'description' => 'required|string|max:4294967290',
        ];
        if ($type == 'create') {
            $rule['images'] = 'required';
        }
        $validator = Validator::make($request->all(), $rule);
        return $validator;
    }
}
