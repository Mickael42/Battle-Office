<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190723090923 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product CHANGE regular_price regular_price INT NOT NULL, CHANGE reduce_price reduce_price DOUBLE PRECISION DEFAULT NULL, CHANGE quantity quantity DOUBLE PRECISION NOT NULL, CHANGE quantity_promotion quantity_promotion INT DEFAULT NULL');
        $this->addSql('ALTER TABLE client DROP order_id_id, CHANGE city city VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` CHANGE address_order address_order VARCHAR(255) DEFAULT NULL, CHANGE address_complement_order address_complement_order VARCHAR(255) DEFAULT NULL, CHANGE city_order city_order VARCHAR(255) DEFAULT NULL, CHANGE zip_code_order zip_code_order INT DEFAULT NULL, CHANGE country_order country_order VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE client ADD order_id_id INT NOT NULL, CHANGE city city VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455FCDAEAAA FOREIGN KEY (order_id_id) REFERENCES `order` (id)');
        $this->addSql('CREATE INDEX IDX_C7440455FCDAEAAA ON client (order_id_id)');
        $this->addSql('ALTER TABLE `order` CHANGE address_order address_order VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE address_complement_order address_complement_order VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE city_order city_order VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE zip_code_order zip_code_order INT DEFAULT NULL, CHANGE country_order country_order VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE product CHANGE regular_price regular_price DOUBLE PRECISION NOT NULL, CHANGE reduce_price reduce_price DOUBLE PRECISION DEFAULT \'NULL\', CHANGE quantity quantity INT NOT NULL, CHANGE quantity_promotion quantity_promotion INT DEFAULT NULL');
    }
}
