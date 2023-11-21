<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231120154318 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE produits_distributeurs (produits_id INT NOT NULL, distributeurs_id INT NOT NULL, INDEX IDX_3F2086E8CD11A2CF (produits_id), INDEX IDX_3F2086E89CE97DF1 (distributeurs_id), PRIMARY KEY(produits_id, distributeurs_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE produits_distributeurs ADD CONSTRAINT FK_3F2086E8CD11A2CF FOREIGN KEY (produits_id) REFERENCES produits (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produits_distributeurs ADD CONSTRAINT FK_3F2086E89CE97DF1 FOREIGN KEY (distributeurs_id) REFERENCES distributeurs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produits ADD categorie_id INT NOT NULL');
        $this->addSql('ALTER TABLE produits ADD CONSTRAINT FK_BE2DDF8CBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categories (id)');
        $this->addSql('CREATE INDEX IDX_BE2DDF8CBCF5E72D ON produits (categorie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produits_distributeurs DROP FOREIGN KEY FK_3F2086E8CD11A2CF');
        $this->addSql('ALTER TABLE produits_distributeurs DROP FOREIGN KEY FK_3F2086E89CE97DF1');
        $this->addSql('DROP TABLE produits_distributeurs');
        $this->addSql('ALTER TABLE produits DROP FOREIGN KEY FK_BE2DDF8CBCF5E72D');
        $this->addSql('DROP INDEX IDX_BE2DDF8CBCF5E72D ON produits');
        $this->addSql('ALTER TABLE produits DROP categorie_id');
    }
}
