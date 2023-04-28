<?php

namespace App\Command;
use App\Entity\Activiter;
use App\Entity\User;
use Swift_Mailer;
use Swift_Message;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'mailReminder',
    description: 'Add a short description for your command',
)]
class MailReminderCommand extends Command
{
    public function __construct(EntityManagerInterface $entityManager, \Swift_Mailer $mailer)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:send-reminders')
            ->setDescription('Sends email reminders for activities starting soon')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $now = new \DateTime();

        $activities = $this->entityManager->createQueryBuilder()
            ->select('a')
            ->from('App\Entity\Activiter', 'a')
            ->where('a.date_debut BETWEEN :now AND :oneHourFromNow')
            ->setParameter('now', $now)
            ->setParameter('oneHourFromNow', $now->modify('+1 hour'))
            ->getQuery()
            ->getResult();

        foreach ($activities as $activity) {
            $user = $activity->getIdUser();

            $message = (new \Swift_Message('Reminder: Activity starting soon'))
                ->setFrom('akram.hadjnaser@esprit.com')
                ->setTo($user->getEmail())
                ->setBody('<p>Your activity is starting in less than 1 hour.</p>',"text/html");

            $this->mailer->send($message);
        }

        $output->writeln('Email reminders sent successfully.');
        return 0;
    }


}
