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
        /* Estilos personalizados */
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

        .btn-edit {
            background-color: #ffc107;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-edit:hover {
            background-color: #e0a800;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-delete:hover {
            background-color: #c82333;
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
        <h3>Tabla de Compras</h3>
        <a href="../Super_Administrador/crear_tipo-compra.php" class="btn btn-create btn-sm">
            <i class="bi bi-person-plus-fill"></i> Crear una Nueva Compra
        </a>

        <!-- Tabla de Compras -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre de Compra</th>
                        <th>Modificar</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody id="tabla-compras">
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
        let compras = [];
        const registrosPorPagina = 10;
        let paginaActual = 1;

        // Cargar Compras desde la API
        function cargarCompras() {
            fetch('http://192.168.100.44:3003/api/serverlab/obtener-tipo_compras')
                .then(response => response.json())
                .then(data => {
                    compras = data;
                    mostrarPagina(paginaActual);
                    generarBotonesPaginacion();
                })
                .catch(error => console.error('Error al obtener las compras:', error));
        }

        function mostrarPagina(pagina) {
            const inicio = (pagina - 1) * registrosPorPagina;
            const fin = Math.min(inicio + registrosPorPagina, compras.length);
            const comprasPagina = compras.slice(inicio, fin);

            const tabla = document.getElementById('tabla-compras');
            tabla.innerHTML = '';

            comprasPagina.forEach(compra => {
                const fila = document.createElement('tr');
                fila.innerHTML = `
                    <td>${compra.id}</td>
                    <td>${compra.nombre_compra}</td>
                    <td><button class="btn btn-edit" onclick="editarCompra(${compra.id})"><i class="bi bi-pencil-square"></i></button></td>
                    <td><button class="btn btn-delete" onclick="eliminarCompra(${compra.id})"><i class="bi bi-trash-fill"></i></button></td>
                `;
                tabla.appendChild(fila);
            });
        }

        function generarBotonesPaginacion() {
            const totalPaginas = Math.ceil(compras.length / registrosPorPagina);
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

        function editarCompra(id) {
            // Obtener los datos de la Compra con el ID dado
            const compra = compras.find(c => c.id === id);

            if (!compra) {
                console.error('Compra no encontrada');
                return;
            }

            // Crear el formulario de edición
            Swal.fire({
                title: 'Editar Tipo de Compra',
                html: `
                    <form id="editCompraForm">
                        <div class="form-group">
                            <label for="editNombre">Nombre de Compra:</label>
                            <input type="text" id="editNombre" class="form-control" value="${compra.nombre_compra}">
                        </div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Guardar Cambios',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const nombreCompra = document.getElementById('editNombre').value;

                    if (!nombreCompra) {
                        Swal.showValidationMessage('Todos los campos obligatorios deben estar completos.');
                        return false;
                    }

                    return {
                        nombre_compra: nombreCompra
                    };
                }
            }).then(result => {
                if (result.isConfirmed) {
                    const formData = result.value;

                    fetch(`http://192.168.100.44:3003/api/serverlab/actualizar-tipo_compra/${id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(formData)
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire('Compra Actualizada', 'El tipo de compra ha sido actualizado correctamente', 'success');
                            cargarCompras();
                        })
                        .catch(error => {
                            console.error('Error al actualizar compra:', error);
                            Swal.fire('Error al actualizar compra', '', 'error');
                        });
                }
            });
        }

        function eliminarCompra(id) {
            // Mostrar mensaje de confirmación
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`http://192.168.100.44:3003/api/serverlab/eliminar-tipo_compra/${id}`, {
                            method: 'DELETE'
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire('Compra eliminada', 'El tipo de compra ha sido eliminado', 'success');
                            cargarCompras();
                        })
                        .catch(error => {
                            console.error('Error al eliminar compra:', error);
                            Swal.fire('Error al eliminar compra', '', 'error');
                        });
                }
            });
        }

        cargarCompras();
    </script>
</body>

<footer>
    &copy; 2024 ABOGAU. Todos los derechos reservados. <a href="../../imgs/Politicas.pdf">Politicas de Privacidad</a>
</footer>

</html>