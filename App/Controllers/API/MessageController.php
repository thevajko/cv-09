<?php

namespace App\Controllers\API;

use App\Models\User;
use App\Models\Message;
use Framework\Core\BaseController;
use Framework\Http\HttpException;
use Framework\Http\Request;
use Framework\Http\Responses\EmptyResponse;
use Framework\Http\Responses\Response;
use JsonException;


class MessageController extends BaseController
{

    /**
     * All actions in this controller needs user to be authenticated
     * @param Request $request
     * @param string $action
     * @return true only if is user authenticated
     * @throws HttpException
     */
    public function authorize(Request $request, string $action) : bool
    {
        // all actions are available only for logged users
        if ($this->user->isLoggedIn()) {
            return true;
        }
        throw new HTTPException(401);
    }

    /**
     * Always returns 501 Not Implemented, API do not need index action
     * @throws HTTPException 501 Not Implemented
     */
    public function index(Request $request): Response
    {
        throw new HTTPException(501,"Not Implemented");
    }

    /**
     * This action receives a message from client and saves it to the DB
     *
     * Input JSON need to be as:
     * {
     * "recipient" : "<null|active user login>",
     * "message": "<message>"
     * }
     *
     * @param Request $request
     * @return EmptyResponse if message is send successfully
     * @throws HTTPException|JsonException 400 Bad Request if input has bad format or private message is
     * @throws \Exception
     */
    public function receiveMessage(Request $request) : Response
    {
        // parse input JSON
        $jsonData = $request->json();

        if (
            is_object($jsonData) // an object is expected
            && property_exists($jsonData, 'recipient') &&  property_exists($jsonData, 'message') // check if object has recipient and message attributes
            && !empty($jsonData->message) // message attribute must not be empty
        ) {
            // create a new message
            $message = new Message();
            // set the logged user as the author of the message
            $message->setAuthor($this->user->getName());
            // if there is a recipient set, the message is private
            if (!empty($jsonData->recipient)) {
                // private message can be sent only if recipient is active
                if (!User::isActive($jsonData->recipient)) {
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
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function getAllMessages(Request $request): Response
    {
        // get lastId parameter, if exists
        $lastId = $request->value("lastId") ?? 0;


        // get all messages, where user is the recipient or the author
        $messages = Message::getAll("id >= ? AND (recipient is NULL OR recipient = ? OR author = ?)", [
            $lastId,
            $this->user->getName(),
            $this->user->getName()
        ]);

        // update datetime of last action for the author
        $author = User::getOne($this->user->getName());
        $author->setLastAction(new \DateTime());
        $author->save();

        return $this->json($messages); // send messages to the client
    }

}