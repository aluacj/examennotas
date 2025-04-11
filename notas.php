<?php
   
function desencriptar($textoEncriptado, $llave) {
    $textoDesencriptado = "";
    for ($i = 0; $i < strlen($textoEncriptado); $i++) {
        $vEntero = ord($textoEncriptado[$i]) - $llave;
        $textoDesencriptado .= chr($vEntero);
    }
    return $textoDesencriptado;
}

if (isset($_POST['crearLlaveMaestra'])) {
    $llaveMaestra = isset($_POST['llaveMaestra']) ? $_POST['llaveMaestra'] : "";

    if ($llaveMaestra !== "") {
        file_put_contents("llaveMaestra.txt", $llaveMaestra);
        echo "<b>Llave maestra creada exitosamente.</b><br>";
    } else {
        echo "<b>Por favor ingresa una llave maestra válida.</b><br>";
    }
}

if (isset($_POST['grabar'])) {
    $nombreArchivo = $_REQUEST['nombre'] . ".txt";
    $notaOriginal = isset($_REQUEST['nota']) ? trim($_REQUEST['nota']) : "";
    $notaEncriptada = isset($_REQUEST['notaEncriptada']) ? trim($_REQUEST['notaEncriptada']) : "";

    $texto = ($notaEncriptada !== "") ? $notaEncriptada : $notaOriginal;
    $llave = ($notaEncriptada !== "") ? (isset($_REQUEST['llave']) ? intval($_REQUEST['llave']) : 0) : 0;

    $ar = fopen($nombreArchivo, "a") or die("Problemas de acceso");
    fputs($ar, $texto . "|||" . $llave . "\n");
    fclose($ar);

    echo "<br><b>SE GUARDÓ CORRECTAMENTE:</b><br>";
    echo "Nota Guardada: " . htmlspecialchars($texto) . "<br>";
}

if (isset($_POST['leer'])) {
    $nombreArchivo = $_REQUEST['Nomnota'] . ".txt";

    if (file_exists($nombreArchivo)) {
        $ar = fopen($nombreArchivo, "r") or die("Problemas de acceso");
        echo "<b>Notas :</b><br>";

        while (!feof($ar)) {
            $linea = fgets($ar);
            $linea = trim($linea);

            if ($linea === "") continue;

            $partes = explode("|||", $linea);

            if (count($partes) === 2) {
                $textoEncriptado = $partes[0];
                echo htmlspecialchars($textoEncriptado) . "<br>";
            } else {
                echo "Error en el formato de la nota: " . htmlspecialchars($linea) . "<br>";
            }
        }

        fclose($ar);
    } else {
        echo "<br><b>No hay archivo con ese nombre.</b><br>";
    }
}

// Desencriptar notas
if (isset($_POST['desencriptar'])) {
    $nombreArchivo = $_REQUEST['Nomnota'] . ".txt";
    $llaveIngresada = isset($_REQUEST['llaveDesencriptar']) ? intval($_REQUEST['llaveDesencriptar']) : null;

    if ($llaveIngresada === null || $llaveIngresada === 0) {
        echo "<b>Por favor ingresa una llave válida para desencriptar.</b><br>";
        return;
    }

    if (file_exists($nombreArchivo)) {
        $ar = fopen($nombreArchivo, "r") or die("Problemas de acceso");
        echo "<b>Notas:</b><br>";

        while (!feof($ar)) {
            $linea = fgets($ar);
            $linea = trim($linea);

            if ($linea === "") continue;

            $partes = explode("|||", $linea);

            if (count($partes) !== 2) {
                echo "Error en el formato de la nota: " . htmlspecialchars($linea) . "<br>";
                continue;
            }

            $textoEncriptado = $partes[0];
            $llaveGuardada = intval($partes[1]);

            if ($llaveGuardada == 0) {
                echo htmlspecialchars($textoEncriptado) . "<br>";
            } elseif ($llaveIngresada === $llaveGuardada) {
                $textoDesencriptado = desencriptar($textoEncriptado, $llaveIngresada);
                echo htmlspecialchars($textoDesencriptado) . "<br>";
            } else {
                if (file_exists("llaveMaestra.txt")) {
                    $llaveMaestra = intval(file_get_contents("llaveMaestra.txt"));

                    if ($llaveIngresada === $llaveMaestra) {
                        $textoDesencriptado = desencriptar($textoEncriptado, $llaveGuardada);
                        echo  htmlspecialchars($textoDesencriptado) . "<br>";
                    } else {
                        echo "Llave incorrecta. No se puede desencriptar esta nota.<br>";
                    }
                } else {
                    echo "No se encontró la llave maestra. Llave incorrecta.<br>";
                }
            }
        }

        fclose($ar);
    } else {
        echo "<br><b>No hay archivo con ese nombre.</b><br>";
    }
}
?>
