<?php

namespace App\Objects;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;

class Mail
{
    use HasFactory;

    /**
     * Construct an object.
     */
    public function __construct($attributes = [])
    {
        // assign object attributes
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Create a new Collection instance.
     */
    public function newCollection(array $objects = []): Collection
    {
        return new Collection($objects);
    }
}
