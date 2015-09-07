<?php
namespace MH4Editor\MH4EditorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\StringInput;

class UpdatePricesCommand extends ContainerAwareCommand
{
    private $output;

    protected function configure()
    {
        $this
            ->setName('items:prices:update')
            ->setDescription('Update Item price by times bought. No more over 30000 atm')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Updating item prices...</comment>');

        $this->output = $output;
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $items = $em->getRepository('MH4EditorBundle:Item')->findAll();

        foreach ($items as $item) {
            
            
            if($item->getId() > 49){
                $item->setIsLocked(true);
                $em->persist($item);
                $em->flush();
                $price = $item->getBuyPrice();
                $rarity = $item->getRarity();
                $tb =  $item->getTimesBought();

                if(($rarity * $tb + 10) > $price){
                    $output->writeln(sprintf('Updating item <comment>%s</comment>...', $item->getName()));
                    $item->setBuyPrice($rarity * $tb + 10);
                    $item->setIsLocked(false);
                    $em->persist($item);
                    $em->flush();
                    $output->writeln('=> Updated!');
                }
                
                
            }
            
        }
        

        //$output->writeln(sprintf('Skipping Cron Task <info>%s</info>', $crontask));

        $output->writeln('<comment>Done!</comment>');
    }
}