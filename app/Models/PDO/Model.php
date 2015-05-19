<?php namespace App\Models\PDO;

use \PDO;
use \stdClass;
use \LogicException;
use \Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
abstract class Model
{
    /** Model table */
    const TABLE_NAME = null;

    /** Field name */
    const FIELD_ID = 'id';

    /**
     * @var string
     */
    private $idx;

    /**
     * @var stdClass
     */
    private $fields;

    /**
     * @var bool
     */
    private $saved;

    /**
     * @var array
     */
    private $dirtyFields;

    /**
     * @var array
     */
    private $links;

    /**
     * Constructor.
     */
    private function __construct()
    {
        // default constructor is prohibited.
    }

    /**
     * @return static
     */
    public static function newInstance()
    {
        $model = new static();
        $model->saved    = false;

        return $model;
    }

    /**
     * @param string $idx
     *
     * @return Model
     */
    public static function existingInstance($idx)
    {
        settype($idx, 'string');
        $model = new static();
        $model->saved = true;
        $model->idx   = $idx;

        return $model;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->idx;
    }

    /**
     * Search models.
     *
     * @return Model[]
     */
    public static function search()
    {
        $tableName = static::TABLE_NAME;
        $primary   = static::FIELD_ID;
        $statement = "SELECT * FROM $tableName";

        $pdo = self::getPdo();
        try {
            $rows = $pdo->query($statement)->fetchAll(PDO::FETCH_CLASS, stdClass::class);
        } finally {
            $pdo = null;
        }

        $result = [];
        foreach ($rows as $row) {
            $instance = static::existingInstance($row->{$primary});
            $instance->fields = $row;
            $result[] = $instance;
        }

        return $result;
    }

    /**
     * Get related models.
     *
     * @param string $modelClass
     * @param string $foreignKey
     *
     * @return Model[]
     */
    protected function hasMany($modelClass, $foreignKey)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $tableName = $modelClass::TABLE_NAME;
        $statement = "SELECT * FROM $tableName WHERE $foreignKey = ?";

        $pdo = self::getPdo();
        try {
            $pdoStatement = $pdo->prepare($statement);
            $pdoStatement->execute([$this->getId()]);
            $rows = $pdoStatement->fetchAll(PDO::FETCH_CLASS, stdClass::class);
        } finally {
            $pdo = null;
        }

        $result  = [];
        /** @noinspection PhpUndefinedFieldInspection */
        $primary = $modelClass::FIELD_ID;
        foreach ($rows as $row) {
            /** @noinspection PhpUndefinedMethodInspection */
            $instance = $modelClass::existingInstance($row->{$primary});
            $instance->fields = $row;
            $result[] = $instance;
        }

