<?php
namespace MH4Editor\MH4EditorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\StringInput;

class ResetQuotasCommand extends ContainerAwareCommand
{
    private $output;

    protected function configure()
    {
        $this
            ->setName('users:reset:quotas')
            ->setDescription('Reset User item and talismans quotas.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Refreshing user quotas...</comment>');

        $this->output = $output;
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $users = $em->getRepository('MH4EditorBundle:User')->findAll();

        foreach ($users as $user) {
            
            $output->writeln(sprintf('Restoring user <comment>%s</comment> quotas...', $user->getUsername()));
            $user->setTalismansQuota($user->getMaxTalismansQuota());
            $user->setItemsQuota($user->getMaxItemsQuota());
            $em->persist($user);
            $em->flush();
            $output->writeln('=> Restored!');
        }
        

        //$output->writeln(sprintf('Skipping Cron Task <info>%s</info>', $crontask));

        $output->writeln('<comment>Done!</comment>');
    }
}