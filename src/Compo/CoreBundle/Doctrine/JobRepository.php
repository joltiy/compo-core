<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\JobQueueBundle\Entity\Job;
use JMS\JobQueueBundle\Event\StateChangeEvent;
use JMS\JobQueueBundle\Retry\ExponentialRetryScheduler;
use JMS\JobQueueBundle\Retry\RetryScheduler;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class JobRepository.
 */
class JobRepository extends EntityRepository
{
    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var
     */
    private $retryScheduler;

    /**
     * @DI\InjectParams({
     *     "dispatcher" = @DI\Inject("event_dispatcher"),
     * })
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @DI\InjectParams({
     *     "retryScheduler" = @DI\Inject("jms_job_queue.retry_scheduler"),
     * })
     *
     * @param RetryScheduler $retryScheduler
     */
    public function setRetryScheduler(RetryScheduler $retryScheduler)
    {
        $this->retryScheduler = $retryScheduler;
    }

    /**
     * @DI\InjectParams({
     *     "registry" = @DI\Inject("doctrine"),
     * })
     *
     * @param RegistryInterface $registry
     */
    public function setRegistry(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param       $command
     * @param array $args
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return mixed
     */
    public function findJob($command, array $args = [])
    {
        return $this->_em->createQuery('SELECT j FROM JMSJobQueueBundle:Job j WHERE j.command = :command AND j.args = :args')
            ->setParameter('command', $command)
            ->setParameter('args', $args, Type::JSON_ARRAY)
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    /**
     * @param       $command
     * @param array $args
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return mixed
     */
    public function getJob($command, array $args = [])
    {
        if (null !== $job = $this->findJob($command, $args)) {
            return $job;
        }

        throw new \RuntimeException(sprintf('Found no job for command "%s" with args "%s".', $command, json_encode($args)));
    }

    /**
     * @param       $command
     * @param array $args
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return Job|mixed
     */
    public function getOrCreateIfNotExists($command, array $args = [])
    {
        if (null !== $job = $this->findJob($command, $args)) {
            return $job;
        }

        $job = new Job($command, $args, false);
        $this->_em->persist($job);
        $this->_em->flush($job);

        $firstJob = $this->_em->createQuery('SELECT j FROM JMSJobQueueBundle:Job j WHERE j.command = :command AND j.args = :args ORDER BY j.id ASC')
            ->setParameter('command', $command)
            ->setParameter('args', $args, 'json_array')
            ->setMaxResults(1)
            ->getSingleResult();

        if ($firstJob === $job) {
            $job->setState(Job::STATE_PENDING);
            $this->_em->persist($job);
            $this->_em->flush($job);

            return $job;
        }

        $this->_em->remove($job);
        $this->_em->flush($job);

        return $firstJob;
    }

    /**
     * @param       $workerName
     * @param array $excludedIds
     * @param array $excludedQueues
     * @param array $restrictedQueues
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return mixed|null
     */
    public function findStartableJob($workerName, array &$excludedIds = [], array $excludedQueues = [], array $restrictedQueues = [])
    {
        while (null !== $job = $this->findPendingJob($excludedIds, $excludedQueues, $restrictedQueues)) {
            if ($job->isStartable() && $this->acquireLock($workerName, $job)) {
                return $job;
            }

            $excludedIds[] = $job->getId();

            // We do not want to have non-startable jobs floating around in
            // cache as they might be changed by another process. So, better
            // re-fetch them when they are not excluded anymore.
            $this->_em->detach($job);
        }

        return null;
    }

    /**
     * @param     $workerName
     * @param Job $job
     *
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return bool
     */
    private function acquireLock($workerName, Job $job)
    {
        $affectedRows = $this->_em->getConnection()->executeUpdate(
            'UPDATE jms_jobs SET workerName = :worker WHERE id = :id AND workerName IS NULL',
            [
                'worker' => $workerName,
                'id' => $job->getId(),
            ]
        );

        if ($affectedRows > 0) {
            $job->setWorkerName($workerName);

            return true;
        }

        return false;
    }

    /**
     * @param $relatedEntity
     *
     * @return mixed
     */
    public function findLastForRelatedEntity($relatedEntity)
    {
        [$relClass, $relId] = $this->getRelatedEntityIdentifier($relatedEntity);

        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata('JMSJobQueueBundle:Job', 'j');

        return $this->_em->createNativeQuery('SELECT j.* FROM jms_jobs j INNER JOIN jms_job_related_entities r ON r.job_id = j.id WHERE r.related_class = :relClass AND r.related_id = :relId', $rsm)
            ->setParameter('relClass', $relClass)
            ->setParameter('relId', $relId)
            ->getResult();
    }

    /**
     * @param $relatedEntity
     *
     * @return mixed
     */
    public function findAllForRelatedEntity($relatedEntity)
    {
        [$relClass, $relId] = $this->getRelatedEntityIdentifier($relatedEntity);

        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata('JMSJobQueueBundle:Job', 'j');

        return $this->_em->createNativeQuery('SELECT j.* FROM jms_jobs j INNER JOIN jms_job_related_entities r ON r.job_id = j.id WHERE r.related_class = :relClass AND r.related_id = :relId', $rsm)
            ->setParameter('relClass', $relClass)
            ->setParameter('relId', $relId)
            ->getResult();
    }

    /**
     * @param $command
     * @param $relatedEntity
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return mixed
     */
    public function findOpenJobForRelatedEntity($command, $relatedEntity)
    {
        return $this->findJobForRelatedEntity($command, $relatedEntity, [Job::STATE_RUNNING, Job::STATE_PENDING, Job::STATE_NEW]);
    }

    /**
     * @param       $command
     * @param       $relatedEntity
     * @param array $states
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return mixed
     */
    public function findJobForRelatedEntity($command, $relatedEntity, array $states = [])
    {
        [$relClass, $relId] = $this->getRelatedEntityIdentifier($relatedEntity);

        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata('JMSJobQueueBundle:Job', 'j');

        $sql = 'SELECT j.* FROM jms_jobs j INNER JOIN jms_job_related_entities r ON r.job_id = j.id WHERE r.related_class = :relClass AND r.related_id = :relId AND j.command = :command';
        $params = new ArrayCollection();
        $params->add(new Parameter('command', $command));
        $params->add(new Parameter('relClass', $relClass));
        $params->add(new Parameter('relId', $relId));

        if (!empty($states)) {
            $sql .= ' AND j.state IN (:states)';
            $params->add(new Parameter('states', $states, Connection::PARAM_STR_ARRAY));
        }

        $sql .= ' LIMIT 0,1';

        return $this->_em->createNativeQuery($sql, $rsm)
            ->setParameters($params)
            ->getOneOrNullResult();
    }

    /**
     * @param       $command
     * @param       $relatedEntity
     * @param array $states
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return mixed
     */
    public function findOneJobForRelatedEntity($command, $relatedEntity, array $states = [])
    {
        [$relClass, $relId] = $this->getRelatedEntityIdentifier($relatedEntity);

        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata('JMSJobQueueBundle:Job', 'j');

        $sql = 'SELECT j.* FROM jms_jobs j INNER JOIN jms_job_related_entities r ON r.job_id = j.id WHERE r.related_class = :relClass AND r.related_id = :relId AND j.command = :command';
        $params = new ArrayCollection();
        $params->add(new Parameter('command', $command));
        $params->add(new Parameter('relClass', $relClass));
        $params->add(new Parameter('relId', $relId));

        if (!empty($states)) {
            $sql .= ' AND j.state IN (:states)';
            $params->add(new Parameter('states', $states, Connection::PARAM_STR_ARRAY));
        }

        $sql .= ' ORDER BY j.id DESC LIMIT 0,1';

        return $this->_em->createNativeQuery($sql, $rsm)
            ->setParameters($params)
            ->getOneOrNullResult();
    }

    /**
     * @param $entity
     *
     * @return array
     */
    private function getRelatedEntityIdentifier($entity)
    {
        \assert('is_object($entity)');

        if ($entity instanceof \Doctrine\Common\Persistence\Proxy) {
            $entity->__load();
        }

        $relClass = ClassUtils::getClass($entity);

        $ManagerForClass = $this->registry->getManagerForClass($relClass);

        if (!$ManagerForClass) {
            throw new \InvalidArgumentException(sprintf('The getManagerForClass for entity of class "%s" was empty.', $relClass));
        }

        $relId = $ManagerForClass->getMetadataFactory()
            ->getMetadataFor($relClass)->getIdentifierValues($entity);

        asort($relId);

        if (!$relId) {
            throw new \InvalidArgumentException(sprintf('The identifier for entity of class "%s" was empty.', $relClass));
        }

        return [$relClass, json_encode($relId)];
    }

    /**
     * @param array $excludedIds
     * @param array $excludedQueues
     * @param array $restrictedQueues
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return mixed
     */
    public function findPendingJob(array $excludedIds = [], array $excludedQueues = [], array $restrictedQueues = [])
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('j')->from('JMSJobQueueBundle:Job', 'j')
            ->orderBy('j.priority', 'ASC')
            ->addOrderBy('j.id', 'ASC');

        $conditions = [];

        $conditions[] = $qb->expr()->isNull('j.workerName');

        $conditions[] = $qb->expr()->lt('j.executeAfter', ':now');
        $qb->setParameter(':now', new \DateTime(), 'datetime');

        $conditions[] = $qb->expr()->eq('j.state', ':state');
        $qb->setParameter('state', Job::STATE_PENDING);

        if (!empty($excludedIds)) {
            $conditions[] = $qb->expr()->notIn('j.id', ':excludedIds');
            $qb->setParameter('excludedIds', $excludedIds, Connection::PARAM_INT_ARRAY);
        }

        if (!empty($excludedQueues)) {
            $conditions[] = $qb->expr()->notIn('j.queue', ':excludedQueues');
            $qb->setParameter('excludedQueues', $excludedQueues, Connection::PARAM_STR_ARRAY);
        }

        if (!empty($restrictedQueues)) {
            $conditions[] = $qb->expr()->in('j.queue', ':restrictedQueues');
            $qb->setParameter('restrictedQueues', $restrictedQueues, Connection::PARAM_STR_ARRAY);
        }

        $qb->where(\call_user_func_array([$qb->expr(), 'andX'], $conditions));

        return $qb->getQuery()->setMaxResults(1)->getOneOrNullResult();
    }

    /**
     * @param Job $job
     * @param     $finalState
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function closeJob(Job $job, $finalState)
    {
        $this->_em->getConnection()->beginTransaction();
        try {
            $visited = [];
            $this->closeJobInternal($job, $finalState, $visited);
            $this->_em->flush();
            $this->_em->getConnection()->commit();

            // Clean-up entity manager to allow for garbage collection to kick in.
            foreach ($visited as $jobItem) {
                // If the job is an original job which is now being retried, let's
                // not remove it just yet.
                if (!$jobItem->isClosedNonSuccessful() || $jobItem->isRetryJob()) {
                    continue;
                }

                $this->_em->detach($jobItem);
            }
        } catch (\Exception $ex) {
            $this->_em->getConnection()->rollBack();

            throw $ex;
        }
    }

    /**
     * @param Job   $job
     * @param       $finalState
     * @param array $visited
     */
    private function closeJobInternal(Job $job, $finalState, array &$visited = [])
    {
        if (\in_array($job, $visited)) {
            return;
        }
        $visited[] = $job;

        if ($job->isInFinalState()) {
            return;
        }

        if (null !== $this->dispatcher && ($job->isRetryJob() || 0 === \count($job->getRetryJobs()))) {
            $event = new StateChangeEvent($job, $finalState);
            $this->dispatcher->dispatch('jms_job_queue.job_state_change', $event);
            $finalState = $event->getNewState();
        }

        switch ($finalState) {
            case Job::STATE_CANCELED:
                $job->setState(Job::STATE_CANCELED);
                $this->_em->persist($job);

                if ($job->isRetryJob()) {
                    $this->closeJobInternal($job->getOriginalJob(), Job::STATE_CANCELED, $visited);

                    return;
                }

                foreach ($this->findIncomingDependencies($job) as $dep) {
                    $this->closeJobInternal($dep, Job::STATE_CANCELED, $visited);
                }

                return;

            case Job::STATE_FAILED:
            case Job::STATE_TERMINATED:
            case Job::STATE_INCOMPLETE:
                if ($job->isRetryJob()) {
                    $job->setState($finalState);
                    $this->_em->persist($job);

                    $this->closeJobInternal($job->getOriginalJob(), $finalState);

                    return;
                }

                // The original job has failed, and we are allowed to retry it.
                if ($job->isRetryAllowed()) {
                    $retryJob = new Job($job->getCommand(), $job->getArgs(), true, $job->getQueue(), $job->getPriority());
                    $retryJob->setMaxRuntime($job->getMaxRuntime());

                    if (null === $this->retryScheduler) {
                        $this->retryScheduler = new ExponentialRetryScheduler(5);
                    }

                    $retryJob->setExecuteAfter($this->retryScheduler->scheduleNextRetry($job));

                    $job->addRetryJob($retryJob);
                    $this->_em->persist($retryJob);
                    $this->_em->persist($job);

                    return;
                }

                $job->setState($finalState);
                $this->_em->persist($job);

                // The original job has failed, and no retries are allowed.
                foreach ($this->findIncomingDependencies($job) as $dep) {
                    // This is a safe-guard to avoid blowing up if there is a database inconsistency.
                    if (!$dep->isPending() && !$dep->isNew()) {
                        continue;
                    }

                    $this->closeJobInternal($dep, Job::STATE_CANCELED, $visited);
                }

                return;

            case Job::STATE_FINISHED:
                if ($job->isRetryJob()) {
                    $job->getOriginalJob()->setState($finalState);
                    $this->_em->persist($job->getOriginalJob());
                }
                $job->setState($finalState);
                $this->_em->persist($job);

                return;

            default:
                throw new \LogicException(sprintf('Non allowed state "%s" in closeJobInternal().', $finalState));
        }
    }

    /**
     * @param Job $job
     *
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return Job[]
     */
    public function findIncomingDependencies(Job $job)
    {
        $jobIds = $this->getJobIdsOfIncomingDependencies($job);
        if (empty($jobIds)) {
            return [];
        }

        return $this->_em->createQuery('SELECT j, d FROM JMSJobQueueBundle:Job j LEFT JOIN j.dependencies d WHERE j.id IN (:ids)')
            ->setParameter('ids', $jobIds)
            ->getResult();
    }

    /**
     * @param Job $job
     *
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return Job[]
     */
    public function getIncomingDependencies(Job $job)
    {
        $jobIds = $this->getJobIdsOfIncomingDependencies($job);
        if (empty($jobIds)) {
            return [];
        }

        return $this->_em->createQuery('SELECT j FROM JMSJobQueueBundle:Job j WHERE j.id IN (:ids)')
            ->setParameter('ids', $jobIds)
            ->getResult();
    }

    /**
     * @param Job $job
     *
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return array
     */
    private function getJobIdsOfIncomingDependencies(Job $job)
    {
        $jobIds = $this->_em->getConnection()
            ->executeQuery('SELECT source_job_id FROM jms_job_dependencies WHERE dest_job_id = :id', ['id' => $job->getId()])
            ->fetchAll(\PDO::FETCH_COLUMN);

        return $jobIds;
    }

    /**
     * @param int $nbJobs
     *
     * @return mixed
     */
    public function findLastJobsWithError($nbJobs = 10)
    {
        return $this->_em->createQuery('SELECT j FROM JMSJobQueueBundle:Job j WHERE j.state IN (:errorStates) AND j.originalJob IS NULL ORDER BY j.closedAt DESC')
            ->setParameter('errorStates', [Job::STATE_TERMINATED, Job::STATE_FAILED])
            ->setMaxResults($nbJobs)
            ->getResult();
    }

    /**
     * @return array
     */
    public function getAvailableQueueList()
    {
        /** @var array $queues */
        $queues = $this->_em->createQuery('SELECT DISTINCT j.queue FROM JMSJobQueueBundle:Job j WHERE j.state IN (:availableStates)  GROUP BY j.queue')
            ->setParameter('availableStates', [Job::STATE_RUNNING, Job::STATE_NEW, Job::STATE_PENDING])
            ->getResult();

        $newQueueArray = [];

        foreach ($queues as $queue) {
            $newQueue = $queue['queue'];
            $newQueueArray[] = $newQueue;
        }

        return $newQueueArray;
    }

    /**
     * @param $jobQueue
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return int
     */
    public function getAvailableJobsForQueueCount($jobQueue)
    {
        $result = $this->_em->createQuery('SELECT j.queue FROM JMSJobQueueBundle:Job j WHERE j.state IN (:availableStates) AND j.queue = :queue')
            ->setParameter('availableStates', [Job::STATE_RUNNING, Job::STATE_NEW, Job::STATE_PENDING])
            ->setParameter('queue', $jobQueue)
            ->setMaxResults(1)
            ->getOneOrNullResult();

        return \count($result);
    }
}
