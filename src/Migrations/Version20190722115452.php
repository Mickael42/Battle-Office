<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190722115452 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, regular_price INT NOT NULL, reduce_price INT DEFAULT NULL, quantity INT NOT NULL, quantity_promotion INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, order_id_id INT NOT NULL, fistname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, phone INT NOT NULL, email VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, address_complement VARCHAR(255) NOT NULL, city VARCHAR(255) DEFAULT NULL, zip_code INT NOT NULL, country VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_C7440455FCDAEAAA (order_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, product_id INT NOT NULL, payment_method VARCHAR(255) NOT NULL, ammount INT NOT NULL, address_order VARCHAR(255) DEFAULT NULL, address_complement_order VARCHAR(255) DEFAULT NULL, city_order VARCHAR(255) DEFAULT NULL, zip_code_order INT DEFAULT NULL, country_order VARCHAR(255) DEFAULT NULL, statut VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_F52993984584665A (product_id), UNIQUE INDEX UNIQ_F529939819EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455FCDAEAAA FOREIGN KEY (order_id_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993984584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939819EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993984584665A');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939819EB6921');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455FCDAEAAA');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE `order`');
    }
}
