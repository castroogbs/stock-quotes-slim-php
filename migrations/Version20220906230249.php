<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Types\Types;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220906230249 extends AbstractMigration
{

    public function up(Schema $schema): void
    {
        $users = $schema->createTable('users');
        $users->addColumn('id', Types::INTEGER)->setAutoincrement(true);
        $users->addColumn('email', Types::STRING);
        $users->addColumn('name', Types::STRING);
        $users->addColumn('password', Types::STRING);
        $users->setPrimaryKey(['id']);
        $users->addUniqueConstraint(['email']);

        $researches = $schema->createTable('stock_quote_researches');
        $researches->addColumn('id', Types::INTEGER)->setAutoincrement(true);
        $researches->addColumn('user_id', Types::INTEGER);
        $researches->addColumn('date', Types::DATETIME_IMMUTABLE);
        $researches->addColumn('name', Types::STRING);
        $researches->addColumn('symbol', Types::STRING);
        $researches->addColumn('open', Types::DECIMAL)
                    ->setPrecision(10)
                    ->setScale(3);

        $researches->addColumn('high', Types::DECIMAL)
                    ->setPrecision(10)
                    ->setScale(3);

        $researches->addColumn('low', Types::DECIMAL)
                    ->setPrecision(10)
                    ->setScale(3);

        $researches->addColumn('close', Types::DECIMAL)
                    ->setPrecision(10)
                    ->setScale(3);
                    
        $researches->setPrimaryKey(['id']);
        $researches->addForeignKeyConstraint('users',['user_id'],['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('users');
        $schema->dropTable('stock_quote_researches');
    }
}
