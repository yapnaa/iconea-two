<?php
namespace App\Tests\Security;

use App\Entity\User;
use App\Mailer\Mailer;
use PHPUnit\Framework\TestCase;

/**
 * 
 */
class MailerTest extends TestCase
{
	public function testConfirmationEmail()
	{
		$user = new User();
		$user->setEmail("john.doe@example.com");

		$swiftMailerMock = $this->getMockBuilder(\Swift_Mailer::class)
			->disableOriginalConstructor()
			->getMock();

		$twigMock = $this->getMockBuilder(\Twig_Environment::class)
			->disableOriginalConstructor()
			->getMock();

		$mailer = new Mailer($swiftMailerMock, $twigMock, "saswata@moviloglobal.com");
		$mailer->sendConfirmationEmail($user);
	}
}