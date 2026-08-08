<?php
require_once __DIR__ . "/Helpers.php";
require_once __DIR__ . "/../core/Response.php";
class OrdenTrabajo extends Helpers
{
    public function __construct()
    {
        parent::__construct();
    }


    public function selectPersonal($idsucursal)
    {
        $page = (int) ($_GET['page'] ?? 1);
        $limit = (int) ($_GET['limit'] ?? 10);
        $search = trim($_GET['search'] ?? '');

        $data = (new DBQuery($this->pdo))
            ->select('*')
            ->from('personal')
            ->search($search, ['nombre', 'num_documento'])
            ->paginate(
                $page,
                $limit
            );

        return Response::json($data);
    }
}