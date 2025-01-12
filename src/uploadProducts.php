<?php
require "../conexion.php";

// Activar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_FILES["archivo"]["tmp_name"])) {
    $file = $_FILES["archivo"]["tmp_name"];
    $handle = fopen($file, "r");

    if ($handle === false) {
        echo json_encode(["success" => false, "error" => "No se pudo leer el archivo."]);
        exit;
    }

    // Preparar la consulta SQL: Insertar o actualizar sin tocar el stock
    $stmt = $conexion->prepare("
        INSERT INTO producto (codigo, descripcion, embalaje, precio, cantidad)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            descripcion = VALUES(descripcion),
            embalaje = VALUES(embalaje),
            precio = VALUES(precio)
    ");
    $stmt->bind_param("sssdi", $codigo, $descripcion, $embalaje, $precio, $cantidad);

    $rowIndex = 0;

    while (($row = fgetcsv($handle, 1000, ";")) !== false) {
        $rowIndex++;

        // Saltar la primera fila (encabezados)
        if ($rowIndex == 1) continue;

        // Verificar datos mínimos válidos
        if (empty($row[0]) || empty($row[1]) || empty($row[3])) continue;

        // Mapear columnas
        $codigo = trim($row[0]);       // Código
        $descripcion = trim($row[1]);  // Descripción
        $embalaje = trim($row[2]);     // Embalaje
        $precio_str = str_replace(',', '.', preg_replace('/[^\d,]/', '', trim($row[3]))); // Precio
        $cantidad = isset($row[4]) ? intval(trim($row[4])) : 0; // Cantidad inicial

        $precio = floatval($precio_str); // Convertir precio

        // Validar antes de ejecutar
        if (!empty($codigo) && !empty($descripcion) && $precio > 0) {
            $stmt->execute();
        }
    }

    fclose($handle);
    $stmt->close();

    echo json_encode(["success" => true, "message" => "Productos cargados correctamente."]);
} else {
    echo json_encode(["success" => false, "error" => "No se subió ningún archivo."]);
}
?>
