<?php
session_start(); // Inicia la sesión
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login ABOGAU</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #0000ff;
            background: url('imgs/fondo.jpg') no-repeat center center fixed;
            background-size: contain;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
            justify-content: space-between;
        }

        header {
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 48px;
        }

        .login-container {
            position: relative;
            padding: 30px;
            border-radius: 60px;
            background-color: rgba(255, 255, 255, 0.9);
            width: 350px;
            text-align: center;
            margin: auto;
            box-shadow: 0 15px 15px rgba(0, 0, 0, 0.3);
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            z-index: -1;
            background: linear-gradient(270deg, #ff0000, #00ff00, #0000ff, #ff0000);
            background-size: 600% 600%;
            border-radius: 60px;
            animation: rgb-border-movement 6s linear infinite;
        }

        @keyframes rgb-border-movement {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        h1 {
            margin-bottom: 20px;
            font-size: 24px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-btn {
            width: 100%;
            padding: 10px;
            background-color: #1da1f2;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        .login-btn:hover {
            background-color: #e07b39;
        }

        .create-btn {
            width: 100%;
            padding: 10px;
            background-color: #181818;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .social-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .forgot-remember {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 14px;
        }

        .forgot-remember label,
        .forgot-remember a {
            color: #333;
            text-decoration: none;
        }

        .forgot-remember a:hover {
            text-decoration: underline;
        }

        footer {
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            text-align: center;
            padding: 10px;
            font-size: 18px;
            position: relative;
        }
    </style>
</head>

<body>

    <header>
        ABOGAU
    </header>
    <br>
    <div class="login-container">
        <h1>Bienvenido</h1>
        <form id="loginForm" method="POST" action="#">
            <input type="text" name="email" placeholder="Correo Electronico" required>
            <input type="password" name="password" placeholder="Contrasena" required>
            <div class="forgot-remember">
                <label><input type="checkbox" name="remember"> Recuerdame </label>
            </div><br>
            <button class="login-btn" type="submit">Inicia Sesion</button>
            <div>
                <label style="align-items: center;">
                    <h4>No tienes cuenta?</h4>
                </label>
            </div>
            <div class="social-buttons">
                <a class="create-btn" href="nuevo_cliente.php">Crear Cuenta</a>
            </div>
        </form>
    </div>
    <br>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Obtener datos del formulario
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Llamada a la API con supresión de errores
        $response = @file_get_contents('http://192.168.100.44:3003/api/serverlab/login', false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode(['email' => $email, 'password' => $password]),
            ]
        ]));

        // Manejo de la respuesta
        if ($response) {
            $data = json_decode($response, true); // Decodificar el JSON
            if (isset($data['message']) && $data['message'] === 'Acceso a Cuenta') {
                // Guardar los datos del usuario en la sesión
                $_SESSION['usuario'] = [
                    'id' => $data['id'],
                    'nombre' => $data['nombre'],
                    'apellido' => $data['apellido'],
                    'email' => $data['email'],
                    'telefono' => $data['telefono'],
                    'imagen' => $data['imagen'],
                    'id_rol' => $data['id_rol'],
                    'id_estado' => $data['id_estado'],
                ];

                // Mostrar mensaje de éxito y redirigir
                echo '<script>
                        Swal.fire({
                            title: "Éxito",
                            text: "Bienvenido ' . htmlspecialchars($data['nombre']) . ' ' . htmlspecialchars($data['apellido']) . '!",
                            icon: "success",
                            confirmButtonText: "Continuar"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "Screens/roles.php";
                            }
                        });
                      </script>';
            } else {
                echo '<script>Swal.fire("Error", "' . htmlspecialchars($data['message']) . '", "error");</script>';
            }
        } else {
            // Mostrar mensaje de error si la API no responde o falla
            echo '<script>Swal.fire("Error", "No se pudo conectar con el sistema.", "error");</script>';
        }
    }
    ?>

    <footer>
        &copy; 2024 ABOGAU. Todos los derechos reservados. <a href="imgs/Politicas.pdf">Politicas de Privacidad</a>
    </footer>

</body>

</html>