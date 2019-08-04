<?php

namespace AppBundle\Events;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JwtCreatedSubscriber
{
    public function updateJwtData(JWTCreatedEvent $event)
    {
        // 1. Récupérer l'utilisateur (pour avoir son firstName et lastName)
        $user = $event->getUser();

        // 2. Enrichir les data pour qu'elles contiennent ces données
        $data['id'] = $user->getId();
        $data['username'] = $user->getEmail();
        $data['firstName'] = $user->getFirstName();
        $data['lastName'] = $user->getLastName();

        $event->setData($data);
    }
}
