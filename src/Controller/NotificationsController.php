<?php 

namespace App\Controller;

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
}