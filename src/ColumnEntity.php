<?php
/**
 * @author Gevorg Mansuryan
 * @copyright 2018
 * @package zend-db-schema-info
 */

namespace Gevman\DbSchemaInfo;

class ColumnEntity
{
    /**
     * @var string name of this column (without quotes).
     */
    protected $name;
    /**
     * @var bool whether this column can be null.
     */
    protected $allowNull;
    /**
     * @var string abstract type of this column. Possible abstract types include:
     * char, string, text, boolean, smallint, integer, bigint, float, decimal, datetime,
     * timestamp, time, date, binary, and money.
     */
    protected $type;
    /**
     * @var string the PHP type of this column. Possible PHP types include:
     * `string`, `bool`, `int`, `float`.
     */
    protected $phpType;
    /**
     * @var string the DB type of this column. Possible DB types vary according to the type of DBMS.
     */
    protected $dbType;
    /**
     * @var mixed default value of this column
     */
    protected $defaultValue;
    /**
     * @var array enumerable values. This is set only if the column is declared to be an enumerable type.
     */
    protected $enumValues;
    /**
     * @var int display size of the column.
     */
    protected $size;
    /**
     * @var int precision of the column data, if it is numeric.
     */
    protected $precision;
    /**
     * @var int scale of the column data, if it is numeric.
     */
    protected $scale;
    /**
     * @var bool whether this column is a primary key
     */
    protected $isPrimaryKey;
    /**
     * @var bool whether this column is auto-incremental
     */
    protected $autoIncrement = false;
    /**
     * @var bool whether this column is unsigned. This is only meaningful
     * when [[type]] is `smallint`, `integer` or `bigint`.
     */
    protected $unsigned;
    /**
     * @var string comment of this column. Not all DBMS support this.
     */
    protected $comment;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ColumnEntity
     */
    public function setName(string $name): ColumnEntity
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAllowNull(): bool
    {
        return $this->allowNull;
    }

    /**
     * @param bool $allowNull
     * @return ColumnEntity
     */
    public function setAllowNull(bool $allowNull): ColumnEntity
    {
        $this->allowNull = $allowNull;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return ColumnEntity
     */
    public function setType(string $type): ColumnEntity
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhpType(): string
    {
        return $this->phpType;
    }

    /**
     * @param string $phpType
     * @return ColumnEntity
     */
    public function setPhpType(string $phpType): ColumnEntity
    {
        $this->phpType = $phpType;
        return $this;
    }

    /**
     * @return string
     */
    public function getDbType(): string
    {
        return $this->dbType;
    }

    /**
     * @param string $dbType
     * @return ColumnEntity
     */
    public function setDbType(string $dbType): ColumnEntity
    {
        $this->dbType = $dbType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param mixed $defaultValue
     * @return ColumnEntity
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @return array
     */
    public function getEnumValues(): array
    {
        return $this->enumValues;
    }

    /**
     * @param array $enumValues
     * @return ColumnEntity
     */
    public function setEnumValues(array $enumValues): ColumnEntity
    {
        $this->enumValues = $enumValues;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $size
     * @return ColumnEntity
     */
    public function setSize(int $size): ColumnEntity
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return int
     */
    public function getPrecision(): int
    {
        return $this->precision;
    }

    /**
     * @param int $precision
     * @return ColumnEntity
     */
    public function setPrecision(int $precision): ColumnEntity
    {
        $this->precision = $precision;
        return $this;
    }

    /**
     * @return int
     */
    public function getScale(): int
    {
        return $this->scale;
    }

    /**
     * @param int $scale
     * @return ColumnEntity
     */
    public function setScale(int $scale): ColumnEntity
    {
        $this->scale = $scale;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPrimaryKey(): bool
    {
        return $this->isPrimaryKey;
    }

    /**
     * @param bool $isPrimaryKey
     * @return ColumnEntity
     */
    public function setIsPrimaryKey(bool $isPrimaryKey): ColumnEntity
    {
        $this->isPrimaryKey = $isPrimaryKey;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAutoIncrement(): bool
    {
        return $this->autoIncrement;
    }

    /**
     * @param bool $autoIncrement
     * @return ColumnEntity
     */
    public function setAutoIncrement(bool $autoIncrement): ColumnEntity
    {
        $this->autoIncrement = $autoIncrement;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUnsigned(): bool
    {
        return $this->unsigned;
    }

    /**
     * @param bool $unsigned
     * @return ColumnEntity
     */
    public function setUnsigned(bool $unsigned): ColumnEntity
    {
        $this->unsigned = $unsigned;
        return $this;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return ColumnEntity
     */
    public function setComment(string $comment): ColumnEntity
    {
        $this->comment = $comment;
        return $this;
    }
}