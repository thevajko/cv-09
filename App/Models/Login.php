<?php

namespace App\Models;
use App\Core\HTTPException;
use App\Core\Model;
use DateTime;

class Login extends Model
{
    protected string $login;
    protected string $last_action;

    public static function getPkColumnName(): string
    {
        // this method must be overridden, because we using login attribute as PK
        return 'login';
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    public function getLastAction(): DateTime
    {
        // internally is last_action presented as string, because of DB
        return new DateTime($this->last_action);
    }

    public function setLastAction(DateTime $last_action): void
    {
        // converting to string presentation of timedate, so ORM can store data to DB
        $this->last_action = $last_action->format( 'Y-m-d H:i:s');
    }

    public static function isActive(string $login){
        throw new HTTPException(501,"Not Implemented");
    }

    public static function getAllActive(){
        throw new HTTPException(501,"Not Implemented");
    }

}