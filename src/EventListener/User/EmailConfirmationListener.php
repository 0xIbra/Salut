<?php
/**
 * Created by PhpStorm.
 * User: ibragim.abubakarov
 * Date: 21/03/2019
 * Time: 09:43
 */

namespace App\EventListener\User;


use App\Entity\User;
use App\Event\User\UserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class EmailConfirmationListener implements EventSubscriberInterface
{

    private $mailer;
    private $tokenGenerator;
    private $translator;
    private $twig;

    public function __construct(\Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator,
                                TranslatorInterface $translator, \Twig\Environment $twig)
    {
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->translator = $translator;
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            UserEvent::ON_REGISTRATION_SUCCESS => 'onRegistrationSuccess'
        ];
    }

    public function onRegistrationSuccess(UserEvent $event)
    {
        $user = $event->getUser();
        $user->setConfirmationToken($this->tokenGenerator->generateToken());
        $this->sendConfirmationEmail($user);
    }

    public function sendConfirmationEmail(User $user)
    {
        try {
            $message = (new \Swift_Message($this->translator->trans('account.confirmation.title')))
                ->setFrom(getenv('APP_SENDER'))
                ->setTo($user->getEmail())
                ->setBody(
                    $this->twig->render('email/registration/registration.html.twig', [
                        'name' => $user->getFirstName(),
                        'confirmationUrl' => getenv('DOMAIN') . '/confirmation/' . $user->getConfirmationToken()
                    ]), 'text/html'
                );
                $this->mailer->send($message);
        } catch (LoaderError $e) {
            throw new \Exception($e->getMessage());
        } catch (RuntimeError $e) {
            throw new \Exception($e->getMessage());
        } catch (SyntaxError $e) {
            throw new \Exception($e->getMessage());
        }
    }

}