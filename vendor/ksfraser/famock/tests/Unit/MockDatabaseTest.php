<?php
declare(strict_types=1);

namespace Ksfraser\FAMock\Tests\Unit;

use Ksfraser\FAMock\MockDatabase;
use PHPUnit\Framework\TestCase;

final class MockDatabaseTest extends TestCase
{
    public function testQueryAndFetchIterateRows(): void
    {
        $db = new MockDatabase([
            ['a' => 1],
            ['a' => 2],
        ], '9_');

        $result = $db->query('SELECT 1');
        $this->assertSame(['a' => 1], $db->fetch($result));
        $this->assertSame(['a' => 2], $db->fetch($result));
        $this->assertFalse($db->fetch($result));

        $this->assertSame('9_', $db->getTablePrefix());
    }

    public function testFetchNonIteratorReturnsFalse(): void
    {
        $db = new MockDatabase([]);
        $this->assertFalse($db->fetch(new \stdClass()));
    }
}
