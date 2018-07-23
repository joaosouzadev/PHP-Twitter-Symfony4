<?php 

namespace App\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Entity\User;
use App\Mailer\Mailer;

class UserSubscriber implements EventSubscriberInterface {

	private $mailer;

	public function __construct(Mailer $mailer) {
		$this->mailer = $mailer;
	}

	public static function getSubscribedEvents() {

		return [
			UserRegisterEvent::NAME => 'onUserRegister'
		];
	}

	public function onUserRegister(UserRegisterEvent $event) {

		$this->mailer->sendConfirmationEmail($event->getRegisteredUser());
	}
}