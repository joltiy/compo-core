<?php

namespace Compo\CoreBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\QueryBuilder;
use JMS\JobQueueBundle\Console\CronCommand;
use JMS\JobQueueBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Types\Type;


/**
 * Class ClearJobQueueCommand
 * @package Compo\CoreBundle\Command
 */
class JobQueueClearCommand extends ContainerAwareCommand implements CronCommand
{
    /**
     * @param \DateTime $lastRunAt
     * @return bool
     */
    public function shouldBeScheduled(\DateTime $lastRunAt)
    {
        $timestamp = time() - $lastRunAt->getTimestamp();

        return $timestamp >= 86400;
    }

    /**
     * @param \DateTime $lastRunAt
     * @return Job
     */
    public function createCronJob(\DateTime $lastRunAt)
    {
        return new Job('compo:job:queue:clear');
    }

    /**
     * Configure CLI command, message, options.
     */
    protected function configure()
    {
        $this->setName('compo:job:queue:clear')
            ->setDescription('jms-job-queue:clean-up');
    }

    /**
     * Code to execute for the command.
     *
     * @param InputInterface  $input  Input object from the console
     * @param OutputInterface $output Output object for the console

     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $doctrine = $container->get('doctrine');
        $manager = $doctrine->getManager();

        /** @var Connection $connection */
        $connection = $doctrine->getConnection();

        $jobsRepository = $manager->getRepository(Job::class);

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $jobsRepository->createQueryBuilder('j');

        $queryBuilder->select('MAX(j.createdAt) AS createdAt , j.command, j.args');

        $queryBuilder->where($queryBuilder->expr()->notIn('j.state', ['new', 'pending', 'running']));

        $queryBuilder->orderBy('j.createdAt', 'desc');

        $queryBuilder->groupBy('j.command', 'j.args');

        $jobs = $queryBuilder->getQuery()->getArrayResult();

        /** @var Job $job */
        foreach ($jobs as $job) {

            $output->writeln($job['command'] . ' ' . implode(' ', $job['args']));

            $queryBuilder = $jobsRepository->createQueryBuilder('j');

            $queryBuilder->where($queryBuilder->expr()->notIn('j.state', ['new', 'pending', 'running']));

            $queryBuilder->andWhere('j.command = :command');
            $queryBuilder->andWhere('j.args = :args');

            $queryBuilder->andWhere('j.createdAt < :createdAt');

            $queryBuilder->setParameter('command', $job['command']);
            $queryBuilder->setParameter('args', $job['args'], Type::JSON_ARRAY);

            $queryBuilder->setParameter('createdAt', $job['createdAt']);


            $queryBuilder->select('j.id');


            $ids = $queryBuilder->getQuery()->getArrayResult();

            foreach ($ids as $item) {
                $connection->executeQuery('DELETE FROM jms_job_related_entities WHERE job_id = :id', ['id' => $item['id']]);
                $connection->executeQuery('DELETE FROM jms_job_dependencies WHERE dest_job_id = :id', ['id' => $item['id']]);
                $connection->executeQuery('DELETE FROM jms_jobs WHERE id = :id', ['id' => $item['id']]);
            }
        }


    }
}
