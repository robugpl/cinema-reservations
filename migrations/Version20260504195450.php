<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260504195450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE movies (title VARCHAR(255) NOT NULL, director VARCHAR(255) NOT NULL, duration_seconds INTEGER NOT NULL, id VARCHAR NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE screening_rooms (name VARCHAR(255) NOT NULL, layout_json CLOB NOT NULL, id VARCHAR NOT NULL, PRIMARY KEY (id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE movies');
        $this->addSql('DROP TABLE screening_rooms');
    }
}
