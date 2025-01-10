<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Coche</title>
    <link rel="stylesheet" href="css/modificar.css">
</head>
<body>
    <h1>Modificar Registro de Coche</h1>
    
    <?php
    include_once("coches.php");
    $fichero = "coches.json";
    $cochesDAO = new CocheDAO($fichero);
    $coches = $cochesDAO->verTodos();
    $cocheSeleccionado = null;
    $fotoErr = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["matriculaSeleccionada"])) {
            $matricula = htmlspecialchars($_POST["matriculaSeleccionada"]);
            $cocheSeleccionado = $cochesDAO->obtenerCoche($matricula);
        } elseif (isset($_POST["actualizar"]) && isset($_POST["matricula"])) {
            $cocheActualizado = new Coche($_POST["matricula"]);
            $cocheActualizado->marca = htmlspecialchars($_POST["marca"]);
            $cocheActualizado->modelo = htmlspecialchars($_POST["modelo"]);
            $cocheActualizado->potencia = intval($_POST["potencia"]);
            $cocheActualizado->vmax = intval($_POST["vmax"]);

            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
                $archivo = $_FILES['foto'];
                $nombreArchivo = $archivo['name'];
                $tipoArchivo = $archivo['type'];
                $tmpArchivo = $archivo['tmp_name'];
                $tamañoArchivo = $archivo['size'];
                $directorioDestino = 'imgs/';
                $fotoDestino = $directorioDestino . $_POST['matricula'] . ".png";
                $tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];

                if (!is_dir($directorioDestino)) {
                    mkdir($directorioDestino, 0777, true);
                }

                if (in_array($tipoArchivo, $tiposPermitidos) && $tamañoArchivo <= 50 * 1024 * 1024) {
                    if (move_uploaded_file($tmpArchivo, $fotoDestino)) {
                        $cocheActualizado->path_imagen = $fotoDestino;
                    } else {
                        $fotoErr = "Error al mover la imagen.";
                    }
                } else {
                    $fotoErr = "El archivo no es válido. Solo se aceptan imágenes JPG, PNG y WebP.";
                }
            } else {
                $cocheActualizado->path_imagen = htmlspecialchars($_POST["path_imagen_actual"]);
            }

            $cochesDAO->actualizar($_POST["matricula"], $cocheActualizado);
            echo "<p>Coche actualizado correctamente.</p>";
        }
    }
    ?>

    <h2>Selecciona el coche que deseas modificar</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <select name="matriculaSeleccionada" required>
            <option value="" disabled selected>Selecciona una matrícula</option>
            <?php
            foreach ($coches as $coche) {
                echo '<option value="' . htmlspecialchars($coche['matricula']) . '">' . htmlspecialchars($coche['matricula']) . '</option>';
            }
            ?>
        </select>
        <input type="submit" value="Seleccionar">
    </form>

    <?php if ($cocheSeleccionado): ?>
        <h2>Modificar datos del coche</h2>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <input type="hidden" name="matricula" value="<?php echo htmlspecialchars($cocheSeleccionado->matricula); ?>">
            <input type="hidden" name="path_imagen_actual" value="<?php echo htmlspecialchars($cocheSeleccionado->path_imagen); ?>">
            
            <label for="marca">Marca:</label>
            <input type="text" id="marca" name="marca" value="<?php echo htmlspecialchars($cocheSeleccionado->marca); ?>" required><br>
            
            <label for="modelo">Modelo:</label>
            <input type="text" id="modelo" name="modelo" value="<?php echo htmlspecialchars($cocheSeleccionado->modelo); ?>" required><br>
            
            <label for="potencia">Potencia (CV):</label>
            <input type="number" id="potencia" name="potencia" value="<?php echo htmlspecialchars($cocheSeleccionado->potencia); ?>" required><br>
            
            <label for="vmax">Velocidad Máxima (km/h):</label>
            <input type="number" id="vmax" name="vmax" value="<?php echo htmlspecialchars($cocheSeleccionado->vmax); ?>" required><br>

            <!-- Mostrar la foto actual del coche -->
            <h3>Foto actual:</h3>
            <div class="foto-container">
                <img class="" src="<?php echo htmlspecialchars($cocheSeleccionado->path_imagen); ?>" alt="Foto del coche" style="max-width: 200px; height: auto;">
            </div>
            <br><br>
            <label for="foto">Nueva Imagen del Coche:</label>
            <input type="file" id="foto" name="foto"><br>
            <span class="error"><?php echo $fotoErr; ?></span><br><br>
            <input type="submit" name="actualizar" value="Actualizar">
        </form>
    <?php endif; ?>
</body>
</html>