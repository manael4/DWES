<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baja</title>
    <link rel="stylesheet" href="css/baja.css">
</head>
<body>
    <h1>Dar de Baja</h1> 
    
    <?php
        include_once("coches.php");  

        function test_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        $lista = [];
        $fichero = "coches.json";
        $json_str = file_get_contents($fichero);
        if ($json_str === false) {
            die("No se pudo leer el archivo.");
        }
        $acoche = json_decode($json_str, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            die("Error al decodificar el JSON: " . json_last_error_msg());
        }
        foreach ($acoche as $value) {
            array_push($lista, $value['matricula']);
        }
        ?>
        <h2>Selecciona los coches que quieres dar de baja</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
        <?php 
            $arrayEliminar = [];
            foreach ($lista as $car): ?>
            <input type="checkbox" id="lista" name="lista[]" value="<?php echo htmlspecialchars($car);?>">
            <label for="<?php echo htmlspecialchars($car); ?>"><?php echo htmlspecialchars($car); ?></label><br>
        <?php endforeach; ?>
            <input type="submit" name="submit" value="Submit">
            <input type="reset" name="reset" value="Reset">
        </form>
        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (empty($_POST['lista'])) {
                    $listaErr = "Selecciona una matricula para poder dar de baja el coche.";
                    echo $listaErr;
                } else {
                    $cocheOne = new CocheDAO($fichero);
                    array_push($arrayEliminar, ...$_POST['lista']);
                    foreach ($arrayEliminar as $value) {
                        $cocheOne->eliminar($value);
                        echo $value;
                    }
                }
            }
        ?>
</body>
</html>