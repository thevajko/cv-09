<?php

namespace App\Controllers\API;

use App\Models\User;
use Framework\Core\BaseController;
use Framework\Http\HttpException;
use Framework\Http\Request;
use Framework\Http\Responses\EmptyResponse;
use Framework\Http\Responses\JsonResponse;
use Framework\Http\Responses\Response;
use JsonException;

/**
 * Contains API for user actions
 */
class AuthController extends BaseController {

    /**
     * Always returns 501 Not Implemented, API do not need index action
     * @throws HTTPException 501 Not Implemented
     */
    public function index(Request $request): Response
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
     * @throws JsonException
     * @throws \Exception
     */
    public function login(Request $request): Response
    {
        // parse input JSON
        $jsonData = $request->json();
        if (
            is_object($jsonData) // an object is expected
            && property_exists($jsonData, 'login') && property_exists($jsonData, 'password') // check if object has login and password attributes
            && $this->app->getAuthenticator()->login($jsonData->login, $jsonData->password) // now we try login to check if send values are ok
        ) {

            // try to get user record, if user was already authenticated
            $logged = User::getOne($jsonData->login);

            // if not, create a new record
            if (empty($logged)) {
                $newLogin = new User($jsonData->login, new \DateTime());
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
        if ($this->user->isLoggedIn()) {
            $user = User::getOne($this->user->getName());
            $user->setLastAction((new \DateTime())->modify('-30 seconds')); // set last action time to 5 minutes ago
            $user->save();
            $this->app->getAuthenticator()->logout();
        }
        return new EmptyResponse();
    }

    /**
     * Action returns the username, if user is logged in or 401 status code, if not
     * No input params. Returns
     * {
     *    "login"    : "<logged user login>",
     * }
     * or 401 if user is not logged in
     *
     * @return JsonResponse
     * @throws HTTPException 401 Unauthorized -  if user is not logged in
     */
    public function status() : JsonResponse {
        if ($this->user->isLoggedIn()) {
            return new JsonResponse(['login' => $this->user->getName()]);
        }
        throw new HTTPException(401,"Unauthorized");
    }

    /**
     * Returns array of active users
     * @return JsonResponse
     * @throws HTTPException 401 Unauthorized -  if user is not logged in
     */
    public function activeUsers() : JsonResponse {
        if ($this->user->isLoggedIn()) {
            $activeUsers = User::getAllActive();
            return new JsonResponse($activeUsers);
        }
        throw new HTTPException(401,"Unauthorized");
    }
}