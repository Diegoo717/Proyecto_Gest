<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Estilos.css">
    <title>Generar plan de alimentación</title>
    <style>
        /* Estilo para el campo de entrada */
        .campos {
            margin-bottom: 5px; /* Reducir el espacio debajo del campo */
        }
        /* Estilo para los mensajes de error */
        .mensaje-error {
            color: red;
            font-size: 12px;
            margin-top: -3px; /* Mueve el mensaje ligeramente hacia arriba */
            margin-bottom: 5px; /* Asegura un pequeño espacio debajo del mensaje */
            line-height: 1;
        }
    </style>
    <script>
        function validarFormulario() {
            let peso = document.getElementById("peso").value;
            let altura = document.getElementById("altura").value;
            let errorPeso = document.getElementById("errorPeso");
            let errorAltura = document.getElementById("errorAltura");
            let esValido = true;

            errorPeso.innerText = "";
            errorAltura.innerText = "";

            if (peso <= 0) {
                errorPeso.innerText = "El peso debe ser mayor a 0.";
                esValido = false;
            }
            if (altura <= 0) {
                errorAltura.innerText = "La altura debe ser mayor a 0.";
                esValido = false;
            }

            return esValido;
        }
    </script>
</head>
<body>
    
    <form class="formulario_registro" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return validarFormulario();">
        <h4>Generar plan personalizado</h4>

        <input class="campos" type="number" name="peso" id="peso" placeholder="Peso" required>
        <div id="errorPeso" class="mensaje-error"></div>

        <input class="campos" type="number" step="0.1" name="altura" id="altura" placeholder="Altura" required>
        <div id="errorAltura" class="mensaje-error"></div>

        <input type="hidden" name="nombre" value="<?php echo $_SESSION['nombre']; ?>">

        <select class="campos" id="genero" name="genero" required>
            <option value="" disabled selected>Genero</option>
            <option value="aumentar">Masculino</option>
            <option value="bajar">Femenino</option>
        </select>

        <select class="campos" id="objetivo" name="objetivo" required>
            <option value="" disabled selected>Selecciona tu objetivo nutricional</option>
            <option value="aumentar">Aumentar masa muscular</option>
            <option value="bajar">Perder grasa</option>
            <option value="mantener">Mantener un peso saludable</option>
        </select>

        <button type="submit" class="botones">Aceptar</button>

        <p><a href="index3.php">Volver</a></p> 

        <?php
        if (isset($error)) {
            echo "<div class='mensaje-error'>$error</div>";
        }
        ?>
    </form>

</body>
</html>
