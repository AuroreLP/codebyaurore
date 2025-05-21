<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Mailer\Test\InteractsWithMailer;
use Zenstruck\Mailer\Test\TestEmail;

class ContactControllerTest extends WebTestCase
{
    use InteractsWithMailer;

    public function testContactFormSendsEmail(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/contact');

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
        $this->mailer()->assertSentEmailCount(1);

        // Récupère l'email envoyé
        $this->mailer()->assertEmailSentTo('aleperff@gmail.com', function(TestEmail $email) {
            $email->assertSubject('Nouveau message de Jean Dupont');
            $email->assertTextContains('Bonjour, je souhaite en savoir plus sur vos services.');
            $email->assertFrom('jean@example.com');

            return true;
        });
    }
}