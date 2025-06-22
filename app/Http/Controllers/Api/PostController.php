<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Storage;
use Str;

class PostController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Post::class);

        $query = Post::query();

        $query->with(['user', 'category']);
        $query->withCount('comments');

        if (!$request->user()->isAdmin()) {
            $query->where(function ($q) use ($request) {
                $q->where('status', 'published')
                    ->orWhere('user_id', $request->user()->id);
            });
        }

        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        $query->latest('published_at');
        $posts = $query->paginate(10);

        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store("posts/{$request->user()->id}", 'public');
            $validatedData['featured_image_url'] = Storage::url($path);
        }

        $validatedData['user_id'] = $request->user()->id;
        $validatedData['slug'] = $validatedData['slug'] = Str::slug($validatedData['title']);

        $post = Post::create($validatedData);

        return response()->json([
            'message' => 'Post creado exitosamente.',
            'data' => new PostResource($post->load(['user', 'category']))
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $this->authorize('view', $post);

        $post->load(['user', 'category']);

        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image_url) {

                $oldUrl = $post->featured_image_url;
                $oldPath = str_replace('/storage/', '', parse_url($oldUrl, PHP_URL_PATH));
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('featured_image')->store('posts/' . $request->user()->id, 'public');
            $validatedData['featured_image_url'] = Storage::url($path);
        }

        if (isset($validatedData['title'])) {
            $validatedData['slug'] = Str::slug($validatedData['title']);
        }

        $post->update($validatedData);

        return response()->json([
            'message' => 'Post actualizado exitosamente.',
            'data' => new PostResource($post->load(['user', 'category']))
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete', Post::findOrFail($id));
        $post = Post::where('status', 'draft')->findOrFail($id);
        if ($post->featured_image_url) {
            $oldUrl = $post->featured_image_url;
            $oldPath = str_replace('/storage/', '', parse_url($oldUrl, PHP_URL_PATH));
            Storage::disk('public')->delete($oldPath);
        }
        $post->delete();
        return response()->json([
            'message' => 'Post eliminado exitosamente.',
        ], 204);

    }
}
