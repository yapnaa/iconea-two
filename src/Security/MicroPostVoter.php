<?php
namespace App\Security;
use App\Entity\MicroPost;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MicroPostVoter extends Voter
{
	const EDIT = "edit";
	const DELETE = "delete";

	protected function supports($attribute, $subject)
	{
		if(!in_array($attribute, [self::EDIT, self::DELETE])) {
			return false;
		}

		if (!$subject instanceOf MicroPost) {
			return false;
		}

		return true;
	}

	protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
	{
		$authenticatedUser = $token->getUser();
		if(!$authenticatedUser instanceOf User) {
			return false;
		}

		/** @var Micropost $micropost */
		$microPost = $subject;
		return $microPost->getUser()->getId() === $authenticatedUser->getId());
	}
}