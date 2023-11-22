<?php

namespace App\Controllers;

use App\Core\AControllerBase;
use App\Core\HTTPException;
use App\Core\Responses\EmptyResponse;
use App\Core\Responses\JsonResponse;
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
    public function authorize($action): bool
    {
        // all actions are available only for logged users
        if ($this->app->getAuth()->isLogged()) {
            return true;
        }
        throw new HTTPException(401);
    }

    /**
     * Always returns 501 Not Implemented, API do not need index action
     * @throws HTTPException 501 Not Implemented
     */
    public function index(): Response
    {
        throw new HTTPException(501, "Not Implemented");
    }

    /**
     * This action receives a message from client and saves it to the DB
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
    public function sendMessage(): Response
    {
        // parse input JSON
        $jsonData = $this->app->getRequest()->getRawBodyJSON();

        if (
            is_object($jsonData) // an object is expected
            && property_exists($jsonData, 'recipient') &&  property_exists($jsonData, 'message') // check if object has recipient and message attributes
            && !empty($jsonData->message) // message attribute must not be empty
        ) {
            // create a new message
            $message = new Message();
            // set the logged user as the author of the message
            $message->setAuthor($this->app->getAuth()->getLoggedUserName());
            // if there is a recipient set, the message is private
            if (!empty(trim($jsonData->recipient))) {
                // private message can be sent only if recipient is active
                if (!Login::isActive($jsonData->recipient)) {
                    // throw exception if recipient is inactive
                    throw new HTTPException(400, 'The recipient is not available');
                }
                // set the recipient
                $message->setRecipient($jsonData->recipient);
            }
            // set the rest of the message and save it
            $message->setCreated(new \DateTime());
            $message->setMessage($jsonData->message);
            $message->save();

            // there is no data to be sent to the client
            return new EmptyResponse();
        }
        // throw out exception if validation fail
        throw new HTTPException(400, 'Bad message structure');
    }

    /**
     * The action returns an array of messages that can logged user receive.
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function getMessages(): Response
    {
        // get lastId parameter, if exists
        $lastId = $this->request()->getValue("lastId");

        $additionalWhere = "";
        $additionalParams = [];

        // add the WHERE clause and parameters, if lastId is set
        if (!empty($lastId)) {
            $additionalWhere = " id < ? AND";
            $additionalParams = [$lastId];
        }

        // get all messages, where user is the recipient or the author
        $messages = Message::getAll(
            $additionalWhere . " ( recipient IS NULL OR recipient = ? OR author = ?)",
            // merge parameter to a single array
            array_merge($additionalParams, [$this->app->getAuth()->getLoggedUserName(), $this->app->getAuth()->getLoggedUserName()])
        );

        // update datetime of last action for the author
        $author = Login::getOne($this->app->getAuth()->getLoggedUserName());
        $author->setLastAction(new \DateTime());
        $author->save();

        return $this->json($messages); // send messages to the client
    }
}
