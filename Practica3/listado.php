<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Coches</title>
    <link rel="stylesheet" href="css/listado.css">
</head>
<body>
    <h1>Listado de Coches</h1>
    
    <?php
        include_once("coches.php");
        $fichero = "coches.json";
        $cochesDAO = new CocheDAO($fichero);
        $coches = $cochesDAO->verTodos();
        if (empty($coches)) {
            echo "<p>No hay coches disponibles en este momento.</p>";
        } else {
            echo '<ul class="listado-coches">';
            foreach ($coches as $coche) {
                echo '<li>';
                echo '<strong>Matrícula:</strong> ' . htmlspecialchars($coche['matricula']) . '<br>';
                echo '<strong>Marca:</strong> ' . htmlspecialchars($coche['marca']) . '<br>';
                echo '<strong>Modelo:</strong> ' . htmlspecialchars($coche['modelo']) . '<br>';
                echo '<strong>Potencia:</strong> ' . htmlspecialchars($coche['potencia']) . ' CV<br>';
                echo '<strong>Velocidad Máxima:</strong> ' . htmlspecialchars($coche['vmax']) . ' km/h<br>';
                echo '<img src="' . htmlspecialchars($coche['path_imagen']) . '" alt="Imagen de ' . htmlspecialchars($coche['marca']) . ' ' . htmlspecialchars($coche['modelo']) . '">';
                echo '</li>';
            }
            echo '</ul>';
        }
    ?>
</body>
</html>