<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Estilos.css">
    <title>Formulario Inicio de sesión</title>
    <style>
        .error-message {
            color: red;
            margin-top: 5px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    
    <form class="formulario_IS" method="post" action="">
        <h4>Iniciar sesión</h4>
        <input class="campos" type="text" name="nombre" id="nombre" placeholder="Ingresar nombre" required> 
        <input class="campos" type="password" name="contraseña" id="contraseña" placeholder="Ingresar contraseña" required>
        <input type="submit" value="Ingresar" class="botones">

        <div id="error-message" class="error-message"></div>

        <p>¿No tienes cuenta? <a href="http://localhost/proyecto/index.php">¡REGISTRATE AQUI!</a></p>
        <p></p>
        <p><a href="index.html">Volver</a></p> 

    </form>

    <?php
    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $servidor = "localhost";
        $usuario = "root";
        $clave = "";
        $basededatos = "registro";

        $enlace = mysqli_connect($servidor, $usuario, $clave, $basededatos);

        $nombre = $_POST['nombre'];
        $contraseña = $_POST['contraseña'];

        // Modificamos la consulta para que sea insensible a mayúsculas y minúsculas
        $consulta = "SELECT * FROM datos WHERE LOWER(nombre) = LOWER(?) AND contraseña = ?";
        $stmt = mysqli_prepare($enlace, $consulta);

        mysqli_stmt_bind_param($stmt, "ss", $nombre, $contraseña);

        mysqli_stmt_execute($stmt);

        $resultado = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($resultado) == 1) {
            // Inicio de sesión exitoso
            // Establecer una variable de sesión para el nombre de usuario
            $_SESSION['nombre'] = $nombre;
            
            // Redirigir al usuario a otra página
            header("Location: http://localhost/proyecto/index3.php");
            exit(); // Asegura que el script se detenga después de la redirección
        } else {
            // Usuario o contraseña incorrectos
            echo '<script>
                    document.getElementById("error-message").innerHTML = "Usuario o contraseña incorrectos.";
                    document.getElementById("error-message").style.display = "block";
                  </script>';
        }

        // Cerrar la conexión y liberar recursos
        mysqli_stmt_close($stmt);
        mysqli_close($enlace);
    }
    ?>


</body>
</html>

