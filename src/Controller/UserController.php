<?php

declare(strict_types=1);

namespace App\Controller;

use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Service\UserService;
use App\Model\User;
use App\Security\JWTUtil;

class UserController
{

     /**
     * @var UserService $service
     */
    protected UserService $service;

    /**
     * @var JWTUtil $jwtUtil
     */
    protected JWTUtil $jwtUtil;

    /**
     * @param UserService $service
     * @param JWTUtil $jwtUtil
     */
    public function __construct(UserService $service, JWTUtil $jwtUtil) {
        $this->service = $service;
        $this->jwtUtil = $jwtUtil;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    #[OA\Post(path: '/users', tags: ["User"])]
    #[OA\Response(response: '201', description: 'User has been created successfully.')]
    #[OA\Response(response: '400', description: 'Invalid data provided or user already exists.')]
    public function create(Request $request, Response $response, array $args): Response {
        [ 'name' => $name, 'email' => $email, 'password' => $password ] = $request->getParsedBody();
        
        $isEmailValid = $this->service->validateEmail($email);

        if (!$isEmailValid || empty($name) || empty($password)) {
            $errorResponse = $response->withStatus(400)
                                    ->withHeader('Content-Type', 'application/json');;
            $errorResponse->getBody()->write( json_encode([ "error" => "Invalid data provided."] ) );
            return $errorResponse;
        }

        $userExists = $this->service->userAlreadyExists($email);
        if($userExists) {
            $errorResponse = $response->withStatus(400)
                                    ->withHeader('Content-Type', 'application/json');;
            $errorResponse->getBody()->write( json_encode([ "error" => "User already exists."] ) );
            return $errorResponse;
        }
        
        $newUser = (new User())
                    ->setEmail($email)
                    ->setName($name)
                    ->setPassword(md5($password));
        
        $user = $this->service->save($newUser);
        $userId = $user->getId();

        $jwtToken = $this->jwtUtil->generateToken( $userId );

        $payload = json_encode(['id'=>$userId]);
        $response->getBody()->write($payload);
        return $response
                ->withHeader('Content-Type', 'application/json')
                ->withAddedHeader('Authorization', 'Bearer ' . $jwtToken)
                ->withStatus(201);
    }
}

?>