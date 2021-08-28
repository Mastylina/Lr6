<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210820103827 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE books DROP CONSTRAINT fk_4a1b2a92db8bf0e2');
        $this->addSql('DROP INDEX idx_4a1b2a92db8bf0e2');
        $this->addSql('ALTER TABLE books DROP relation_books_user_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE books ADD relation_books_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE books ADD CONSTRAINT fk_4a1b2a92db8bf0e2 FOREIGN KEY (relation_books_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_4a1b2a92db8bf0e2 ON books (relation_books_user_id)');
    }
}
