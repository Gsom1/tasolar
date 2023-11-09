<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231109130937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE card_balance_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE card_balance (id INT NOT NULL, card_number VARCHAR(16) NOT NULL, balance INT NOT NULL, currency VARCHAR(8) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FBEE9588E4AF4C20 ON card_balance (card_number)');
        $this->addSql('ALTER TABLE credit_card_transaction_parameters ALTER type TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE credit_card_transaction_parameters ALTER type TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE payment_transaction ALTER status TYPE INTEGER');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE card_balance_id_seq CASCADE');
        $this->addSql('DROP TABLE card_balance');
        $this->addSql('ALTER TABLE payment_transaction ALTER status TYPE INT');
        $this->addSql('ALTER TABLE credit_card_transaction_parameters ALTER type TYPE VARCHAR(16)');
    }
}
