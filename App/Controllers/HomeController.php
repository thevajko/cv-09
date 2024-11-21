<?php

namespace App\Controllers;

use App\Core\AControllerBase;
use App\Core\Responses\Response;
use App\Models\Message;

/**
 * Class HomeController
 * Example class of a controller
 * @package App\Controllers
 */
class HomeController extends AControllerBase
{
    /**
     * Authorize controller actions
     * @param $action
     * @return bool
     */
    public function authorize($action)
    {
        return true;
    }

    /**
     * Example of an action (authorization needed)
     * @return \App\Core\Responses\Response|\App\Core\Responses\ViewResponse
     */
    public function index(): Response
    {
        return $this->html();
    }

    /**
     * Example of an action accessible without authorization
     * @return \App\Core\Responses\ViewResponse
     */
    public function contact(): Response
    {
        return $this->html();
    }

    public function sendJson(): Response
    {
        return $this->html();
    }

    public function showJson()
    {
        $message = new Message();
        $message->setAuthor('patrik');
        $message->setRecipient('peter');
        $message->setMessage('Ahoj');

        return $this->json($message);
    }

    public function receiveJson()
    {
        $data = $this->request()->getRawBodyJSON();
        return $this->json($data);
    }
}
