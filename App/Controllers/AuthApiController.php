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
        $jsonData = $this->app->getRequest()->getRawBodyJSON();
        if (
            is_object($jsonData) // an object is expected
            && property_exists($jsonData, 'login') && property_exists($jsonData, 'password') // check if object has login and password attributes
            && $this->app->getAuth()->login($jsonData->login, $jsonData->password) // now we try login to check if send values are ok
        ) {

            // try to get login record, if user was already authenticated
            $logged = Login::getOne($jsonData->login);

            // if not, create a new record
            if (empty($logged)) {
                $newLogin = new Login();
                $newLogin->setLogin($jsonData->login);
                $newLogin->setLastAction(new \DateTime());
                $newLogin->save();
            } else {
                // if yes, just update the last action time
                $logged->setLastAction(new \DateTime());
                $logged->save();
            }
            // there is no data to be sent to the client
            return new EmptyResponse();
        } else {
            // if validation fails, throw an exception
            throw new HTTPException(400, 'Bad credentials.');
        }
    }

    /**
     * Just only logout user id logged
     * @return EmptyResponse Always returns EmptyResponse
     * @throws \Exception if there is DB problem
     */
    public function logout(): Response
    {
        // check, if the user is logged in
        if ($this->app->getAuth()->isLogged()) {
            // if he is logged, we need his login record from DB
            $logged = Login::getOne($this->app->getAuth()->getLoggedUserName());

            // if there is record in DB, delete it
            if (!empty($logged)) {
                $logged->delete();
            }
            // logout
            $this->app->getAuth()->logout();
        }
        // there is no data to be sent to the client
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
        // status is available only for logged users
        if ($this->app->getAuth()->isLogged()) {
            // as a result send the current logged username
            return $this->json([
                'login' => $this->app->getAuth()->getLoggedUserName()
            ]);
        }
        // send status code 401, if user is not logged in
        throw new HTTPException(401);
    }

    /**
     * Returns array of active users
     * @return \App\Core\Responses\JsonResponse
     * @throws HTTPException 401 Unauthorized -  if user is not logged in
     */
    public function activeUsers() {
        // list of active users is available only for the logged user
        if ($this->app->getAuth()->isLogged()) {
            // return the list
            return $this->json(Login::getAllActive());
        }
        // send status code 401, if user is not logged in
        throw new HTTPException(401);
    }
}