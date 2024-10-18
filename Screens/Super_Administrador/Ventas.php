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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        <h3>Tabla de Ventas</h3>

        <!-- Tabla de usuarios -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Cantidad</th>
                        <th>Tipo de Agua</th>
                        <th>Tipo de Compra</th>
                        <th>Usuario</th>
                        <th>Fecha</th>
                        <th>Modificar</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody id="tabla-ventas">
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

        <h3>Estadísticas de Ventas</h3>

        <div class="row">
            <div class="col-md-6">
                <h4>Ventas por Tipo de Agua</h4>
                <canvas id="ventasPorTipoAgua"></canvas>
            </div>
            <div class="col-md-6">
                <h4>Ventas por Tipo de Compra</h4>
                <canvas id="ventasPorTipoCompra"></canvas>
            </div>
        </div>
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

            fetch('http://192.168.100.44:3003/api/serverlab/respaldar-base-datos')
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    } else {
                        throw new Error('Error en la exportación de la base de datos');
                    }
                })
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Exportación Exitosa!',
                        text: 'La base de datos ha sido exportada correctamente.'
                    });
                    window.location.href = data.link_de_descarga;
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        text: error.message
                    });
                });
        }
    </script>

    <script>
        let ventas = [];
        let aguas = [];
        let compras = [];
        let usuarios = [];
        const registrosPorPagina = 10;
        let paginaActual = 1;

        function cargarVentas() {
            fetch('http://192.168.100.44:3003/api/serverlab/obtener-ventas')
                .then(response => response.json())
                .then(data => {
                    ventas = data;
                    return Promise.all([cargarAguas(), cargarCompras(), cargarUsuarios()]);
                })
                .then(() => {
                    mostrarPagina(paginaActual);
                    generarBotonesPaginacion();
                    generarGraficas(); // Llamar a generarGraficas solo cuando todos los datos estén cargados
                })
                .catch(error => console.error('Error al obtener las ventas:', error));
        }

        function cargarAguas() {
            return fetch('http://192.168.100.44:3003/api/serverlab/obtener-tipo_agua')
                .then(response => response.json())
                .then(data => {
                    aguas = data;
                })
                .catch(error => console.error('Error al obtener las aguas:', error));
        }

        function cargarCompras() {
            return fetch('http://192.168.100.44:3003/api/serverlab/obtener-tipo_compras')
                .then(response => response.json())
                .then(data => {
                    compras = data;
                })
                .catch(error => console.error('Error al obtener las compras:', error));
        }

        function cargarUsuarios() {
            return fetch('http://192.168.100.44:3003/api/serverlab/obtener-usuarios')
                .then(response => response.json())
                .then(data => {
                    usuarios = data;
                })
                .catch(error => console.error('Error al obtener los usuarios:', error));
        }

        function mostrarPagina(pagina) {
            const inicio = (pagina - 1) * registrosPorPagina;
            const fin = Math.min(inicio + registrosPorPagina, ventas.length);
            const ventasPagina = ventas.slice(inicio, fin);

            const tabla = document.getElementById('tabla-ventas');
            tabla.innerHTML = '';

            ventasPagina.forEach(venta => {
                const tipoAgua = aguas.find(a => a.id === venta.id_tipoagua)?.nombre_agua || 'Desconocido';
                const tipoCompra = compras.find(c => c.id === venta.id_tipocompra)?.nombre_compra || 'Desconocido';
                const usuario = usuarios.find(u => u.id === venta.id_usuario)?.nombre || 'Desconocido';

                // Formatear la fecha
                const fecha = new Date(venta.fecha);
                const opciones = {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: true
                };
                const fechaFormateada = fecha.toLocaleString('es-MX', opciones);

                const fila = document.createElement('tr');
                fila.innerHTML = `
            <td>${venta.id}</td>
            <td>${venta.cantidad}</td>
            <td>${tipoAgua}</td>
            <td>${tipoCompra}</td>
            <td>${usuario}</td>
            <td>${fechaFormateada}</td>
            <td>
                <button class="btn btn-edit" onclick="editarVenta(${venta.id})"><i class="bi bi-pencil-square"></i></button>
            </td>
            <td>
                <button class="btn btn-delete" onclick="eliminarVenta(${venta.id})"><i class="bi bi-trash-fill"></i></button>
            </td>
        `;
                tabla.appendChild(fila);
            });
        }

        function generarBotonesPaginacion() {
            const totalPaginas = Math.ceil(ventas.length / registrosPorPagina);
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

        function editarVenta(id) {
            const venta = ventas.find(v => v.id === id);
            const tipoAgua = aguas.find(a => a.id === venta.id_tipoagua)?.nombre_agua || '';
            const tipoCompra = compras.find(c => c.id === venta.id_tipocompra)?.nombre_compra || '';
            const usuario = usuarios.find(u => u.id === venta.id_usuario)?.nombre || '';

            if (!venta) {
                console.error('Venta no encontrada');
                return;
            }

            Swal.fire({
                title: 'Editar Venta',
                html: `
            <form id="editVentaForm">
                <div class="form-group">
                    <label for="editCantidad">Cantidad:</label>
                    <input type="number" id="editCantidad" class="form-control" value="${venta.cantidad}">
                </div>
                <div class="form-group">
                    <label for="editTipoAgua">Tipo de Agua:</label>
                    <select id="editTipoAgua" class="form-control">
                        ${aguas.map(a => `
                            <option value="${a.id}" ${a.id === venta.id_tipoagua ? 'selected' : ''}>${a.nombre_agua}</option>
                        `).join('')}
                    </select>
                </div>
                <div class="form-group">
                    <label for="editTipoCompra">Tipo de Compra:</label>
                    <select id="editTipoCompra" class="form-control">
                        ${compras.map(c => `
                            <option value="${c.id}" ${c.id === venta.id_tipocompra ? 'selected' : ''}>${c.nombre_compra}</option>
                        `).join('')}
                    </select>
                </div>
                <div class="form-group">
                    <label for="editUsuario">Usuario:</label>
                    <select id="editUsuario" class="form-control">
                        ${usuarios.map(u => `
                            <option value="${u.id}" ${u.id === venta.id_usuario ? 'selected' : ''}>${u.nombre}</option>
                        `).join('')}
                    </select>
                </div>
            </form>
        `,
                showCancelButton: true,
                confirmButtonText: 'Guardar Cambios',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const cantidad = document.getElementById('editCantidad').value;
                    const id_tipoagua = document.getElementById('editTipoAgua').value;
                    const id_tipocompra = document.getElementById('editTipoCompra').value;
                    const id_usuario = document.getElementById('editUsuario').value;

                    if (!cantidad || !id_tipoagua || !id_tipocompra || !id_usuario) {
                        Swal.showValidationMessage('Todos los campos son obligatorios.');
                        return false;
                    }

                    return {
                        cantidad,
                        id_tipoagua,
                        id_tipocompra,
                        id_usuario
                    };
                }
            }).then(result => {
                if (result.isConfirmed) {
                    const {
                        cantidad,
                        id_tipoagua,
                        id_tipocompra,
                        id_usuario
                    } = result.value;

                    fetch(`http://192.168.100.44:3003/api/serverlab/actualizar-venta/${id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                cantidad,
                                id_tipoagua,
                                id_tipocompra,
                                id_usuario
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Error en la actualización');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                Swal.fire('¡Actualización Exitosa!', 'La venta ha sido actualizada correctamente.', 'success');
                                cargarVentas(); // Recargar la tabla de ventas
                                generarGraficas();
                            } else {
                                Swal.fire('Error', 'Hubo un problema al actualizar la venta', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error al actualizar la venta:', error);
                            Swal.fire('¡Actualización Exitosa!', 'La venta ha sido actualizada correctamente.', 'success');
                            generarGraficas();
                            cargarVentas(); // Recargar la tabla de ventas
                        });
                }
            });
        }

        function eliminarVenta(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás deshacer esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminarlo',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`http://192.168.100.44:3003/api/serverlab/eliminar-venta/${id}`, {
                            method: 'DELETE',
                        })
                        .then(response => {
                            if (response.ok) {
                                Swal.fire('¡Eliminado!', 'La venta ha sido eliminada.', 'success');
                                cargarVentas(); // Recargar la tabla
                            } else {
                                Swal.fire('¡Error!', 'No se pudo eliminar la venta.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error al eliminar la venta:', error);
                        });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            cargarVentas(); // Cargar las ventas al inicio
        });
    </script>

    <script>
        function generarGraficas() {
            // Verificar que se hayan cargado los datos necesarios
            if (ventas.length > 0 && aguas.length > 0 && compras.length > 0) {
                generarGraficaPorTipoAgua();
                generarGraficaPorTipoCompra();
            } else {
                console.error('Los datos necesarios para las gráficas no están completamente cargados.');
            }
        }

        function generarGraficaPorTipoAgua() {
            const ctx = document.getElementById('ventasPorTipoAgua').getContext('2d');

            // Contar las ventas por tipo de agua
            const ventasPorAgua = aguas.map(agua => {
                return {
                    nombre: agua.nombre_agua,
                    cantidad: ventas.filter(venta => venta.id_tipoagua === agua.id).reduce((total, venta) => total + venta.cantidad, 0)
                };
            });

            const nombresAguas = ventasPorAgua.map(agua => agua.nombre);
            const cantidadesAguas = ventasPorAgua.map(agua => agua.cantidad);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: nombresAguas,
                    datasets: [{
                        label: 'Ventas por Tipo de Agua',
                        data: cantidadesAguas,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        function generarGraficaPorTipoCompra() {
            const ctx = document.getElementById('ventasPorTipoCompra').getContext('2d');

            // Contar las ventas por tipo de compra
            const ventasPorCompra = compras.map(compra => {
                return {
                    nombre: compra.nombre_compra,
                    cantidad: ventas.filter(venta => venta.id_tipocompra === compra.id).reduce((total, venta) => total + venta.cantidad, 0)
                };
            });

            const nombresCompras = ventasPorCompra.map(compra => compra.nombre);
            const cantidadesCompras = ventasPorCompra.map(compra => compra.cantidad);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: nombresCompras,
                    datasets: [{
                        label: 'Ventas por Tipo de Compra',
                        data: cantidadesCompras,
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Cargar los datos al iniciar la página
        cargarVentas();
    </script>

</body>


<footer>
    &copy; 2024 ABOGAU. Todos los derechos reservados. <a href="../../imgs/Politicas.pdf">Politicas de Privacidad</a>
</footer>

</html>