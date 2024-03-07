<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240306182122 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, pokemon_name_id INT NOT NULL, moves JSON DEFAULT NULL, ability VARCHAR(50) NOT NULL, ivs JSON NOT NULL, nature VARCHAR(20) NOT NULL, INDEX IDX_C4E0A61F1D7DE047 (pokemon_name_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61F1D7DE047 FOREIGN KEY (pokemon_name_id) REFERENCES pokedex (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61F1D7DE047');
        $this->addSql('DROP TABLE team');
    }
}
