<?php namespace App\Http\Controllers\PDO;

use \App\Models\PDO\Author;
use \Illuminate\Http\Response;
use \App\Http\Controllers\Controller;

/**
 * Author controller.
 */
class AuthorsController extends Controller
{
    /**
     * JSON API extensions supported by this controller.
     *
     * NOTE: Here it's declared for illustration/testing purposes only.
     * This controller does not support these JSON API extensions.
     *
     * If you do not use API extensions do not forget to remove this line in real application.
     *
     * @var string
     */
    protected $extensions = 'ext1,ex3';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return $this->getContentResponse(Author::search());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $attributes = array_get($this->getDocument(), 'data.attributes', []);

        $this->validateOrFail($attributes, [
            Author::FIELD_FIRST_NAME => 'required|alpha_dash',
            Author::FIELD_LAST_NAME  => 'required|alpha_dash'
        ]);

        $author = Author::newInstance();
        $author->setFirstName($attributes[Author::FIELD_FIRST_NAME]);
        $author->setLastName($attributes[Author::FIELD_LAST_NAME]);
        if (array_has($attributes, Author::FIELD_TWITTER) === true) {
            $author->setTwitter($attributes[Author::FIELD_TWITTER]);
        }
        $author->save();

        return $this->getCreatedResponse($author);
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
        return $this->getContentResponse(Author::findOrFail($idx));
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
        $attributes = array_get($this->getDocument(), 'data.attributes', []);

        $this->validateOrFail($attributes, [
            Author::FIELD_FIRST_NAME => 'sometimes|required|alpha_dash',
            Author::FIELD_LAST_NAME  => 'sometimes|required|alpha_dash'
        ]);

        /** @var Author $author */
        $author = Author::existingInstance($idx);
        if (array_has($attributes, Author::FIELD_FIRST_NAME) === true) {
            $author->setFirstName($attributes[Author::FIELD_FIRST_NAME]);
        }
        if (array_has($attributes, Author::FIELD_LAST_NAME) === true) {
            $author->setLastName($attributes[Author::FIELD_LAST_NAME]);
        }
        if (array_has($attributes, Author::FIELD_TWITTER) === true) {
            $author->setTwitter($attributes[Author::FIELD_TWITTER]);
        }
        $author->save();

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
        $author = Author::existingInstance($idx);
        $author->delete();

        return $this->getCodeResponse(Response::HTTP_NO_CONTENT);
    }
}
