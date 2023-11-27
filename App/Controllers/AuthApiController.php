<?php

namespace App\Controllers;

use App\Core\AControllerBase;
use App\Core\HTTPException;
use App\Core\Responses\EmptyResponse;
use App\Core\Responses\Response;
use App\Models\Login;
use Cassandra\Date;

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
        if ($this->request()->isContentTypeJSON()) {
            $c = $this->request()->getRawBodyJSON();
            if ( is_object($c)
                && property_exists($c, "login")
                &&  property_exists($c, "password")
            ) {
                if ($c->password && $c->login
                    && $this->app->getAuth()->login($c->login , $c->password)) {

                    $l = Login::getOne($c->login);
                    if ($l == null) {
                        $l = new Login();
                        $l->setLogin($c->login);
                    }
                    $l->setLastAction(new \DateTime());
                    $l->save();

                    return new EmptyResponse();
                }
            }
        }
        throw new HTTPException(400);
    }

    /**
     * Just only logout user id logged
     * @return EmptyResponse Always returns EmptyResponse
     * @throws \Exception if there is DB problem
     */
    public function logout(): Response
    {

        if ($this->app->getAuth()->isLogged()) {
            $login = Login::getOne($this->app->getAuth()->getLoggedUserId());
            if ($login) {
                $login->delete();
                $this->app->getAuth()->logout();
            }
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