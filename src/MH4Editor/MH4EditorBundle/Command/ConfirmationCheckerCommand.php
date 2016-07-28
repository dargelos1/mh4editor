<?php
namespace MH4Editor\MH4EditorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\StringInput;

class ConfirmationCheckerCommand extends ContainerAwareCommand
{
    private $output;

    protected function configure()
    {
        $this
            ->setName('users:confirm:check')
            ->setDescription('Checks if a user has checked his/her confirmation token. After 24 if not confirmed, then deletes the entry.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Checking tokens...</comment>');

        $this->output = $output;
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $users = null;
        $query = $em->createQuery(
            "SELECT u FROM MH4EditorBundle:User u
            WHERE u.confirmationToken IS NOT NULL");

        $users = $query->getResult();

        foreach ($users as $user) {
            
            $output->writeln(sprintf('Checking for user <comment>%s</comment> token...', $user->getUsername()));
            $registerDate = $user->getRegisterDate()->format("U");
            $secondsLeft = ($registerDate + 86400) - time();

            if($secondsLeft <= 0 ){
                //Si la fecha de registro + 24h es menor o igual que ahora significa que se ha acabado el tiempo
                $em->remove($user);
                $em->flush();
                $output->writeln('=> Removed due confirmation inactivity!');
            }else{
                $h = intval($secondsLeft/3600);
                $m = $secondsLeft/60;
                $m = $m%60;
                $s = $secondsLeft%60;
                $output->writeln(sprintf('The user <comment>%s</comment> still have %02d:%02d:%02d to confirm',
                    $user->getUsername(),
                    $h,$m,$s
                ));
            }
            
            
        }
        

        //$output->writeln(sprintf('Skipping Cron Task <info>%s</info>', $crontask));

        $output->writeln('<comment>Done!</comment>');
    }
}