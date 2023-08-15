<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230815093229 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, company_name VARCHAR(255) NOT NULL, registration_code VARCHAR(100) NOT NULL, vat VARCHAR(120) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, mobile_phone VARCHAR(255) DEFAULT NULL, status VARCHAR(100) DEFAULT \'completed\', INDEX idx_registration_code (registration_code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company_turnover (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, year VARCHAR(50) NOT NULL, non_current_assets VARCHAR(100) DEFAULT NULL, current_assets VARCHAR(100) DEFAULT NULL, equity_capital VARCHAR(100) DEFAULT NULL, amounts_payable_and_other_liabilities VARCHAR(100) DEFAULT NULL, sales_revenue VARCHAR(100) DEFAULT NULL, profit_loss_before_taxes VARCHAR(100) DEFAULT NULL, profit_before_taxes_margin VARCHAR(100) DEFAULT NULL, net_profit_loss VARCHAR(100) DEFAULT NULL, net_profit_margin VARCHAR(100) DEFAULT NULL, INDEX IDX_1EAFB341979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE company_turnover ADD CONSTRAINT FK_1EAFB341979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company_turnover DROP FOREIGN KEY FK_1EAFB341979B1AD6');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE company_turnover');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
