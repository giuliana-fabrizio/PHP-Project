<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @return Event[] Returns an array of Event objects
     */
    public function findByFilters(?string $title, ?string $date, ?string $isPublic): array
    {
        $qb = $this->createQueryBuilder('e');

        if ($title !== null && $title !== '') {
            $qb->andWhere('e.title LIKE :title')
                ->setParameter('title', '%' . $title . '%');
        }

        // TODO
        if ($date !== null && $date !== '') {
            $qb->andWhere('e.datetime = :date')
                ->setParameter('date', $date);
        }

        if ($isPublic !== null && $isPublic !== '') {
            $qb->andWhere('e.is_public = :isPublic')
                ->setParameter('isPublic', $isPublic);
        }

        return $qb->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Event[] Returns an array of Event objects
     */
    public function findAvailables(): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.is_public = 0')
            ->andWhere('e.datetime_start >= :today')
            ->andWhere('SIZE(e.participants) < e.participant_count')
            ->setParameter('today', new \DateTime('today'))
            ->getQuery()
            ->getResult();
    }
}
