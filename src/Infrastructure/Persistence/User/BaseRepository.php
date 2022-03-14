<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use FaaPz\PDO\Database;
use FaaPz\PDO\Statement\SelectStatement;

class BaseRepository
{
    protected Database $db;
    protected SelectStatement $query;
    protected string $table = '';
    protected string $className;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function find(): self
    {
        $this->query = $this->db->select()->from($this->table);

        return $this;
    }

    public function findOne(int $id): self
    {
        $this->query = $this->db
            ->select()
            ->from($this->table)
            ->where('id', '=', $id)
        ;

        return $this->get();
    }

    public function where($column, $operator = null, $value = null, $chainType = 'AND'): self
    {
        $this->query->where($column, $operator, $value, $chainType);

        return $this;
    }

    public function getLast()
    {
        $this->query->limit(1)->orderBy('id', 'DESC');

        return $this->get();
    }

    public function get()
    {
        $data = $this->getRows();

        return $this->className::createFromArray($data);
    }

    private function getRows(): ?array
    {
        $stmt = $this->query->execute();
        $result = $stmt->fetch();

        return $result ?: null;
    }
}
