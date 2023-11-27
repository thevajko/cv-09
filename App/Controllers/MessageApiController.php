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
        if (!$this->app->getAuth()->isLogged()) {
            throw new HTTPException(401);
        }
        return true;
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
    public function sendMessage() {
        $data = $this->request()->getRawBodyJSON();
        if (!is_object($data) || !property_exists($data, "recipient") || empty($data->message)) {
            throw new HTTPException(400);
        }
        if ($data->recipient != null && !Login::isActive($data->recipient)) {
            throw new HTTPException(400);
        }

        $message = new Message();
        $message->setMessage($data->message);
        $message->setAuthor($this->app->getAuth()->getLoggedUserId());
        $message->setCreated(new \DateTime());
        $message->setRecipient($data->recipient);
        $message->save();
        return new EmptyResponse();
    }

    /**
     * Returns array of messages that can logged user receive.
     *
     * @return \App\Core\Responses\JsonResponse
     * @throws \Exception
     */
    public function getMessages()
    {
        $lastId = $this->request()->getValue("lastId") ?? 0;

        $data = Message::getAll("id >= ? AND (recipient is NULL OR recipient = ? OR author = ?)", [
            $lastId,
            $this->app->getAuth()->getLoggedUserId(),
            $this->app->getAuth()->getLoggedUserId()
        ]);

        return $this->json($data);
    }
}
