<?php

namespace App\Repository;

use App\Entity\FollowNotification;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FollowNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method FollowNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method FollowNotification[]    findAll()
 * @method FollowNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FollowNotificationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FollowNotification::class);
    }
}
