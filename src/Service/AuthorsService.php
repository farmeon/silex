<?php

namespace Service;

class AuthorsService extends BaseService
{
    public function getOne($id)
    {
        return $this->db->fetchAssoc("SELECT * FROM authors WHERE id=?", [(int) $id]);
    }

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM authors");
    }

    function save($authors)
    {
        $this->db->insert("authors", $authors);

        return $this->db->lastInsertId();
    }

    function update($id, $name)
    {
        return $this->db->update('authors', $name, ['id' => $id]);
    }

    function updatePatch($id, $name)
    {
        return $this->db->update('authors', array_diff($name, ['']), ['id' => $id]);
    }

    function delete($id)
    {
        return $this->db->delete("authors", ["id" => $id]);
    }
}
