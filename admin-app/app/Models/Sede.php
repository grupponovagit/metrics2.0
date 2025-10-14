<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    use HasFactory;

    protected $table = 'sedi';

    protected $fillable = [
        'id_sede',
        'nome_sede',
        'comune',
    ];

    /**
     * Relazioni
     */
    public function operatori()
    {
        return $this->hasMany(Operatore::class, 'id_sede', 'id_sede');
    }

    public function oreLavorate()
    {
        return $this->hasMany(OreLavorate::class, 'id_sede', 'id_sede');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'id_sede', 'id_sede');
    }

    public function vendite()
    {
        return $this->hasMany(Vendita::class, 'id_sede', 'id_sede');
    }
}

