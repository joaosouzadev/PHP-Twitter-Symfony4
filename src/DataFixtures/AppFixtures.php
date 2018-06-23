<?php 

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture {

	private $passwordEncoder;


	public function __construct(UserPasswordEncoderInterface $passwordEncoder){
		$this->passwordEncoder = $passwordEncoder;
	}

	public function load(ObjectManager $manager){
		$this->loadMicroPosts($manager);
		$this->loadUsers($manager);
	}

	private function loadMicroPosts(ObjectManager $manager){
		for ($i=0; $i < 2; $i++) { 
			$microPost = new MicroPost();
			$microPost->setText('Some random text ' . rand(0, 100));
			$microPost->setTime(new \DateTime('2018-05-21'));
			$manager->persist($microPost);
		}

		$manager->flush();
	}

	private function loadUsers(ObjectManager $manager){
		$user = new User();
		$user->setUsername('joao_victor');
		$user->setFullName('João Victor Moreira de Souza');
		$user->setEmail('joaosouza@gmail.com');
		$user->setPassword($this->passwordEncoder->encodePassword($user, 'joao123'));

		$manager->persist($user);
		$manager->flush();
	}
}