<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211122114241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment ADD command_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D33E1689A FOREIGN KEY (command_id) REFERENCES command (id)');
        $this->addSql('CREATE INDEX IDX_6D28840D33E1689A ON payment (command_id)');
        $this->addSql('ALTER TABLE product ADD command_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD33E1689A FOREIGN KEY (command_id) REFERENCES command (id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD33E1689A ON product (command_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D33E1689A');
        $this->addSql('DROP INDEX IDX_6D28840D33E1689A ON payment');
        $this->addSql('ALTER TABLE payment DROP command_id');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD33E1689A');
        $this->addSql('DROP INDEX IDX_D34A04AD33E1689A ON product');
        $this->addSql('ALTER TABLE product DROP command_id');
    }
}
