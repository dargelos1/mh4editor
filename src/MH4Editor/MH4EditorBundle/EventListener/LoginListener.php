<?php
namespace MH4Editor\MH4EditorBundle\EventListener;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\SecurityContext;
use MH4Editor\MH4CipherBundle\Services\MH4CipherService as MH4Cipher;
use MH4Editor\MH4EditorBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine; // for Symfony 2.1.0+
// use Symfony\Bundle\DoctrineBundle\Registry as Doctrine; // for Symfony 2.0.x
/**
 * Custom login listener.
 */
class LoginListener
{
	/** @var \Symfony\Component\Security\Core\SecurityContext */
	private $securityContext;
	
	/** @var \Doctrine\ORM\EntityManager */
	private $em;

	private $request;

	private $mh4Cipher;
	
	/**
	 * Constructor
	 * 
	 * @param SecurityContext $securityContext
	 * @param Doctrine        $doctrine
	 */
	public function __construct(SecurityContext $securityContext, Doctrine $doctrine, MH4Cipher $mh4Cipher)
	{
		$this->securityContext = $securityContext;
		$this->em              = $doctrine->getEntityManager();
		$this->mh4Cipher       = $mh4Cipher;
	}
	
	/**
	 * Do the magic.
	 * 
	 * @param InteractiveLoginEvent $event
	 */
	public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
	{
		$this->request = $event->getRequest();
		if ($this->securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
			// user has just logged in
			$user = $event->getAuthenticationToken()->getUser();
			if($user !== null){
				$mh4Cipher = $this->mh4Cipher;
				$UA = $this->request->headers->get('User-Agent');
		    	$IP = $this->request->getClientIp();
		    	$user->setLastIP($IP);
		    	$user->setLastLogin(new \DateTime());
		    	$user->setLastUserAgent($UA);
		    	if($user->getMH4FilePath() !== null && file_exists($user->getUploadRootDir()."/".$user->getMH4FilePath())){

	                
	                if(!file_exists($user->getUploadRootDir()."/".$user->getUploadDir()."/decrypted.bin")){

	                    $status = $mh4Cipher->MH4Decrypt($user->getAbsolutePath() ,$user->getUploadDir()."/decrypted.bin",$event->getAuthenticationToken());
	                    if($status){

	                        $HR = $mh4Cipher->getHunterRanking($user);
	                        $HRUSer = $user->getHunterHR();

	                        if($HR !== $HRUSer){
	                        	$user->setHunterHR($HR);
		                        $user->setMaxTalismansQuota(intval(($HR * 0.1) + User::TALISMAN_QUOTA_BASE));
		                        $user->setMaxItemsQuota(intval(($HR * 5) + User::ITEM_QUOTA_BASE));
		                        $incTalismanQuota = $user->getMaxTalismansQuota() - $user->getTalismansQuota();
		                        $incItemQuota = $user->getMaxItemsQuota() - $user->getItemsQuota();
		                        $user->setTalismansQuota($user->getTalismansQuota() + $incTalismanQuota);
		                        $user->setItemsQuota($user->getItemsQuota() + $incItemQuota);
	                        }

	                        
	                    }

	                }else{
	                        $HR = $mh4Cipher->getHunterRanking($user);
	                        $HRUSer = $user->getHunterHR();

	                        if($HR !== $HRUSer){
	                        	$user->setHunterHR($HR);
		                        $user->setMaxTalismansQuota(intval(($HR * 0.1) + User::TALISMAN_QUOTA_BASE));
		                        $user->setMaxItemsQuota(intval(($HR * 5) + User::ITEM_QUOTA_BASE));
		                        $incTalismanQuota = $user->getMaxTalismansQuota() - $user->getTalismansQuota();
		                        $incItemQuota = $user->getMaxItemsQuota() - $user->getItemsQuota();
		                        $user->setTalismansQuota($user->getTalismansQuota() + $incTalismanQuota);
		                        $user->setItemsQuota($user->getItemsQuota() + $incItemQuota);
	                        }
	                }
	            }
	            
		    	$this->em->persist($user);
	        	$this->em->flush();
			}
	    	
		}
		
		if ($this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
			// user has logged in using remember_me cookie
		}
		
		// do some other magic here
		//$user = $event->getAuthenticationToken()->getUser();
		
		// ...
	}
}