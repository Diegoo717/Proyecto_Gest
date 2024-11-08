<?php
session_start(); // Iniciar la sesión para acceder a las variables de sesión

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['nombre'])) {
    // Si el usuario no ha iniciado sesión, redirigirlo al formulario de inicio de sesión (index2.php)
    header("Location: http://localhost/proyecto/index2.php");
    exit();
}

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Procesar los datos del formulario
    $nombre = $_POST['nombre'];
    $peso = $_POST['peso'];
    $altura = $_POST['altura'];
    $objetivo = $_POST['objetivo'];

    // Determinar el número correspondiente al plan seleccionado
    switch ($objetivo) {
        case "aumentar":
            $plan = 1;
            break;
        case "bajar":
            $plan = 2;
            break;
        case "mantener":
            $plan = 3;
            break;
        default:
            $plan = null; // En caso de no seleccionar un objetivo válido
    }

    // Verificar si se ha seleccionado un objetivo válido
    if ($plan !== null) {
        // Establecer conexión con la base de datos
        $servidor = "localhost";
        $usuario = "root";
        $clave = "";
        $basededatos = "registro";

        $enlace = mysqli_connect($servidor, $usuario, $clave, $basededatos);

        // Verificar si la conexión fue exitosa
        if (!$enlace) {
            die("Error de conexión: " . mysqli_connect_error());
        }

        // Consulta para verificar si ya existe un registro con el mismo nombre
        $consulta_existencia = "SELECT * FROM planseleccionado WHERE nombre = ?";
        $stmt_existencia = mysqli_prepare($enlace, $consulta_existencia);
        mysqli_stmt_bind_param($stmt_existencia, "s", $nombre);
        mysqli_stmt_execute($stmt_existencia);
        mysqli_stmt_store_result($stmt_existencia);

        // Si existe un registro con el mismo nombre, eliminarlo
        if (mysqli_stmt_num_rows($stmt_existencia) > 0) {
            $consulta_eliminar = "DELETE FROM planseleccionado WHERE nombre = ?";
            $stmt_eliminar = mysqli_prepare($enlace, $consulta_eliminar);
            mysqli_stmt_bind_param($stmt_eliminar, "s", $nombre);
            mysqli_stmt_execute($stmt_eliminar);
        }

        // Preparar la consulta SQL para insertar los datos en la tabla planseleccionado
        $consulta_insertar = "INSERT INTO planseleccionado (nombre, peso, altura, plan) VALUES (?, ?, ?, ?)";
        $stmt_insertar = mysqli_prepare($enlace, $consulta_insertar);

        // Verificar si la preparación de la consulta fue exitosa
        if ($stmt_insertar) {
            // Vincular los parámetros a la consulta preparada
            mysqli_stmt_bind_param($stmt_insertar, "sddi", $nombre, $peso, $altura, $plan);

            // Ejecutar la consulta
            if (mysqli_stmt_execute($stmt_insertar)) {
                // Redirigir a index3.html
                header("Location: http://localhost/proyecto/index3.php");
                exit();
            } else {
                // Error al ejecutar la consulta
                echo "Error al generar el plan de alimentación: " . mysqli_error($enlace);
            }

            // Cerrar la consulta preparada
            mysqli_stmt_close($stmt_insertar);
        } else {
            // Error al preparar la consulta
            echo "Error: " . mysqli_error($enlace);
        }

        // Cerrar la conexión a la base de datos
        mysqli_close($enlace);
    } else {
        // Mostrar mensaje de error si no se selecciona un objetivo válido
        echo "Por favor, selecciona un objetivo nutricional válido.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Estilos.css">
    <title>Generar plan de alimentación</title>
</head>
<body>
    
    <form class="formulario_registro" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <h4>Generar plan personalizado</h4>
        <input class="campos" type="number" name="peso" id="peso" placeholder="Peso" required>
        <input class="campos" type="number" step="0.1" name="altura" id="altura" placeholder="Altura" required>
        <input type="hidden" name="nombre" value="<?php echo $_SESSION['nombre']; ?>">
        
        <select class="campos" id="objetivo" name="objetivo" required>
            <option value="" disabled selected>Selecciona tu objetivo nutricional</option>
            <option value="aumentar">Aumentar masa muscular</option>
            <option value="bajar">Perder grasa</option>
            <option value="mantener">Mantener un peso saludable</option>
        </select>

        <button type="submit" class="botones">Aceptar</button>

        <p><a href="index3.php">Volver</a></p> 
    </form>

</body>
</html>