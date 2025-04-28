<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250401091203 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rendre la relation entre Comment et Article obligatoire (article_id NOT NULL)';
    }

    public function up(Schema $schema): void
    {
        // Supprimer la contrainte existante
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C7294869C');

        // Modifier la colonne pour qu'elle soit NOT NULL
        $this->addSql('ALTER TABLE comment MODIFY article_id INT NOT NULL');

        // Réajouter la contrainte avec ON DELETE CASCADE pour éviter les incohérences
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C7294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // Supprimer la contrainte pour revenir en arrière
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C7294869C');

        // Remettre la colonne en NULLABLE
        $this->addSql('ALTER TABLE comment MODIFY article_id INT DEFAULT NULL');

        // Réajouter la contrainte initiale (sans ON DELETE CASCADE si ce n'était pas le cas avant)
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
    }
}
