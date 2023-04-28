<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230307045753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activiter (id INT AUTO_INCREMENT NOT NULL, ref_categ_id INT NOT NULL, id_user_id INT NOT NULL, titre VARCHAR(255) NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, INDEX IDX_16C6E236E009D95 (ref_categ_id), INDEX IDX_16C6E2379F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE actualite (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, categorie VARCHAR(255) NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie_activite (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentaire_act (id INT AUTO_INCREMENT NOT NULL, id_actualite_id INT DEFAULT NULL, id_user_id INT NOT NULL, contenu LONGTEXT NOT NULL, date DATETIME NOT NULL, INDEX IDX_348D924281454501 (id_actualite_id), INDEX IDX_348D924279F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, id_user_id INT DEFAULT NULL, titre VARCHAR(20) NOT NULL, description VARCHAR(255) NOT NULL, date DATE NOT NULL, img LONGTEXT NOT NULL, lieu VARCHAR(30) NOT NULL, pos1 DOUBLE PRECISION NOT NULL, pos2 DOUBLE PRECISION NOT NULL, INDEX IDX_3BAE0AA779F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offre (id INT AUTO_INCREMENT NOT NULL, id_user_id INT NOT NULL, id_category_id INT DEFAULT NULL, date DATE NOT NULL, affiche VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, description LONGTEXT NOT NULL, nbplace INT NOT NULL, INDEX IDX_AF86866F79F37AE5 (id_user_id), INDEX IDX_AF86866FA545015 (id_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participant (id INT AUTO_INCREMENT NOT NULL, event_id INT DEFAULT NULL, id_user_id INT DEFAULT NULL, date_participation DATE NOT NULL, INDEX IDX_D79F6B1171F7E88B (event_id), INDEX IDX_D79F6B1179F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, date DATE NOT NULL, etat TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reclamations (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, date DATE NOT NULL, etat TINYINT(1) DEFAULT NULL, INDEX IDX_1CAD6B76A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, id_offre_id INT NOT NULL, id_user_id INT NOT NULL, date DATE NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_42C849551C13BCCF (id_offre_id), INDEX IDX_42C8495579F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stars (id INT AUTO_INCREMENT NOT NULL, u_id_id INT DEFAULT NULL, id_offre_id INT DEFAULT NULL, rate_index INT DEFAULT NULL, INDEX IDX_11DC02C6782F39A (u_id_id), INDEX IDX_11DC02C1C13BCCF (id_offre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, diplome VARCHAR(255) DEFAULT NULL, experience VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, reset_token VARCHAR(255) DEFAULT NULL, status VARCHAR(10) DEFAULT \'Actif\', is_blocked TINYINT(1) NOT NULL, is_approved TINYINT(1) NOT NULL, etat VARCHAR(10) DEFAULT \'Actif\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activiter ADD CONSTRAINT FK_16C6E236E009D95 FOREIGN KEY (ref_categ_id) REFERENCES categorie_activite (id)');
        $this->addSql('ALTER TABLE activiter ADD CONSTRAINT FK_16C6E2379F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commentaire_act ADD CONSTRAINT FK_348D924281454501 FOREIGN KEY (id_actualite_id) REFERENCES actualite (id)');
        $this->addSql('ALTER TABLE commentaire_act ADD CONSTRAINT FK_348D924279F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA779F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT FK_AF86866F79F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT FK_AF86866FA545015 FOREIGN KEY (id_category_id) REFERENCES categorie_activite (id)');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B1171F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B1179F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reclamations ADD CONSTRAINT FK_1CAD6B76A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849551C13BCCF FOREIGN KEY (id_offre_id) REFERENCES offre (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495579F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE stars ADD CONSTRAINT FK_11DC02C6782F39A FOREIGN KEY (u_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE stars ADD CONSTRAINT FK_11DC02C1C13BCCF FOREIGN KEY (id_offre_id) REFERENCES offre (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activiter DROP FOREIGN KEY FK_16C6E236E009D95');
        $this->addSql('ALTER TABLE activiter DROP FOREIGN KEY FK_16C6E2379F37AE5');
        $this->addSql('ALTER TABLE commentaire_act DROP FOREIGN KEY FK_348D924281454501');
        $this->addSql('ALTER TABLE commentaire_act DROP FOREIGN KEY FK_348D924279F37AE5');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA779F37AE5');
        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY FK_AF86866F79F37AE5');
        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY FK_AF86866FA545015');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B1171F7E88B');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B1179F37AE5');
        $this->addSql('ALTER TABLE reclamations DROP FOREIGN KEY FK_1CAD6B76A76ED395');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849551C13BCCF');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495579F37AE5');
        $this->addSql('ALTER TABLE stars DROP FOREIGN KEY FK_11DC02C6782F39A');
        $this->addSql('ALTER TABLE stars DROP FOREIGN KEY FK_11DC02C1C13BCCF');
        $this->addSql('DROP TABLE activiter');
        $this->addSql('DROP TABLE actualite');
        $this->addSql('DROP TABLE categorie_activite');
        $this->addSql('DROP TABLE commentaire_act');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE offre');
        $this->addSql('DROP TABLE participant');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE reclamations');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE stars');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
