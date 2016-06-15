<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160615212842 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE articles (id INT AUTO_INCREMENT NOT NULL COMMENT \'Article ID\', type ENUM(\'news\', \'photo_report\', \'article\') NOT NULL COMMENT \'Type(DC2Type:ArticleType)\', name VARCHAR(255) NOT NULL COMMENT \'Name\', body LONGTEXT DEFAULT NULL COMMENT \'Body\', is_publish TINYINT(1) NOT NULL COMMENT \'Is publish\', publish_at DATETIME DEFAULT NULL COMMENT \'Publish At\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE article_movie (article_id INT NOT NULL COMMENT \'Article ID\', movie_id INT NOT NULL COMMENT \'Movie ID\', INDEX IDX_6AE9807E7294869C (article_id), INDEX IDX_6AE9807E8F93B6FC (movie_id), PRIMARY KEY(article_id, movie_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL COMMENT \'Image ID\', author_id INT NOT NULL COMMENT \'User ID\', name VARCHAR(255) NOT NULL COMMENT \'Name\', image VARCHAR(255) DEFAULT NULL COMMENT \'Image\', updated_at DATETIME DEFAULT NULL COMMENT \'Updated at\', INDEX IDX_E01FBE6AF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movies (id INT AUTO_INCREMENT NOT NULL COMMENT \'Movie ID\', group_id INT DEFAULT NULL COMMENT \'Movie Group ID\', detail_id INT DEFAULT NULL COMMENT \'Movie Detail ID\', name VARCHAR(255) NOT NULL COMMENT \'Name\', INDEX IDX_C61EED30FE54D947 (group_id), UNIQUE INDEX UNIQ_C61EED30D8D003BB (detail_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movie_tag (movie_id INT NOT NULL COMMENT \'Movie ID\', tag_id INT NOT NULL COMMENT \'Tag ID\', INDEX IDX_DCD9F2918F93B6FC (movie_id), INDEX IDX_DCD9F291BAD26311 (tag_id), PRIMARY KEY(movie_id, tag_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movie_comments (id INT AUTO_INCREMENT NOT NULL COMMENT \'Movie Comment ID\', author_id INT NOT NULL COMMENT \'User ID\', movie_id INT NOT NULL COMMENT \'Movie ID\', body LONGTEXT NOT NULL COMMENT \'Body\', is_publish TINYINT(1) NOT NULL COMMENT \'Is publish\', created_at DATETIME NOT NULL COMMENT \'Created At\', INDEX IDX_83680DA7F675F31B (author_id), INDEX IDX_83680DA78F93B6FC (movie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movie_comment_image (movie_comment_id INT NOT NULL COMMENT \'Movie Comment ID\', image_id INT NOT NULL COMMENT \'Image ID\', INDEX IDX_D3540B5911990D2F (movie_comment_id), INDEX IDX_D3540B593DA5256D (image_id), PRIMARY KEY(movie_comment_id, image_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movie_details (id INT AUTO_INCREMENT NOT NULL COMMENT \'Movie Detail ID\', body LONGTEXT NOT NULL COMMENT \'Body\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movie_groups (id INT AUTO_INCREMENT NOT NULL COMMENT \'Movie Group ID\', name VARCHAR(255) NOT NULL COMMENT \'Name\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movie_sessions (id INT AUTO_INCREMENT NOT NULL COMMENT \'Session ID\', movie_id INT DEFAULT NULL COMMENT \'Movie ID\', name VARCHAR(255) NOT NULL COMMENT \'Name\', INDEX IDX_4696069E8F93B6FC (movie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tags (id INT AUTO_INCREMENT NOT NULL COMMENT \'Tag ID\', name VARCHAR(255) NOT NULL COMMENT \'Name\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_groups (id INT AUTO_INCREMENT NOT NULL COMMENT \'Group ID\', name VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_953F224D5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL COMMENT \'User ID\', username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL COMMENT \'Last name\', firstname VARCHAR(255) DEFAULT NULL COMMENT \'First name\', avatar VARCHAR(255) DEFAULT NULL COMMENT \'Avatar file path\', api_token VARCHAR(255) DEFAULT NULL COMMENT \'Token for API\', api_token_expire_at DATETIME DEFAULT NULL COMMENT \'Time when API token will be expired\', created_at DATETIME NOT NULL COMMENT \'Created At\', updated_at DATETIME NOT NULL COMMENT \'Updated At\', UNIQUE INDEX UNIQ_1483A5E992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_1483A5E9A0D96FBF (email_canonical), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_group (user_id INT NOT NULL COMMENT \'User ID\', group_id INT NOT NULL COMMENT \'Group ID\', INDEX IDX_8F02BF9DA76ED395 (user_id), INDEX IDX_8F02BF9DFE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article_movie ADD CONSTRAINT FK_6AE9807E7294869C FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article_movie ADD CONSTRAINT FK_6AE9807E8F93B6FC FOREIGN KEY (movie_id) REFERENCES movies (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6AF675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE movies ADD CONSTRAINT FK_C61EED30FE54D947 FOREIGN KEY (group_id) REFERENCES movie_groups (id)');
        $this->addSql('ALTER TABLE movies ADD CONSTRAINT FK_C61EED30D8D003BB FOREIGN KEY (detail_id) REFERENCES movie_details (id)');
        $this->addSql('ALTER TABLE movie_tag ADD CONSTRAINT FK_DCD9F2918F93B6FC FOREIGN KEY (movie_id) REFERENCES movies (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE movie_tag ADD CONSTRAINT FK_DCD9F291BAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE movie_comments ADD CONSTRAINT FK_83680DA7F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE movie_comments ADD CONSTRAINT FK_83680DA78F93B6FC FOREIGN KEY (movie_id) REFERENCES movies (id)');
        $this->addSql('ALTER TABLE movie_comment_image ADD CONSTRAINT FK_D3540B5911990D2F FOREIGN KEY (movie_comment_id) REFERENCES movie_comments (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE movie_comment_image ADD CONSTRAINT FK_D3540B593DA5256D FOREIGN KEY (image_id) REFERENCES images (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE movie_sessions ADD CONSTRAINT FK_4696069E8F93B6FC FOREIGN KEY (movie_id) REFERENCES movies (id)');
        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9DFE54D947 FOREIGN KEY (group_id) REFERENCES user_groups (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE article_movie DROP FOREIGN KEY FK_6AE9807E7294869C');
        $this->addSql('ALTER TABLE movie_comment_image DROP FOREIGN KEY FK_D3540B593DA5256D');
        $this->addSql('ALTER TABLE article_movie DROP FOREIGN KEY FK_6AE9807E8F93B6FC');
        $this->addSql('ALTER TABLE movie_tag DROP FOREIGN KEY FK_DCD9F2918F93B6FC');
        $this->addSql('ALTER TABLE movie_comments DROP FOREIGN KEY FK_83680DA78F93B6FC');
        $this->addSql('ALTER TABLE movie_sessions DROP FOREIGN KEY FK_4696069E8F93B6FC');
        $this->addSql('ALTER TABLE movie_comment_image DROP FOREIGN KEY FK_D3540B5911990D2F');
        $this->addSql('ALTER TABLE movies DROP FOREIGN KEY FK_C61EED30D8D003BB');
        $this->addSql('ALTER TABLE movies DROP FOREIGN KEY FK_C61EED30FE54D947');
        $this->addSql('ALTER TABLE movie_tag DROP FOREIGN KEY FK_DCD9F291BAD26311');
        $this->addSql('ALTER TABLE user_group DROP FOREIGN KEY FK_8F02BF9DFE54D947');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6AF675F31B');
        $this->addSql('ALTER TABLE movie_comments DROP FOREIGN KEY FK_83680DA7F675F31B');
        $this->addSql('ALTER TABLE user_group DROP FOREIGN KEY FK_8F02BF9DA76ED395');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE article_movie');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE movies');
        $this->addSql('DROP TABLE movie_tag');
        $this->addSql('DROP TABLE movie_comments');
        $this->addSql('DROP TABLE movie_comment_image');
        $this->addSql('DROP TABLE movie_details');
        $this->addSql('DROP TABLE movie_groups');
        $this->addSql('DROP TABLE movie_sessions');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE user_groups');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE user_group');
    }
}
