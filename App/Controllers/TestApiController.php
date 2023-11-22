<?php

namespace App\Controllers;

use App\Core\AControllerBase;
use App\Core\Responses\EmptyResponse;
use App\Core\Responses\Response;
use App\Models\Login;


/**
 * This controller is used only as helper for HTTP tests
 */
class TestApiController extends AControllerBase
{

    public function index(): Response
    {
        return new EmptyResponse();
    }


    /**
     * This action shifts datetime of last activity do the set count of minutes (can be negative)
     * @return EmptyResponse
     * @throws \Exception
     */
    public function shiftActiveTimes(){

        $minutes = $this->request()->getValue('minutes');

        $logins = Login::getAll();

        foreach ($logins as $login) {
            $login->setLastAction( $login->getLastAction()->modify($minutes . " minutes"));
            $login->save();
        }

        return new EmptyResponse();
    }

}