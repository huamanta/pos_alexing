<?php
// get_ubigeo_sample.php
require_once __DIR__ . '/../configuraciones/Conexion.php';

// Assuming ejecutarConsulta and limpiarCadena are globally available as per Conexion.php

function getSampleData($tableName, $limit = 5) {
    global $conexion; // Access the global connection object

    $results = [];
    if ($tableName === 'ubigeo_peru_provinces') {
        $sql = "SELECT id, name, department_id FROM ubigeo_peru_provinces LIMIT " . (int)$limit;
    } elseif ($tableName === 'ubigeo_peru_districts') {
        $sql = "SELECT id, name, province_id FROM ubigeo_peru_districts LIMIT " . (int)$limit;
    } else {
        return "Invalid table name.";
    }

    $query = ejecutarConsulta($sql);
    if ($query) {
        while ($row = $query->fetch_assoc()) {
            $results[] = $row;
        }
    }
    return $results;
}

echo "--- ubigeo_peru_provinces Sample ---\\n";
$provinces = getSampleData('ubigeo_peru_provinces');
foreach ($provinces as $row) {
    echo "ID: " . $row['id'] . ", Name: " . $row['name'] . ", Department ID: " . $row['department_id'] . "\\n";
}

echo "\\n--- ubigeo_peru_districts Sample ---\\n";
$districts = getSampleData('ubigeo_peru_districts');
foreach ($districts as $row) {
    echo "ID: " . $row['id'] . ", Name: " . $row['name'] . ", Province ID: " . $row['province_id'] . "\\n";
}

$conexion->close();
?>
