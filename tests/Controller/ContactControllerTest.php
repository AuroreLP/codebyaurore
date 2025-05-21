<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Mime\Email;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;

class ContactControllerTest extends WebTestCase
{
    use MailerAssertionsTrait;

    public function testContactFormSendsEmail(): void
    {
        $client = static::createClient();

        $crawler = $client->request('POST', '/contact');

        $form = $crawler->selectButton('Envoyer')->form([
            'message[lastname]' => 'Dupont',
            'message[firstname]' => 'Jean',
            'message[email]' => 'jean@example.com',
            'message[phone]' => '0600000000',
            'message[subject]' => 'Demande de renseignement',
            'message[content]' => 'Bonjour, je souhaite en savoir plus sur vos services.',
        ]);

        $client->submit($form);

        // Vérifie que la réponse redirige
        $this->assertResponseRedirects('/contact');

        // Suivre la redirection
        $client->followRedirect();

        // Vérifie le message de succès
        $this->assertSelectorTextContains('div.alert.alert-success', 'Votre message a été envoyé avec succès.');

        // Vérifie qu’un email a bien été envoyé
        $this->assertEmailCount(1);

        // Vérifie le contenu de l'email
        $email = $this->getMailerMessage(0);
        $this->assertInstanceOf(Email::class, $email);
        $this->assertEmailHeaderSame($email, 'Subject', 'Nouveau message de Jean Dupont');
        $this->assertEmailTextBodyContains($email, 'Bonjour, je souhaite en savoir plus sur vos services.');
    }
}
