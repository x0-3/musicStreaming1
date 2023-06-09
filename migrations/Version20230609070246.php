<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230609070246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album ADD uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('UPDATE album SET uuid = UUID()');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_39986E43D17F50A6 ON album (uuid)');
        $this->addSql('ALTER TABLE comment ADD uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('UPDATE comment SET uuid = UUID()');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9474526CD17F50A6 ON comment (uuid)');
        $this->addSql('ALTER TABLE genre ADD uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('UPDATE genre SET uuid = UUID()');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_835033F8D17F50A6 ON genre (uuid)');
        $this->addSql('ALTER TABLE playlist ADD uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('UPDATE playlist SET uuid = UUID()');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D782112DD17F50A6 ON playlist (uuid)');
        $this->addSql('ALTER TABLE song ADD uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('UPDATE song SET uuid = UUID()');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_33EDEEA1D17F50A6 ON song (uuid)');
        $this->addSql('ALTER TABLE user ADD uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('UPDATE user SET uuid = UUID()');        
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649D17F50A6 ON user (uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_39986E43D17F50A6 ON album');
        $this->addSql('ALTER TABLE album DROP uuid');
        $this->addSql('DROP INDEX UNIQ_835033F8D17F50A6 ON genre');
        $this->addSql('ALTER TABLE genre DROP uuid');
        $this->addSql('DROP INDEX UNIQ_D782112DD17F50A6 ON playlist');
        $this->addSql('ALTER TABLE playlist DROP uuid');
        $this->addSql('DROP INDEX UNIQ_9474526CD17F50A6 ON comment');
        $this->addSql('ALTER TABLE comment DROP uuid');
        $this->addSql('DROP INDEX UNIQ_33EDEEA1D17F50A6 ON song');
        $this->addSql('ALTER TABLE song DROP uuid');
        $this->addSql('DROP INDEX UNIQ_8D93D649D17F50A6 ON user');
        $this->addSql('ALTER TABLE user DROP uuid');
    }
}
