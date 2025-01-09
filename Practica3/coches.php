<?php
    class Coche{
        private $matricula,$marca,$modelo,$potencia,$vmax,$path_imagen;
        public function __construct($matricula){
            $this->matricula=$matricula;
            $this->marca="";
            $this->modelo="";
            $this->potencia=0;
            $this->vmax=0;
            $this->path_imagen="";
        }
        public function __set($property, $value) {
            $this->$property = $value;
        }
        public function __get($property) {
            return $this->$property;
        }
        public function __toString(){
            return $this->matricula." ".$this->marca." ".$this->modelo." ".$this->potencia." ".$this->vmax." ".$this->path_imagen."\n";
        }
        public function __toString2(){
            return "Matricula: ".$this->matricula." Marca: ".$this->marca." Modelo: ".$this->modelo." Potencia(CV): ".$this->potencia." Velocidad máxima(Km/h): ".$this->vmax."\n";
        }
    }
    interface ICocheDAO{
        public function crear(Coche $coche);
        public function obtenerCoche($matricula);
        public function eliminar($matricula);
        public function actualizar($matricula, Coche $nuevoCoche);
        public function verTodos();
    }
    class CocheDAO implements ICocheDAO{
        public $archivo;
        public function __construct($archivo){
            $this->archivo=$archivo;
        }
        public function crear(Coche $coche){
            $fallo=false;
            $json_str = file_get_contents($this->archivo);
            if ($json_str === false) {
                die("No se pudo leer el archivo.");
            }
            $coches = [];
            if (!strlen($json_str)==0) {
                $coches = json_decode($json_str, true);
            }
            if (json_last_error() !== JSON_ERROR_NONE) {
                die("Error al decodificar el JSON: " . json_last_error_msg());
            }
            foreach ($coches as $value) {
                if ($value['matricula']===$coche->matricula) {
                    $fallo=true;
                }
            }
            if ($fallo===true) {
                echo("Ya hay un coche con esa misma matricula\n");
            } else {
                array_push($coches,[
                    "matricula" => $coche->matricula,
                    "marca" => $coche->marca,
                    "modelo" => $coche->modelo,
                    "potencia" => $coche->potencia,
                    "vmax" => $coche->vmax,
                    "path_imagen" => $coche->path_imagen
                ]);
                $json_str= json_encode($coches, JSON_PRETTY_PRINT);
                if (file_put_contents($this->archivo, $json_str) === false) {
                    die("No se pudo guardar el archivo JSON.");
                }
                echo "El coche ha sido dado de alta satisfactoriamente\n    ";
            } 
        }
        public function obtenerCoche($matricula){
            $json_str = file_get_contents($this->archivo);
            if ($json_str === false) {
                die("No se pudo leer el archivo.");
            }
            $coches = [];
            if (!strlen($json_str)==0) {
                $coches = json_decode($json_str, true);
            }
            if (json_last_error() !== JSON_ERROR_NONE) {
                die("Error al decodificar el JSON: " . json_last_error_msg());
            }
            foreach ($coches as $value) {
                if ($value['matricula']===$matricula) {
                    $coche=new Coche($matricula);
                    $coche->marca=$value["marca"];
                    $coche->modelo=$value["modelo"];
                    $coche->potencia=$value["potencia"];
                    $coche->vmax=$value["vmax"];
                    $coche->path_imagen=$value["path_imagen"];
                    return $coche;
                }
            }
        }
        public function eliminar($matricula){
            $fallo=true;
            $json_str = file_get_contents($this->archivo);
            if ($json_str === false) {
                die("No se pudo leer el archivo.");
            }
            $coches = [];
            if (!strlen($json_str)==0) {
                $coches = json_decode($json_str, true);
            }
            if (json_last_error() !== JSON_ERROR_NONE) {
                die("Error al decodificar el JSON: " . json_last_error_msg());
            }
            foreach ($coches as $key => $value) {
                if ($value['matricula']==$matricula) {
                    unset($coches[$key]);
                    $fallo=false;
                }
            }
            if($fallo){
                echo "No hay ningún coche con esa matricula\n";
            } else {
                $json_str= json_encode($coches, JSON_PRETTY_PRINT);
                if (file_put_contents($this->archivo, $json_str) === false) {
                    die("No se pudo guardar el archivo JSON.");
                }
                echo "El coche ha sido eliminado satisfactoriamente\n    ";
            }
            
        }
        public function actualizar($matricula, Coche $nuevoCoche){
            $fallo=true;
            $json_str = file_get_contents($this->archivo);
            if ($json_str === false) {
                die("No se pudo leer el archivo.");
            }
            $coches = [];
            if (!strlen($json_str)==0) {
                $coches = json_decode($json_str, true);
            }
            if (json_last_error() !== JSON_ERROR_NONE) {
                die("Error al decodificar el JSON: " . json_last_error_msg());
            }
            foreach ($coches as $key => $value) {
                if ($value['matricula']==$matricula) {
                    $value['marca']=$nuevoCoche->marca;
                    $value['modelo']=$nuevoCoche->modelo;
                    $value['potencia']=$nuevoCoche->potencia;
                    $value['vmax']=$nuevoCoche->vmax;
                    $value['path_imagen']=$nuevoCoche->path_imagen;
                    $fallo=false;
                }
            }
            if($fallo){
                echo "No hay ningún coche con esa matricula\n";
                return null;
            } else {
                $json_str= json_encode($coches, JSON_PRETTY_PRINT);
                if (file_put_contents($this->archivo, $json_str) === false) {
                    die("No se pudo guardar el archivo JSON.");
                }
                echo "El coche ha sido eliminado satisfactoriamente\n    ";
            }
        }
        public function verTodos(){
            $fallo=false;
            $json_str = file_get_contents($this->archivo);
            if ($json_str === false) {
                die("No se pudo leer el archivo.");
            }
            $coches = [];
            if (!strlen($json_str)==0) {
                $coches = json_decode($json_str, true);
            }
            if (json_last_error() !== JSON_ERROR_NONE) {
                die("Error al decodificar el JSON: " . json_last_error_msg());
            }
            return $coches;
        }
        public function __set($property, $value) {
            $this->$property = $value;
        }
        public function __get($property) {
            return $this->$property;
        }
    }
    $p = new CocheDAO('coches.json');
    
    
     
?>
