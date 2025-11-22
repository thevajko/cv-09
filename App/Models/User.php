<?php

namespace App\Models;

use DateTime;
use Framework\Core\IIdentity;
use Framework\Core\Model;

class User extends Model implements IIdentity
{
    protected ?string $login;
    protected ?string $lastAction;

    public function __construct(?string $username = null, ?DateTime $lastAction = null)
    {
        $this->login = $username;
        if ($lastAction !== null) {
            $this->lastAction = $lastAction->format('Y-m-d H:i:s');
        }
    }

    /**
     * This method overrides the Model method, because we use the login attribute as a primary key
     * @return string
     */
    public static function getPkColumnName(): string
    {
        return 'login';
    }

    /** region Getters and setters */
    public function getLogin(): string
    {
        return $this->login;
    }

    public function getName(): string
    {
        return $this->getLogin();
    }


    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    public function getLastAction(): DateTime
    {
        // internally is last_action presented as string, because of DB
        return new DateTime($this->lastAction);
    }

    public function setLastAction(DateTime $lastAction): void
    {
        $this->lastAction = $lastAction->format('Y-m-d H:i:s');
    }

    /** end region */

    /**
     * Returns true, if user is active (logged in)
     * @param string $login
     * @return bool
     * @throws \Exception
     */
    public static function isActive(string $login): bool
    {
        return count(User::getAll("last_action > DATE_ADD(NOW(), INTERVAL -30 SECOND ) AND login like ?", [$login])) > 0;
    }

    /**
     * Return all active users
     * @return array
     * @throws \Exception
     */
    public static function getAllActive(): array
    {
        return User::getAll("last_action > DATE_ADD(NOW(), INTERVAL -30 SECOND )", []);
    }

}