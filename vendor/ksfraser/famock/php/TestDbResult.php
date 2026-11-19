<?php

/**
 * TestDbResult - Simple result set wrapper for unit tests
 *
 * Provides a lightweight result set implementation for use in mock
 * database adapters during unit testing. Simulates PDO or mysqli result set behavior.
 *
 * Usage:
 *   $result = new TestDbResult([
 *       ['id' => 1, 'name' => 'Item 1'],
 *       ['id' => 2, 'name' => 'Item 2'],
 *   ]);
 *   $row = $result->fetch(); // ['id' => 1, 'name' => 'Item 1']
 *   $row = $result->fetch(); // ['id' => 2, 'name' => 'Item 2']
 *   $row = $result->fetch(); // false
 */
class TestDbResult
{
    /** @var array<int, array<string, mixed>> */
    private $rows;

    /** @var int Current position in result set */
    private $position = 0;

    /**
     * Create a result set wrapper
     *
     * @param array<int, array<string, mixed>> $rows Array of associative arrays representing rows
     */
    public function __construct(array $rows = [])
    {
        $this->rows = array_values($rows); // Re-index to ensure numeric keys
        $this->position = 0;
    }

    /**
     * Fetch next row from result set
     *
     * @return array<string, mixed>|false Associative array of the row, or false if no more rows
     */
    public function fetch()
    {
        if ($this->position >= count($this->rows)) {
            return false;
        }

        $row = $this->rows[$this->position];
        $this->position++;

        return $row;
    }

    /**
     * Get number of rows in result set
     *
     * @return int Number of rows
     */
    public function rowCount(): int
    {
        return count($this->rows);
    }

    /**
     * Reset result set cursor to beginning
     *
     * @return void
     */
    public function reset(): void
    {
        $this->position = 0;
    }
}
