<?php namespace App\Http\Controllers\JsonApi;

use \Illuminate\Http\Response;
use \App\Http\Controllers\Controller;
use \Neomerx\Limoncello\Http\JsonApiTrait;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Limoncello\Contracts\IntegrationInterface;

/**
 * @package Neomerx\LimoncelloShot
 */
abstract class JsonApiController extends Controller
{
    use JsonApiTrait;

    /**
     * Constructor.
     */
    public function __construct()
    {
        /** @var IntegrationInterface integration */
        $integration = app(IntegrationInterface::class);
        $this->initJsonApiSupport($integration);
    }

    /**
     * @param object|array                                                       $data
     * @param int                                                                $statusCode
     * @param array<string,\Neomerx\JsonApi\Contracts\Schema\LinkInterface>|null $links
     * @param mixed                                                              $meta
     *
     * @return Response
     */
    public function getResponse($data, $statusCode = Response::HTTP_OK, $links = null, $meta = null)
    {
        // If you use Eloquent the following helper method will assist you to
        // encode database collections and keep your code clean.

        $data = $data instanceof Collection ? $data->all() : $data;

        return $this->getContentResponse($data, $statusCode, $links, $meta);
    }
}
