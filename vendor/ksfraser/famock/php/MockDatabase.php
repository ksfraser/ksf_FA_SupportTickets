<?php

namespace Ksfraser\FAMock;

use Ksfraser\Frontaccounting\GenCat\DatabaseInterface;

/**
 * Simple DatabaseInterface implementation for unit tests.
 *
 * Returns a fixed set of rows for any query.
 */
class MockDatabase implements DatabaseInterface
{
    /** @var string */
    private $tablePrefix;

    /** @var array<int, array<string, mixed>> */
    private $rows;

    /**
     * @param array<int, array<string, mixed>> $rows Rows returned by fetch() for any query
     * @param string $tablePrefix Table prefix returned by getTablePrefix()
     */
    public function __construct(array $rows = [], string $tablePrefix = '0_')
    {
        $this->rows = array_values($rows);
        $this->tablePrefix = $tablePrefix;
    }

    public function query($query, $error_message = 'Database query failed')
    {
        return new \ArrayIterator($this->rows);
    }

    public function fetch($result)
    {
        if ($result instanceof \Iterator) {
            if (!$result->valid()) {
                return false;
            }
            $current = $result->current();
            $result->next();
            return $current;
        }

        return false;
    }

    public function getTablePrefix()
    {
        return $this->tablePrefix;
    }
}
