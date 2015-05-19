<?php namespace App\Http\Controllers\Eloquent;

use \App\Models\Eloquent\Site;
use \App\Http\Controllers\Controller;
use \Symfony\Component\HttpFoundation\Response;

/**
 * Site Controller.
 */
class SitesController extends Controller
{
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
}
