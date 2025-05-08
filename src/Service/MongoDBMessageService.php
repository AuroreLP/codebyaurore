<?php

namespace App\Service;

use App\Document\Message;
use Doctrine\ODM\MongoDB\DocumentManager;

class MongoDBMessageService
{
    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    // Enregistrer le message dans MongoDB
    public function saveMessage($lastname, $firstname, $phone, $email, $subject, $content)
    {
        $messageDoc = new Message();
        $messageDoc->setLastname($lastname);
        $messageDoc->setFirstname($firstname);
        $messageDoc->setPhone($phone);
        $messageDoc->setEmail($email);
        $messageDoc->setSubject($subject);
        $messageDoc->setContent($content);
        $messageDoc->setCreatedAt(new \DateTime());

        // Persister le message dans MongoDB
        $this->documentManager->persist($messageDoc);
        $this->documentManager->flush(); // Sauvegarde dans MongoDB

        return $messageDoc->getId(); // Retourne l'ID du message
    }
}