<?php namespace App\Jwt;

use \App\Models\User;

interface UserJwtCodecInterface
{
    /**
     * Encode user to JWT.
     *
     * @param User $user
     *
     * @return string
     */
    public function encode(User $user);

    /**
     * Decode user from JWT.
     *
     * @param string $jwt
     *
     * @return User|null
     */
    public function decode($jwt);
}
