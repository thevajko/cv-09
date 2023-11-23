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
        $data = $this->request()->getRawBodyJSON();

        if (is_object($data)
        && property_exists($data, 'login') && property_exists($data, 'password')
        && $this->app->getAuth()->login($data->login, $data->password)
        ) {
            $logged = Login::getOne($data->login);
            if (empty($logged)) {
                $login = new Login();
                $login->setLogin($data->login);
                $login->setLastAction(new \DateTime());
                $login->save();
            } else {
                $logged->setLastAction(new \DateTime());
                $logged->save();
            }
            return new EmptyResponse();
        } else {
            throw new HTTPException('400', 'Bad request');
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
            $login = Login::getOne($this->app->getAuth()->getLoggedUserName());
            if (!empty($login)) {
                $login->delete();
            }
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
    public function status() {
        if ($this->app->getAuth()->isLogged()) {
            return $this->json((object)[
                'login' => $this->app->getAuth()->getLoggedUserName()
            ]);
        } else {
            throw new HTTPException('401', 'Not authorized');
        }
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