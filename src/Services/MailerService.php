<?php

namespace App\Services;
use App\Entity\User;
/**
 * Class MailerService.
 */
class MailerService
{
    private $mailer;
    private $twig;
    /**
     * MailerService constructor.
     *
     * @param \Swift_Mailer     $mailer
     * @param \Twig_Environment $twig
     */
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
    }
    /**
     * @param User $user
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendInvitation(User $user): void
    {
        $message = $this->mailer->createMessage();
        $message->setFrom('bills@home.com')
            ->setTo($user->getEmail())
            ->setSubject('Invitation to Shortlist for Companies')
            ->setBody($this->twig->render('emails/invitation.html.twig', ['user' => $user]), 'text/html');
        $this->mailer->send($message);
    }
    /**
     * @param User $user
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendAccountConfirmed(User $user): void
    {
        $message = $this->mailer->createMessage();
        $message->setFrom('bills@home.com')
            ->setTo($user->getEmail())
            ->setSubject('Invitation to Utility Bills')
            ->setBody($this->twig->render('emails/account_confirmed.twig', ['user' => $user]), 'text/html');
        $this->mailer->send($message);
    }

    /**
     * @param User $user
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendPasswordChanged(User $user): void
    {
        $message = $this->mailer->createMessage();
        $message->setFrom('bills@home.com')
            ->setTo($user->getEmail())
            ->setSubject('Password changed for Utility Bills')
            ->setBody($this->twig->render('emails/password_changed.html.twig', ['user' => $user]), 'text/html');
        $this->mailer->send($message);
    }

    /**
     * @param User $user
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendResetLink(User $user): void
    {
        $message = $this->mailer->createMessage();
        $message->setFrom('bills@home.com')
            ->setTo($user->getEmail())
            ->setSubject('Password change for Utility BIlls')
            ->setBody($this->twig->render('emails/reset_password.html.twig', ['user' => $user]), 'text/html');
        $this->mailer->send($message);
    }
    /**
     * @param User $user
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function confirmPasswordChange(User $user): void
    {
        $message = $this->mailer->createMessage();
        $message->setFrom('bills@home.com')
            ->setTo($user->getEmail())
            ->setSubject('Password change for Shortlist')
            ->setBody($this->twig->render('emails/password_changed.html.twig', ['user' => $user]), 'text/html');
        $this->mailer->send($message);
    }
    /**
     * @param User $user
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function deleteFromTeamRequestLink(User $user, Team $team, array $emails): void
    {
        $message = $this->mailer->createMessage();
        $message->setFrom('bills@home.com')
            ->setTo($emails)
            ->setSubject("Remove from from team request")
            ->setBody($this->twig->render('emails/delete_from_team.html.twig', ['user' => $user, 'team' => $team]), 'text/html');
        $this->mailer->send($message);
    }
}