<?php namespace App\Jwt;

use \App\Models\User;
use \Firebase\JWT\JWT;

final class UserJwtCodec implements UserJwtCodecInterface
{
    /** Expiration period for JWT in seconds - app specific (can be changed) */
    const EXPIRATION_PERIOD_IN_SECONDS = 600;

    /** JWT signing algorithm - app specific (can be changed) */
    const SIGNING_ALGORITHM = 'HS512';

    /** JWT claim name for user ID - app specific (can be changed) */
    const CLAIM_USER_ID = 'user_id';

    /** JWT claim name for 'issued at' - standard (do not change) */
    const CLAIM_ISSUED_AT = 'iat';

    /** JWT claim name for 'expiration time' - standard (do not change) */
    const CLAIM_EXPIRATION_TIME = 'exp';

    /**
     * @inheritdoc
     */
    public function encode(User $user)
    {
        $issuedAt = time();
        $token    = [
            self::CLAIM_ISSUED_AT       => $issuedAt,
            self::CLAIM_EXPIRATION_TIME => $issuedAt + self::EXPIRATION_PERIOD_IN_SECONDS,
            self::CLAIM_USER_ID         => $user->getAuthIdentifier(),
        ];
        $jwt = JWT::encode($token, $this->getSigningKey(), self::SIGNING_ALGORITHM);

        return $jwt;
    }

    /**
     * @inheritdoc
     */
    public function decode($jwt)
    {
        $payload = JWT::decode($jwt, $this->getSigningKey(), [self::SIGNING_ALGORITHM]);
        $userId  = isset($payload->{self::CLAIM_USER_ID}) === true ? $payload->{self::CLAIM_USER_ID} : null;
        $user    = $userId !== null ? User::find($userId) : null;

        return $user;
    }

    /**
     * @return string
     */
    private function getSigningKey()
    {
        /** @var string $key */
        $key = env('APP_KEY');
        assert('$key !== null', 'Encryption key must be configured.');

        return $key;
    }
}
