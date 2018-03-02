<?php
/**
 * @author Gevorg Mansuryan
 * @copyright 2018
 * @package zend-db-schema-info
 */

namespace Gevman\DbSchemaInfo;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;

class Schema
{
// The following are the supported abstract column data types.
    const TYPE_PK = 'pk';
    const TYPE_UPK = 'upk';
    const TYPE_BIGPK = 'bigpk';
    const TYPE_UBIGPK = 'ubigpk';
    const TYPE_CHAR = 'char';
    const TYPE_STRING = 'string';
    const TYPE_TEXT = 'text';
    const TYPE_SMALLINT = 'smallint';
    const TYPE_INTEGER = 'integer';
    const TYPE_BIGINT = 'bigint';
    const TYPE_FLOAT = 'float';
    const TYPE_DOUBLE = 'double';
    const TYPE_DECIMAL = 'decimal';
    const TYPE_DATETIME = 'datetime';
    const TYPE_TIMESTAMP = 'timestamp';
    const TYPE_TIME = 'time';
    const TYPE_DATE = 'date';
    const TYPE_BINARY = 'binary';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_MONEY = 'money';

    public $typeMap = [
        'tinyint' => self::TYPE_SMALLINT,
        'bit' => self::TYPE_INTEGER,
        'smallint' => self::TYPE_SMALLINT,
        'mediumint' => self::TYPE_INTEGER,
        'int' => self::TYPE_INTEGER,
        'integer' => self::TYPE_INTEGER,
        'bigint' => self::TYPE_BIGINT,
        'float' => self::TYPE_FLOAT,
        'double' => self::TYPE_DOUBLE,
        'real' => self::TYPE_FLOAT,
        'decimal' => self::TYPE_DECIMAL,
        'numeric' => self::TYPE_DECIMAL,
        'tinytext' => self::TYPE_TEXT,
        'mediumtext' => self::TYPE_TEXT,
        'longtext' => self::TYPE_TEXT,
        'longblob' => self::TYPE_BINARY,
        'blob' => self::TYPE_BINARY,
        'text' => self::TYPE_TEXT,
        'varchar' => self::TYPE_STRING,
        'string' => self::TYPE_STRING,
        'char' => self::TYPE_CHAR,
        'datetime' => self::TYPE_DATETIME,
        'year' => self::TYPE_DATE,
        'date' => self::TYPE_DATE,
        'time' => self::TYPE_TIME,
        'timestamp' => self::TYPE_TIMESTAMP,
        'enum' => self::TYPE_STRING,
        'varbinary' => self::TYPE_BINARY,
    ];

    private $dbAdapter;

    public function __construct(Adapter $dbAdapter) {
        $this->dbAdapter = $dbAdapter;
    }

    /**
     * @return array
     */
    public function getTables()
    {
        $sql = 'SHOW TABLES';

        $statement = $this->dbAdapter->createStatement($sql);
        $statement->prepare();

        $tables = [];
        foreach ($statement->execute() as $row) {
            $tables[] = array_shift($row);
        }

        return $tables;
    }

    /**
     * @param $table
     * @return array|bool
     * @throws \Exception
     */
    public function getTableColumns($table)
    {
        $sql = 'SHOW FULL COLUMNS FROM ' . $table;
        try {
            $statement = $this->dbAdapter->createStatement($sql);
            $statement->prepare();
            $columns = $statement->execute();
        } catch (\Exception $e) {
            $previous = $e->getPrevious();
            if ($previous instanceof \PDOException && strpos($previous->getMessage(), 'SQLSTATE[42S02') !== false) {
                // table does not exist
                // https://dev.mysql.com/doc/refman/5.5/en/error-messages-server.html#error_er_bad_table_error
                return false;
            }
            throw $e;
        }

        $result = [];

        foreach ($columns as $info) {
            $info = array_change_key_case($info, CASE_LOWER);
            $column = $this->loadColumnSchema($info);
            $result[$column->getName()] = $column;
        }

        return $result;
    }

