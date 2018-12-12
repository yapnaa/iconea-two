<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FollowNotificationRepository")
 */
class FollowNotification extends Notification
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $followedBy;

    public function getFollowedBy()
    {
        return $this->followedBy;
    }

    public function setFollowedBy($followedBy)
    {
        $this->followedBy = $followedBy;
    }
}
