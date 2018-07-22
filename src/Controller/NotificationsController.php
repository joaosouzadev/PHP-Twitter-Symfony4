<?php 

namespace App\Controller;

use App\Entity\Notifications;
use App\Repository\NotificationsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
* @Security("is_granted('ROLE_USER')")
* @Route("/notifications")
*/
class NotificationsController extends Controller {

	public function __construct(NotificationsRepository $notificationsRepository) {
		$this->notificationsRepository = $notificationsRepository;
	}

	/**
	* @Route("/unread-count", name="notifications_unread")
	*/
	public function unreadCount() {

		return new JsonResponse([
			'count' => $this->notificationsRepository->findUnseenByUser($this->getUser())
		]);
	}

	/**
	* @Route("/all", name="notifications_all")
	*/
	public function notifications() {
		return $this->render('notifications/notifications.html.twig', [
			'notifications' => $this->notificationsRepository->findBy([
				'seen' => false, 
				'user' => $this->getUser()
			])
		]);
	}

	/**
	* @Route("/acknowledge/{id}", name="notifications_acknowledge")
	*/
	public function acknowledge(Notifications $notification) {
		$notification->setSeen(true);
		$this->getDoctrine()->getManager()->flush();

		return $this->redirectToRoute('notifications_all');
	}

	/**
	* @Route("/acknowledge-all", name="notifications_acknowledge_all")
	*/
	public function acknowledgeAll() {
		$this->notificationsRepository->markAllAsReadByUser($this->getUser());
		$this->getDoctrine()->getManager()->flush();

		return $this->redirectToRoute('notifications_all');
	}
}