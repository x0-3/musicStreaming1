<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230502093825 extends AbstractMigration
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
        $this->addSql('ALTER TABLE user_song DROP FOREIGN KEY FK_496CA268A0BDB2F3');
        $this->addSql('ALTER TABLE user_song DROP FOREIGN KEY FK_496CA268A76ED395');
        $this->addSql('ALTER TABLE playlist_user DROP FOREIGN KEY FK_2D8AE12B6BBD148');
        $this->addSql('ALTER TABLE playlist_user DROP FOREIGN KEY FK_2D8AE12BA76ED395');
        $this->addSql('ALTER TABLE playlist_song DROP FOREIGN KEY FK_93F4D9C36BBD148');
        $this->addSql('ALTER TABLE playlist_song DROP FOREIGN KEY FK_93F4D9C3A0BDB2F3');
        $this->addSql('DROP TABLE subscribe_user');
        $this->addSql('DROP TABLE subscribe');
        $this->addSql('DROP TABLE user_song');
        $this->addSql('DROP TABLE playlist_user');
        $this->addSql('DROP TABLE playlist_song');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subscribe_user (subscribe_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_3A629B71A76ED395 (user_id), INDEX IDX_3A629B71C72A4771 (subscribe_id), PRIMARY KEY(subscribe_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE subscribe (id INT AUTO_INCREMENT NOT NULL, date_follow DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_song (user_id INT NOT NULL, song_id INT NOT NULL, INDEX IDX_496CA268A0BDB2F3 (song_id), INDEX IDX_496CA268A76ED395 (user_id), PRIMARY KEY(user_id, song_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE playlist_user (playlist_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_2D8AE12B6BBD148 (playlist_id), INDEX IDX_2D8AE12BA76ED395 (user_id), PRIMARY KEY(playlist_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE playlist_song (playlist_id INT NOT NULL, song_id INT NOT NULL, INDEX IDX_93F4D9C36BBD148 (playlist_id), INDEX IDX_93F4D9C3A0BDB2F3 (song_id), PRIMARY KEY(playlist_id, song_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE subscribe_user ADD CONSTRAINT FK_3A629B71A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subscribe_user ADD CONSTRAINT FK_3A629B71C72A4771 FOREIGN KEY (subscribe_id) REFERENCES subscribe (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_song ADD CONSTRAINT FK_496CA268A0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_song ADD CONSTRAINT FK_496CA268A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE playlist_user ADD CONSTRAINT FK_2D8AE12B6BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE playlist_user ADD CONSTRAINT FK_2D8AE12BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE playlist_song ADD CONSTRAINT FK_93F4D9C36BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE playlist_song ADD CONSTRAINT FK_93F4D9C3A0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
