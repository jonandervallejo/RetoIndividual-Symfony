<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241016074849 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE curso (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) DEFAULT NULL, �descripcion VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usuario (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, root TINYINT(1) NOT NULL, apellido1 VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usuario_curso (id INT AUTO_INCREMENT NOT NULL, id_usuario_id INT DEFAULT NULL, id_curso_id INT DEFAULT NULL, INDEX IDX_D7E52AF27EB2C349 (id_usuario_id), INDEX IDX_D7E52AF2D710A68A (id_curso_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE usuario_curso ADD CONSTRAINT FK_D7E52AF27EB2C349 FOREIGN KEY (id_usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE usuario_curso ADD CONSTRAINT FK_D7E52AF2D710A68A FOREIGN KEY (id_curso_id) REFERENCES curso (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE usuario_curso DROP FOREIGN KEY FK_D7E52AF27EB2C349');
        $this->addSql('ALTER TABLE usuario_curso DROP FOREIGN KEY FK_D7E52AF2D710A68A');
        $this->addSql('DROP TABLE curso');
        $this->addSql('DROP TABLE usuario');
        $this->addSql('DROP TABLE usuario_curso');
    }
}
