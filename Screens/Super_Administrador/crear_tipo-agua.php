<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Tipo de Agua</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Crear Nuevo Tipo de Agua</h2>
        <form id="crearAguaForm">
            <div class="form-group">
                <label for="nombre_agua">Nombre del Agua</label>
                <input type="text" class="form-control" id="nombre_agua" name="nombre_agua" required>
            </div>
            <button type="submit" class="btn btn-primary">Crear Agua</button>
        </form>
    </div>

    <script>
        document.getElementById('crearAguaForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Obtener los datos del formulario
            const formData = new FormData(this);

            // Convertir FormData a JSON
            const data = Object.fromEntries(formData.entries());

            // Enviar datos al servidor usando fetch
            fetch('http://192.168.100.44:3003/api/serverlab/nuevo-tipo_agua', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json', // Enviar datos como JSON
                    },
                    body: JSON.stringify(data) // Convertir objeto a JSON
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message === 'Tipo de Agua creado exitosamente!') {
                        // Mostrar mensaje de éxito con SweetAlert
                        Swal.fire({
                            icon: 'success',
                            title: 'Agua creada',
                            text: 'El agua ha sido creada exitosamente.',
                        }).then(() => {
                            // Redirigir o limpiar el formulario si se desea
                            window.location.href = document.referrer || "Screens/Super_administrador/Tipos_Aguas.php";
                        });
                    } else {
                        // Mostrar mensaje de error con SweetAlert
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Ocurrió un error al crear el Agua.',
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