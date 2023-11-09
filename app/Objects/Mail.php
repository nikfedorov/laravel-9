<?php

namespace App\Objects;

use Illuminate\Database\Eloquent\Factories\HasFactory;

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
}
