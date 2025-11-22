<?php

namespace App\Controllers\API;

use App\Models\User;
use Framework\Core\BaseController;
use Framework\Http\HttpException;
use Framework\Http\Request;
use Framework\Http\Responses\EmptyResponse;
use Framework\Http\Responses\Response;

/**
 * This controller is used only as helper for HTTP tests
 */
class TestController extends BaseController
{

    public function index(Request $request): Response
    {
        throw new HTTPException(501, "Not Implemented");
    }


    /**
     * This action shifts datetime of last activity to the defined number of
     * minutes (can be negative)
     *
     * @return EmptyResponse
     * @throws \Exception
     */
    public function shiftActiveTimes(Request $request): Response
    {
        $minutes = $request->value('minutes');

        $users = User::getAll();

        foreach ($users as $user) {
            $user->setLastAction($user->getLastAction()->modify($minutes . " minutes"));
            $user->save();
        }
        return new EmptyResponse();
    }
}
