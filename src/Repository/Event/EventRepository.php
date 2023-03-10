<?php

declare(strict_types = 1);

namespace App\Repository\Event;

use App\DataTransferObjects\EventFilterDto;
use App\Entity\Event\Event;
use App\Entity\User;
use App\Exception\ShouldNotHappenException;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly Security $security
    ) {
        parent::__construct($registry, Event::class);
    }

    public function save(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->persist($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    public function remove(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()
            ->remove($entity);

        if ($flush) {
            $this->getEntityManager()
                ->flush();
        }
    }

    public function findByEventFilter(EventFilterDto $eventFilterDto, bool $isQuery = false): mixed
    {
        $user = $this->security->getUser();

        if (! $user instanceof User) {
            throw new ShouldNotHappenException('user required');
        }

        $qb = $this->createQueryBuilder('e');

        if ($eventFilterDto->getTitle()) {
            $qb->andWhere(
                $qb->expr()
                    ->like('e.title', ':title')
            )->setParameter('title', '%' . $eventFilterDto->getTitle() . '%');
        }

        if ($eventFilterDto->getAddress()) {
            $qb->andWhere(
                $qb->expr()
                    ->like('e.address', ':address')
            )->setParameter('address', '%' . $eventFilterDto->getAddress() . '%');
        }

        $now = new DateTimeImmutable();
        $qb->andWhere($qb->expr()->lte(':now', 'e.startAt'))
            ->setParameter('now', $now, Types::DATETIME_IMMUTABLE);

        if ($eventFilterDto->getStartAt() instanceof DateTimeImmutable) {
            $qb->andWhere(
                $qb->expr()
                    ->gt('e.startAt', ':startAt')
            )->setParameter('startAt', $eventFilterDto->getStartAt(), Types::DATETIME_IMMUTABLE);
        }

        if ($eventFilterDto->getEndAt() instanceof DateTimeImmutable) {
            $qb->andWhere(
                $qb->expr()
                    ->lt('e.endAt', ':endAt')
            )->setParameter('endAt', $eventFilterDto->getEndAt(), Types::DATETIME_IMMUTABLE);
        }

        $qb->orderBy('e.createdAt', 'DESC');
        $qb->leftJoin('e.eventUsers', 'eu');
        $qb->leftJoin('eu.owner', 'euo');
        $qb->leftJoin('e.groupEvent', 'ge');
        $qb->leftJoin('ge.group', 'g');
        $qb->leftJoin('g.groupUsers', 'gu');

        $qb->andWhere(
            $qb->expr()
                ->orX($qb->expr()->isNull('e.groupEvent'), $qb->expr()->eq(':userId', 'gu.owner'))
        )->setParameter('userId', $user->getId(), 'uuid');

        if ($isQuery) {
            return $qb->getQuery();
        }

        return $qb->getQuery()
            ->getResult();
    }

    public function findByKeyword(mixed $keyword, bool $isQuery = false): mixed
    {
        $user = $this->security->getUser();

        if (! $user instanceof User) {
            throw new ShouldNotHappenException('user required');
        }

        $qb = $this->createQueryBuilder('e');

        $now = new DateTimeImmutable();

        $qb->andWhere(
            $qb->expr()
                ->like('lower(e.title)', ':title')
        )->setParameter('title', '%' . strtolower((string)$keyword) . '%');

        $qb->andWhere($qb->expr()->lt(':now', 'e.startAt'))
            ->setParameter('now', $now, Types::DATETIME_IMMUTABLE);

        if ($isQuery) {
            return $qb->getQuery();
        }

        return $qb->getQuery()
            ->getResult();
    }
}
