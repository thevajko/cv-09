<?php

namespace App\Models;


use DateTime;
use Framework\Core\Model;

class Message extends Model
{
    protected null|int $id = null;
    protected string $author;
    protected ?string $recipient;
    protected ?string $created;
    protected string $message;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function getCreated(): DateTime
    {
        return new DateTime($this->created);
    }

    public function setCreated(DateTime $created): void
    {
        $this->created = $created->format('Y-m-d H:i:s');
    }

    public function getRecipient(): string
    {
        return $this->recipient;
    }

    public function setRecipient(?string $recipient): void
    {
        $this->recipient = $recipient;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
