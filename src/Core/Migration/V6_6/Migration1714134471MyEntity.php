<?php declare(strict_types=1);

namespace Shopware\Core\Migration\V6_6;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
#[Package('core')]
class Migration1714134471MyEntity extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1714134471;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
CREATE TABLE `my_entity` (
  `id` binary(16) NOT NULL,
  `number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` json DEFAULT NULL,
  `position` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `weight` float DEFAULT NULL,
  `highlight` tinyint DEFAULT NULL,
  `release` datetime(3) DEFAULT NULL,
  `product_id` binary(16) DEFAULT NULL,
  `follow_id` binary(16) DEFAULT NULL,
  `created_at` datetime(3) NOT NULL,
  `updated_at` datetime(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }
}
