<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190307224526 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE db_cart ADD date VARCHAR(15) DEFAULT NULL, ADD idprod VARCHAR(10) NOT NULL, DROP day, CHANGE name name VARCHAR(50) DEFAULT NULL, CHANGE price price VARCHAR(12) DEFAULT NULL, CHANGE dateadd dateadd VARCHAR(15) DEFAULT NULL, CHANGE dateupdate dateupdate VARCHAR(15) DEFAULT NULL, CHANGE idday idday VARCHAR(15) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE db_cart ADD day TEXT NOT NULL COLLATE utf8mb4_unicode_ci, DROP date, DROP idprod, CHANGE name name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE price price DOUBLE PRECISION DEFAULT NULL, CHANGE dateadd dateadd TEXT NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE dateupdate dateupdate TEXT NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE idday idday VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
