<?php

declare(strict_types=1);

class FluentPaginator
{
    private PDO $pdo;

    private string $baseQuery = '';

    private array $baseParams = [];

    // WHERE personalizados
    private array $whereConditions = [];
    private array $whereParams = [];

    // Filtros dinámicos
    private array $filters = [];

    // Search
    private array $searchColumns = [];
    private string $searchTerm = '';

    // Soft delete
    private array $orders = [];

    // Paginación
    private int $page = 1;
    private int $perPage = 15;
    private int $total = 0;
    private int $lastPage = 0;


    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    /**
     * Query principal
     */
    public function query(string $sql, array $params = []): self
    {
        $this->baseQuery = trim($sql);
        $this->baseParams = $params;

        return $this;
    }


    /**
     * WHERE personalizado
     *
     * Ej:
     * ->where(
     *     "u.estado = :estado",
     *     ["estado"=>1]
     * )
     */
    /**
     * WHERE dinámico con operador
     *
     * Ej:
     * ->where("estado", "=", 1)
     * ->where("precio", ">=", 100)
     */
    public function where(
        string $column,
        string $operator,
        mixed $value
    ): self {

        $key = "where_" . count($this->whereConditions);


        switch (strtoupper($operator)) {


            case 'IN':

                $placeholders = [];

                foreach ($value as $index => $item) {

                    $param = $key . "_" . $index;

                    $placeholders[] = ":" . $param;

                    $this->whereParams[$param] = $item;
                }


                $this->whereConditions[] =
                    "{$column} IN (" .
                    implode(",", $placeholders) .
                    ")";

                break;



            case 'BETWEEN':

                $param1 = $key . "_from";
                $param2 = $key . "_to";


                $this->whereConditions[] =
                    "{$column} BETWEEN :{$param1} AND :{$param2}";


                $this->whereParams[$param1] = $value[0];
                $this->whereParams[$param2] = $value[1];

                break;



            case 'IS NULL':
            case 'IS NOT NULL':

                $this->whereConditions[] =
                    "{$column} {$operator}";

                break;



            default:

                $this->whereConditions[] =
                    "{$column} {$operator} :{$key}";


                $this->whereParams[$key] = $value;

                break;
        }


        return $this;
    }

    public function orderBy(
        string $column,
        string $direction = 'ASC'
    ): self {

        $direction = strtoupper($direction);

        if (!in_array($direction, ['ASC', 'DESC'], true)) {
            $direction = 'ASC';
        }

        $this->orders[] = "{$column} {$direction}";

        return $this;
    }


    private function buildOrderBy(): string
    {
        if (empty($this->orders)) {
            return '';
        }

        return ' ORDER BY ' . implode(', ', $this->orders);
    }


    /**
     * Soft deletes
     */
    public function withSoftDeletes(string $column = 'deleted_at'): self
    {
        $this->whereConditions[] = "{$column} IS NULL";

        return $this;
    }


    /**
     * Filtros exactos
     *
     * Ej:
     * ->filter([
     *   "u.estado"=>1
     * ])
     */
    public function filter(array $conditions): self
    {
        foreach ($conditions as $column => $value) {

            if ($value === '' || $value === null) {
                continue;
            }

            $key = "filter_" . count($this->filters);

            $this->where($key, '=', $value);
        }

        return $this;
    }


    /**
     * Búsqueda global
     */
    public function search(string $term, array $columns): self
    {
        $term = trim($term);

        if ($term !== '' && !empty($columns)) {

            $this->searchTerm = $term;
            $this->searchColumns = $columns;
        }

        return $this;
    }



    /**
     * Ejecuta paginado
     */
    public function paginate(
        int $page = 1,
        int $perPage = 15
    ): array {


        $this->page = max(1, $page);

        $this->perPage = max(
            1,
            min(100, $perPage)
        );


        [
            $whereSql,
            $params
        ] = $this->buildWhereClause();

        $orderSql = $this->buildOrderBy();



        $finalParams = array_merge(
            $this->baseParams,
            $params
        );


        $this->calculateTotal(
            $whereSql,
            $finalParams
        );



        $offset = ($this->page - 1) * $this->perPage;


        $sql = "
            {$this->baseQuery}
            {$whereSql}
            {$orderSql}
            LIMIT :limit
            OFFSET :offset
        ";


        $stmt = $this->pdo->prepare($sql);


        $this->bindParams(
            $stmt,
            $finalParams
        );


        $stmt->bindValue(
            ':limit',
            $this->perPage,
            PDO::PARAM_INT
        );


        $stmt->bindValue(
            ':offset',
            $offset,
            PDO::PARAM_INT
        );


        $stmt->execute();


        return [

            "data" => $stmt->fetchAll(PDO::FETCH_ASSOC),

            "meta" => [

                "current_page" => $this->page,

                "last_page" => $this->lastPage,

                "per_page" => $this->perPage,

                "total" => $this->total,

                "from" =>
                    $this->total
                    ? (($this->page - 1) * $this->perPage) + 1
                    : null,

                "to" =>
                    min(
                        $this->page * $this->perPage,
                        $this->total
                    )
            ]
        ];
    }



    /**
     * Construcción WHERE
     */
    private function buildWhereClause(): array
    {

        $conditions = $this->whereConditions;

        $params = $this->whereParams;



        /**
         * Search
         */
        if ($this->searchTerm !== '') {

            $search = [];

            foreach ($this->searchColumns as $i => $column) {

                $key = "search_" . $i;


                $search[] =
                    "LOWER({$column})
                     LIKE LOWER(:{$key})";


                $params[$key] =
                    "%{$this->searchTerm}%";
            }


            $conditions[] =
                "(" . implode(
                    " OR ",
                    $search
                ) . ")";
        }



        if (empty($conditions)) {
            return [
                '',
                []
            ];
        }



        $connector =
            $this->hasWhere()
            ? " AND "
            : " WHERE ";



        return [

            $connector .
            implode(
                " AND ",
                $conditions
            ),

            $params
        ];
    }




    private function calculateTotal(string $whereSql, array $params): void
    {

        $sql = "SELECT COUNT(*) 
             FROM (
                {$this->baseQuery}
                {$whereSql}
             ) AS total";


        $stmt = $this->pdo->prepare($sql);



        $this->bindParams(
            $stmt,
            $params
        );


        $stmt->execute();



        $this->total =
            (int) $stmt->fetchColumn();


        $this->lastPage =
            (int) ceil(
                $this->total /
                $this->perPage
            );


        if (
            $this->page > $this->lastPage
            &&
            $this->lastPage > 0
        ) {
            $this->page = $this->lastPage;
        }
    }



    private function bindParams(
        PDOStatement $stmt,
        array $params
    ): void {


        foreach ($params as $key => $value) {

            $type =
                is_int($value)
                ? PDO::PARAM_INT
                : PDO::PARAM_STR;


            $stmt->bindValue(
                ":" . $key,
                $value,
                $type
            );
        }
    }



    private function hasWhere(): bool
    {
        return preg_match(
            '/\bWHERE\b/i',
            $this->baseQuery
        ) === 1;
    }
}