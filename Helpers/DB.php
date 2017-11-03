<?php
/**
 * Created by PhpStorm.
 * User: christopherdoriott
 * Date: 11/1/17
 * Time: 4:44 PM
 */

include_once 'Sanitizer.php';

class DB {
    const HOST = '127.0.0.1';
    const USERNAME = 'XXXXXXXXX';
    const PASSWORD = 'XXXXXXXXX';
    const DATABASE_NAME = 'XXXXXXXXX';

    protected $connection;
    protected $lastId;


    public function __construct()
    {
        $this->connect();
    }

    /**
     * Connects to the database
     *
     * @throws Exception
     */
    protected function connect() {
        $connection = mysqli_connect(self::HOST, self::USERNAME, self::PASSWORD, self::DATABASE_NAME);
        if (mysqli_connect_errno()) {
            throw new Exception(mysqli_connect_error());
        }

        $this->connection = $connection;
    }

    /**
     * Inserts a record into a given table. The record's Id is returned from the function
     *
     * @param array $dataArray
     * @param string $table
     * @return int
     *
     * @throws Exception
     */
    public function insertRecord(array $dataArray, string $table): int
    {
        if (empty($dataArray)) {
            throw new Exception('Data cannot be empty in insertion');
        }

        if (empty($table)) {
            throw new Exception('Table cannot be empty in insertion');
        }

        $columnArray = array_keys($dataArray);
        $query = "INSERT INTO ".$table." (".implode(',', $columnArray).") VALUES(";
        $dataArray = array_map('Sanitizer::escapeTextForDatabase', $dataArray);

        $query .= '"'.implode('","', $dataArray);
        $query .= '");';

        $runQuery = $this->runQuery($query);

        if (!$runQuery) {
           throw new Exception('Database query failed');
        }

        return mysqli_insert_id($this->connection);
    }

    /**
     * Creates a basic (no JOINS, GROUP BY, etc.) SELECT statement and returns an associative array
     *
     * @param string $table
     * @param array $columns
     * @param array $whereCondition
     *
     * @return array
     */
    public function simpleSelect(string $table, array $columns = ['*'], array $whereCondition = [], $orderBy = ''): array
    {
        $query = 'SELECT '.implode(',', $columns);
        $query .= ' FROM '.$table;
        if (!empty($whereCondition)) {
            $query .= $this->buildWhereCondition($whereCondition);
        }

        if (!empty($orderBy)) {
            $query .= ' ORDER BY '.$orderBy;
        }
        $query .= ';';

        $queryResult = $this->connection->query($query);
        return mysqli_fetch_all($queryResult, MYSQLI_ASSOC);
    }

    /**
     * Builds a WHERE condition for a function given an array of arrays. Each subarray must contain a column,
     * operator (=, LIKE, etc.), and value for comparison
     *
     *
     * @param array $whereCondition
     *
     * @return string
     */
    protected function buildWhereCondition(array $whereCondition): string
    {
        $query = " WHERE ";
        $i=0;
        foreach ($whereCondition as $conditionData) {
            $i++;
            $query .= $conditionData['column']. ' '.$conditionData['operator'].' '.Sanitizer::escapeTextForDatabase($conditionData['value']);

            if ($i != count($whereCondition)) {
                $query .= " AND ";
            }
        }
        return $query;
    }

    /**
     * Runs a SQL Query
     * @param string $query
     *
     * @return bool
     */
    protected function runQuery(string $query): bool
    {
        if ($this->connection->query($query) === true) {
            return true;
        }

        return false;
    }
}