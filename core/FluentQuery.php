<?php

declare(strict_types=1);

class DBQuery
{

    private PDO $pdo;

    private string $baseQuery = '';

    private array $baseParams = [];

    // WHERE personalizados
    private array $whereConditions = [];
    private array $whereParams = [];

    // Search
    private array $searchColumns = [];
    private string $searchTerm = '';

    // Soft delete
    private array $orders = [];

    private array $rawWhere = [];

    private bool $applySoftDelete = false;
    private string $softDeleteColumn = 'deleted_at';

    private ?int $limit = null;
    private ?int $offset = null;
    private array $groups = [];
    private array $selects = [];
    private string $from = '';
    private array $joins = [];

    private bool $lockForUpdate = false;

    private array $havings = [];
    private array $havingBindings = [];

    // Paginación
    private int $page = 1;
    private int $perPage = 15;
    private int $total = 0;
    private int $lastPage = 0;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function whereExists(string $sql): self
    {
        return $this->whereRaw("EXISTS ($sql)");
    }

    public function whereNotExists(string $sql): self
    {
        return $this->whereRaw("NOT EXISTS ($sql)");
    }

    public function having(
        string $column,
        string $operator,
        $value
    ): self {

        $key = 'having_' . count($this->havingBindings);

        $this->havings[] = "{$column} {$operator} :{$key}";

        $this->havingBindings[$key] = $value;

        return $this;
    }


    public function havingRaw(
        string $sql,
        array $bindings = []
    ): self {

        foreach ($bindings as $value) {

            $key = 'having_' . count($this->havingBindings);

            $sql = preg_replace(
                '/\?/',
                ':' . $key,
                $sql,
                1
            );

            $this->havingBindings[$key] = $value;
        }

        $this->havings[] = $sql;

        return $this;
    }

    private function buildHaving(): string
    {
        if (empty($this->havings)) {
            return '';
        }

        return "\nHAVING " . implode(
            ' AND ',
            $this->havings
        );
    }

    public function forUpdate(): self
    {
        $this->lockForUpdate = true;

        return $this;
    }

    public function groupBy(string|array $columns): self
    {
        if (is_array($columns)) {
            foreach ($columns as $column) {
                $this->groups[] = $column;
            }
        } else {
            $this->groups[] = $columns;
        }

        return $this;
    }

    private function buildGroupBy(): string
    {
        if (empty($this->groups)) {
            return '';
        }

        return "\nGROUP BY "
            . implode(', ', $this->groups);
    }

    public function select(array|string $columns = ['*']): self
    {
        if (is_string($columns)) {
            $columns = [$columns];
        }

        $this->selects = $columns;

        return $this;
    }

    public function from(string $table): self
    {
        $this->from = $table;

        return $this;
    }

    public function join(
        string $table,
        string $condition,
        string $type = 'INNER'
    ): self {

        $type = strtoupper($type);

        $allowed = [
            'INNER',
            'LEFT',
            'RIGHT',
            'FULL'
        ];

        if (!in_array($type, $allowed, true)) {
            $type = 'INNER';
        }

        $this->joins[] =
            "{$type} JOIN {$table} ON {$condition}";

        return $this;
    }


    public function leftJoin(
        string $table,
        string $condition
    ): self {
        return $this->join(
            $table,
            $condition,
            'LEFT'
        );
    }

