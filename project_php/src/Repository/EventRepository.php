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
    public function findByFilters(?string $title, ?string $date_start, ?string $date_end, ?string $isPublic): array
    {
        $qb = $this->createQueryBuilder('e');

        if ($title !== null && $title !== '') {
            $qb->andWhere('e.title LIKE :title')
                ->setParameter('title', '%' . $title . '%');
        }

        if ($date_start !== null && $date_start !== '') {
            $dateTime = new \DateTime($date_start);
            $formattedDate = $dateTime->format('Y-m-d hh:mm:ss');

            $qb->andWhere('e.datetime_start >= :date_start')
                ->setParameter('date_start', $formattedDate . '%');
        }

        if ($date_end !== null && $date_end !== '') {
            $dateTime = (new \DateTime($date_end))->setTime(23, 59, 59);

            $qb->andWhere('e.datetime_end <= :date_end')
                ->setParameter('date_end', $dateTime);
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
            ->where('e.is_public = 1')
            ->andWhere('e.datetime_start >= :today')
            ->andWhere('SIZE(e.participants) < e.participant_count')
            ->setParameter('today', new \DateTime('today'))
            ->getQuery()
            ->getResult();
    }
}
