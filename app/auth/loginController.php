<?php
session_start(); 

class LoginController {
    public function index() {
        renderView('auth/login');
    }

    // método para procesar el formulario de login
    public function login() {
        require_once '../../config/config.php';
        require_once '../../config/db.php';
        require_once '../models/User.php';
        require_once __DIR__ . '/../helpers.php';  

        if (is_post()) {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // validar que los campos no estén vacíos
            if (empty($email) || empty($password)) {
                $_SESSION['error'] = "Todos los campos son obligatorios.";
                header('Location: ' . base_url('/')); // redirigir al formulario de login
                exit();
            }

            // obtener la conexión a la base de datos
            $db = Database::getConnection();

            // buscar el usuario en la base de datos
            $query = "SELECT * FROM users WHERE email = $1";
            $result = pg_query_params($db, $query, array($email));

            if ($result && pg_num_rows($result) > 0) {
                $user = pg_fetch_assoc($result); // obtener los datos del usuario

                // verificar la contraseña
                if (password_verify($password, $user['password'])) {
                    // iniciar sesión (guardar datos del usuario en la sesión)
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'lastname' => $user['last_name'],
                        'email' => $user['email']
                    ];
                    header('Location: ' . base_url('reservations/make')); // cambia esto a la ruta correcta
                    exit();
                } else {
                    $_SESSION['error'] = "Credenciales incorrectas.";
                    header('Location: ' . base_url('/')); // redirigir al formulario de login
                    exit();
                }
            } else {
                $_SESSION['error'] = "El usuario no existe.";
                header('Location: ' . base_url('/')); // redirigir al formulario de login
                exit();
            }
        } else {
            $this->index();
        }
    }
}

$loginController = new LoginController();
$loginController->login();