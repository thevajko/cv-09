<?php

namespace App\Controllers;

use App\Core\AControllerBase;
use App\Core\HTTPException;
use App\Core\Responses\EmptyResponse;
use App\Core\Responses\Response;
use App\Models\Login;
use App\Models\Message;


class MessageApiController extends AControllerBase
{


    /**
     * All actions in this controller needs user to be authenticated
     * @param $action
     * @return true only if is user authenticated
     * @throws HTTPException 401 Unauthorized if user is not logged in
     */
    public function authorize($action)
    {
        throw new HTTPException(501,"Not Implemented");
    }
    /**
     * Always returns 501 Not Implemented, API do not need index action
     * @throws HTTPException 501 Not Implemented
     */
    public function index(): Response
    {
        throw new HTTPException(501,"Not Implemented");
    }

    /**
     * Input JSON need to be as:
     * {
     * "recipient" : "<null|active user login>",
     * "message": "<message>"
     * }
     *
     * @return EmptyResponse if message is send successfully
     * @throws HTTPException 400 Bad Request if input has bad format or private message is sent to unactive user
     * @throws \JsonException
     */
    public function sendMessage(){
        throw new HTTPException(501,"Not Implemented");
    }

    /**
     * Returns array of messages that can logged user receive.
     *
     * @return \App\Core\Responses\JsonResponse
     * @throws \Exception
     */
    public function getMessages()
    {
        throw new HTTPException(501,"Not Implemented");
    }
}
