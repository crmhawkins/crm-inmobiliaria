<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocInmueble extends Model
{
    use HasFactory;

    protected $table = "docs_inmueble";

    protected $fillable = [
        "inmueble_id",
        "rutas",
    ];
}
