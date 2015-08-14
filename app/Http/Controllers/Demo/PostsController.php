<?php namespace App\Http\Controllers\Demo;

use \App\Models\Post;
use \Symfony\Component\HttpFoundation\Response;
use \App\Http\Controllers\JsonApi\JsonApiController;
use \Illuminate\Contracts\Validation\ValidationException;

/**
 * @package Neomerx\LimoncelloShot
 */
class PostsController extends JsonApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return $this->getResponse(Post::with(['author', 'comments', 'author.posts'])->get()->all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $this->checkParametersEmpty();

        $content    = $this->getDocument();
        $attributes = array_get($content, 'data.attributes', []);

        $attributes['author_id'] = array_get($content, 'data.relationships.author.data.id', null);
        $attributes['site_id']   = array_get($content, 'data.relationships.site.data.id', null);

        /** @var \Illuminate\Validation\Validator $validator */
        $rules = [
            'title'     => 'required',
            'body'      => 'required',
            'author_id' => 'required|integer',
            'site_id'   => 'required|integer'
        ];
        /** @noinspection PhpUndefinedClassInspection */
        $validator = \Validator::make($attributes, $rules);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $post = new Post($attributes);
        $post->save();

        return $this->getCreatedResponse($post);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $idx
     * @return Response
     */
    public function show($idx)
    {
        $this->checkParametersEmpty();

        return $this->getResponse(Post::findOrFail($idx));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $idx
     * @return Response
     */
    public function update($idx)
    {
        $this->checkParametersEmpty();

        $content = $this->getDocument();
        $attributes = array_get($content, 'data.attributes', []);

        $attributes['author_id'] = array_get($content, 'data.relationships.author.data.id', null);
        $attributes['site_id']   = array_get($content, 'data.relationships.site.data.id', null);
        $attributes = array_filter($attributes, function ($value) {
            return $value !== null;
        });

        /** @var \Illuminate\Validation\Validator $validator */
        $rules = [
            'title'     => 'sometimes|required',
            'body'      => 'sometimes|required',
            'author_id' => 'sometimes|required|integer',
            'site_id'   => 'sometimes|required|integer'
        ];
        /** @noinspection PhpUndefinedClassInspection */
        $validator = \Validator::make($attributes, $rules);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $post = Post::findOrFail($idx);
        $post->fill($attributes);
        $post->save();

        return $this->getCodeResponse(Response::HTTP_NO_CONTENT);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $idx
     * @return Response
     */
    public function destroy($idx)
    {
        $this->checkParametersEmpty();

        $comment = Post::findOrFail($idx);
        $comment->delete();

        return $this->getCodeResponse(Response::HTTP_NO_CONTENT);
    }
}
