<?php namespace App\Http\Controllers\PDO;

use \App\Models\PDO\Post;
use \App\Models\PDO\Comment;
use \Illuminate\Http\Response;
use \App\Http\Controllers\Controller;

/**
 * Comments controller.
 */
class CommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return $this->getContentResponse(Comment::search());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $content = $this->getDocument();

        $body   = array_get($content, 'data.attributes.body', null);
        $postId = array_get($content, 'data.links.author.linkage.id', null);

        $this->validateOrFail([
            Comment::FIELD_BODY    => $body,
            Comment::FIELD_POST_ID => $postId,
        ], [
            Comment::FIELD_BODY    => 'required',
            Comment::FIELD_POST_ID => 'required|integer',
        ]);

        /** @var Post $post */
        $post = Post::existingInstance($postId);
        /** @var Comment $comment */
        $comment = Comment::newInstance();
        $comment->setBody($body);
        $comment->setPost($post);
        $comment->save();

        return $this->getCreatedResponse($comment);
    }

    /**
     * Display the specified resource.
     *
     * @param int $idx
     *
     * @return Response
     */
    public function show($idx)
    {
        return $this->getContentResponse(Comment::findOrFail($idx));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $idx
     *
     * @return Response
     */
    public function update($idx)
    {
        $content = $this->getDocument();

        $attributes = array_get($content, 'data.attributes', []);
        $postId     = array_get($content, 'data.links.author.linkage.id', null);
        if ($postId !== null) {
            $attributes[Comment::FIELD_POST_ID] = $postId;
        }

        $this->validateOrFail($attributes, [
            Comment::FIELD_BODY    => 'sometimes|required',
            Comment::FIELD_POST_ID => 'sometimes|required|integer',
        ]);

        /** @var Comment $comment */
        $comment = Comment::existingInstance($idx);
        if (array_has($attributes, Comment::FIELD_BODY) === true) {
            $comment->setBody($attributes[Comment::FIELD_BODY]);
        }
        if ($postId !== null) {
            /** @var Post $post */
            $post = Post::existingInstance($postId);
            $comment->setPost($post);
        }
        $comment->save();

        return $this->getCodeResponse(Response::HTTP_NO_CONTENT);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $idx
     *
     * @return Response
     */
    public function destroy($idx)
    {
        $comment = Comment::existingInstance($idx);
        $comment->delete();

        return $this->getCodeResponse(Response::HTTP_NO_CONTENT);
    }
}
