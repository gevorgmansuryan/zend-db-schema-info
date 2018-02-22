# Zend Db Schema Info
### Database Schema information provider for zend framework

## Installation

```bash
composer require gevman/zend-db-schema-info
```

## Available methods

##### `string[]` Schema::getTables(`void`)
Returns table list

###### example
```php
$schema = new Schema($container->get(\Zend\Db\Adapter\Adapter::class));

var_dump($schema->getTables());
```



##### `ColumnEntity[]` Schema::getTableColumns(`string` $table)
Returns list of information data objects for all columns in specified table

###### example
```php
$schema = new Schema($container->get(\Zend\Db\Adapter\Adapter::class));

var_dump($schema->getTableColumns('users));
```

Data Object `ColumnEntity` definition

- `string` $name - name of this column (without quotes).
- `bool` $allowNull - whether this column can be null.
- `string` $type - abstract type of this column. Possible abstract types include: char, string, text, boolean, smallint, integer, bigint, float, decimal, datetime, timestamp, time, date, binary, and money.
- `string` $phpType - string the PHP type of this column. Possible PHP types include: `string`, `boolean`, `integer`, `double`.
- `string` $dbType - the DB type of this column. Possible DB types vary according to the type of DBMS.
- `mixed` $defaultValue - default value of this column
- `array` $enumValues - enumerable values. This is set only if the column is declared to be an enumerable type.
- `int` $size - display size of the column.
- `int` $precision - precision of the column data, if it is numeric.
- `int` $scale - scale of the column data, if it is numeric.
- `bool` $isPrimaryKey - whether this column is a primary key
- `bool` $autoIncrement - whether this column is auto-incremental
- `bool` $unsigned - bool whether this column is unsigned. This is only meaningful when [[type]] is `smallint`, `integer` or `bigint`.
- `string` $comment - comment of this column. Not all DBMS support this.