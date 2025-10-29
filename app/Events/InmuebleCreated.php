<?php

namespace App\Events;

use App\Models\Inmuebles;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InmuebleCreated
{
    use Dispatchable, SerializesModels;

    public $inmueble;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Inmuebles $inmueble)
    {
        $this->inmueble = $inmueble;
    }
}

