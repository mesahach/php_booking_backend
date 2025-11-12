<?php
namespace MyApp\Models;

class TransactionModel
{
    // CRUD
    public function create(): array
    { /* read body, validate, insert */
        return ['status' => true];
    }
    public function get(int $id): array
    {
        return ['status' => true, 'data' => ['id' => $id]];
    }
    public function getAll(): array
    {
        return ['status' => true, 'data' => []];
    }
    public function update(int $id): array
    {
        return ['status' => true];
    }
    public function delete(int $id): array
    {
        return ['status' => true];
    }

    // Custom
    public function verify(int $id): array
    {
        return ['status' => true, 'verified_id' => $id];
    }
    public function search(string $q): array
    {
        return ['status' => true, 'query' => $q, 'data' => []];
    }
    public function getRecent(int $limit): array
    {
        return ['status' => true, 'limit' => $limit, 'data' => []];
    }
    public function refund(int $id): array
    {
        return ['status' => true, 'refunded_id' => $id];
    }
}
