<?php 

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class UserType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options){

		$builder->add('username', TextType::class, ['label' => 'Usuário'])
			->add('email', EmailType::class, ['label' => 'E-mail'])
			->add('plainPassword', RepeatedType::class, [
					'type' => PasswordType::class,
					'first_options' => ['label' => 'Senha'],
					'second_options' => ['label' => 'Repetir Senha']
			])
			->add('fullName', TextType::class, ['label' => 'Nome Completo'])
			->add('termsAgreed', CheckboxType::class, [
					'mapped' => false,
					'constraints' => new IsTrue(),
					'label' => 'Eu concordo com os Termos de Serviço'
			])
			->add('Register', SubmitType::class, ['label' => 'Registrar']);
	}

	public function configureOptions(OptionsResolver $resolver){

		$resolver->setDefaults([
			'data_class' => User::class
		]);
	}
}