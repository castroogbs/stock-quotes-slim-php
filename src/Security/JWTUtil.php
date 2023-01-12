<?php

declare(strict_types=1);

namespace App\Security;

use DateTime;
use PsrJwt\Factory\Jwt;
use ReallySimpleJWT\Tokens;


class JWTUtil {

    /**
     * @var string $jwtSecret
     */
    private string $jwtSecret;

    /**
     * @var int $jwtExpiration
     */
    private int $jwtExpiration;

    public function __construct() {
        $this->jwtSecret = $_ENV['JWT_SECRET'];
        $this->jwtExpiration = intval($_ENV['JWT_EXPIR']);
    }

    /**
     * @param int $userId
     * @return string 
     */
    public function generateToken(int $userId): string {
        $factory = new Jwt();
        $builder = $factory->builder();

        $token = $builder->setSecret($this->jwtSecret)
            ->setPayloadClaim('uid', $userId)
            ->setExpiration( $this->getExpirationTimestamp() )
            ->build();

        return $token->getToken();
    }

    /**
     * @param string $token
     * @return string 
     */
    public function getTokenPayload(string $token): array {
        return (new Tokens)->getPayload($token, $this->jwtSecret);
    }

    /**
     * @return int
     */
    private function getExpirationTimestamp(): int {
        $time = intval($this->jwtExpiration); // seconds
        $date = new DateTime('+'.$time.' seconds');
        return $date->getTimestamp();
    }
}

?>