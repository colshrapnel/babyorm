<?php
namespace BabyORM;

abstract class DataMapper
{
    protected $db;
    protected $class;
    protected $table;
    protected $primary = 'id';
    protected $fields = [];

    public function __construct(DB $db)
    {
        $this->db = $db;
    }

    public function findBySql($sql, $params = [])
    {
        return $this->db->query($sql, $params)->fetchAll(\PDO::FETCH_CLASS, $this->class);
    }

    public function find($id)
    {
        $table = $this->db->escapeIdent($this->table);
        $primary = $this->db->escapeIdent($this->primary);
        $sql = "SELECT * FROM $table WHERE $primary = ?";
        return $this->db->query($sql, [$id])->fetchObject($this->class);
    }

    public function delete($object)
    {
        $table = $this->db->escapeIdent($this->table);
        $primary = $this->db->escapeIdent($this->primary);
        $sql = "DELETE FROM $table WHERE $primary = ?";
        $this->db->query($sql, [$object->id]);
    }

    public function save($object)
    {
        if (!empty($object->id)) {
            $this->update($object);
        } else {
            $this->insert($object);
        }
    }

    protected function insert($object)
    {
        $names = '';
        $data = [];
        foreach ($this->fields as $name) {
            if (property_exists($object, $name)) {
                $names .= $names ? ',' : '';
                $names .= $this->db->escapeIdent($name);
                $data[] = $object->{$name};
            }
        }
        $values = str_repeat('?,', count($data) - 1) . '?';
        $table = $this->db->escapeIdent($this->table);

        $sql = "INSERT INTO $table ($names) VALUES ($values)";
        $this->db->query($sql, $data);
        $object->{$this->primary} = $this->db->pdo->lastInsertId();

    }
    protected function update($object)
    {
        $data = [];
        $set = "";
        foreach($this->fields as $key)
        {
            if (property_exists($object, $key)) {
                $set .= $set ? ',' : '';
                $set .= $this->db->escapeIdent($key) . " = ?";
                $data[] = $object->{$key};
            }
        }
        $data[] = $object->{$this->primary};

        $table = $this->db->escapeIdent($this->table);
        $primary = $this->db->escapeIdent($this->primary);
        $sql = "UPDATE $table SET $set WHERE $primary = ?";
        $this->db->query($sql, $data);
    }
}
