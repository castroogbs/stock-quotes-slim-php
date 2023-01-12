<?php

declare(strict_types=1);

namespace App\Controller;

use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Service\AuthService;
use App\Security\JWTUtil;

#[OA\Info(title: "PHP Challenge", version: "0.1")]
class AuthController
{

    /**
     * @var AuthService
     */
    protected AuthService $service;

    /**
     * @var JWTUtil $jwtUtil
     */
    protected JWTUtil $jwtUtil;

    /**
     * @param AuthService $service
     * @param JWTUtil $jwtUtil
     */
    public function __construct(AuthService $service, JWTUtil $jwtUtil) {
        $this->service = $service;
        $this->jwtUtil = $jwtUtil;
    }

 /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    #[OA\Post(path: '/login', tags: ["Auth"])]
    #[OA\Response(response: '200', description: 'Logged successfully.')]
    #[OA\Response(response: '404', description: 'User not found.')]
    #[OA\Response(response: '400', description: 'Fields cannot be empty.')]
    public function login(Request $request, Response $response, array $args): Response {
        [ 'user' => $user, 'password' => $password ] = $request->getParsedBody();

        if (empty($user) || empty($password)) {
            $errorResponse = $response->withStatus(400)
                                    ->withHeader('Content-Type', 'application/json');;
            $errorResponse->getBody()->write( json_encode([ "error" => "Fields cannot be empty."] ) );
            return $errorResponse;
        }

        $user = $this->service->validateUser($user, md5($password));
        
        if(!$user) {
            $errorResponse = $response->withStatus(404)
                                    ->withHeader('Content-Type', 'application/json');;
            $errorResponse->getBody()->write( json_encode([ "error" => "User not found."] ) );
            return $errorResponse;
        }

        $userId = $user->getId();
        $jwtToken = $this->jwtUtil->generateToken($userId);

        $payload = json_encode([
            'id'=>$userId,
            'name'=>$user->getName()
        ]);
        $response->getBody()->write($payload);
        return $response
                ->withHeader('Content-Type', 'application/json')
                ->withAddedHeader('Authorization', 'Bearer ' . $jwtToken);
        
    }

}

?>