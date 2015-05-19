<?php namespace App\Http\Controllers\PDO;

use \App\Models\PDO\Site;
use \Illuminate\Http\Response;
use \App\Http\Controllers\Controller;

/**
 * Site controller.
 */
class SitesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return $this->getContentResponse(Site::search());
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
            Site::FIELD_NAME => 'required|min:5',
        ]);

        $site = Site::newInstance();
        $site->setName($attributes[Site::FIELD_NAME]);
        $site->save();

        return $this->getCreatedResponse($site);
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
        return $this->getContentResponse(Site::findOrFail($idx));
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
            Site::FIELD_NAME => 'sometimes|required|min:5',
        ]);

        /** @var Site $site */
        $site = Site::existingInstance($idx);
        if (array_has($attributes, Site::FIELD_NAME) === true) {
            $site->setName($attributes[Site::FIELD_NAME]);
        }
        $site->save();

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
        $author = Site::existingInstance($idx);
        $author->delete();

        return $this->getCodeResponse(Response::HTTP_NO_CONTENT);
    }
}
