<?php

namespace WAFWork\Database;

class Connection
{
    /**
     * The PDO instance
     *
     * @var \PDO
     */
    protected $pdo;

    /**
     * The default fetch mode
     *
     * @var int
     */
    protected $fetchMode = \PDO::FETCH_ASSOC;

    /**
     * Create a new database connection
     *
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param array $options
     */
    public function __construct($dsn, $username = null, $password = null, array $options = [])
    {
        // Set default options
        $options = array_merge([
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => $this->fetchMode,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ], $options);
        
        try {
            $this->pdo = new \PDO($dsn, $username, $password, $options);
        } catch (\PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Get the PDO instance
     *
     * @return \PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * Run a select query
     *
     * @param string $query
     * @param array $params
     * @return array
     */
    public function select($query, array $params = [])
    {
        $statement = $this->executeQuery($query, $params);
        
        return $statement->fetchAll($this->fetchMode);
    }

    /**
     * Run a select query and get the first result
     *
     * @param string $query
     * @param array $params
     * @return mixed
     */
    public function selectOne($query, array $params = [])
    {
        $statement = $this->executeQuery($query, $params);
        
        return $statement->fetch($this->fetchMode);
    }

    /**
     * Run an insert query
     *
     * @param string $table
     * @param array $data
     * @return int|bool
     */
    public function insert($table, array $data)
    {
        $columns = array_keys($data);
        $placeholders = array_map(function ($column) {
            return ':' . $column;
        }, $columns);
        
        $query = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
        
        $statement = $this->executeQuery($query, $data);
        
        return $this->pdo->lastInsertId();
    }

    /**
     * Run an update query
     *
     * @param string $table
     * @param array $data
     * @param array $conditions
     * @return int
     */
    public function update($table, array $data, array $conditions)
    {
        $set = [];
        $where = [];
        
        foreach ($data as $column => $value) {
            $set[] = $column . ' = :' . $column;
        }
        
        foreach ($conditions as $column => $value) {
            $where[] = $column . ' = :where_' . $column;
            $data['where_' . $column] = $value;
        }
        
        $query = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $table,
            implode(', ', $set),
            implode(' AND ', $where)
        );
        
        $statement = $this->executeQuery($query, $data);
        
        return $statement->rowCount();
    }

    /**
     * Run a delete query
     *
     * @param string $table
     * @param array $conditions
     * @return int
     */
    public function delete($table, array $conditions)
    {
        $where = [];
        $params = [];
        
        foreach ($conditions as $column => $value) {
            $where[] = $column . ' = :' . $column;
            $params[$column] = $value;
        }
        
        $query = sprintf(
            'DELETE FROM %s WHERE %s',
            $table,
            implode(' AND ', $where)
        );
        
        $statement = $this->executeQuery($query, $params);
        
        return $statement->rowCount();
    }

    /**
     * Run a raw query
     *
     * @param string $query
     * @param array $params
     * @return \PDOStatement
     */
    public function query($query, array $params = [])
    {
        return $this->executeQuery($query, $params);
    }

    /**
     * Execute a query
     *
     * @param string $query
     * @param array $params
     * @return \PDOStatement
     */
    protected function executeQuery($query, array $params = [])
    {
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);
        
        return $statement;
    }

    /**
     * Begin a transaction
     *
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit a transaction
     *
     * @return bool
     */
    public function commit()
    {
        return $this->pdo->commit();
    }

    /**
     * Rollback a transaction
     *
     * @return bool
     */
    public function rollBack()
    {
        return $this->pdo->rollBack();
    }

    /**
     * Get a record by ID
     *
     * @param string $table
     * @param mixed $id
     * @param string $primaryKey
     * @return array|null
     */
    public function find($table, $id, $primaryKey = 'id')
    {
        $query = "SELECT * FROM {$table} WHERE {$primaryKey} = :id LIMIT 1";
        
        return $this->selectOne($query, ['id' => $id]);
    }

    /**
     * Get all records from a table
     *
     * @param string $table
     * @return array
     */
    public function all($table)
    {
        $query = "SELECT * FROM {$table}";
        
        return $this->select($query);
    }

    /**
     * Get records by a where clause
     *
     * @param string $table
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @return array
     */
    public function where($table, $column, $operator, $value)
    {
        $query = "SELECT * FROM {$table} WHERE {$column} {$operator} :value";
        
        return $this->select($query, ['value' => $value]);
    }
} 