<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="css/styles.css" rel="stylesheet">
    </head>
    <body>
        <?php
            include_once("coches.php");
            $fotoDestino = "";
            function test_input($data) {
                $data = trim($data);
                $data = stripslashes($data);
                $data = htmlspecialchars($data);
                return $data;
            }  

            $listado = fopen("marcas_vehiculos.txt","r");
            $listamarca=[];
            while (!feof($listado)) {
                $linea = fgets($listado);
                array_push($listamarca,$linea);
            }

            $matriculaErr = $marcaErr= $modeloErr = $fotoErr = "";
            $matricula = $marca = $modelo = $foto = "";
            $velocidad = $potencia = 0;
            $matriculaOK = $marcaOK = $fotoOK = false;
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST["matricula"])) {
                    if (empty($_POST["matricula"])) {
                        $matriculaErr = "La matrícula no puede estar vacía";
                    } else {
                        if (!preg_match("/^[0-9]{4}[-][A-Z]{3}$/",$_POST["matricula"])) {
                            $matriculaErr = "Matricula no válida";
                            $matricula = test_input($_POST["matricula"]);
                        } else {
                            $matriculaOK = true;
                            $matricula = test_input($_POST["matricula"]);
                        }
                    }
                } else {
                    $matriculaErr = "La matrícula es necesaria";
                }
                if (isset($_POST["marca"])) {
                    if (empty($_POST["marca"])) {
                        $marcaErr = "La marca no puede estar vacía";
                    } else {
                        if (in_array($marca,$listamarca)) {
                            $marcaErr = "Marca no válida";
                        } else {
                            $marcaOK = true;
                            $marca = test_input($_POST["marca"]);
                        }
                    }
                } else {
                    $marcaErr = "La marca es necesaria";
                }
                if (isset($_POST["modelo"])) {
                    if (empty($_POST["modelo"])) {
                        $modelo = "";
                    } else {
                        $modelo = test_input($_POST["modelo"]);
                    }
                } else {
                    $modeloErr = "El modelo es necesario";
                }
                if (isset($_POST["potencia"])) {
                    if (empty($_POST["potencia"])) {
                        $potencia = 0;
                    } else {
                        $potencia = test_input($_POST["potencia"]);
                    }
                } else {
                    $potencia = 0;
                }
                if (isset($_POST["velocidad"])) {
                    if (empty($_POST["velocidad"])) {
                        $velocidad = 0;
                    } else {
                        $velocidad = test_input($_POST["velocidad"]);
                    }
                } else {
                    $velocidad = 0;
                }
                if (isset($_FILES['foto'])) {
                    $archivo = $_FILES['foto'];
                    if ($archivo['error'] == UPLOAD_ERR_OK) {
                        $nombreArchivo = $archivo['name'];
                        $tipoArchivo = $archivo['type'];
                        $tmpArchivo = $archivo['tmp_name'];
                        $tamañoArchivo = $archivo['size'];
                
                        // Directorio donde se almacenará el archivo subido
                        $directorioDestino = 'imgs/';
                
                        // Asegurarse de que el directorio existe
                        if (!is_dir($directorioDestino)) {
                            mkdir($directorioDestino, 0777, true); // Crear el directorio si no existe
                        }
                        $fotoDestino = $directorioDestino . $matricula.".png";
                        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg']; 
                        if (in_array($tipoArchivo, $tiposPermitidos)) {
                            $maxTamaño = 50 * 1024 * 1024; 
                            if ($tamañoArchivo <= $maxTamaño) {
                                if ($matriculaOK && $marcaOK) {
                                    if (move_uploaded_file($tmpArchivo, $fotoDestino)) {
                                        $fotoErr = "El coche se ha registrado correctamente: " . $nombreArchivo;
                                        $fotoOK=true;
                                    } else {
                                        $fotoErr = "Hubo un error al mover la imagen.";
                                    }
                                }
                            } else {
                                $fotoErr = "El archivo es demasiado grande. El tamaño máximo permitido es 2MB.";
                            }
                        } else {
                            $fotoErr = "Tipo de archivo no permitido. Solo se aceptan imágenes JPG, PNG y archivos PDF.";
                        }
                    } else {
                        // Mostrar el error si hubo problemas con la subida
                        $fotoErr = "Error al subir el archivo. Código de error: " . $archivo['error'];
                    }
                } else {
                    $fotoErr = "No se ha recibido ningún archivo.";
                }
                if ($matriculaOK && $marcaOK && $fotoOK) {
                    $coches = new CocheDAO("coches.json");
                    $coche = new Coche($matricula);
                    $coche->marca = $marca;
                    $coche->modelo = $modelo;
                    $coche->vmax = $velocidad;
                    $coche->potencia = $potencia;
                    $coche->path_imagen = $fotoDestino;
                    $coches->crear($coche);
                }
            }
        ?>
        
        <p><span class="error">* Campo requerido</span></p>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">  
            Matrícula <input type="text" name="matricula" value="<?php echo $matricula;?>">
            <span class="error">* <?php echo $matriculaErr;?></span>
            <br><br>
            Marca <span class="error">* <?php echo $marcaErr;?></span>
            <select id="opciones" name="marca" required>
                <option value="">Elige una opción</option>
                <?php
                // Generar las opciones del select recorriendo el array de las marcas del archivo txt
                foreach ($listamarca as $car) {
                    if ($car===$marca) {
                        echo "<option selected value=\"" . htmlspecialchars($car). "\">" . htmlspecialchars($car) . "</option>";
                    } else {
                        echo "<option value=\"" . htmlspecialchars($car). "\">" . htmlspecialchars($car) . "</option>";
                    }
                }
                ?>
            </select>
            <br><br>
            Modelo <input type="text" name="modelo" value="<?php echo $modelo;?>">
            <span class="error"><?php echo $modeloErr;?></span>
            <br><br>
            Potencia <input type="number" name="potencia" value="<?php echo $potencia;?>"> 
            <br><br>
            Velocidad <input type="number" name="velocidad" value="<?php echo $velocidad;?>"> 
            <br><br>
            Imagen del coche <input type="file" name="foto" id="foto" value="<?php echo $foto;?>">
            <span class="error">* <?php echo $fotoErr;?></span>
            <br><br>
            <input type="submit" name="submit" value="Submit">
            <input type="reset" name="reset" value="Reset">  
        </form>
    </body>
</html>