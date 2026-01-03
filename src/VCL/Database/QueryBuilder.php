<?php
/**
 * This file is part of the VCL for PHP project
 *
 * Copyright (c) 2004-2008 qadram software S.L.
 * Copyright (c) 2024-2025 Gunnar Beushausen
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 */

declare(strict_types=1);

namespace VCL\Database;

use Doctrine\DBAL\Query\QueryBuilder as DBALQueryBuilder;
use Doctrine\DBAL\Result;

/**
 * QueryBuilder provides a fluent interface for building SQL queries.
 *
 * This class wraps Doctrine DBAL's QueryBuilder with a VCL-style API.
 *
 * Example:
 * ```php
 * $qb = new QueryBuilder($connection);
 *
 * $users = $qb
 *     ->Select('id', 'username', 'email')
 *     ->From('users')
 *     ->Where('status', '=', 'active')
 *     ->AndWhere('role', 'IN', ['admin', 'moderator'])
 *     ->OrderBy('username')
 *     ->Limit(10)
 *     ->FetchAll();
 * ```
 */
class QueryBuilder
{
    protected Connection $connection;
    protected DBALQueryBuilder $qb;
    protected array $params = [];
    protected array $types = [];
    protected int $paramCounter = 0;
    protected string $mainTable = '';
    protected ?string $mainAlias = null;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->connection->Open();
        $this->qb = $this->connection->Dbal()->createQueryBuilder();
    }

    /**
     * Start a SELECT query.
     *
     * @param string ...$columns Columns to select
     */
    public function Select(string ...$columns): self
    {
        if (empty($columns)) {
            $columns = ['*'];
        }
        $this->qb->select(...$columns);
        return $this;
    }

    /**
     * Add columns to SELECT.
     */
    public function AddSelect(string ...$columns): self
    {
        $this->qb->addSelect(...$columns);
        return $this;
    }

    /**
     * SELECT DISTINCT.
     */
    public function Distinct(): self
    {
        $this->qb->distinct();
        return $this;
    }

    /**
     * Set the FROM table.
     *
     * @param string $table Table name
     * @param string|null $alias Optional alias
     */
    public function From(string $table, ?string $alias = null): self
    {
        $this->mainTable = $table;
        $this->mainAlias = $alias;
        $this->qb->from($table, $alias);
        return $this;
    }

    /**
     * Add a JOIN.
     *
     * @param string $table Table to join
     * @param string $alias Alias for joined table
     * @param string $condition Join condition
     */
    public function Join(string $table, string $alias, string $condition): self
    {
        $this->qb->join($this->GetMainAlias(), $table, $alias, $condition);
        return $this;
    }

    /**
     * Add an INNER JOIN.
     */
    public function InnerJoin(string $table, string $alias, string $condition): self
    {
        $this->qb->innerJoin($this->GetMainAlias(), $table, $alias, $condition);
        return $this;
    }

    /**
     * Add a LEFT JOIN.
     */
    public function LeftJoin(string $table, string $alias, string $condition): self
    {
        $this->qb->leftJoin($this->GetMainAlias(), $table, $alias, $condition);
        return $this;
    }

    /**
     * Add a RIGHT JOIN.
     */
    public function RightJoin(string $table, string $alias, string $condition): self
    {
        $this->qb->rightJoin($this->GetMainAlias(), $table, $alias, $condition);
        return $this;
    }

    /**
     * Add a WHERE condition.
     *
     * @param string $column Column name
     * @param string $operator Comparison operator (=, !=, <, >, <=, >=, LIKE, IN, NOT IN, IS NULL, IS NOT NULL)
     * @param mixed $value Value to compare (ignored for IS NULL / IS NOT NULL)
     */
    public function Where(string $column, string $operator, mixed $value = null): self
    {
        $condition = $this->BuildCondition($column, $operator, $value);
        $this->qb->where($condition);
        return $this;
    }

    /**
     * Add an AND WHERE condition.
     */
    public function AndWhere(string $column, string $operator, mixed $value = null): self
    {
        $condition = $this->BuildCondition($column, $operator, $value);
        $this->qb->andWhere($condition);
        return $this;
    }

    /**
     * Add an OR WHERE condition.
     */
    public function OrWhere(string $column, string $operator, mixed $value = null): self
    {
        $condition = $this->BuildCondition($column, $operator, $value);
        $this->qb->orWhere($condition);
        return $this;
    }

    /**
     * Add a raw WHERE expression.
     */
    public function WhereRaw(string $expression): self
    {
        $this->qb->where($expression);
        return $this;
    }

    /**
     * Add a raw AND WHERE expression.
     */
    public function AndWhereRaw(string $expression): self
    {
        $this->qb->andWhere($expression);
        return $this;
    }

    /**
     * Add a raw OR WHERE expression.
     */
    public function OrWhereRaw(string $expression): self
    {
        $this->qb->orWhere($expression);
        return $this;
    }

    /**
     * Add GROUP BY.
     */
    public function GroupBy(string ...$columns): self
    {
        $this->qb->groupBy(...$columns);
        return $this;
    }

    /**
     * Add to GROUP BY.
     */
    public function AddGroupBy(string ...$columns): self
    {
        $this->qb->addGroupBy(...$columns);
        return $this;
    }

    /**
     * Add HAVING condition.
     */
    public function Having(string $condition): self
    {
        $this->qb->having($condition);
        return $this;
    }

    /**
     * Add AND HAVING condition.
     */
    public function AndHaving(string $condition): self
    {
        $this->qb->andHaving($condition);
        return $this;
    }

    /**
     * Add OR HAVING condition.
     */
    public function OrHaving(string $condition): self
    {
        $this->qb->orHaving($condition);
        return $this;
    }

    /**
     * Add ORDER BY.
     *
     * @param string $column Column to order by
     * @param string $direction ASC or DESC
     */
    public function OrderBy(string $column, string $direction = 'ASC'): self
    {
        $this->qb->orderBy($column, strtoupper($direction));
        return $this;
    }

    /**
     * Add to ORDER BY.
     */
    public function AddOrderBy(string $column, string $direction = 'ASC'): self
    {
        $this->qb->addOrderBy($column, strtoupper($direction));
        return $this;
    }

    /**
     * Set LIMIT.
     */
    public function Limit(int $limit): self
    {
        $this->qb->setMaxResults($limit);
        return $this;
    }

    /**
     * Set OFFSET.
     */
    public function Offset(int $offset): self
    {
        $this->qb->setFirstResult($offset);
        return $this;
    }

    /**
     * Set a named parameter.
     */
    public function SetParameter(string $name, mixed $value, mixed $type = null): self
    {
        if ($type === null) {
            $this->qb->setParameter($name, $value);
        } else {
            $this->qb->setParameter($name, $value, $type);
        }
        return $this;
    }

    /**
     * Set multiple parameters.
     */
    public function SetParameters(array $params, array $types = []): self
    {
        $this->qb->setParameters($params, $types);
        return $this;
    }

    /**
     * Execute the query and return Result.
     */
    public function Execute(): Result
    {
        return $this->qb->executeQuery();
    }

    /**
     * Execute and fetch all rows.
     */
    public function FetchAll(): array
    {
        return $this->Execute()->fetchAllAssociative();
    }

    /**
     * Execute and fetch first row.
     */
    public function FetchOne(): array|false
    {
        return $this->Execute()->fetchAssociative();
    }

    /**
     * Execute and fetch single column.
     */
    public function FetchColumn(): array
    {
        return $this->Execute()->fetchFirstColumn();
    }

    /**
     * Execute and fetch single value.
     */
    public function FetchScalar(): mixed
    {
        return $this->Execute()->fetchOne();
    }

    /**
     * Get the SQL string.
     */
    public function GetSQL(): string
    {
        return $this->qb->getSQL();
    }

    /**
     * Get all parameters.
     */
    public function GetParameters(): array
    {
        return $this->qb->getParameters();
    }

    /**
     * Reset the query builder.
     */
    public function Reset(): self
    {
        $this->qb = $this->connection->Dbal()->createQueryBuilder();
        $this->params = [];
        $this->types = [];
        $this->paramCounter = 0;
        return $this;
    }

    /**
     * Clone for subqueries.
     */
    public function CreateSubQuery(): self
    {
        return new self($this->connection);
    }

    /**
     * Get underlying DBAL QueryBuilder.
     */
    public function GetDbalQueryBuilder(): DBALQueryBuilder
    {
        return $this->qb;
    }

    // -------------------------------------------------------------------------
    // INSERT / UPDATE / DELETE
    // -------------------------------------------------------------------------

    /**
     * Start an INSERT query.
     */
    public function Insert(string $table): self
    {
        $this->qb->insert($table);
        return $this;
    }

    /**
     * Set value for INSERT.
     */
    public function SetValue(string $column, mixed $value): self
    {
        $param = $this->CreateParam($value);
        $this->qb->setValue($column, ':' . $param);
        return $this;
    }

    /**
     * Set multiple values for INSERT.
     */
    public function SetValues(array $values): self
    {
        foreach ($values as $column => $value) {
            $this->SetValue($column, $value);
        }
        return $this;
    }

    /**
     * Start an UPDATE query.
     */
    public function Update(string $table, ?string $alias = null): self
    {
        $this->qb->update($table);
        return $this;
    }

    /**
     * Set column value for UPDATE.
     */
    public function Set(string $column, mixed $value): self
    {
        $param = $this->CreateParam($value);
        $this->qb->set($column, ':' . $param);
        return $this;
    }

    /**
     * Start a DELETE query.
     */
    public function Delete(string $table, ?string $alias = null): self
    {
        $this->qb->delete($table);
        return $this;
    }

    /**
     * Execute INSERT/UPDATE/DELETE and return affected rows.
     */
    public function ExecuteStatement(): int
    {
        return $this->qb->executeStatement();
    }

    // -------------------------------------------------------------------------
    // Helper Methods
    // -------------------------------------------------------------------------

    /**
     * Build a condition expression.
     */
    protected function BuildCondition(string $column, string $operator, mixed $value): string
    {
        $operator = strtoupper(trim($operator));

        switch ($operator) {
            case 'IS NULL':
                return "{$column} IS NULL";

            case 'IS NOT NULL':
                return "{$column} IS NOT NULL";

            case 'IN':
            case 'NOT IN':
                if (!is_array($value)) {
                    $value = [$value];
                }
                $placeholders = [];
                foreach ($value as $v) {
                    $param = $this->CreateParam($v);
                    $placeholders[] = ':' . $param;
                }
                $list = implode(', ', $placeholders);
                return "{$column} {$operator} ({$list})";

            case 'BETWEEN':
                if (is_array($value) && count($value) >= 2) {
                    $param1 = $this->CreateParam($value[0]);
                    $param2 = $this->CreateParam($value[1]);
                    return "{$column} BETWEEN :{$param1} AND :{$param2}";
                }
                throw new EDatabaseError("BETWEEN requires array with 2 values");

            case 'LIKE':
            case 'NOT LIKE':
                $param = $this->CreateParam($value);
                return "{$column} {$operator} :{$param}";

            default:
                // Standard operators: =, !=, <>, <, >, <=, >=
                $param = $this->CreateParam($value);
                return "{$column} {$operator} :{$param}";
        }
    }

    /**
     * Create a unique parameter and register its value.
     */
    protected function CreateParam(mixed $value): string
    {
        $name = 'p' . (++$this->paramCounter);
        $this->qb->setParameter($name, $value);
        return $name;
    }

    /**
     * Get the main table alias.
     */
    protected function GetMainAlias(): string
    {
        return $this->mainAlias ?? $this->mainTable;
    }
}
