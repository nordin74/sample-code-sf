<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201025230305 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Set up tables';
    }

    public function up(Schema $schema) : void
    {
        $this->createTable($schema, 'mo');
        $this->createTable($schema, 'mofailed');
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('mofailed');
        $schema->dropTable('mo');
    }


    private function createTable(Schema $schema, string $tableName)
    {
        $table = $schema->createTable($tableName);
        if($this->connection->getDatabasePlatform()->getName() === 'mysql') {
            $table->addOption('engine', 'MyISAM');
        }

        $table->addColumn('id', Types::INTEGER, ['autoincrement' => true]);
        $table->addColumn('msisdn', Types::INTEGER);
        $table->addColumn('operatorid', Types::SMALLINT);
        $table->addColumn('text', Types::STRING, ['length' => 15]);
        $table->addColumn('auth_token', Types::STRING, ['length' => 60, 'notnull' => false]);
        $table->addColumn('node', Types::STRING, ['node' => 45]);
        $table->addColumn('created_at', Types::DATETIME_IMMUTABLE);
        $table->setPrimaryKey(['id']);
    }
}