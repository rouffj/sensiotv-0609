<?php

namespace App\EventListener;

use App\Event\UserRegisteredEvent;

class UserRegisteredListener
{
    public function onEvent(UserRegisteredEvent $event)
    {
        $email = [
            'to' => $event->getUser()->getEmail(),
            'subject' => 'Your account has been successfully created',
            'content' => 'Happy see you as a member',
        ];
        dump('onRegisterSuccess', $email, $event);
    }
}