<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['nombre'])) {
    // Si el usuario no ha iniciado sesión, redirigirlo al formulario de inicio de sesión (index2.php)
    header("Location: http://localhost/proyecto/index2.php");
    exit();
}

// Verificar si se hizo clic en el botón de "Cerrar Sesión"
if (isset($_GET['cerrar_sesion'])) {
    // Destruir todas las variables de sesión
    session_unset();
    // Destruir la sesión
    session_destroy();
    // Redirigir al formulario de inicio de sesión
    header("Location: http://localhost/proyecto/index2.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Estilos.css">
    <title>index3</title>
    <style>
        #titulo {
            position: relative;
            animation: moveText 4s infinite alternate; /* Animación de movimiento */
        }

        @keyframes moveText {
            0% {
                left: 0;
            }
            100% {
                left: 50px; /* Distancia de movimiento */
            }
        }

        #imagen-container {
            text-align: center; /* Para centrar la imagen */
            margin-top: 50px; /* Ajusta este valor según sea necesario */
        }
    </style>
</head>
<body>

<header>
    <nav>
        <h1 id="titulo">TUS PLANES</h1>
        <ul>
            <li><a href="index4.php">GENERAR PLAN DE ALIMENTACION</a></li>
            <li><a href="index5.php">PERFIL</a></li>
            <li><a href="index3.php?cerrar_sesion=true">CERRAR SESIÓN</a></li>
        </ul>
    </nav>
</header>

<!-- Contenedor para la imagen -->
<div id="imagen-container"></div>

<script>
    <?php
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

    // Obtener el nombre de usuario de la sesión actual
    $nombre_usuario = $_SESSION['nombre'];

    // Consultar el plan seleccionado por el usuario
    $consulta = "SELECT plan FROM planseleccionado WHERE nombre = ?";
    $stmt = mysqli_prepare($enlace, $consulta);
    mysqli_stmt_bind_param($stmt, "s", $nombre_usuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $plan);
    mysqli_stmt_fetch($stmt);

    // Verificar si se encontró el nombre con respecto a la variable de sesión
    if ($plan !== null) {
        echo 'console.log("Se encontró el nombre ' . $nombre_usuario . ' en la base de datos");';
    } else {
        echo 'console.log("No se encontró el nombre ' . $nombre_usuario . ' en la base de datos");';
    }

    // Cerrar la conexión a la base de datos y liberar recursos
    mysqli_stmt_close($stmt);
    mysqli_close($enlace);
    ?>

    // Crear un elemento de imagen
    var img = document.createElement("img");

    // Establecer el src de la imagen según el plan obtenido de la base de datos
    switch (<?php echo $plan ?? 0; ?>) {
        case 1:
            img.src = "dietaAumenMM.png";
            img.style.border = "5px solid black"; // Establecer el borde negro
            break;
        case 2:
            img.src = "dietaBajarPeso.jpg";
            img.style.border = "5px solid black"; // Establecer el borde negro
            break;
        case 3:
            img.src = "dietaMantPeso.jpg";
            img.style.border = "5px solid black"; // Establecer el borde negro
            break;
        default:
            // En caso de que no se seleccione ningún plan
            img.src = ""; // No se establece ninguna imagen
    }

    // Establecer el tamaño de la imagen
    img.style.width = "40%";

    // Agregar la imagen al contenedor
    document.getElementById("imagen-container").appendChild(img);
</script>

</body>
</html>


