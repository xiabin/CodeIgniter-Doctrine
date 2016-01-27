<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->library('doctrine');
		$em = $this->doctrine->em;

//		$cart->addItem($item);
		$userGroup =$em->getRepository('Entity\UserGroup')->find(1);
		if ($userGroup === null) {
			echo "No userGroup found.\n";
			exit(1);
		}
		echo "---------------<br/>";
		foreach($userGroup->getUsers() as $value){
			echo $value->getUsername()."<br/>";
		}


	}

	public function test(){
		$this->load->library('doctrine');
		$em = $this->doctrine->em;
		/** @var  $userGroup Entity\UserGroup */
		$userGroup =$em->getRepository('Entity\UserGroup')->find(1);

		$name  = '111';
		$email = '111@lattecake.com';

		/** @var  $userInfo Entity\User */
		$userInfo =$em->getRepository('Entity\User')->findOneBy([
			'username' => $name
		]);

		if( $userInfo )
		{
			throw Doctrine\DBAL\Exception\NotNullConstraintViolationException::unknownColumnType("{$name} 已存在");
		}

		$user = new Entity\User;
		$user->setUsername($name);
		$user->setPassword($name);
		$user->setEmail($email);
		$user->setGroup($userGroup);

		$em->persist($user);
		$em->flush();
	}
}
