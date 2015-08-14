<?php namespace App\Http\Controllers\Demo;

use \Auth;
use \Validator;
use \App\Models\User;
use \App\Jwt\UserJwtCodecInterface;
use \Symfony\Component\HttpFoundation\Response;
use \App\Http\Controllers\JsonApi\JsonApiController;
use \Illuminate\Contracts\Validation\ValidationException;

/**
 * @package Neomerx\LimoncelloShot
 */
class UsersController extends JsonApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $this->checkParametersEmpty();

        return $this->getResponse(User::all());
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
        $rules     = [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:6|max:255',
        ];
        $validator = Validator::make($attributes, $rules);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $author = new User($attributes);
        $author->save();

        return $this->getCreatedResponse($author);
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

        return $this->getResponse(User::findOrFail($id));
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
        $attributes = array_filter($attributes, function ($value) {
            return $value !== null;
        });

        /** @var \Illuminate\Validation\Validator $validator */
        $rules     = [
            'name'     => 'sometimes|required|string|max:255',
            'email'    => 'sometimes|required|email|max:255|unique:users',
            'password' => 'sometimes|required|string|min:6|max:255',
        ];
        $validator = Validator::make($attributes, $rules);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $author = User::findOrFail($id);
        $author->fill($attributes);
        $author->save();

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

        $author = User::findOrFail($id);
        $author->delete();

        return $this->getCodeResponse(Response::HTTP_NO_CONTENT);
    }

    /**
     * Get JWT for signed in user.
     *
     * @return string
     */
    public function getSignedInUserJwt()
    {
        $this->checkParametersEmpty();

        $currentUser = Auth::user();
        /** @var UserJwtCodecInterface $userCodec */
        $userCodec   = app(UserJwtCodecInterface::class);

        return $userCodec->encode($currentUser);
    }
}
