<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250115112844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bilera (id INT AUTO_INCREMENT NOT NULL, izena VARCHAR(255) NOT NULL, lekua VARCHAR(255) NOT NULL, data DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bisita (id INT AUTO_INCREMENT NOT NULL, izena VARCHAR(255) DEFAULT NULL, nondik VARCHAR(255) DEFAULT NULL, itxita TINYINT(1) NOT NULL DEFAULT FALSE, data DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bisitaria (id INT AUTO_INCREMENT NOT NULL, bilera_id INT DEFAULT NULL, bisita_id INT DEFAULT NULL, izena VARCHAR(255) NOT NULL, abizena VARCHAR(255) NOT NULL, nondik VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, INDEX IDX_FF74794FD69F818F (bilera_id), INDEX IDX_FF74794FBF8B5873 (bisita_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE langilea (id INT AUTO_INCREMENT NOT NULL, izena VARCHAR(255) NOT NULL, abizena VARCHAR(255) NOT NULL, telefonoa VARCHAR(255) NOT NULL, nondik VARCHAR(255) NOT NULL, giltza TINYINT(1) NOT NULL, data DATETIME NOT NULL, firma VARCHAR(255) NOT NULL, irteera TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bisitaria ADD CONSTRAINT FK_FF74794FD69F818F FOREIGN KEY (bilera_id) REFERENCES bilera (id)');
        $this->addSql('ALTER TABLE bisitaria ADD CONSTRAINT FK_FF74794FBF8B5873 FOREIGN KEY (bisita_id) REFERENCES bisita (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bisitaria DROP FOREIGN KEY FK_FF74794FD69F818F');
        $this->addSql('ALTER TABLE bisitaria DROP FOREIGN KEY FK_FF74794FBF8B5873');
        $this->addSql('DROP TABLE bilera');
        $this->addSql('DROP TABLE bisita');
        $this->addSql('DROP TABLE bisitaria');
        $this->addSql('DROP TABLE langilea');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
