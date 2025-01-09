<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250107082414 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bisitaria ADD CONSTRAINT FK_FF74794FD69F818F FOREIGN KEY (bilera_id) REFERENCES bilera (id)');
        $this->addSql('ALTER TABLE bisitaria ADD CONSTRAINT FK_FF74794FBF8B5873 FOREIGN KEY (bisita_id) REFERENCES bisita (id)');
        $this->addSql('CREATE INDEX IDX_FF74794FD69F818F ON bisitaria (bilera_id)');
        $this->addSql('CREATE INDEX IDX_FF74794FBF8B5873 ON bisitaria (bisita_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bisitaria DROP FOREIGN KEY FK_FF74794FD69F818F');
        $this->addSql('ALTER TABLE bisitaria DROP FOREIGN KEY FK_FF74794FBF8B5873');
        $this->addSql('DROP INDEX IDX_FF74794FD69F818F ON bisitaria');
        $this->addSql('DROP INDEX IDX_FF74794FBF8B5873 ON bisitaria');
    }
}
