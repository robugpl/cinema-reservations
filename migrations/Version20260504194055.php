<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260504194055 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE orders (email VARCHAR NOT NULL, total_amount INTEGER NOT NULL, id VARCHAR NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE order_tickets (order_id VARCHAR NOT NULL, ticket_id VARCHAR NOT NULL, PRIMARY KEY (order_id, ticket_id), CONSTRAINT FK_A23C34BF8D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A23C34BF700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_A23C34BF8D9F6D38 ON order_tickets (order_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A23C34BF700047D2 ON order_tickets (ticket_id)');
        $this->addSql('CREATE TABLE reservations (screening_id VARCHAR NOT NULL, seat_id VARCHAR NOT NULL, status VARCHAR NOT NULL, email VARCHAR NOT NULL, date DATETIME NOT NULL, expiration_date DATETIME NOT NULL, id VARCHAR NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE screenings (start_date DATETIME NOT NULL, screening_room_id VARCHAR NOT NULL, movie_id VARCHAR NOT NULL, id VARCHAR NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE screening_reservations (screening_id VARCHAR NOT NULL, reservation_id VARCHAR NOT NULL, PRIMARY KEY (screening_id, reservation_id), CONSTRAINT FK_F4BF4C0670F5295D FOREIGN KEY (screening_id) REFERENCES screenings (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F4BF4C06B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservations (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F4BF4C0670F5295D ON screening_reservations (screening_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F4BF4C06B83297E7 ON screening_reservations (reservation_id)');
        $this->addSql('CREATE TABLE tickets (screening_id VARCHAR NOT NULL, seat_id VARCHAR NOT NULL, price INTEGER NOT NULL, id VARCHAR NOT NULL, PRIMARY KEY (id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE order_tickets');
        $this->addSql('DROP TABLE reservations');
        $this->addSql('DROP TABLE screenings');
        $this->addSql('DROP TABLE screening_reservations');
        $this->addSql('DROP TABLE tickets');
    }
}
