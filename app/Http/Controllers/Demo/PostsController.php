<?php namespace App\Http\Controllers\Demo;

use \Validator;
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
        $this->checkParametersEmpty();

        return $this->getResponse(Post::all());
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

        $attributes['author_id'] = array_get($content, 'data.links.author.linkage.id', null);
        $attributes['site_id']   = array_get($content, 'data.links.site.linkage.id', null);

        /** @var \Illuminate\Validation\Validator $validator */
        $rules = [
            'title'     => 'required',
            'body'      => 'required',
            'author_id' => 'required|integer',
            'site_id'   => 'required|integer'
        ];
        $validator = Validator::make($attributes, $rules);
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
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $this->checkParametersEmpty();

        return $this->getResponse(Post::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $this->checkParametersEmpty();

        $content = $this->getDocument();
        $attributes = array_get($content, 'data.attributes', []);

        $attributes['author_id'] = array_get($content, 'data.links.author.linkage.id', null);
        $attributes['site_id']   = array_get($content, 'data.links.site.linkage.id', null);
        $attributes = array_filter($attributes, function ($value) {return $value !== null;});

        /** @var \Illuminate\Validation\Validator $validator */
        $rules = [
            'title'     => 'sometimes|required',
            'body'      => 'sometimes|required',
            'author_id' => 'sometimes|required|integer',
            'site_id'   => 'sometimes|required|integer'
        ];
        $validator = Validator::make($attributes, $rules);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $post = Post::findOrFail($id);
        $post->fill($attributes);
        $post->save();

        return $this->getCodeResponse(Response::HTTP_NO_CONTENT);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $this->checkParametersEmpty();

        $comment = Post::findOrFail($id);
        $comment->delete();

        return $this->getCodeResponse(Response::HTTP_NO_CONTENT);
    }
}
