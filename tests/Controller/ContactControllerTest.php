<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Mailer\DataCollector\MessageDataCollector;

class ContactControllerTest extends WebTestCase
{
    public function testContactFormSendsEmail(): void
    {
        $client = static::createClient();

        // Activer le profiler pour collecter les emails
        $client->enableProfiler();

        // Simuler une requête POST vers la route /contact
        $crawler = $client->request('GET', '/contact');
        $form = $crawler->selectButton('Envoyer')->form();
        $form['message[lastname]'] = 'Dupont';
        $form['message[firstname]'] = 'Jean';
        $form['message[email]'] = 'jean@example.com';
        $form['message[phone]'] = '0600000000';
        $form['message[subject]'] = 'Demande de renseignement';
        $form['message[content]'] = 'Bonjour, je souhaite en savoir plus sur vos services.';

        // Soumettre le formulaire
        $client->submit($form);

        // Vérifier que la réponse est une redirection
        $this->assertResponseRedirects('/contact');

        // Vérifier que le message de succès est dans la session flash
        $client->followRedirect();
        $this->assertSelectorTextContains('div.alert.alert-success', 'Votre message a été envoyé avec succès.');

        // Vérifier que le profiler est activé
        $profile = $client->getProfile();
        $this->assertNotNull($profile, 'The profiler is not enabled.');

        // Récupérer le profiler et vérifier qu'un email a été envoyé
        $mailCollector = $profile->getCollector('mailer');
        $this->assertInstanceOf(MessageDataCollector::class, $mailCollector, 'The mailer collector is not an instance of MessageDataCollector.');


        // Vérifier qu'un email a été envoyé
        $this->assertSame(1, $mailCollector->count(), 'No email was sent.');

        // Vérifier les détails de l'email envoyé
        $sentEmails = $mailCollector->getRecords();
        $this->assertCount(1, $sentEmails);

        $email = $sentEmails[0]->getMessage();
        $this->assertSame('jean@example.com', $email->getTo()[0]->getAddress());
        $this->assertSame('Demande de renseignement', $email->getSubject());
        $this->assertStringContainsString('Bonjour, je souhaite en savoir plus sur vos services.', $email->getTextBody());
    }
}