        return $result;
    }

    /**
     * Save the model to database.
     */
    public function save()
    {
        $tableName = static::TABLE_NAME;

        if ($this->isExisting() === true) {
            $eqPairs  = '';
            $values = [];
            foreach ($this->dirtyFields as $name => $value) {
                $values[] = $value;
                $eqPairs .= $name . ' = ?,';
            }
            $eqPairs   = substr($eqPairs, 0, -1);
            $primary   = static::FIELD_ID;
            $statement = "UPDATE $tableName SET $eqPairs WHERE $primary = ?";
            $values[]  = $this->idx;
        } else {
            $statement = "INSERT INTO $tableName";
            $values    = (array)$this->getFields();
            if (empty($values) === false) {
                $params  = '';
                $columns = '';
                foreach ($values as $name => $value) {
                    $params  .= ':' . $name . ',';
                    $columns .= $name . ',';
                }
                $params    = substr($params, 0, -1);
                $columns   = substr($columns, 0, -1);
                $statement .= " ($columns) VALUES($params)";
            } else {
                $statement .= ' DEFAULT VALUES';
            }
        }

        $pdo = self::getPdo();
        try {
            $pdoStatement = $pdo->prepare($statement);
            $pdoStatement->execute($values);
            if ($pdoStatement->rowCount() > 0) {
                if ($this->isExisting() === true) {
                    $this->dirtyFields = null;
                } else {
                    $this->idx = $pdo->lastInsertId(static::FIELD_ID);
                }
                $this->saved = true;
            } else {
                // no rows have been modified
                $this->throwNotFound();
            }
        } finally {
            $pdo = null;
        }
    }

    /**
     * @param string $idx
     *
     * @return Model
     */
    public static function findOrFail($idx)
    {
        $result = static::existingInstance($idx);
        $result->loadFields();

        return $result;
    }

    /**
     * Delete model.
     *
     * @return bool
     */
    public function delete()
    {
        $tableName = static::TABLE_NAME;
        $primary   = static::FIELD_ID;
        $idx       = $this->getId();
        $statement = "DELETE FROM $tableName WHERE $primary = ?";

        $pdo = self::getPdo();
        try {
            $result = $pdo->query($statement)->execute([$idx]);
        } finally {
            $pdo = null;
        }

        return $result;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return void
     */
    protected function setField($name, $value)
    {
        if ($this->isExisting() === false) {
            if ($this->isLoaded() === false) {
                $this->fields = new stdClass();
            }
            $this->fields->{$name} = $value;
        } elseif ($this->isExisting() === true) {
            if ($this->isLoaded() === true) {
                $this->fields->{$name} = $value;
            }
            $this->dirtyFields[$name] = $value;
        }

        $this->saved = false;
    }

    /**
     * Get field.
     *
     * @param string $name
     *
     * @return mixed
     */
    protected function getField($name)
    {
        $fields = $this->getFields();
        return $fields === null ? null : $fields->{$name};
    }

    /**
     * @return stdClass|null
     */
    protected function getFields()
    {
        if ($this->isExisting() === true && $this->isLoaded() === false) {
            if ($this->isDirty() === true) {
                // if model has unsaved data it's not allowed to override them by
                // loading fields from database
                throw new LogicException();
            }
            $this->loadFields();
        }
        return $this->fields;
    }

    /**
     * Set link.
     *
     * @param string $name
     * @param Model  $model
     *
     * @return void
     */
    protected function setLink($name, Model $model = null)
    {
        $idx = $model === null ? null : $model->getId();
        $this->links[$name] = $model;
        $this->setField($name, $idx);
    }

    /**
     * Get link.
     *
     * @param string $name
     * @param string $modelClass
     *
     * @return Model|null
     */
    protected function getLink($name, $modelClass)
    {
        if (array_has($this->links, $name) === true) {
            return $this->links[$name];
        }

        $resourceId = $this->getField($name);

        /** @noinspection PhpUndefinedMethodInspection */
        return $resourceId === null ? null : $modelClass::existingInstance($resourceId);
    }

    /**
     * @return bool
     */
    public function isExisting()
    {
        return $this->idx !== null;
    }

    /**
     * Get 'true' if model has unsaved fields.
     *
     * @return bool
     */
    public function isDirty()
    {
        return empty($this->dirtyFields) === false;
    }

    /**
     * @return bool
     */
    public function isLoaded()
    {
        return $this->fields !== null;
    }

    /**
     * Throws exceptions when no database records have been modified.
     */
    protected function throwNotFound()
    {
        throw new ModelNotFoundException();
    }

    /**
     * Load fields from database.
     */
    private function loadFields()
    {
        $tableName = static::TABLE_NAME;
        $primary   = static::FIELD_ID;
        $idx       = $this->getId();
        $statement = "SELECT * FROM $tableName WHERE $primary = $idx";

        $pdo = self::getPdo();
        try {
            $tmp = new stdClass();
            $pdoStatement = $pdo->query($statement, PDO::FETCH_INTO, $tmp);
            $pdoStatement->fetch();
            empty((array)$tmp) === false ?: $this->throwNotFound();
            $this->fields = $tmp;
        } finally {
            $pdo = null;
        }
    }

    /**
     * @return PDO
     */
    private static function getPdo()
    {
        $filePath = env('DB_DATABASE');
        $pdo      = new PDO('sqlite:' . $filePath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }
}
