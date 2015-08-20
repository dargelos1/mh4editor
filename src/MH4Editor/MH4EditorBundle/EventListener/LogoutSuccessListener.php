<?php

namespace MH4Editor\MH4EditorBundle\EventListener;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\DependencyInjection\Container;
class LogoutSuccessListener implements LogoutSuccessHandlerInterface{

	private $securityContext;
	private $translator;
	private $session;
	private $container;

	public function __construct(SecurityContext $securityContext, TranslatorInterface $translator, Session $session, Container $container){

		$this->securityContext = $securityContext;
		$this->translator = $translator;
		$this->session = $session;
		$this->container = $container;
	}

	public function onLogoutSuccess(Request $request){

		$token = $this->securityContext->getToken();
		if($token !== null){
			$user = $token->getUser();
			if($user->getIsBanned()){
				//ladybug_dump($this->container);die;

				//return new Response($this->session->getFlashBag()->get("banned"),200);
				$lastUser = $user->getUsername();
				$user = null;
				//ladybug_dump($this->session->getFlashBag()->get("banned"));die;
				return new RedirectResponse($this->container->get('router')->generate('mh4_editor_homepage'));
			}
		}
		//return new Response($this->session->getFlashBag()->get("banned"),200);
		return new RedirectResponse($this->container->get('router')->generate('mh4_editor_homepage'));
	}
}