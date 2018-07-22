<?php 

namespace App\EventListener;

use App\Entity\LikeNotification;
use App\Entity\MicroPost;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\OnFlushEventArgs;

class LikeNotificationSubscriber implements EventSubscriber {

	public function getSubscribedEvents() {
		return [
			Events::onFlush
		];
	}

	public function onFlush(OnFlushEventArgs $args) {
		$em = $args->getEntityManager();
		$unityOfWork = $em->getUnitOfWork();

		foreach ($unityOfWork->getScheduledCollectionUpdates() as $collectionUpdate) {
			if (!$collectionUpdate->getOwner() instanceof MicroPost) {
				continue;
			}

			// dump($collectionUpdate->getMapping());die;
			if ('likedBy' !== $collectionUpdate->getMapping()['fieldName']) {
				continue;
			}

			$insertDiff = $collectionUpdate->getInsertDiff();

			if (!count($insertDiff)) {
				return;
			}

			$microPost = $collectionUpdate->getOwner();

			$notification = new LikeNotification();
			$notification->setUser($microPost->getUser());
			$notification->setMicroPost($microPost);
			$notification->setLikedBy(reset($insertDiff)); // pega o primeiro elemento do array insertDiff

			$em->persist($notification);
			$unityOfWork->computeChangeSet(
				$em->getCLassMetaData(LikeNotification::class), 
				$notification
			);
		}
	}
}