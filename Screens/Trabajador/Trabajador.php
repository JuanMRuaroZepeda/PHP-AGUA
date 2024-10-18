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
            max-width: 800px;
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
        <h1>Trabajador</h1>
        <ul>
            <li><a href="Trabajador.php">Tipos de Aguas</a></li>
            <li><a href="Trabajador_TiposCompras.php">Tipos de Compras</a></li>
            <li><a href="Ventas.php">Ventas</a></li>
            <li><a href="../../logout.php">Cerrar Sesión</a></li>
        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="container">
        <h1>Panel del Trabajador</h1>
        <h3>Tabla de Aguas</h3>
        <a href="../Super_Administrador/crear_tipo-agua.php" class="btn btn-create btn-sm">
            <i class="bi bi-person-plus-fill"></i> Crear una Nueva Agua
        </a>

        <!-- Tabla de usuarios -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre de Agua</th>
                        <th>Modificar</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody id="tabla-aguas">
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
        let aguas = [];
        const registrosPorPagina = 10;
        let paginaActual = 1;

        // Cargar Aguas desde la API
        function cargarAguas() {
            fetch('http://192.168.100.44:3003/api/serverlab/obtener-tipo_agua')
                .then(response => response.json())
                .then(data => {
                    aguas = data;
                    mostrarPagina(paginaActual);
                    generarBotonesPaginacion();
                })
                .catch(error => console.error('Error al obtener las aguas:', error));
        }

        function mostrarPagina(pagina) {
            const inicio = (pagina - 1) * registrosPorPagina;
            const fin = Math.min(inicio + registrosPorPagina, aguas.length);
            const aguasPagina = aguas.slice(inicio, fin);

            const tabla = document.getElementById('tabla-aguas');
            tabla.innerHTML = '';

            aguasPagina.forEach(agua => {
                const fila = document.createElement('tr');
                fila.innerHTML = `
                    <td>${agua.id}</td>
                    <td>${agua.nombre_agua}</td>
                    <td><button class="btn btn-edit" onclick="editarAgua(${agua.id})"><i class="bi bi-pencil-square"></i></button></td>
                    <td><button class="btn btn-delete" onclick="eliminarAgua(${agua.id})"><i class="bi bi-trash-fill"></i></button></td>
                `;
                tabla.appendChild(fila);
            });
        }

        function generarBotonesPaginacion() {
            const totalPaginas = Math.ceil(aguas.length / registrosPorPagina);
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

        function editarAgua(id) {
            // Obtener los datos del agua con el ID dado
            const agua = aguas.find(a => a.id === id);

            if (!agua) {
                console.error('Agua no encontrado');
                return;
            }

            // Crear el formulario de edición
            Swal.fire({
                title: 'Editar Agua',
                html: `
            <form id="editAguaForm">
                <div class="form-group">
                    <label for="editNombre">Nombre de Agua:</label>
                    <input type="text" id="editNombre" class="form-control" value="${agua.nombre_agua}">
                </div>
            </form>
        `,
                showCancelButton: true,
                confirmButtonText: 'Guardar Cambios',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    // Obtener los datos del formulario
                    const nombreAgua = document.getElementById('editNombre').value;

                    // Validación básica
                    if (!nombreAgua) {
                        Swal.showValidationMessage('Todos los campos obligatorios deben estar completos.');
                        return false;
                    }

                    // Retornar los datos a enviar como JSON
                    return {
                        nombre_agua: nombreAgua
                    };
                }
            }).then(result => {
                if (result.isConfirmed) {
                    const formData = result.value;

                    // Hacer la petición para actualizar el tipo de agua usando JSON en lugar de FormData
                    fetch(`http://192.168.100.44:3003/api/serverlab/actualizar-tipo_agua/${id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json' // Enviar datos como JSON
                            },
                            body: JSON.stringify(formData) // Convertir a JSON
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Error en la respuesta del servidor');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Error', 'Hubo un problema al actualizar el tipo de agua', 'error');
                            } else {
                                Swal.fire('Actualizado', 'El tipo de agua ha sido actualizado correctamente', 'success');
                                cargarAguas(); // Recargar la tabla de aguas
                            }
                        })
                        .catch(error => {
                            console.error('Error al actualizar el tipo de agua:', error);
                            Swal.fire('Error', 'No se pudo actualizar el tipo de agua', 'error');
                        });
                }
            });
        }

        function eliminarAgua(id) {
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
                    fetch(`http://192.168.100.44:3003/api/serverlab/eliminar-tipo_agua/${id}`, {
                            method: 'DELETE',
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Error al eliminar el tipo de agua');
                            }
                            return response.json();
                        })
                        .then(() => {
                            Swal.fire('Eliminado', 'El tipo de agua ha sido eliminado.', 'success');
                            cargarAguas(); // Recargar la lista de usuarios
                        })
                        .catch(error => {
                            console.error('Error al eliminar el tipo de agua:', error);
                            Swal.fire('Error', 'Hubo un problema al eliminar el tipo de agua.', 'error');
                        });
                }
            });
        }

        // Inicializar la carga de usuarios
        cargarAguas();
    </script>
</body>

<footer>
    &copy; 2024 ABOGAU. Todos los derechos reservados. <a href="../../imgs/Politicas.pdf">Politicas de Privacidad</a>
</footer>

</html>