<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210331103517 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, id_user INT DEFAULT NULL, date_rec DATE NOT NULL, objet VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, statut TINYINT(1) NOT NULL, idReserv INT NOT NULL, INDEX IDX_CE6064046B3CA4B (id_user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponse (id INT AUTO_INCREMENT NOT NULL, id_user INT DEFAULT NULL, date_rep DATE NOT NULL, description VARCHAR(255) NOT NULL, idRec INT DEFAULT NULL, INDEX IDX_5FB6DEC76B3CA4B (id_user), UNIQUE INDEX UNIQ_5FB6DEC7454DD7AB (idRec), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE6064046B3CA4B FOREIGN KEY (id_user) REFERENCES client (id)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC76B3CA4B FOREIGN KEY (id_user) REFERENCES client (id)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC7454DD7AB FOREIGN KEY (idRec) REFERENCES reclamation (id)');
        $this->addSql('ALTER TABLE reservation CHANGE id_user id_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation RENAME INDEX idx_42c849557f449e57 TO IDX_42C849556B3CA4B');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC7454DD7AB');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE reponse');
        $this->addSql('ALTER TABLE reservation CHANGE id_user id_user INT NOT NULL');
        $this->addSql('ALTER TABLE reservation RENAME INDEX idx_42c849556b3ca4b TO IDX_42C849557F449E57');
    }
}
