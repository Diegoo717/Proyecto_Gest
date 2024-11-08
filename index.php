<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Estilos.css">
    <title>Formulario de Registro Usuario</title>
    <style>
        .error-message {
            color: red;
            margin-top: 1px;
            margin-bottom: 20px; 
            font-size: 14px;
            display: none;
        }
    </style>
    <script>
        function validarFormulario() {
            var contraseña = document.getElementById("contraseña").value;
            var confirContraseña = document.getElementById("confir_contraseña").value;
            var email = document.getElementById("correo").value;
            var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            var emailError = document.getElementById("email-error");
            var passwordError = document.getElementById("password-error");

            emailError.innerHTML = ""; // Limpiar mensaje previo
            passwordError.innerHTML = ""; // Limpiar mensaje previo
            emailError.style.display = "none"; // Ocultar mensaje previo
            passwordError.style.display = "none"; // Ocultar mensaje previo

            var valid = true;

            if (!emailPattern.test(email)) {
                emailError.innerHTML = "Por favor, ingresa un correo electrónico válido.";
                emailError.style.display = "block";
                valid = false;
            }

            if (contraseña !== confirContraseña) {
                passwordError.innerHTML = "Las contraseñas no coinciden. Por favor, intenta de nuevo.";
                passwordError.style.display = "block";
                valid = false;
            }

            return valid;
        }
    </script>
</head>
<body>

    <form name="registro" class="formulario_registro" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return validarFormulario();">

        <h4>Registro Usuario</h4>
        <input class="campos" type="text" name="nombre" id="nombre" placeholder="Ingresar nombre" required> 
        <input class="campos" type="email" name="correo" id="correo" placeholder="Ingresar correo" required>
        <div id="email-error" class="error-message"></div>
        <input class="campos" type="password" name="contraseña" id="contraseña" placeholder="Ingresar contraseña" required>
        <input class="campos" type="password" name="confir_contraseña" id="confir_contraseña" placeholder="Confirmar contraseña" required>
        <div id="password-error" class="error-message"></div>
        <input class="campos" type="number" name="edad" id="edad" placeholder="Edad" required>
        <p><input type="checkbox" id="terminos_condiciones" required> Estoy de acuerdo con los <a href="https://www.nutricionenforma.com/terminos-y-condiciones/">términos y condiciones</a></p>
        <p></p>
        <input class="botones" type="submit" name="aceptar" id="submitBtn" value="Registrar">

        <p><a href="index2.php">¿Ya tengo cuenta?</a></p>
        <p></p>
        <p><a href="index.html">Volver</a></p> 

    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $servidor = "localhost";
        $usuario = "root";
        $clave = "";
        $basededatos = "registro";

        $enlace = mysqli_connect($servidor, $usuario, $clave, $basededatos);

        if (!$enlace) {
            die("Conexión fallida: " . mysqli_connect_error());
        }

        $nombre = $_POST['nombre'];
        $email = $_POST['correo']; 
        $contraseña = $_POST['contraseña'];
        $edad = $_POST['edad'];

        $insertarDatos = "INSERT INTO datos (nombre, email, contraseña, edad) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($enlace, $insertarDatos);
        
        mysqli_stmt_bind_param($stmt, "sssi", $nombre, $email, $contraseña, $edad);
        
        $ejecutarInsertar = mysqli_stmt_execute($stmt);

        if ($ejecutarInsertar) {
            echo "Registro exitoso.";
            header("Location: http://localhost/proyecto/index.html");
        } else {
            echo "Error al registrar usuario: " . mysqli_error($enlace);
        }

        mysqli_stmt_close($stmt);
        mysqli_close($enlace);
    }
    ?>

</body>
</html>
