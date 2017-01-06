<?php

namespace Grow\Models;

abstract class Model
{
    /**
     * The table name associated with the model.
     *
     * @var string
     */
    protected $table;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     *
     *
     * @var array
     */
    protected $columns = [];


    public function getColumns()
    {
        return $this->columns;
    }


    public function getColumn($columnName)
    {
        return $this->columns[$columnName];
    }
}