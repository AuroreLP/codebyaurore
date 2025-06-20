<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findValidatedComments(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.status = :status')
            ->setParameter('status', 'validé')
            ->orderBy('c.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPendingComments(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.status = :status')
            ->setParameter('status', 'en attente')
            ->orderBy('c.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findValidByArticle(Article $article): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.article = :article')
            ->andWhere('c.status = :status')
            ->setParameter('article', $article)
            ->setParameter('status', comment::STATUS_VALIDATED)
            ->orderBy('c.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

}
