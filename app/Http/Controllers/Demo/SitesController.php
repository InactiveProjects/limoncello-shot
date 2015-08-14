<?php namespace App\Http\Controllers\Demo;

use \Validator;
use \App\Models\Site;
use \Symfony\Component\HttpFoundation\Response;
use \App\Http\Controllers\JsonApi\JsonApiController;
use \Illuminate\Contracts\Validation\ValidationException;

/**
 * @package Neomerx\LimoncelloShot
 */
class SitesController extends JsonApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $this->checkParametersEmpty();

        return $this->getResponse(Site::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $this->checkParametersEmpty();

        $attributes = array_get($this->getDocument(), 'data.attributes', []);

        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($attributes, ['name' => 'required|min:5']);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $site = new Site($attributes);
        $site->save();

        return $this->getCreatedResponse($site);
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

        return $this->getResponse(Site::findOrFail($id));
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

        $attributes = array_get($this->getDocument(), 'data.attributes', []);

        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($attributes, ['name' => 'sometimes|required|min:5']);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $site = Site::findOrFail($id);
        $site->fill($attributes);
        $site->save();

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

        $site = Site::findOrFail($id);
        $site->delete();

        return $this->getCodeResponse(Response::HTTP_NO_CONTENT);
    }
}
