<?php

require_once __DIR__ . '/../core/Model.php';

class Role extends Model {
    protected $table  = 'cat_rol';
    protected $primaryKey = 'id_rol';

    protected $fillable = [
        'rol_description',
        'id_usuario_alta',
        'fecha_alta'
    ];
}