<?php
namespace MH4Editor\MH4EditorBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Session\Session;
use MH4Editor\MH4EditorBundle\Entity\User;

class KernelListener
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    private $securityContext;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokenStorage
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     */
    public function __construct(RouterInterface $router, SecurityContext $securityContext, TranslatorInterface $translator,Session $session)
    {
        $this->router = $router;
        $this->translator = $translator;
        $this->securityContext = $securityContext;
        $this->session = $session;
    }
    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $responseEvent
     */
	public function onKernelRequest(GetResponseEvent $responseEvent)
	{

            if (HttpKernelInterface::MASTER_REQUEST != $responseEvent->getRequestType()) {
                return;
            }

            if($this->securityContext->getToken() !== null){

                $user = $this->securityContext->getToken()->getUser();
                if ( $user instanceof User) {
                    //ladybug_dump($this->securityContext->getToken());die;

                    //If user does not confirmed the token, return a message to confirm it
                    if($user->getConfirmationToken() !== null){
                        $this->session->set("_locale",$user->getLocale());
                        $message = $this->translator->trans("Your account isn't confirmed yet. Confirm to log in.");
                        $this->session->getFlashBag()->set("confirm_account",$message);
                        $responseEvent->setResponse(new RedirectResponse($this->router->generate('mh4_logout_frontend')));
                    }
                    //If is banned, redirect to logout url with flashbag message
                    else if($user->getIsBanned()){
                        $this->session->set("_locale",$user->getLocale());
                        $message = $this->translator->trans("Your account have been banned.");
                        $this->session->getFlashBag()->set("banned",$message);
                        $responseEvent->setResponse(new RedirectResponse($this->router->generate('mh4_logout_frontend')));
                    }

                }
            }

            return;
	}
}