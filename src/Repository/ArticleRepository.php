<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findPublishedWithTagsAndCategory(): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'c')
            ->addSelect('c')
            ->leftJoin('a.tags', 't')
            ->addSelect('t')
            ->andWhere('a.status = :status')
            ->setParameter('status', 'published')
            ->orderBy('a.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPublished(): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.tags', 't')              // jointure tags
            ->addSelect('t')                       // et sélection
            ->andWhere('a.status = :status')
            ->setParameter('status', 'published')
            ->orderBy('a.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findFiltered(?Category $category = null, ?Tag $tag = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'c')
            ->addSelect('c')
            ->leftJoin('a.tags', 't')
            ->addSelect('t')
            ->andWhere('a.status = :status')
            ->setParameter('status', Article::STATUS_PUBLISHED)
            ->orderBy('a.publishedAt', 'DESC');

        if ($category) {
            $qb->andWhere('a.category = :category')
            ->setParameter('category', $category);
        }

        if ($tag) {
            $qb->andWhere(':tag MEMBER OF a.tags')
            ->setParameter('tag', $tag);
        }

        return $qb->getQuery()->getResult();
    }


    //    /**
    //     * @return Article[] Returns an array of Article objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Article
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
