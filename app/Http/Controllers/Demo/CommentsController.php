<?php namespace App\Http\Controllers\Demo;

use \Validator;
use \App\Models\Comment;
use \Symfony\Component\HttpFoundation\Response;
use \App\Http\Controllers\JsonApi\JsonApiController;
use \Illuminate\Contracts\Validation\ValidationException;

/**
 * @package Neomerx\LimoncelloShot
 */
class CommentsController extends JsonApiController
{
    protected $allowedFilteringParameters = ['ids'];

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        /*
         *  Parameters are passed just for illustration purposes.
         *  Please note you have to declare allowed parameters in $allowedFilteringParameters field.
         */
        $ids = $this->getParameters()->getFilteringParameters()['ids'];
        $ids ?: null; // avoid 'unused' warning

        return $this->getResponse(Comment::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $this->checkParametersEmpty();

        $content = $this->getDocument();

        $attributes            = array_get($content, 'data.attributes', []);
        $attributes['post_id'] = array_get($content, 'data.links.author.linkage.id', null);

        /** @var \Illuminate\Validation\Validator $validator */
        $rules     = ['body' => 'required', 'post_id' => 'required|integer'];
        $validator = Validator::make($attributes, $rules);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $comment = new Comment($attributes);
        $comment->save();

        return $this->getCreatedResponse($comment);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $this->checkParametersEmpty();

        return $this->getResponse(Comment::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function update($id)
    {
        $this->checkParametersEmpty();

        $content = $this->getDocument();

        $attributes = array_get($content, 'data.attributes', []);
        $postId     = array_get($content, 'data.links.author.linkage.id', null);
        if ($postId !== null) {
            $attributes['post_id'] = $postId;
        }

        /** @var \Illuminate\Validation\Validator $validator */
        $rules     = ['post_id' => 'sometimes|required|integer'];
        $validator = Validator::make($attributes, $rules);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $comment = Comment::findOrFail($id);
        $comment->fill($attributes);
        $comment->save();

        return $this->getCodeResponse(Response::HTTP_NO_CONTENT);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $this->checkParametersEmpty();

        $comment = Comment::findOrFail($id);
        $comment->delete();

        return $this->getCodeResponse(Response::HTTP_NO_CONTENT);
    }
}
