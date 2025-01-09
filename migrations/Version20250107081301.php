<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250107081301 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bilera (id INT AUTO_INCREMENT NOT NULL, izena VARCHAR(255) NOT NULL, lekua VARCHAR(255) NOT NULL, data DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bisita (id INT AUTO_INCREMENT NOT NULL, izena VARCHAR(255) NOT NULL, nondik VARCHAR(255) NOT NULL, itxita TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bisitaria (id INT AUTO_INCREMENT NOT NULL, izena VARCHAR(255) NOT NULL, abizena VARCHAR(255) NOT NULL, nondik VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, bilera_id INT DEFAULT NULL, bisita_id INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE bilera');
        $this->addSql('DROP TABLE bisita');
        $this->addSql('DROP TABLE bisitaria');
    }
}
