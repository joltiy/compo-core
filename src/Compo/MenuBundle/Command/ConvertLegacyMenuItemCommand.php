<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\MenuBundle\Command;

use Compo\MenuBundle\Entity\MenuItem;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertLegacyMenuItemCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('compo:menu:convert:legacy-menu-item')
            ->setDescription('compo:menu:convert:legacy-menu-item');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $em = $container->get('doctrine')->getManager();

        $menuItemRepository = $em->getRepository(MenuItem::class);

        /** @var MenuItem[] $items */
        $items = $menuItemRepository->findAll();

        foreach ($items as $item) {
            $targetId = null;

            switch ($item->getType()):
                case 'page':
                    if ($item->getPage()) {
                        $targetId = $item->getPage()->getId();
                    }
            break;
            case 'country':
                    if ($item->getCountry()) {
                        $targetId = $item->getCountry()->getId();
                    }
            break;
            case 'manufacture':
                    if ($item->getManufacture()) {
                        $targetId = $item->getManufacture()->getId();
                    }
            break;
            case 'tagging':
                    if ($item->getTagging()) {
                        $targetId = $item->getTagging()->getId();
                    }
            break;
            case 'catalog':
                    if ($item->getCatalog()) {
                        $targetId = $item->getCatalog()->getId();
                    }
            break;
            endswitch;

            if ($targetId) {
                $item->setTargetId($targetId);
            } else {
                $item->setType('url');
            }
        }

        $em->flush();
    }
}
