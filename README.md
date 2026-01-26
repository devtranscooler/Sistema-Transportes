# 🚀 Sistema Transcooler - Framework PHP

Un framework MVC ligero y potente construido específicamente para Transcooler Mexico.

## 📋 Tabla de Contenidos

- [Características](#-características)
- [Estructura de Archivos](#-estructura-de-archivos)
- [Uso Básico](#-uso-básico)
- [Componentes](#-componentes)

---

## ✨ Características

✅ **Patrón MVC** - Código organizado y mantenible  
✅ **ORM Simple** - CRUD automático para modelos  
✅ **Prepared Statements** - 100% protegido contra SQL Injection  
✅ **Sistema de Componentes** - UI reutilizable  
✅ **Router Flexible** - URLs amigables  
✅ **Sistema de Sesiones** - Login/logout integrado  
✅ **Zero Dependencies** - No requiere Composer ni librerías externas

---

## 📁 Estructura de Archivos

```
transcooler/
│
├── config/
│   └── Database.php             # Conexión a BD (Singleton)
│
├── core/
│   ├── Model.php                # Clase base para modelos
│   ├── View.php                 # Sistema de vistas
│   ├── Request.php              # Manejo de peticiones (En construcción)
│   └── Router.php               # Sietema de enrutamiento (En prueba)
│
├── models/
|   ├── menu.php                 # Modelo menu (En desarrollo)
│   └── Usuario.php              # Modelo Usuario
|
├── routes/
│   └── web.php                  # Manejo de rutas del sistema
│
├── views/
│   ├── layouts/
│   │   ├── main.php             # Layout principal
│   │   └── login.php            # Layout de login
│   ├── components/
│   │   └── forms/
│   │       ├── input.php        # Input reutilizable
│   │       ├── button.php       # Botón reutilizable
│   │       └── select.php       # Select reutilizable
│   └── pages/
│       ├── dashboard.php
│       └── usuarios/
│           ├── index.php        # Lista
│           └── form.php         # Formulario
│
├── controllers/
│   └── AuthController.php       # Controlador de autenticación
│
├── public/                       # Carpeta pública (DocumentRoot)
│   ├── index.php                # Punto de entrada
│   ├── .htaccess                # Reescritura de URLs
│   └── assets/
│       ├── css/
│       ├── js/
│       └── img/
│
├── system/
│   ├── autoload.php             # Autoload de clases
│   ├── helpers.php              # Funciones auxiliares
│   └── bd.ini                   # Configuración BD
│
└── storage/
    └── logs/
        └── app.log              # Logs de la aplicación
```

---

## 🎯 Uso Básico

### Crear un Modelo

```php
<?php
// models/Unidad.php

class Unidad extends Model {
    protected $table = 'unidades';
    protected $primaryKey = 'id';
    protected $fillable = [
        'numero_economico',
        'marca',
        'modelo',
        'placas',
        'estatus'
    ];

    // Método personalizado
    public function getMantenimientos() {
        $sql = "SELECT * FROM mantenimientos WHERE id_unidad = ?";
        return $this->db->fetchAll($sql, [$this->id]);
    }
}
```

### Usar el Modelo

```php
// Crear nueva unidad
$unidad = new Unidad();
$unidad->numero_economico = 'TC-001';
$unidad->marca = 'Freightliner';
$unidad->modelo = 'Cascadia';
$unidad->save();

// Buscar por ID
$unidad = Unidad::find(5);
echo $unidad->numero_economico;

// Buscar con condiciones
$activas = Unidad::where('estatus', 'activo');

// Actualizar
$unidad->placas = 'ABC-123';
$unidad->save();

// Eliminar
$unidad->delete();
```

### Crear una Vista

```php
<?php
// views/pages/unidades/index.php

// Usar componentes reutilizables
echo component('card', [
    'title' => 'Unidades Activas',
    'content' => component('table', [
        'headers' => ['#', 'Económico', 'Marca', 'Modelo', 'Estatus'],
        'rows' => array_map(function($u) {
            return [
                $u->id,
                $u->numero_economico,
                $u->marca,
                $u->modelo,
                $u->estatus
            ];
        }, $unidades)
    ])
]);
?>
```

### Crear un Controlador

```php
<?php
// controllers/UnidadController.php

class UnidadController {

    public function index() {
        AuthController::requireAuth();

        $unidades = Unidad::all();

        View::render('pages/unidades/index', [
            'title' => 'Unidades',
            'unidades' => $unidades
        ]);
    }

    public function store() {
        AuthController::requirePermission('unidades.crear');

        $unidad = new Unidad();
        $unidad->numero_economico = $_POST['numero_economico'];
        $unidad->marca = $_POST['marca'];
        // ... más campos

        if ($unidad->save()) {
            flash('success', 'Unidad guardada');
            View::redirect('/unidades');
        }
    }
}
```

### Registrar Rutas

```php
// En web.php

$router->get('/unidades', [UnidadController::class, 'index']);
$router->post('/unidades/guardar', [UnidadController::class, 'store']);
$router->get('/unidades/editar/{id}', [UnidadController::class, 'edit']);
```

---

## 🧩 Componentes

### Input

```php
<?= component('forms/input', [
    'name' => 'email',
    'label' => 'Correo Electrónico',
    'type' => 'email',
    'required' => true,
    'placeholder' => 'tu@email.com'
]) ?>
```

### Botón

```php
<?= component('forms/button', [
    'text' => 'Guardar',
    'type' => 'submit',
    'color' => 'primary',
    'icon' => 'save'
]) ?>
```

### Select

```php
<?= component('forms/select', [
    'name' => 'marca',
    'label' => 'Marca',
    'options' => ['Freightliner' => 'Freightliner', 'Kenworth' => 'Kenworth'],
    'required' => true
]) ?>
```

### Tabla

```php
<?= component('table', [
    'headers' => ['ID', 'Nombre', 'Email'],
    'rows' => $usuarios,
    'actions' => function($row) {
        return '<button>Editar</button>';
    }
]) ?>
```

---

### ¿Puedo usar jQuery?

¡Sí! Ya está incluido en el layout. Solo usa `$()` normalmente.

### ¿Cómo agrego validación de formularios?

```php
// En el modelo
public function validate() {
    $errors = [];

    if (empty($this->numero_economico)) {
        $errors[] = 'El número económico es requerido';
    }

    return $errors;
}

// En el controlador
$errors = $unidad->validate();
if (!empty($errors)) {
    flash('error', implode('<br>', $errors));
    View::back();
}
```

### ¿Cómo hago consultas complejas?

```php
// En el modelo
public function getUnidadesConMantenimientos() {
    $sql = "SELECT u.*,
                   COUNT(m.id) as total_mantenimientos
            FROM unidades u
            LEFT JOIN mantenimientos m ON u.id = m.id_unidad
            WHERE u.estatus = ?
            GROUP BY u.id";

    return $this->db->fetchAll($sql, ['activo']);
}
```

---

## 📞 Soporte

Si algo no funciona:

1. Revisa los logs en `storage/logs/app.log`
2. Verifica que PHP >= 7.4
3. Asegúrate que mysqli esté habilitado
4. Revisa permisos de carpetas
