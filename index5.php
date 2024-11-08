<?php
    session_start(); // Iniciar sesión si no está iniciada
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Estilos.css">
    <title>Perfil de Usuario</title>
</head>
<body>
    
    <form class="formulario_registro">

        <h4>PERFIL</h4>

        <img class="mmm" src="perfil.png">

        <?php
            if(isset($_SESSION['nombre'])) {
                $nombre = $_SESSION['nombre'];

                $servidor = "localhost";
                $usuario = "root";
                $clave = "";
                $basededatos = "registro";

                $enlace = mysqli_connect($servidor, $usuario, $clave, $basededatos);

                // Verificar la conexión
                if (!$enlace) {
                    die("Error de conexión: " . mysqli_connect_error());
                }

                // Consulta para obtener los datos del usuario
                $consulta = "SELECT nombre, edad, email FROM datos WHERE nombre = ?";
                $stmt = mysqli_prepare($enlace, $consulta);
                mysqli_stmt_bind_param($stmt, "s", $nombre);
                mysqli_stmt_execute($stmt);
                $resultado = mysqli_stmt_get_result($stmt);

                // Mostrar los datos del usuario
                if (mysqli_num_rows($resultado) > 0) {
                    $fila = mysqli_fetch_assoc($resultado);
                    echo "<p>Nombre: " . $fila['nombre'] . "</p>";
                    echo "<br>";
                    echo "<p>Correo: " . $fila['email'] . "</p>";
                    echo "<br>";
                    echo "<p>Edad: " . $fila['edad'] . "</p>";
                    echo "<br>";
                } else {
                    echo "No se encontraron datos.";
                }

                // Liberar el resultado y cerrar la conexión
                mysqli_stmt_close($stmt);
                mysqli_close($enlace);
            } else {
                echo "Debe iniciar sesión para ver su perfil.";
            }
        ?>

        <p><a href="index3.php">Volver</a></p>

    </form>

</body>
</html>
