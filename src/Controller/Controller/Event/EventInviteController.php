<?php

declare(strict_types = 1);

namespace App\Controller\Controller\Event;

use App\Entity\Event\EventInvite;
use App\Factory\Event\EventRejectionFactory;
use App\Factory\Event\EventUserFactory;
use App\Factory\Event\EventUserTicketFactory;
use App\Repository\Event\EventInviteRepository;
use App\Repository\Event\EventRejectionRepository;
use App\Repository\Event\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/events')]
class EventInviteController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly EventInviteRepository $eventInviteRepository,
        private readonly EventUserFactory $eventUserFactory,
        private readonly EventUserTicketFactory $eventTicketFactory,
        private readonly EventRejectionFactory $eventRejectionFactory,
        private readonly EventRejectionRepository $eventRejectionRepository
    ) {
    }

    #[Route(path: '/invitations', name: 'invitations')]
    public function index(): Response
    {
        return $this->render('/event/invite/index.html.twig');
    }

    #[Route(path: '/invite/accept/{id}', name: 'accept_event_invite')]
    public function accept(EventInvite $eventInvite): Response
    {
        $eventUser = $this->eventUserFactory->create($eventInvite->getOwner(), $eventInvite->getEvent());
        $eventInvite->getEvent()
            ->addEventUser($eventUser);

        if ($eventInvite->getEvent()->isIsTicketed()) {
            $eventUserTicket = $this->eventTicketFactory->createTicketAndEventUserTicket($eventUser);
            $eventUser->setEventUserTicket($eventUserTicket);
        }

        $this->eventRepository->save($eventInvite->getEvent(), true);
        $this->eventInviteRepository->remove($eventInvite, true);

        return $this->render('/event/invite/index.html.twig');
    }

    #[Route(path: '/invite/reject/{id}', name: 'reject_event_invite')]
    public function reject(EventInvite $eventInvite): Response
    {
        $eventRejection = $this->eventRejectionFactory->create($eventInvite->getEvent(), $eventInvite->getOwner());
        $this->eventRejectionRepository->save($eventRejection, true);
        $this->eventInviteRepository->remove($eventInvite, true);

        return $this->redirectToRoute('invitations');
    }
}
