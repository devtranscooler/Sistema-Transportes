<?php

/**
 * UserController -
 * 
 */
class UserController
{

    /**
     * Listar usuarios - Renderiza la vista HTML
     * GET /usuarios
     */
    public function index()
    {
        try {
            $usuarios = Usuario::all();

            View::render('pages/usuarios/index', [
                'title' => 'Usuarios',
                'usuarios' => $usuarios
            ]);
        } catch (Exception $e) {
            View::error(500, $e->getMessage());
        }
    }

    /**
     * Obtener un usuario específico 
     * GET /api/usuarios/{id}
     */
    public function show($params)
    {
        try {
            $usuario = Usuario::find($params['id']);

            if (!$usuario) {
                AjaxResponse::notFound('Usuario no encontrado');
            }

            // Retornar JSON con todos los datos
            AjaxResponse::data([
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'apellidoP' => $usuario->apellidoP,
                'apellidoM' => $usuario->apellidoM,
                'email' => $usuario->email,
                'fecNac' => $usuario->fecNac,
                'movil' => $usuario->movil,
                'telefono' => $usuario->telefono,
                'noEmpleado' => $usuario->noEmpleado,
                'puesto' => $usuario->puesto,
                'area' => $usuario->area,
                'cedis' => $usuario->cedis,
                'jefeInmediato' => $usuario->jefeInmediato,
                'idRol' => $usuario->idRol,
                'fecContratacion' => $usuario->fecContratacion,
                'diasVacaciones' => $usuario->diasVacaciones,
                'diasVacDisfrutados' => $usuario->diasVacDisfrutados,
                'estatus' => $usuario->estatus
            ]);
        } catch (Exception $e) {
            AjaxResponse::error('Error: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Guardar usuario - SOLO AJAX
     * POST /usuarios/guardar
     */
    public function save()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];

            if (empty($data)) {
                AjaxResponse::validationError(['No se recibieron datos']);
            }

            $id = $data['id'] ?? null;

            // Buscar o crear usuario
            if ($id) {
                $usuario = Usuario::find($id);

                if (!$usuario) {
                    AjaxResponse::notFound('Usuario no encontrado');
                }
                $mensaje = 'Usuario actualizado correctamente';
            } else {
                $usuario = new Usuario();
                $mensaje = 'Usuario creado correctamente';
            }

            // Llenar datos
            $usuario->fill($data);

            // Password solo si se proporciona
            if (!empty($_POST['password'])) {
                $usuario->password = $_POST['password'];
            }

            // Validar
            $errors = $usuario->validate();

            if (!empty($errors)) {
                AjaxResponse::validationError($errors);
            }

            // Guardar
            if ($usuario->save()) {
                AjaxResponse::success($mensaje, ['usuario' => $usuario->toJson()]);
            } else {
                AjaxResponse::error('Error al guardar usuario');
            }
        } catch (Exception $e) {
            AjaxResponse::error('Error: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Eliminar usuario - SOLO AJAX
     * DELETE /usuarios/eliminar/{id}
     */
    public function delete($params)
    {
        try {
            $usuario = Usuario::find($params['id']);

            if (!$usuario) {
                AjaxResponse::notFound('Usuario no encontrado');
            }

            // No permitir eliminar tu propio usuario
            if ($usuario->id == Usuario::current()->id) {
                AjaxResponse::forbidden('No puedes eliminarte a ti mismo');
            }

            if ($usuario->delete()) {
                AjaxResponse::deleted('Usuario eliminado correctamente');
            } else {
                AjaxResponse::error('Error al eliminar usuario');
            }
        } catch (Exception $e) {
            AjaxResponse::error('Error: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Buscar usuarios - SOLO AJAX
     * GET /api/usuarios/buscar?q=termino
     */
    public function search()
    {
        try {
            $termino = $_GET['q'] ?? '';

            if (strlen($termino) < 3) {
                AjaxResponse::data([]);
            }

            $db = Database::getInstance();
            $sql = "SELECT 
                        id, 
                        CONCAT(nombre, ' ', apellidoP, ' ', IFNULL(apellidoM, '')) as nombreCompleto,
                        email,
                        movil,
                        puesto,
                        area,
                        estatus
                    FROM usuarios 
                    WHERE 
                        nombre LIKE ? OR 
                        apellidoP LIKE ? OR 
                        apellidoM LIKE ? OR
                        email LIKE ? OR 
                        movil LIKE ? OR
                        noEmpleado LIKE ?
                    LIMIT 10";

            $like = "%{$termino}%";
            $result = $db->query($sql, [$like, $like, $like, $like, $like, $like]);

            $usuarios = [];
            while ($row = $result->fetch_assoc()) {
                $usuarios[] = $row;
            }

            AjaxResponse::data($usuarios);
        } catch (Exception $e) {
            AjaxResponse::error('Error: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Validar email único - SOLO AJAX
     * GET /api/usuarios/validar-email?email=test@mail.com&id=5
     */
    public function validateEmail()
    {
        try {
            $email = $_GET['email'] ?? '';
            $userId = $_GET['id'] ?? null;

            if (empty($email)) {
                AjaxResponse::data(['disponible' => false, 'message' => 'Email requerido']);
            }
            

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                AjaxResponse::data(['disponible' => false, 'message' => 'El email no es válido']);
            }

            $db = Database::getInstance();

            if ($userId) {
                $sql = "SELECT COUNT(*) as count FROM usuarios WHERE email = ? AND id != ?";
                $result = $db->fetch($sql, [$email, $userId]);
            } else {
                $sql = "SELECT COUNT(*) as count FROM usuarios WHERE email = ?";
                $result = $db->fetch($sql, [$email]);
            }

            $disponible = $result['count'] == 0;

            AjaxResponse::data([
                'disponible' => $disponible,
                'message' => $disponible ? 'Email disponible' : 'Email ya está en uso'
            ]);
        } catch (Exception $e) {
            AjaxResponse::error('Error: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Exportar usuarios a CSV
     * GET /usuarios/exportar
     */
    public function export()
    {
        try {
            $usuarios = Usuario::all();

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="usuarios_' . date('Y-m-d') . '.csv"');

            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8

            // Encabezados
            fputcsv($output, [
                'ID',
                'Nombre',
                'Apellido P',
                'Apellido M',
                'Email',
                'Móvil',
                'Teléfono',
                'No. Empleado',
                'Puesto',
                'Área',
                'CEDIS',
                'Jefe',
                'Estatus',
                'Fecha Contratación'
            ]);

            // Datos
            foreach ($usuarios as $usuario) {
                fputcsv($output, [
                    $usuario->id,
                    $usuario->nombre,
                    $usuario->apellidoP,
                    $usuario->apellidoM,
                    $usuario->email,
                    $usuario->movil,
                    $usuario->telefono,
                    $usuario->noEmpleado,
                    $usuario->puesto,
                    $usuario->area,
                    $usuario->cedis,
                    $usuario->jefeInmediato,
                    $usuario->estatus,
                    $usuario->fecContratacion
                ]);
            }

            fclose($output);
            exit;
        } catch (Exception $e) {
            flash('error', 'Error al exportar: ' . $e->getMessage());
            View::redirect('/usuarios');
        }
    }

    // ==========================================
    // MÉTODOS ANTIGUOS (mantener por compatibilidad)
    // Puedes eliminarlos después si no los usas
    // ==========================================

    /**
     * Formulario de creación (página completa)
     * GET /usuarios/crear
     */
    public function create()
    {
        View::render('pages/usuarios/form', [
            'title' => 'Crear Usuario',
            'usuario' => new Usuario(),
        ]);
    }

    /**
     * Formulario de edición (página completa)
     * GET /usuarios/editar/{id}
     */
    public function edit($params)
    {
        $usuario = Usuario::find($params['id']);

        if (!$usuario) {
            flash('error', 'Usuario no encontrado');
            View::redirect('/usuarios');
            return;
        }

        View::render('pages/usuarios/form', [
            'title' => 'Editar Usuario',
            'usuario' => $usuario,
        ]);
    }
}
