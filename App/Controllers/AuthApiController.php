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
        // parse input JSON
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
        throw new HTTPException(501,"Not Implemented");
    }

    /**
     * Action returns the username, if user is logged in or 401 status code, if not
     * No input params. Returns
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