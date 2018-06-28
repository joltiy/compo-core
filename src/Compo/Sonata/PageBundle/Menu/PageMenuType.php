<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\PageBundle\Menu;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\Sonata\PageBundle\Entity\Page;

class PageMenuType
{
    use ContainerAwareTrait;

    public function getChoices()
    {
        $repository = $this->getContainer()->get('doctrine')->getRepository(Page::class);

        /** @var Page[] $items */
        $items = $repository->findBy(['routeName' => 'page_slug'], ['parent' => 'asc', 'position' => 'asc']);

        $choices = [];

        foreach ($items as $item) {
            $choices[$item->getName()] = $item->getId();
        }

        return $choices;
    }

    public function getName()
    {
        return 'page';
    }

    public function fillMenuItem(&$item)
    {
        $repository = $this->getContainer()->get('doctrine')->getRepository(Page::class);

        $item['url'] = $this->getContainer()->get('router')->generate('page_slug', ['path' => $repository->find($item['node']->getTargetId())->getUrl()]);
    }
}
