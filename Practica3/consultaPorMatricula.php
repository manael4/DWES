<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar por Matrícula</title>
    <link rel="stylesheet" href="css/consulta.css">
</head>
<body>
    <h1>Consultar por Matrícula</h1>
    
    <?php
        include_once("coches.php");  

        $fichero = "coches.json";
        $cochesDAO = new CocheDAO($fichero);
        $cocheEncontrado = null;
        $matricula = "";

        // Procesar el formulario
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['matriculaConsulta'])) {
            $matricula = htmlspecialchars(trim($_POST['matriculaConsulta']));
            if (!empty($matricula)) {
                $cocheEncontrado = $cochesDAO->obtenerCoche($matricula);
                if ($cocheEncontrado === null) {
                    echo "<p class='error'>No se encontró ningún coche con la matrícula: " . htmlspecialchars($matricula) . ".</p>";
                }
            } else {
                echo "<p class='error'>Por favor, introduce una matrícula válida.</p>";
            }
        }
    ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">  
        <label for="matriculaConsulta">Matrícula:</label>
        <input type="text" id="matriculaConsulta" name="matriculaConsulta" value="<?php echo htmlspecialchars($matricula); ?>" required>
        <input type="submit" name="submit" value="Consultar">
        <input type="reset" name="reset" value="Limpiar">
    </form>

    <?php if ($cocheEncontrado): ?>
        <h2>Información del Coche</h2>
        <ul>
            <li><strong>Matrícula:</strong> <?php echo htmlspecialchars($cocheEncontrado->matricula); ?></li>
            <li><strong>Marca:</strong> <?php echo htmlspecialchars($cocheEncontrado->marca); ?></li>
            <li><strong>Modelo:</strong> <?php echo htmlspecialchars($cocheEncontrado->modelo); ?></li>
            <li><strong>Potencia (CV):</strong> <?php echo htmlspecialchars($cocheEncontrado->potencia); ?></li>
            <li><strong>Velocidad Máxima (km/h):</strong> <?php echo htmlspecialchars($cocheEncontrado->vmax); ?></li>
                <li>
                    <strong>Imagen:</strong><br>
                    <img src="<?php echo htmlspecialchars($cocheEncontrado->path_imagen); ?>" alt="Imagen del coche" style="max-width: 600px; max-height: 400px;">
                </li>
        </ul>
    <?php endif; ?>
</body>
</html>