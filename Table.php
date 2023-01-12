<?php
class Table
{
    public string $name;
    public string $primaryKey;
    public array $columns;

    public function __construct(string $name, string $primaryKey, array $columns)
    {
        $this->name = $name;
        $this->primaryKey = $primaryKey;
        $this->columns = $columns;
    }
}