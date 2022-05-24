<?php

namespace App\Http\Controllers\Admin;

use App\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    protected $validationRules = [
        'title' => 'required|max:100',
        'slug' => 'required|unique:posts|max:100',
        'body' => 'required',
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::paginate(10);
        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, $this->validationRules);

        $post = Post::create($request->all() + ['user_id' => auth()->id()]);

        return redirect()->route('admin.posts.index', $post->slug)->with('status', 'Your post has been created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {

        return view('admin.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        if (auth()->id() !== $post->user_id) {
            abort(403);
        }
        return view('admin.posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        if(auth()->id() !== $post->user_id) {
            abort(403);
        }
        $this->validate($request, $this->validationRules);
        $post->update($request->all());
        return redirect()->route('admin.posts.edit', $post->slug)->with('status', 'Your post has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        if(auth()->id() !== $post->user_id) {
            abort(403);
        }
        $previousUrl = url()->previous();

        if ($previousUrl === route('admin.posts.edit', $post->slug)) {
            $previousUrl = route('admin.posts.index');
        }

        $post->delete();

        return redirect($previousUrl)->with('status', 'Your post has been deleted');
    }

    public function myindex()
    {
        $posts = Post::where('user_id', auth()->id())->paginate(10);
        return view('admin.posts.index', compact('posts'));
    }
}
