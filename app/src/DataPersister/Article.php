<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Article as ArticleEntity;
use App\Factory\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class Article implements ContextAwareDataPersisterInterface
{
    private ContextAwareDataPersisterInterface $decorated;
    private MailerInterface $mailer;
    private ?UserInterface $user;
    private Email $email;

    public function __construct(
        ContextAwareDataPersisterInterface $decorated,
        MailerInterface $mailer,
        Security $security,
        Email $email
    ) {
        $this->decorated = $decorated;
        $this->mailer = $mailer;
        $this->user = $security->getUser();
        $this->email = $email;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof ArticleEntity ? $this->decorated->supports($data, $context) : false;
    }

    public function persist($data, array $context = [])
    {
        $result = $this->decorated->persist($data, $context);

//        dd($context['collection_operation_name']) === Request::METHOD_POST, null !== $this->user);
        if (strtoupper($context['collection_operation_name']) === Request::METHOD_POST && null !== $this->user) {
            $email = $this->email->create(
                $this->user->getUsername(),
                'no-reply@miniblog.fr',
                'Article #' . $data->getId(),
                $data->getContent()
            );

            $this->mailer->send($email);
        }

        return $result;
    }

    public function remove($data, array $context = [])
    {
        return $this->decorated->remove($data, $context);
    }
}
