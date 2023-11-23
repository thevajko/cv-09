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
        if ($this->app->getAuth()->isLogged()) {
            return true;
        }
        throw new HTTPException(401, 'Not authorized');
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
    public function sendMessage()
    {
        $data = $this->request()->getRawBodyJSON();

        if (is_object($data)
            && property_exists($data, 'message') && property_exists($data, 'recipient')
            && !empty($data->message)
        ) {
            $message = new Message();
            $message->setAuthor($this->app->getAuth()->getLoggedUserName());
            $message->setMessage($data->message);
            $message->setCreated(new \DateTime());

            if (!empty($data->recipient)) {
                if (!Login::isActive($data->recipient)) {
                    throw new HTTPException(400, 'Recipient is not active');
                }
            }
            $message->setRecipient($data->recipient);
            $message->save();
            return new EmptyResponse();
        } else {
            throw new HTTPException(400, 'bad request');
        }
    }

    /**
     * Returns array of messages that can logged user receive.
     *
     * @return \App\Core\Responses\JsonResponse
     * @throws \Exception
     */
    public function getMessages()
    {
        $data = $this->request()->getValue("lastId");

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
