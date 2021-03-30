<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210330114727 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, figure_id INT NOT NULL, user_id INT NOT NULL, content VARCHAR(500) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_9474526C5C011B5 (figure_id), INDEX IDX_9474526CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE figure (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, tricks_group_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_2F57B37AA76ED395 (user_id), INDEX IDX_2F57B37ADE4E02E0 (tricks_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, figure_id INT DEFAULT NULL, link VARCHAR(500) DEFAULT NULL, INDEX IDX_E01FBE6A5C011B5 (figure_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tricks_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, login VARCHAR(60) NOT NULL, password VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, token VARCHAR(500) DEFAULT NULL, image VARCHAR(500) DEFAULT NULL, reset_token VARCHAR(500) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE videos (id INT AUTO_INCREMENT NOT NULL, figure_id INT DEFAULT NULL, link VARCHAR(500) NOT NULL, INDEX IDX_29AA64325C011B5 (figure_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C5C011B5 FOREIGN KEY (figure_id) REFERENCES figure (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE figure ADD CONSTRAINT FK_2F57B37AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE figure ADD CONSTRAINT FK_2F57B37ADE4E02E0 FOREIGN KEY (tricks_group_id) REFERENCES tricks_group (id)');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A5C011B5 FOREIGN KEY (figure_id) REFERENCES figure (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE videos ADD CONSTRAINT FK_29AA64325C011B5 FOREIGN KEY (figure_id) REFERENCES figure (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C5C011B5');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A5C011B5');
        $this->addSql('ALTER TABLE videos DROP FOREIGN KEY FK_29AA64325C011B5');
        $this->addSql('ALTER TABLE figure DROP FOREIGN KEY FK_2F57B37ADE4E02E0');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE figure DROP FOREIGN KEY FK_2F57B37AA76ED395');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE figure');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE tricks_group');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE videos');
    }
}
