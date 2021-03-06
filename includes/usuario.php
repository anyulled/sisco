<?php

/**
 * Clase para manejo de usuarios
 *
 * @author anyul
 */
class usuario extends db implements crud {

    const tabla = "usuario";

    public function insertar($data) {
        $empresa = $data['empresa_id'];
        $tipo_usuario = $data['tipo_usuario_id'];
        unset($data['tipo_usuario_id']);
        unset($data['empresa_id']);
        $result = $this->insert(self::tabla, $data);
        $this->exec_query("insert into usuario_empresa_rol(usuario_id,empresa_id,tipo_usuario_id) values({$result['insert_id']}, {$empresa}, {$tipo_usuario})
on duplicate key update tipo_usuario_id = {$tipo_usuario};");
        return $result;
    }

    public function actualizar($id, $data) {
        $result = $this->update(self::tabla, $data, array("id" => $id));
        $this->log("Usuario $id:{$data['Nombre']} actualizado.");
        return $result;
    }

    public function borrar($id) {
        $temp_usuario = $this->ver($id);
        $result = $this->delete(self::tabla, $id);
        $this->log("Usuario $id:{$temp_usuario['Nombre']} Borrado.");
        return $result;
    }

    /**
     * funcion que retorna una matriz de datos de un usuario determinado
     * @param int $id id del usuario
     * @return array arreglo de datos
     */
    public function ver($id) {
        return $result = $this->dame_query("
                select usuario.*, 
                tipo_usuario.nombre 'tipo_usuario' 
                from usuario 
                inner join usuario_empresa_rol on usuario_empresa_rol.usuario_id = usuario.id
                inner join tipo_usuario on usuario_empresa_rol.tipo_usuario_id = tipo_usuario.id
                    where usuario.id=$id");
    }

    /**
     * funcion que retorna un arreglo de usuarios
     * @return Array
     */
    public function listar() {
        return $this->dame_query("select id, nombre from " . self::tabla);
    }

    /**
     * Gestiona el inicio de sesión en el sistema
     * @param String $usuario
     * @param String $password
     * @return boolean 
     */
    public function login($usuario, $password, $empresa) {
        $bitacora = new bitacora();
        $result = array();
        try {
            $result = $this->dame_query("select * from usuario where login='$usuario' and clave='$password'");
            if ($result['suceed'] == 'true' && count($result['data']) > 0) {
                $autorizado = $this->dame_query("select usuario_empresa_rol.*, tipo_usuario.nombre 'tipo_usuario' , empresa.nombre 'empresa' 
                    from usuario_empresa_rol
                    inner join tipo_usuario on usuario_empresa_rol.tipo_usuario_id = tipo_usuario.id
                    inner join empresa on usuario_empresa_rol.empresa_id = empresa.id
                        where usuario_id='{$result['data'][0]['id']}' and empresa_id='$empresa'");
                if ($autorizado['suceed'] && count($autorizado['data']) > 0) {
                    session_start();
                    $_SESSION['usuario'] = $result['data'][0];
                    $_SESSION['usuario']['empresa_id'] = $autorizado['data'][0]['empresa_id'];
                    $_SESSION['usuario']['empresa'] = $autorizado['data'][0]['empresa'];
                    $_SESSION['usuario']['tipo_usuario'] = $autorizado['data'][0]['tipo_usuario'];
                    $_SESSION['status'] = 'logueado';
                    $bitacora->log($result['data'][0]['id'], "inicio sesion");
                    header("location:" . ROOT . "/sistema/usuario/");
                    return $result;
                } else {
                    $bitacora->log($result['data'][0]['id'], "intento fallido de inicio de sesión para empresa:$empresa por $usuario");
                    $result['suceed'] = false;
                    $result['error'] = "Usuario no autorizado para esta empresa";
                    return $result;
                }
            } else {
                $bitacora->log("1", "intento fallido de inicio de sesión= usuario: $usuario, password: $password");
                $result['suceed'] = false;
                $result['error'] = "Login y/o clave inválidos";
                return $result;
            }
        } catch (Exception $exc) {
            trigger_error($exc->getTraceAsString(), E_USER_NOTICE);
            $result['suceed'] = false;
            $result['error'] = "Error desconocido. Contacte al administrador del sistema.";
            return $result;
        }
    }

    /**
     * Confirma que el usuario sea haya logueado en el sistema 
     */
    public function confirmar_miembro() {
        session_start();
        if (!isset($_SESSION['status']) || $_SESSION['status'] != 'logueado' || !isset($_SESSION['usuario'])) {
            session_destroy();
            header("location:" . ROOT . "/login.php");
        }
    }

    /**
     * Cierra la sesión 
     */
    public function logout() {
        session_start();
        if (isset($_SESSION['status'])) {
            unset($_SESSION['status']);
            unset($_SESSION['usuario']);
            session_unset();
            session_destroy();

            if (isset($_COOKIE[session_name()]))
                setcookie(session_name(), '', time() - 1000);
            header("location:" . ROOT . "/login.php");
        }
    }

}
?>