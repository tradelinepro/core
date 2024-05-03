<?php declare(strict_types=1);
/*
 * @author digital.manufaktur GmbH
 * @link   https://www.digitalmanufaktur.com/
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tradelinepro\Core\Migration\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
#[Package('core')]
abstract class AbstractMigrationStep extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return (int) \mb_substr(\basename(\strtr(static::class, '\\', '/')), 9);
    }

    public function updateDestructive(Connection $connection): void
    {
    }

    protected function tableExists(Connection $connection, string $tableName): bool
    {
        $sql = <<< SQL
SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '{$connection->getDatabase()}'
    AND TABLE_NAME = '$tableName' LIMIT 1
SQL;

        return (bool) $connection->fetchOne($sql);
    }

    protected function foreignKeyExists(Connection $connection, string $tableName, string $constraintName): bool
    {
        $sql = <<< SQL
SELECT COUNT(*) FROM `TABLE_CONSTRAINTS` WHERE CONSTRAINT_SCHEMA = '{$connection->getDatabase()}' AND TABLE_NAME = '$tableName' AND `CONSTRAINT_NAME` = '$constraintName' LIMIT 1
SQL;

        return (bool) $connection->fetchOne($sql);
    }

    protected function minifyQuery(string $string): string
    {
        return \implode(' ', \array_filter(\array_map('trim', \explode("\n", $string))));
    }
}
