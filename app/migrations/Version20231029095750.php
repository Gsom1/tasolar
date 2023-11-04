<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231029095750 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE credit_card_transaction_parameters_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE credit_card_transaction_parameters (id INT NOT NULL, transaction_id UUID DEFAULT NULL, card_number VARCHAR(16) NOT NULL, expiry VARCHAR(8) NOT NULL, name VARCHAR(64) NOT NULL, type VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B39043132FC0CB0F ON credit_card_transaction_parameters (transaction_id)');
        $this->addSql('COMMENT ON COLUMN credit_card_transaction_parameters.transaction_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE payment_transaction (id UUID NOT NULL, amount BIGINT NOT NULL, currency VARCHAR(8) NOT NULL, status INTEGER NOT NULL, merchant_id VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN payment_transaction.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE credit_card_transaction_parameters ADD CONSTRAINT FK_B39043132FC0CB0F FOREIGN KEY (transaction_id) REFERENCES payment_transaction (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE credit_card_transaction_parameters_id_seq CASCADE');
        $this->addSql('ALTER TABLE credit_card_transaction_parameters DROP CONSTRAINT FK_B39043132FC0CB0F');
        $this->addSql('DROP TABLE credit_card_transaction_parameters');
        $this->addSql('DROP TABLE payment_transaction');
    }
}
