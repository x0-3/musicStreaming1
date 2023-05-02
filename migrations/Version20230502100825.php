<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230502100825 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscribe_user DROP FOREIGN KEY FK_3A629B71A76ED395');
        $this->addSql('ALTER TABLE subscribe_user DROP FOREIGN KEY FK_3A629B71C72A4771');
        $this->addSql('DROP TABLE subscribe_user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subscribe_user (subscribe_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_3A629B71C72A4771 (subscribe_id), INDEX IDX_3A629B71A76ED395 (user_id), PRIMARY KEY(subscribe_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE subscribe_user ADD CONSTRAINT FK_3A629B71A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subscribe_user ADD CONSTRAINT FK_3A629B71C72A4771 FOREIGN KEY (subscribe_id) REFERENCES subscribe (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
