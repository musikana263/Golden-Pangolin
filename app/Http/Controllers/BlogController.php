<?php

namespace App\Http\Controllers;

use App\Blog;
use App\Category;
use App\Http\Requests\Blog\CreateBlogRequest;
use App\Http\Requests\Blog\UpdateBlogRequest;
use App\Services\ImageService;

class BlogController extends Controller
{
    public function __construct(private ImageService $imageService)
    {
        $this->middleware('verifyCategoriesCount')->only(['create', 'store']);
    }

    public function index()
    {
        // Order by the admin-controlled drag-reorder, then by newest first as
        // a tiebreaker for fresh posts that haven't been positioned yet.
        $blog = Blog::orderBy('sort_order')->orderByDesc('id')->get();
        return view('blog.index')->with('blog', $blog);
    }

    public function create()
    {
        return view('blog.create')->with('categories', Category::all());
    }

    public function store(CreateBlogRequest $request)
    {
        // upload image
        $image = $this->imageService->upload($request->image, 'blogs');
        // create post
        $destination = Blog::create([
            'title' => $request->title,
            'description' => $request->description,
            'content' => $request->content,
            'image' => $image,
            'published_at' => $request->filled('published_at') ? $request->published_at : now(),
            'category_id' => $request->category,
        ]);

        // flash message
        session()->flash('success', 'Blog Created Successfully');

        // redirect
        return redirect(route('blog.index'));
    }

    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Blog $blog)
    {
        return view('blog.create')->with('blog', $blog)->with('categories', Category::all());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBlogRequest $request, Blog $blog)
    {
        $data = $request->only(['title', 'description', 'published_at', 'content']);

        if ($request->filled('category')) {
            $data['category_id'] = $request->category;
        }

        if ($request->hasFile('image')) {
            $image = $this->imageService->upload($request->image, 'blogs');
            $blog->deleteImage();
            $data['image'] = $image;
        }

        $blog->update($data);

        session()->flash('success', 'Blog updated successfully.');

        return redirect(route('blog.index'));
    }

    public function destroy($id)
    {
        $blog = Blog::withTrashed()->where('id', $id)->firstOrFail();

        if ($blog->trashed()) {
            $blog->deleteImage();
            $blog->forceDelete();
        } else {
            $blog->delete();
        }

        session()->flash('success', 'Blog post deleted.');

        return redirect(route('blog.index'));
    }
}
