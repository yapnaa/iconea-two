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
		$swiftMailerMock->expects($this->once())->method('send')
			->with($this->callback(function ($subject) {
				$messageStr = (string)$subject;
				#dump($messageStr);
				#return true;
				return strpos($messageStr, "From: saswata@moviloglobal.com") !== false
					&& strpos($messageStr, "Content-Type: text/html; charset=utf-8") !== false
					&& strpos($messageStr, "Subject: Welcome to the Micro-Post App") !== false
					&& strpos($messageStr, "To: john.doe@example.com") !== false;
			}));

		$twigMock = $this->getMockBuilder(\Twig_Environment::class)
			->disableOriginalConstructor()
			->getMock();
		$twigMock->expects($this->once())->method('render')
			->with('email/registration.html.twig', ['user'=>$user])
			->willReturn('Thsi AAA is a message body');

		$mailer = new Mailer($swiftMailerMock, $twigMock, "saswata@moviloglobal.com");
		$mailer->sendConfirmationEmail($user);
	}
}