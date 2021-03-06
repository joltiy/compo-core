<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Resolver;

use Doctrine\ORM\NonUniqueResultException;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
final class DefaultSettingsResolver implements SettingsResolverInterface
{
    /**
     * @var RepositoryInterface
     */
    private $settingsRepository;

    /**
     * @param RepositoryInterface $settingsRepository
     */
    public function __construct(RepositoryInterface $settingsRepository)
    {
        $this->settingsRepository = $settingsRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($schemaAlias, $namespace = null)
    {
        try {
            $criteria = ['schemaAlias' => $schemaAlias];

            if (null !== $namespace) {
                $criteria['namespace'] = $namespace;
            }

            return $this->settingsRepository->findOneBy($criteria);
        } catch (NonUniqueResultException $exception) {
            throw new \LogicException(
                sprintf('Multiple schemas found for "%s". You should probably define a custom settings resolver for this schema.', $schemaAlias),
                0,
                $exception
            );
        }
    }
}