    public function rightJoin(
        string $table,
        string $condition
    ): self {
        return $this->join(
            $table,
            $condition,
            'RIGHT'
        );
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    public function latest(string $column = 'id'): self
    {
        return $this->orderBy($column, 'DESC');
    }

    public function whereLike(
        string $column,
        string $value
    ): self {
        return $this->where(
            $column,
            'LIKE',
            "%{$value}%"
        );
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

    public function get(): array
    {
        [$whereSql, $params] = $this->buildWhereClause();

        $sql = $this->buildQuery()
            . $whereSql
            . $this->buildGroupBy()
            . $this->buildHaving()
            . $this->buildOrderBy();

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->lockForUpdate) {
            $sql .= " FOR UPDATE";
        }

        $stmt = $this->pdo->prepare($sql);

        $this->bindParams($stmt, array_merge($this->baseParams, $params, $this->havingBindings));

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function first(): ?array
    {
        [$whereSql, $params] = $this->buildWhereClause();

        $sql = $this->buildQuery()
            . $whereSql
            . $this->buildGroupBy()
            . $this->buildHaving()
            . $this->buildOrderBy()
            . " LIMIT 1";

        if ($this->lockForUpdate) {
            $sql .= " FOR UPDATE";
        }

        $stmt = $this->pdo->prepare($sql);

        $this->bindParams($stmt, array_merge($this->baseParams, $params, $this->havingBindings));

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function count(): int
    {
        [$whereSql, $params] = $this->buildWhereClause();

        $sql = "
                SELECT COUNT(*)
                FROM (
                    {$this->buildQuery()}
                    {$whereSql}
                    {$this->buildGroupBy()}
                    {$this->buildHaving()}
                ) t
                ";

        $stmt = $this->pdo->prepare($sql);

        $this->bindParams(
            $stmt,
            array_merge(
                $this->baseParams,
                $params,
                $this->havingBindings
            )
        );

        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function exists(): bool
    {
        $clone = clone $this;
        return $clone->count() > 0;
    }


    public function value(string $column): mixed
    {
        $row = $this->first();

        return $row[$column] ?? null;
    }

    public function pluck(string $column): array
    {
        return array_column(
            $this->get(),
            $column
        );
    }

    public function find($id, string $pk = 'id'): ?array
    {
        $clone = clone $this;

        return $clone
            ->where($pk, '=', $id)
            ->first();
    }

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

    public function whereNull(string $column): self
    {
        return $this->where(
            $column,
            'IS NULL',
            null
        );
    }

    public function whereNotNull(string $column): self
    {
        return $this->where(
            $column,
            'IS NOT NULL',
            null
        );
    }

    public function whereIn(
        string $column,
        array $values
    ): self {
        return $this->where(
            $column,
            'IN',
            $values
        );
    }

    public function whereBetween(
        string $column,
        mixed $from,
        mixed $to
    ): self {
        return $this->where(
            $column,
            'BETWEEN',
            [$from, $to]
        );
    }

    public function whereDate(
        string $column,
        string $date
    ): self {
        return $this->whereRaw(
            "DATE($column)=:date",
            [
                "date" => $date
            ]
        );
    }

    public function whereRaw(string $sql, array $params = []): self
    {
        $this->rawWhere[] = [
            'sql' => $sql,
            'params' => $params
        ];

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

    private function buildQuery(): string
    {
        if (!empty($this->baseQuery)) {
            return $this->baseQuery;
        }

        if (empty($this->from)) {
            throw new Exception(
                'No se ha definido la tabla FROM.'
            );
        }

        $sql = 'SELECT ';

        $sql .= empty($this->selects)
            ? '*'
            : implode(",\n", $this->selects);

        $sql .= "\nFROM {$this->from}";

        if (!empty($this->joins)) {
            $sql .= "\n" . implode("\n", $this->joins);
        }

        return $sql;
    }

    /**
     * Soft deletes
     */
    public function softDeletes(string $column = 'deleted_at'): self
    {
        $this->applySoftDelete = true;
        $this->softDeleteColumn = $column;

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

            $this->where($column, '=', $value);
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
     * Construcción WHERE
     */
    private function buildWhereClause(): array
    {
        $conditions = $this->whereConditions;
        $params = $this->whereParams;

        // Agregar WHERE RAW
        if (!empty($this->rawWhere)) {
            foreach ($this->rawWhere as $raw) {

                $conditions[] = '(' . $raw['sql'] . ')';

                foreach ($raw['params'] as $k => $v) {
                    $params[$k] = $v;
                }
            }
        }

        // Search
        if ($this->searchTerm !== '') {

            $search = [];

            foreach ($this->searchColumns as $i => $column) {

                $key = "search_" . $i;

                $search[] = "LOWER({$column}) LIKE LOWER(:{$key})";

                $params[$key] = "%{$this->searchTerm}%";
            }

            $conditions[] = "(" . implode(" OR ", $search) . ")";
        }

        if ($this->applySoftDelete) {
            $conditions[] = "{$this->softDeleteColumn} IS NULL";
        }

        if (empty($conditions)) {
            return ['', []];
        }

        $connector = $this->hasWhere() ? " AND " : " WHERE ";

        return [
            $connector . implode(" AND ", $conditions),
            $params
        ];
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
            $params,
            $this->havingBindings
        );


        $this->calculateTotal(
            $whereSql,
            $finalParams
        );



        $offset = ($this->page - 1) * $this->perPage;


        $sql = "
            {$this->buildQuery()}
            {$whereSql}
            {$this->buildGroupBy()}
            {$this->buildHaving()}
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




    private function calculateTotal(string $whereSql, array $params): void
    {

        $sql = "
            SELECT COUNT(*)
            FROM (
                {$this->buildQuery()}
                {$whereSql}
                {$this->buildGroupBy()}
                {$this->buildHaving()}
            ) total
            ";


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

            // quitar : si viene incluido
            $key = ltrim($key, ':');

            if (is_int($value)) {
                $type = PDO::PARAM_INT;
            } elseif (is_bool($value)) {
                $type = PDO::PARAM_BOOL;
            } elseif ($value === null) {
                $type = PDO::PARAM_NULL;
            } else {
                $type = PDO::PARAM_STR;
            }

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

    public function reset(): self
    {
        $this->baseQuery = '';
        $this->baseParams = [];

        $this->whereConditions = [];
        $this->whereParams = [];

        $this->orders = [];

        $this->rawWhere = [];

        $this->searchColumns = [];
        $this->searchTerm = '';

        $this->applySoftDelete = false;
        $this->softDeleteColumn = 'deleted_at';

        $this->page = 1;
        $this->perPage = 15;
        $this->total = 0;
        $this->lastPage = 0;

        $this->selects = [];
        $this->from = '';
        $this->joins = [];

        $this->limit = null;
        $this->offset = null;
        $this->orders = [];
        $this->groups = [];
        $this->rawWhere = [];

        return $this;
    }
}