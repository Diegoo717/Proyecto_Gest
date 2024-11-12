<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['nombre'])) {
    header("Location: index2.php");
    exit();
}

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

// Obtener el nombre del usuario de la sesión actual
$nombre_usuario = $_SESSION['nombre'];

// Consultar los datos del usuario
$consulta = "SELECT datos.nombre, datos.email, datos.edad, perfil_usuario.imagen_perfil 
             FROM datos 
             LEFT JOIN perfil_usuario ON datos.id = perfil_usuario.usuario_id 
             WHERE datos.nombre = ?";
$stmt = mysqli_prepare($enlace, $consulta);
mysqli_stmt_bind_param($stmt, "s", $nombre_usuario);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $nombre, $email, $edad, $imagen_perfil);
mysqli_stmt_fetch($stmt);

// Si no hay una imagen de perfil, usar una predeterminada
$imagen_perfil = $imagen_perfil ?? 'perfil.png';

mysqli_stmt_close($stmt);

// Inicializar variable para el mensaje de error
$errorMensaje = "";

// Procesar la imagen si se sube una nueva
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['imagen']) && !empty($_FILES['imagen']['name'])) {
        $nombreArchivo = $_FILES['imagen']['name'];
        $tempArchivo = $_FILES['imagen']['tmp_name'];
        $directorioDestino = "uploads/";

        // Crear el directorio si no existe
        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0777, true);
        }

        $rutaDestino = $directorioDestino . $nombreArchivo;

        // Mover el archivo cargado al directorio de destino
        if (move_uploaded_file($tempArchivo, $rutaDestino)) {
            // Actualizar la ruta en la base de datos
            $consulta = "INSERT INTO perfil_usuario (usuario_id, imagen_perfil) 
                         VALUES ((SELECT id FROM datos WHERE nombre = ?), ?) 
                         ON DUPLICATE KEY UPDATE imagen_perfil = ?";
            $stmt = mysqli_prepare($enlace, $consulta);
            mysqli_stmt_bind_param($stmt, "sss", $nombre_usuario, $rutaDestino, $rutaDestino);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['perfil'] = $rutaDestino; // Actualizar la sesión con la nueva imagen
                $imagen_perfil = $rutaDestino; // Actualizar la imagen mostrada
            } else {
                $errorMensaje = "Error al guardar la imagen en la base de datos.";
            }

            mysqli_stmt_close($stmt);

            // Recargar la página para mostrar la nueva imagen
            header("Location: index5.php");
            exit();
        } else {
            $errorMensaje = "Error al mover el archivo.";
        }
    }
}

// Cerrar la conexión
mysqli_close($enlace);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: white;

            /* Fondo degradado animado basado en verde */
            background: linear-gradient(-45deg, #008259, #00a972, #006f4b, #004a31);
            background-size: 400% 400%;
            animation: gradientBG 10s ease infinite;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        header {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
        }

        header h1 {
            margin: 0;
            font-size: 24px;
        }

        header nav ul {
            list-style: none;
            padding: 0;
            margin: 10px 0 0;
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        header nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            transition: color 0.3s;
        }

        header nav ul li a:hover {
            color: #00a972;
        }

        .perfil-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: rgba(0, 0, 0, 0.95);
            border-radius: 15px;
            padding: 40px 30px;
            max-width: 450px;
            margin: 50px auto;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.7);
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            object-fit: cover;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.5);
        }

        .datos-usuario {
            margin-top: 20px;
            font-size: 18px;
            text-align: center;
            line-height: 1.6;
        }

        .file-upload-button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            text-align: center;
        }

        .file-upload-button:hover {
            background-color: #0056b3;
            transform: scale(1.1);
        }

        input[type="file"] {
            display: none;
        }

        .error {
            color: red;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Perfil de Usuario</h1>
        <nav>
            <ul>
                <li><a href="index3.php">Inicio</a></li>
                <li><a href="index3.php?cerrar_sesion=true">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <div class="perfil-container">
        <img src="<?= htmlspecialchars($imagen_perfil) ?>" alt="Foto de Perfil" class="profile-picture">
        <div class="datos-usuario">
            <p><strong>Nombre:</strong> <?= htmlspecialchars($nombre) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
            <p><strong>Edad:</strong> <?= htmlspecialchars($edad) ?></p>
        </div>
        <form action="index5.php" method="post" enctype="multipart/form-data" id="uploadForm">
            <input type="file" name="imagen" id="imagen" accept=".jpg,.jpeg,.png" onchange="document.getElementById('uploadForm').submit();">
            <button type="button" class="file-upload-button" onclick="document.getElementById('imagen').click();">
                Cambiar Imagen de Perfil
            </button>
        </form>

        <?php if (!empty($errorMensaje)): ?>
            <p class="error"><?= htmlspecialchars($errorMensaje) ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
