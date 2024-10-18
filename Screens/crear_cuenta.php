<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Usuario</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Crear Nuevo Usuario</h2>
        <form id="crearUsuarioForm" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>

            <div class="form-group">
                <label for="apellido">Apellidos</label>
                <input type="text" class="form-control" id="apellido" name="apellido" required>
            </div>

            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" class="form-control" id="telefono" name="telefono" required>
            </div>

            <div class="form-group">
                <label for="imagen">Imagen de Perfil</label>
                <input type="file" class="form-control-file" id="imagen" name="imagen" required>
            </div>

            <div class="form-group">
                <label for="id_rol">Rol</label>
                <select class="form-control" id="id_rol" name="id_rol" required>
                    <option value="" disabled selected>Selecciona un rol</option>
                    <option value="1">Administrador</option>
                    <option value="2">Usuario</option>
                    <option value="3">Supervisor</option>
                    <option value="4">Invitado</option>
                </select>
            </div>

            <div class="form-group">
                <label for="id_estado">Estado</label>
                <select class="form-control" id="id_estado" name="id_estado" required>
                    <option value="" disabled selected>Selecciona un estado</option>
                    <option value="1">Activo</option>
                    <option value="2">Inactivo</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Crear Usuario</button>
        </form>
    </div>

    <script>
        document.getElementById('crearUsuarioForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Obtener los datos del formulario
            const formData = new FormData(this);

            // Enviar datos al servidor usando fetch
            fetch('http://192.168.100.44:3003/api/serverlab/nuevo-usuario', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message === 'Usuario creado exitosamente') {
                        // Mostrar mensaje de éxito con SweetAlert
                        Swal.fire({
                            icon: 'success',
                            title: 'Usuario creado',
                            text: 'El usuario ha sido creado exitosamente.',
                        }).then(() => {
                            // Recargar la página o limpiar el formulario si se desea
                            window.location.href = document.referrer || "Screens/Super_admin.php";
                        });
                    } else {
                        // Mostrar mensaje de error con SweetAlert
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Ocurrió un error al crear el usuario.',
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Mostrar mensaje de error con SweetAlert
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo conectar con el servidor. Inténtalo de nuevo más tarde.',
                    });
                });
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>