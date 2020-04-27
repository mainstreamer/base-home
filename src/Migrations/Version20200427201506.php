<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200427201506 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE users_places (user_id VARCHAR(255) NOT NULL, place_id VARCHAR(255) NOT NULL, INDEX IDX_F148E2C5A76ED395 (user_id), INDEX IDX_F148E2C5DA6A219 (place_id), PRIMARY KEY(user_id, place_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE users_places ADD CONSTRAINT FK_F148E2C5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE users_places ADD CONSTRAINT FK_F148E2C5DA6A219 FOREIGN KEY (place_id) REFERENCES place (id)');
        $this->addSql('ALTER TABLE place DROP FOREIGN KEY FK_741D53CDA76ED395');
        $this->addSql('DROP INDEX IDX_741D53CDA76ED395 ON place');
        $this->addSql('ALTER TABLE place DROP user_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE users_places');
        $this->addSql('ALTER TABLE place ADD user_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE place ADD CONSTRAINT FK_741D53CDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_741D53CDA76ED395 ON place (user_id)');
    }
}
