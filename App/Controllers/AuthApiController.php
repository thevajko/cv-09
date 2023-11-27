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
class AuthApiController extends AControllerBase
{

    /**
     * Always returns 501 Not Implemented, API do not need index action
     * @throws HTTPException 501 Not Implemented
     */
    public function index(): Response
    {
        throw new HTTPException(501, "Not Implemented");
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
        try {
            $json = $this->request()->getRawBodyJSON();
            if (is_object($json)
                && !empty($json->login)
                && !empty($json->password)
                && $this->app->getAuth()->login($json->login, $json->password)) {

                $dbLogin = Login::getOne($json->login);
                if ($dbLogin == null) {
                    $dbLogin = new Login();
                    $dbLogin->setLogin($json->login);
                }
                $dbLogin->setLastAction(new \DateTime());
                $dbLogin->save();

                return new EmptyResponse();
            }
            throw new HTTPException(400);
        } catch (\JsonException $exception) {
            throw new HTTPException(400, h: $exception);
        }
    }

    /**
     * Just only logout user id logged
     * @return EmptyResponse Always returns EmptyResponse
     * @throws \Exception if there is DB problem
     */
    public function logout(): Response
    {
        if ($this->app->getAuth()->isLogged()) {
            $dbLogin = Login::getOne($this->app->getAuth()->getLoggedUserId());
            $dbLogin?->delete();
            $this->app->getAuth()->logout();
        }
        return new EmptyResponse();
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
    public function status()
    {
        if ($this->app->getAuth()->isLogged()) {
            $login = $this->app->getAuth()->getLoggedUserId();
            $response = new \stdClass();
            $response->login = $login;

            return $this->json($response);
        }
        throw new HTTPException(401);
    }

    /**
     * Returns array of active users
     * @return \App\Core\Responses\JsonResponse
     * @throws HTTPException 401 Unauthorized -  if user is not logged in
     */
    public function activeUsers()
    {
        throw new HTTPException(501, "Not Implemented");
    }
}