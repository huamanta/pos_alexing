<?php
$op = $_GET['op'] ?? '';

switch ($op) {
    case 'listar':
        require_once "../modelos/Contratos.php";
        $contratos = new Contratos();
        $fecha_inicio = $_GET['fecha_inicio'] ?? '';
        $fecha_fin = $_GET['fecha_fin'] ?? '';
        $datos = $contratos->listar($fecha_inicio, $fecha_fin);
        $data = [];
        foreach ($datos as $key => $value) {
            $data[] = [
                "0" => $value['fecha_contrato'],
                "1" => $value['num_documento'],
                "2" => $value['nombre'],
                "3" => $value['tipo'] . str_pad($value['correlativo'], 9, '0', STR_PAD_LEFT),
                "4" => $value['estado'] == 1 ? '<span class="badge badge-success">Vigente</span>' : '<span class="badge badge-danger">Finalizado</span>',
                "5" => $value['formapago'],
                "6" => number_format($value['total_venta'], 2, '.', ','),
                "7" => '<button class="btn btn-info btn-sm" onclick="verContrato(' . $value['idventa'] . ')" title="Ver documentación del contrato"><i class="fa fa-copy"></i></button>
                        <button class="btn btn-primary btn-sm" onclick="descargarContrato(' . $value['idventa'] . ')" title="Descargar contrato"><i class="fa fa-lock"></i></button>
                        <button class="btn btn-secondary btn-sm" onclick="imprimirContrato(' . $value['idventa'] . ')" title="Imprimir contrato"><i class="fa fa-trash"></i></button>',
            ];
        }
        $results = [
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ];
        echo json_encode($results);

        break;
}