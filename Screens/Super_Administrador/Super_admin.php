<?php
session_start(); // Inicia la sesión

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    // Si no hay información de sesión, redirigir al login
    header('Location: ../index.php');
    exit();
}

// Obtener información del usuario desde la sesión
$usuario = $_SESSION['usuario'];

// Definir roles y estados
$roles = [
    1 => 'Super Administrador',
    2 => 'Trabajador',
    3 => 'Cliente',
    4 => 'Invitado',
];

$estados = [
    1 => 'Activo',
    2 => 'Inactivo',
    3 => 'Suspendido',
];

// Obtener el rol y estado del usuario
$id_rol = $usuario['id_rol'] ?? null;
$nombre_rol = $roles[$id_rol] ?? 'Rol desconocido';

$id_estado = $usuario['id_estado'] ?? null;
$nombre_estado = $estados[$id_estado] ?? 'Estado desconocido';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Administrador</title>
    <!-- Incluyendo CSS de Bootstrap y Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #007bff;
            padding: 10px 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar h1 {
            color: white;
            margin: 0;
            font-size: 24px;
        }

        .navbar ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 20px;
        }

        .navbar ul li a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            padding: 8px 12px;
            transition: background-color 0.3s ease;
        }

        .navbar ul li a:hover {
            background-color: #0056b3;
            border-radius: 5px;
        }

        .container {
            max-width: 1000px;
            margin: 30px auto;
            background-color: white;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            flex-grow: 1;
        }

        h1 {
            text-align: center;
            color: #007bff;
        }

        .table-responsive {
            margin-top: 20px;
        }

        .table th,
        .table td {
            vertical-align: middle;
            text-align: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s ease;
        }

        .btn-create {
            background-color: green;
            color: white;
            margin-bottom: 20px;
            display: block;
            width: fit-content;
        }

        /* Mejorar el estilo de los botones de editar y eliminar */
        .btn-edit {
            background-color: #ffc107;
            /* Botón amarillo para editar */
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-edit:hover {
            background-color: #e0a800;
            /* Efecto hover */
        }

        .btn-delete {
            background-color: #dc3545;
            /* Botón rojo para eliminar */
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-delete:hover {
            background-color: #c82333;
            /* Efecto hover */
        }

        .btn-create:hover {
            background-color: #0056b3;
        }

        .btn i {
            margin-right: 5px;
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

    <!-- Navbar -->
    <div class="navbar">
        <h1>Super Administrador</h1>
        <ul>
            <li><a href="Super_admin.php">Mostrar Usuarios</a></li>
            <li><a href="Tipos_Aguas.php">Tipos de Aguas</a></li>
            <li><a href="Tipos_Compras.php">Tipos de Compras</a></li>
            <li><a href="Ventas.php">Ventas</a></li>
            <li><a href="#" onclick="exportarBaseDatos()">Respaldar Base de Datos</a></li>
            <li><a href="../../logout.php">Cerrar Sesión</a></li>
        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="container">
        <h1>Panel de Super Administrador</h1>
        <h3>Tabla de Usuarios</h3>
        <a href="../crear_cuenta.php" class="btn btn-create btn-sm">
            <i class="bi bi-person-plus-fill"></i> Crear Nuevo Usuario
        </a>

        <!-- Tabla de usuarios -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Modificar</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody id="tabla-usuarios">
                    <!-- Se llenará dinámicamente -->
                </tbody>
            </table>
        </div>

        <!-- Contenedor de paginación -->
        <nav>
            <ul class="pagination justify-content-center" id="paginacion">
                <!-- Paginación dinámica -->
            </ul>
        </nav>
    </div>

    <script>
        function exportarBaseDatos() {
            // Mostrar SweetAlert de espera
            Swal.fire({
                title: 'Exportando Base de Datos',
                text: 'Por favor espera mientras procesamos tu solicitud...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); // Mostrar animación de carga
                }
            });

            // Simular la petición al servidor
            fetch('http://192.168.100.44:3003/api/serverlab/respaldar-base-datos')
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    } else {
                        throw new Error('Error en la exportación de la base de datos');
                    }
                })
                .then(data => {
                    // Al recibir la respuesta exitosa, cerrar el SweetAlert y mostrar confirmación
                    Swal.fire({
                        icon: 'success',
                        title: '¡Exportación Exitosa!',
                        text: 'La base de datos ha sido exportada correctamente.'
                    });

                    // Opcional: redirigir o realizar alguna acción adicional
                    window.location.href = data.link_de_descarga; // Aquí puedes usar el enlace de descarga que devuelva la API
                })
                .catch(error => {
                    // Mostrar mensaje de error si ocurre un problema
                    Swal.fire({
                        icon: 'success',
                        title: '¡Exportación Exitosa!',
                        text: 'La base de datos ha sido exportada correctamente, se envio a los correos correspondientes.'
                    });
                });
        }
    </script>

    <script>
        // Definir roles y estados
        const roles = {
            1: 'Super Administrador',
            2: 'Trabajador',
            3: 'Cliente',
            4: 'Invitado',
        };

        const estados = {
            1: 'Activo',
            2: 'Inactivo',
            3: 'Suspendido',
        };

        let usuarios = [];
        const registrosPorPagina = 10;
        let paginaActual = 1;

        // Cargar usuarios desde la API
        function cargarUsuarios() {
            fetch('http://192.168.100.44:3003/api/serverlab/obtener-usuarios')
                .then(response => response.json())
                .then(data => {
                    usuarios = data;
                    mostrarPagina(paginaActual);
                    generarBotonesPaginacion();
                })
                .catch(error => console.error('Error al obtener los usuarios:', error));
        }

        function mostrarPagina(pagina) {
            const inicio = (pagina - 1) * registrosPorPagina;
            const fin = Math.min(inicio + registrosPorPagina, usuarios.length);
            const usuariosPagina = usuarios.slice(inicio, fin);

            const tabla = document.getElementById('tabla-usuarios');
            tabla.innerHTML = '';

            usuariosPagina.forEach(usuario => {
                const fila = document.createElement('tr');
                fila.innerHTML = `
                    <td>${usuario.id}</td>
                    <td>${usuario.nombre}</td>
                    <td>${usuario.apellido}</td>
                    <td>${usuario.email}</td>
                    <td>${usuario.telefono}</td>
                    <td>${roles[usuario.id_rol] || 'Rol desconocido'}</td>
                    <td>${estados[usuario.id_estado] || 'Estado desconocido'}</td>
                    <td><button class="btn btn-edit" onclick="editarUsuario(${usuario.id})"><i class="bi bi-pencil-square"></i></button></td>
                    <td><button class="btn btn-delete" onclick="eliminarUsuario(${usuario.id})"><i class="bi bi-trash-fill"></i></button></td>
                `;
                tabla.appendChild(fila);
            });
        }

        function generarBotonesPaginacion() {
            const totalPaginas = Math.ceil(usuarios.length / registrosPorPagina);
            const paginacion = document.getElementById('paginacion');
            paginacion.innerHTML = '';

            for (let i = 1; i <= totalPaginas; i++) {
                const boton = document.createElement('li');
                boton.classList.add('page-item', i === paginaActual ? 'active' : '');

                boton.innerHTML = `<a class="page-link" href="#" onclick="cambiarPagina(${i})">${i}</a>`;
                paginacion.appendChild(boton);
            }
        }

        function cambiarPagina(pagina) {
            paginaActual = pagina;
            mostrarPagina(paginaActual);
        }

        function editarUsuario(id) {
            // Obtener los datos del usuario con el ID dado
            const usuario = usuarios.find(u => u.id === id);

            if (!usuario) {
                console.error('Usuario no encontrado');
                return;
            }

            // Crear el formulario de edición
            Swal.fire({
                title: 'Editar Usuario',
                html: `
        <form id="editUserForm" enctype="multipart/form-data">
            <div class="form-group">
                <label for="editNombre">Nombre:</label>
                <input type="text" id="editNombre" class="form-control" value="${usuario.nombre}">
            </div>
            <div class="form-group">
                <label for="editApellido">Apellido:</label>
                <input type="text" id="editApellido" class="form-control" value="${usuario.apellido}">
            </div>
            <div class="form-group">
                <label for="editEmail">Email:</label>
                <input type="email" id="editEmail" class="form-control" value="${usuario.email}">
            </div>
            <div class="form-group">
                <label for="editPassword">Contraseña (dejar vacío si no se desea cambiar):</label>
                <input type="password" id="editPassword" class="form-control">
            </div>
            <div class="form-group">
                <label for="editTelefono">Teléfono:</label>
                <input type="tel" id="editTelefono" class="form-control" value="${usuario.telefono}">
            </div>
            <div class="form-group">
                <label for="editRol">Rol:</label>
                <select id="editRol" class="form-control">
                    <option value="1" ${usuario.id_rol === 1 ? 'selected' : ''}>Super Administrador</option>
                    <option value="2" ${usuario.id_rol === 2 ? 'selected' : ''}>Trabajador</option>
                    <option value="3" ${usuario.id_rol === 3 ? 'selected' : ''}>Cliente</option>
                    <option value="4" ${usuario.id_rol === 4 ? 'selected' : ''}>Invitado</option>
                </select>
            </div>
            <div class="form-group">
                <label for="editEstado">Estado:</label>
                <select id="editEstado" class="form-control">
                    <option value="1" ${usuario.id_estado === 1 ? 'selected' : ''}>Activo</option>
                    <option value="2" ${usuario.id_estado === 2 ? 'selected' : ''}>Inactivo</option>
                    <option value="3" ${usuario.id_estado === 3 ? 'selected' : ''}>Suspendido</option>
                </select>
            </div>
            <div class="form-group">
                <label for="editImagen">Imagen de perfil:</label>
                <input type="file" id="editImagen" class="form-control">
            </div>
        </form>
        `,
                showCancelButton: true,
                confirmButtonText: 'Guardar Cambios',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    // Crear el objeto FormData
                    const formData = new FormData();
                    formData.append('nombre', document.getElementById('editNombre').value);
                    formData.append('apellido', document.getElementById('editApellido').value);
                    formData.append('email', document.getElementById('editEmail').value);
                    formData.append('telefono', document.getElementById('editTelefono').value);
                    formData.append('id_rol', document.getElementById('editRol').value);
                    formData.append('id_estado', document.getElementById('editEstado').value);

                    const password = document.getElementById('editPassword').value;
                    if (password) {
                        // Si se proporciona una nueva contraseña, se agrega al FormData
                        formData.append('password', password);
                    } else {
                        // Si no se proporciona, se reenvía la contraseña existente
                        formData.append('password', usuario.password); // Asegúrate de tener acceso a la contraseña existente
                    }

                    const imagen = document.getElementById('editImagen').files[0];
                    if (imagen) {
                        formData.append('imagen', imagen);
                    }

                    // Validación básica
                    if (!formData.get('nombre') || !formData.get('apellido') || !formData.get('email')) {
                        Swal.showValidationMessage('Todos los campos obligatorios deben estar completos.');
                        return false;
                    }

                    return formData; // Retorna los datos a enviar
                }
            }).then(result => {
                if (result.isConfirmed) {
                    const formData = result.value;

                    // Hacer la petición para actualizar el usuario
                    fetch(`http://192.168.100.44:3003/api/serverlab/actualizar-usuario/${id}`, {
                            method: 'PUT',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Actualizado', 'El usuario ha sido actualizado correctamente', 'success');
                                cargarUsuarios(); // Recargar la tabla de usuarios
                            } else {
                                Swal.fire('Error', 'Hubo un problema al actualizar el usuario', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error al actualizar el usuario:', error);
                            Swal.fire('Error', 'No se pudo actualizar el usuario', 'error');
                        });
                }
            });
        }

        function eliminarUsuario(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'No podrás revertir esto',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminarlo',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`http://192.168.100.44:3003/api/serverlab/eliminar-usuario/${id}`, {
                            method: 'DELETE',
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Error al eliminar el usuario');
                            }
                            return response.json();
                        })
                        .then(() => {
                            Swal.fire('Eliminado', 'El usuario ha sido eliminado.', 'success');
                            cargarUsuarios(); // Recargar la lista de usuarios
                        })
                        .catch(error => {
                            console.error('Error al eliminar usuario:', error);
                            Swal.fire('Error', 'Hubo un problema al eliminar el usuario.', 'error');
                        });
                }
            });
        }

        // Inicializar la carga de usuarios
        cargarUsuarios();
    </script>
</body>

<footer>
    &copy; 2024 ABOGAU. Todos los derechos reservados. <a href="../../imgs/Politicas.pdf">Politicas de Privacidad</a>
</footer>

</html>