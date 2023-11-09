<?php

namespace App\Services;

use App\Utilities\Contracts\RedisHelperInterface;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class RedisHelper implements RedisHelperInterface
{
    /**
     * Prefix for stored Mails.
     *
     * @var string
     */
    protected $prefix;

    /**
     * Instantiate Service.
     */
    public function __construct()
    {
        $this->prefix = 'mail::';
    }

    /**
     * Store the id of a message along with a message subject in Redis.
     *
     * @param  mixed  $id
     */
    public function storeRecentMessage(string $messageSubject, string $toEmailAddress): void
    {
        $id = $this->prefix.$toEmailAddress.':'.now()->format('Ymd.His');
        Redis::set($id, json_encode([
            'email' => $toEmailAddress,
            'subject' => $messageSubject,
        ]));
    }

    /**
     * Get a list of sent emails.
     */
    public function getList()
    {
        return Redis::keys($this->prefix.'*');
    }

    /**
     * Get a single email by key.
     */
    public function get($id)
    {
        $id = Str::replace(config('database.redis.options.prefix'), '', $id);

        return json_decode(Redis::get($id));
    }

    /**
     * Delete all emails.
     */
    public function deleteAll()
    {
        // get a list of all keys
        $keys = collect(Redis::keys($this->prefix.'*'))

            // remove prefixes set by laravel
            ->map(function ($key) {
                return Str::replace(config('database.redis.options.prefix'), '', $key);
            });

        // delete all keys
        Redis::del($keys->toArray());
    }
}
