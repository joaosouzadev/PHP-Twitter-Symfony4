<?php 

namespace App\Event;

use Symfony\Component\EventDispatcher\Event;
use App\Entity\User;

class UserRegisterEvent extends Event {

	const NAME = 'user.register';

	private $registeredUser;

	public function __construct(User $registeredUser) {
		$this->registeredUser = $registeredUser;
	}

	public function getRegisteredUser(): User {
		return $this->registeredUser;
	}
}