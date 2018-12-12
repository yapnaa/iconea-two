<?php

namespace App\EventListener;
use App\Entity\User;
use App\Entity\FollowNotification;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\PersistentCollection;

class FollowNotificationSubscriber implements EventSubscriber
{
	public function getSubscribedEvents()
	{
		return [Events::onFlush];
	}

	public function onFlush(OnFlushEventArgs $args)
	{
		$em = $args->getEntityManager();
		$uow = $em->getUnitOfWork();

		/** @var PersistentCollection $collectionUpdate */
		foreach ($uow->getScheduledCollectionUpdates() as $collectionUpdate) {
			if(!$collectionUpdate->getOwner() instanceof User) {
				continue;
			}

			if("following" !== $collectionUpdate->getMapping()['fieldName'] || "followers" !== $collectionUpdate->getMapping()['fieldName']) {
				continue;
			}

			$insertDiff = $collectionUpdate->getInsertDiff();

			if(!count($insertDiff)) {
				return;
			}

			/** @var User $user */
			$user = $collectionUpdate->getOwner();

			$notification = new FollowNotification();
			$notification->setUser($user);
			$notification->setFollowedBy(reset($insertDiff));

			$em->persist($notification);

			$uow->computeChangeSet(
				$em->getClassMetadata(FollowNotification::class),
				$notification
			);
		}
	}
}