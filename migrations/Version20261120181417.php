<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261120181417 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE category (
              id INT AUTO_INCREMENT NOT NULL,
              code VARCHAR(30) NOT NULL,
              title VARCHAR(100) NOT NULL,
              UNIQUE INDEX UNIQ_64C19C177153098 (code),
              PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE device (
              id INT AUTO_INCREMENT NOT NULL,
              name VARCHAR(100) NOT NULL,
              protocol VARCHAR(50) NOT NULL,
              unit VARCHAR(20) DEFAULT NULL,
              category_id INT NOT NULL,
              INDEX IDX_92FB68E12469DE2 (category_id),
              PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              device
            ADD
              CONSTRAINT FK_92FB68E12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE device DROP FOREIGN KEY FK_92FB68E12469DE2');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE device');
    }
}
