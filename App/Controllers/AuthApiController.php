<?php

namespace App\Controllers;

use App\Core\AControllerBase;
use App\Core\HTTPException;
use App\Core\Responses\EmptyResponse;
use App\Core\Responses\Response;
use App\Models\Login;

/**
 * Contains API for user actions
 */
class AuthApiController extends AControllerBase {

    /**
     * Always returns 501 Not Implemented, API do not need index action
     * @throws HTTPException 501 Not Implemented
     */
    public function index(): Response
    {
        throw new HTTPException(501,"Not Implemented");
    }

    /**
     * Expect JSON input as:
     *
     * {
     * "login"    : "<login>",
     * "password" : "<password>"
     * }
     *
     * @return EmptyResponse
     * @throws HTTPException 400 Bad credencial if input rules fails
     * @throws \JsonException
     */
    public function login(): Response
    {
        throw new HTTPException(501,"Not Implemented");
    }

    /**
     * Just only logout user id logged
     * @return EmptyResponse Always returns EmptyResponse
     * @throws \Exception if there is DB problem
     */
    public function logout(): Response
    {
        throw new HTTPException(501,"Not Implemented");
    }


    /**
     * No input params. returns
     * {
     *    "login"    : "<logged user login>",
     * }
     * or 401 if user is not logged in
     * @return \App\Core\Responses\JsonResponse
     * @throws HTTPException 401 Unauthorized -  if user is not logged in
     */
    public function status() {
        throw new HTTPException(501,"Not Implemented");
    }

    /**
     * Returns array of active users
     * @return \App\Core\Responses\JsonResponse
     * @throws HTTPException 401 Unauthorized -  if user is not logged in
     */
    public function activeUsers() {
        throw new HTTPException(501,"Not Implemented");
    }
}