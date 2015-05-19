<?php namespace App\Http\Controllers\PDO;

use \App\Models\PDO\Site;
use \App\Models\PDO\Post;
use \App\Models\PDO\Author;
use \Illuminate\Http\Response;
use \App\Http\Controllers\Controller;

/**
 * Post controller.
 */
class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return $this->getContentResponse(Post::search());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $content    = $this->getDocument();
        $attributes = array_get($content, 'data.attributes', []);

        $attributes[Post::FIELD_AUTHOR_ID] = array_get($content, 'data.links.author.linkage.id', null);
        $attributes[Post::FIELD_SITE_ID]   = array_get($content, 'data.links.site.linkage.id', null);

        $this->validateOrFail($attributes, [
            Post::FIELD_TITLE     => 'required',
            Post::FIELD_BODY      => 'required',
            Post::FIELD_AUTHOR_ID => 'required|integer',
            Post::FIELD_SITE_ID   => 'required|integer'
        ]);

        $post = Post::newInstance();
        $post->setTitle($attributes[Post::FIELD_TITLE]);
        $post->setBody($attributes[Post::FIELD_BODY]);
        /** @var Author $author */
        $author = Author::existingInstance($attributes[Post::FIELD_AUTHOR_ID]);
        $post->setAuthor($author);
        /** @var Site $site */
        $site = Site::existingInstance($attributes[Post::FIELD_SITE_ID]);
        $post->setSite($site);
        $post->save();

        return $this->getCreatedResponse($post);
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
        return $this->getContentResponse(Post::findOrFail($idx));
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

        $attributes[Post::FIELD_AUTHOR_ID] = array_get($content, 'data.links.author.linkage.id', null);
        $attributes[Post::FIELD_SITE_ID]   = array_get($content, 'data.links.site.linkage.id', null);
        $attributes = array_filter($attributes, function ($value) {
            return $value !== null;
        });

        $this->validateOrFail($attributes, [
            Post::FIELD_TITLE     => 'sometimes|required',
            Post::FIELD_BODY      => 'sometimes|required',
            Post::FIELD_AUTHOR_ID => 'sometimes|required|integer',
            Post::FIELD_SITE_ID   => 'sometimes|required|integer'
        ]);

        /** @var Post $post */
        $post = Post::existingInstance($idx);
        if (array_has($attributes, Post::FIELD_TITLE) === true) {
            $post->setTitle($attributes[Post::FIELD_TITLE]);
        }
        if (array_has($attributes, Post::FIELD_BODY) === true) {
            $post->setBody($attributes[Post::FIELD_BODY]);
        }
        if (array_has($attributes, Post::FIELD_AUTHOR_ID) === true) {
            /** @var Author $author */
            $author = Author::existingInstance($attributes[Post::FIELD_AUTHOR_ID]);
            $post->setAuthor($author);
        }
        if (array_has($attributes, Post::FIELD_SITE_ID) === true) {
            /** @var Site $site */
            $site = Site::existingInstance($attributes[Post::FIELD_SITE_ID]);
            $post->setSite($site);
        }
        $post->save();

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
        $comment = Post::existingInstance($idx);
        $comment->delete();

        return $this->getCodeResponse(Response::HTTP_NO_CONTENT);
    }
}