    private function loadColumnSchema($info)
    {
        $column = new ColumnEntity();

        $column->setName($info['field']);
        $column->setAllowNull($info['null'] === 'YES');
        $column->setIsPrimaryKey(strpos($info['key'], 'PRI') !== false);
        $column->setAutoIncrement(stripos($info['extra'], 'auto_increment') !== false);
        $column->setComment($info['comment']);

        $column->setDbType($info['type']);
        $column->setUnsigned(stripos($column->getDbType(), 'unsigned') !== false);

        $column->setType(self::TYPE_STRING);
        if (preg_match('/^(\w+)(?:\(([^\)]+)\))?/', $column->getDbType(), $matches)) {
            $type = strtolower($matches[1]);
            if (isset($this->typeMap[$type])) {
                $column->setType($this->typeMap[$type]);
            }
            if (!empty($matches[2])) {
                if ($type === 'enum') {
                    preg_match_all("/'[^']*'/", $matches[2], $values);
                    foreach ($values[0] as $i => $value) {
                        $values[$i] = trim($value, "'");
                    }
                    $column->setEnumValues($values);
                } else {
                    $values = explode(',', $matches[2]);
                    $column->setSize((int) $values[0]);
                    $column->setPrecision((int) (int) $values[0]);
                    if (isset($values[1])) {
                        $column->setScale((int) $values[1]);
                    }
                    if ($column->getSize() === 1 && $type === 'bit') {
                        $column->setType('boolean');
                    } elseif ($type === 'bit') {
                        if ($column->getSize() > 32) {
                            $column->setType(self::TYPE_BIGINT);
                        } elseif ($column->getSize() === 32) {
                            $column->setType(self::TYPE_INTEGER);
                        }
                    }
                }
            }
        }

        $column->setPhpType($this->getColumnPhpType($column));

        if (!$column->isPrimaryKey()) {
            if ($column->getType() === 'timestamp' && $info['default'] === 'CURRENT_TIMESTAMP') {
                $column->setDefaultValue(new Expression('CURRENT_TIMESTAMP'));
            } elseif (isset($type) && $type === 'bit') {
                $column->setDefaultValue(bindec(trim($info['default'], 'b\'')));
            } else {
                $column->setDefaultValue($this->phpTypecast($column, $info['default']));
            }
        }

        return $column;
    }

    private function phpTypecast(ColumnEntity $column, $value)
    {
        if ($value === '' && $column->getType() !== self::TYPE_TEXT && $column->getType() !== self::TYPE_STRING && $column->getType() !== self::TYPE_BINARY && $column->getType() !== self::TYPE_CHAR) {
            return null;
        }
        if ($value === null || gettype($value) === $column->getPhpType() || $value instanceof Expression) {
            return $value;
        }
        switch ($column->getPhpType()) {
            case 'resource':
            case 'string':
                if (is_resource($value)) {
                    return $value;
                }
                if (is_float($value)) {
                    // ensure type cast always has . as decimal separator in all locales
                    return (string) $value;
                }
                return (string) $value;
            case 'integer':
                return (int) $value;
            case 'boolean':
                // treating a 0 bit value as false too
                // https://github.com/yiisoft/yii2/issues/9006
                return (bool) $value && $value !== "\0";
            case 'double':
                return (float) $value;
        }

        return $value;
    }

    private function getColumnPhpType(ColumnEntity $column)
    {
        static $typeMap = [
            // abstract type => php type
            'smallint' => 'int',
            'integer' => 'int',
            'bigint' => 'int',
            'boolean' => 'bool',
            'float' => 'float',
            'double' => 'float',
            'binary' => 'resource',
        ];
        if (isset($typeMap[$column->getType()])) {
            if ($column->getType() === 'bigint') {
                return PHP_INT_SIZE === 8 && !$column->isUnsigned() ? 'int' : 'string';
            } elseif ($column->getType() === 'integer') {
                return PHP_INT_SIZE === 4 && $column->isUnsigned() ? 'string' : 'int';
            }

            return $typeMap[$column->getType()];
        }

        return 'string';
    }
}