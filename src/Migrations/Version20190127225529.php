<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190127225529 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE place (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, INDEX IDX_741D53CDA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tariff (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, user_id INT DEFAULT NULL, price INT DEFAULT NULL, description LONGTEXT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_9465207DC54C8C93 (type_id), INDEX IDX_9465207DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meter (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, unit_id INT DEFAULT NULL, place_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_5E010CAEC54C8C93 (type_id), INDEX IDX_5E010CAEF8BD700D (unit_id), INDEX IDX_5E010CAEDA6A219 (place_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meter_tariff (meter_id INT NOT NULL, tariff_id INT NOT NULL, INDEX IDX_1BB2FE976E15CA9E (meter_id), INDEX IDX_1BB2FE9792348FD2 (tariff_id), PRIMARY KEY(meter_id, tariff_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE picture (id INT AUTO_INCREMENT NOT NULL, original_name VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, created DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE indication (id INT AUTO_INCREMENT NOT NULL, meter_id INT DEFAULT NULL, tariff_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, value INT NOT NULL, date DATETIME NOT NULL, text_date VARCHAR(255) NOT NULL, unit VARCHAR(255) DEFAULT NULL, file VARCHAR(255) DEFAULT NULL, INDEX IDX_D15065D76E15CA9E (meter_id), INDEX IDX_D15065D792348FD2 (tariff_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bill (id INT AUTO_INCREMENT NOT NULL, place_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, amount INT DEFAULT NULL, period DATETIME DEFAULT NULL, text_period VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, date DATETIME NOT NULL, text_date VARCHAR(255) NOT NULL, type VARCHAR(255) DEFAULT NULL, note LONGTEXT DEFAULT NULL, actually_paid INT DEFAULT NULL, pay_date DATETIME DEFAULT NULL, pay_date_text VARCHAR(255) DEFAULT NULL, file VARCHAR(255) DEFAULT NULL, is_paid TINYINT(1) NOT NULL, INDEX IDX_7A2119E3DA6A219 (place_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meter_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, icon VARCHAR(32) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unit (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, picture_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, roles VARCHAR(255) DEFAULT NULL, profile_pic VARCHAR(255) DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) DEFAULT \'0\' NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649EE45BDBF (picture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tariff_type (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, INDEX IDX_2BBAEAA7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file_upload (id INT AUTO_INCREMENT NOT NULL, bill_id INT DEFAULT NULL, original_name VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, created DATETIME NOT NULL, INDEX IDX_AFAAC0A01A8C12F5 (bill_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE place ADD CONSTRAINT FK_741D53CDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tariff ADD CONSTRAINT FK_9465207DC54C8C93 FOREIGN KEY (type_id) REFERENCES tariff_type (id)');
        $this->addSql('ALTER TABLE tariff ADD CONSTRAINT FK_9465207DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE meter ADD CONSTRAINT FK_5E010CAEC54C8C93 FOREIGN KEY (type_id) REFERENCES meter_type (id)');
        $this->addSql('ALTER TABLE meter ADD CONSTRAINT FK_5E010CAEF8BD700D FOREIGN KEY (unit_id) REFERENCES unit (id)');
        $this->addSql('ALTER TABLE meter ADD CONSTRAINT FK_5E010CAEDA6A219 FOREIGN KEY (place_id) REFERENCES place (id)');
        $this->addSql('ALTER TABLE meter_tariff ADD CONSTRAINT FK_1BB2FE976E15CA9E FOREIGN KEY (meter_id) REFERENCES meter (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE meter_tariff ADD CONSTRAINT FK_1BB2FE9792348FD2 FOREIGN KEY (tariff_id) REFERENCES tariff (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE indication ADD CONSTRAINT FK_D15065D76E15CA9E FOREIGN KEY (meter_id) REFERENCES meter (id)');
        $this->addSql('ALTER TABLE indication ADD CONSTRAINT FK_D15065D792348FD2 FOREIGN KEY (tariff_id) REFERENCES tariff (id)');
        $this->addSql('ALTER TABLE bill ADD CONSTRAINT FK_7A2119E3DA6A219 FOREIGN KEY (place_id) REFERENCES place (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649EE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id)');
        $this->addSql('ALTER TABLE tariff_type ADD CONSTRAINT FK_2BBAEAA7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE file_upload ADD CONSTRAINT FK_AFAAC0A01A8C12F5 FOREIGN KEY (bill_id) REFERENCES bill (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE meter DROP FOREIGN KEY FK_5E010CAEDA6A219');
        $this->addSql('ALTER TABLE bill DROP FOREIGN KEY FK_7A2119E3DA6A219');
        $this->addSql('ALTER TABLE meter_tariff DROP FOREIGN KEY FK_1BB2FE9792348FD2');
        $this->addSql('ALTER TABLE indication DROP FOREIGN KEY FK_D15065D792348FD2');
        $this->addSql('ALTER TABLE meter_tariff DROP FOREIGN KEY FK_1BB2FE976E15CA9E');
        $this->addSql('ALTER TABLE indication DROP FOREIGN KEY FK_D15065D76E15CA9E');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649EE45BDBF');
        $this->addSql('ALTER TABLE file_upload DROP FOREIGN KEY FK_AFAAC0A01A8C12F5');
        $this->addSql('ALTER TABLE meter DROP FOREIGN KEY FK_5E010CAEC54C8C93');
        $this->addSql('ALTER TABLE meter DROP FOREIGN KEY FK_5E010CAEF8BD700D');
        $this->addSql('ALTER TABLE place DROP FOREIGN KEY FK_741D53CDA76ED395');
        $this->addSql('ALTER TABLE tariff DROP FOREIGN KEY FK_9465207DA76ED395');
        $this->addSql('ALTER TABLE tariff_type DROP FOREIGN KEY FK_2BBAEAA7A76ED395');
        $this->addSql('ALTER TABLE tariff DROP FOREIGN KEY FK_9465207DC54C8C93');
        $this->addSql('DROP TABLE place');
        $this->addSql('DROP TABLE tariff');
        $this->addSql('DROP TABLE meter');
        $this->addSql('DROP TABLE meter_tariff');
        $this->addSql('DROP TABLE picture');
        $this->addSql('DROP TABLE indication');
        $this->addSql('DROP TABLE bill');
        $this->addSql('DROP TABLE meter_type');
        $this->addSql('DROP TABLE unit');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE tariff_type');
        $this->addSql('DROP TABLE file_upload');
    }
}
