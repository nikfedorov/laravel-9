<?php

namespace App\Utilities\Contracts;

interface RedisHelperInterface
{
    /**
     * Store the id of a message along with a message subject in Redis.
     *
     * @param  mixed  $id
     */
    public function storeRecentMessage(string $messageSubject, string $toEmailAddress): void;
}
