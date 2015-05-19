<?php

namespace App\Http\Controllers;

use \App\Services\LumenIntegration;
use \Neomerx\Limoncello\Http\JsonApiTrait;
use \Illuminate\Contracts\Validation\Factory;
use \Illuminate\Contracts\Validation\Validator;
use \Laravel\Lumen\Routing\Controller as BaseController;
use \Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Base controller.
 */
class Controller extends BaseController
{
    use JsonApiTrait;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->integration = new LumenIntegration();
        $this->initJsonApiSupport();
    }

    /**
     * Get validator.
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     *
     * @return Validator
     */
    protected function getValidator(array $data, array $rules, array $messages = [], array $customAttributes = [])
    {
        /** @var Factory $factory */
        $factory = app('validator');
        return $factory->make($data, $rules, $messages, $customAttributes);
    }

    /**
     * Validate or throw exception.
     *
     * @param array $attributes
     * @param array $rules
     *
     * @throws BadRequestHttpException
     */
    protected function validateOrFail(array $attributes, array $rules)
    {
        $validator = $this->getValidator($attributes, $rules);
        if ($validator->fails()) {
            throw new BadRequestHttpException();
        }
    }
}
