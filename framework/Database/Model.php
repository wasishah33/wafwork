<?php

namespace WAFWork\Database;

use WAFWork\Core\Application;

abstract class Model
{
    /**
     * The table associated with the model
     *
     * @var string
     */
    protected $table;

    /**
     * The primary key column
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The attributes that aren't mass assignable
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The model's attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The attributes that have been changed
     *
     * @var array
     */
    protected $dirty = [];

    /**
     * Create a new model instance
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    /**
     * Fill the model with an array of attributes
     *
     * @param array $attributes
     * @return $this
     */
    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if ($this->isFillable($key)) {
                $this->setAttribute($key, $value);
            }
        }
        
        return $this;
    }

    /**
     * Determine if the given attribute is fillable
     *
     * @param string $key
     * @return bool
     */
    protected function isFillable($key)
    {
        if (in_array($key, $this->guarded)) {
            return false;
        }
        
        return empty($this->fillable) || in_array($key, $this->fillable);
    }

    /**
     * Set a given attribute on the model
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
        $this->dirty[$key] = true;
        
        return $this;
    }

    /**
     * Get an attribute from the model
     *
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Get all attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get the dirty attributes
     *
     * @return array
     */
    public function getDirty()
    {
        return array_intersect_key($this->attributes, $this->dirty);
    }

    /**
     * Save the model to the database
     *
     * @return bool
     */
    public function save()
    {
        $db = $this->getConnection();
        
        if (isset($this->attributes[$this->primaryKey])) {
            // Update
            $dirty = $this->getDirty();
            
            if (empty($dirty)) {
                return true;
            }
            
            $id = $this->attributes[$this->primaryKey];
            
            return $db->update($this->getTable(), $dirty, [$this->primaryKey => $id]);
        } else {
            // Insert
            $id = $db->insert($this->getTable(), $this->attributes);
            
            if ($id) {
                $this->setAttribute($this->primaryKey, $id);
                return true;
            }
            
            return false;
        }
    }

    /**
     * Delete the model from the database
     *
     * @return bool
     */
    public function delete()
    {
        if (!isset($this->attributes[$this->primaryKey])) {
            return false;
        }
        
        $id = $this->attributes[$this->primaryKey];
        
        return $this->getConnection()->delete($this->getTable(), [$this->primaryKey => $id]);
    }

    /**
     * Get the table name
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table ?? strtolower(pluralize(class_basename($this)));
    }

    /**
     * Get the database connection
     *
     * @return Connection
     */
    protected function getConnection()
    {
        return Application::getInstance()->getContainer()->resolve('db');
    }

    /**
     * Create a new model instance
     *
     * @param array $attributes
     * @return static
     */
    public static function create(array $attributes)
    {
        $model = new static($attributes);
        $model->save();
        
        return $model;
    }

    /**
     * Find a model by its primary key
     *
     * @param mixed $id
     * @return static|null
     */
    public static function find($id)
    {
        $instance = new static();
        $result = $instance->getConnection()->find($instance->getTable(), $id, $instance->primaryKey);
        
        if ($result) {
            return new static($result);
        }
        
        return null;
    }

    /**
     * Find all models
     *
     * @return array
     */
    public static function all()
    {
        $instance = new static();
        $results = $instance->getConnection()->all($instance->getTable());
        
        return array_map(function ($attributes) {
            return new static($attributes);
        }, $results);
    }

    /**
     * Find models by a where clause
     *
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @return array
     */
    public static function where($column, $operator, $value = null)
    {
        // If only two arguments are provided, assume the operator is =
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $instance = new static();
        $results = $instance->getConnection()->where($instance->getTable(), $column, $operator, $value);
        
        return array_map(function ($attributes) {
            return new static($attributes);
        }, $results);
    }

    /**
     * Dynamically retrieve attributes on the model
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically set attributes on the model
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Determine if an attribute exists on the model
     *
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Unset an attribute on the model
     *
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->attributes[$key]);
    }
} 