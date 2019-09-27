<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CommentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @param null|string $term
     * @return Comment[]
     */
    public function findAllWithSearch(?string $term)
    {
        $qb = $this->createQueryBuilder('comment')
        ->innerJoin('comment.article', 'article')
        // solution to the n1 problem, telling the qb to also SELECT * on article
        ->addSelect('article');

        if ($term) {
            // careful with using orWhere
            $qb->andWhere('comment.content LIKE :term OR comment.authorName LIKE :term OR article.title LIKE :term')
                ->setParameter('term', '%'.$term.'%')
            ;
        }

        return $qb
            ->orderBy('comment.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('c')
            ->where('c.something = :value')->setParameter('value', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
