<?php

namespace Compo\CoreBundle\Twig;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Sonata\CoreBundle\Model\ManagerInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Provider\Pool;
use Sonata\MediaBundle\Twig\TokenParser\MediaTokenParser;
use Sonata\MediaBundle\Twig\TokenParser\PathTokenParser;
use Sonata\MediaBundle\Twig\TokenParser\ThumbnailTokenParser;

class MediaExtension extends \Twig_Extension
{
    use ContainerAwareTrait;


    /**
     * @var ManagerInterface
     */
    protected $mediaManager;

    /**
     * @param ManagerInterface $mediaManager
     */
    public function __construct(ManagerInterface $mediaManager)
    {
        $this->mediaManager = $mediaManager;
    }


    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('media_width', array($this, 'getWidth'), array('is_safe' => array('html'))),

            new \Twig_SimpleFunction('media_height', array($this, 'getHeight'), array('is_safe' => array('html'))),

        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'compo_media';
    }

    /**
     * @return string
     */
    public function getWidth($media)
    {
        $media = $this->getMedia($media);

        if ($media) {
            return $media->getWidth();
        }

        return null;
    }

    /**
     * @return string
     */
    public function getHeight($media)
    {
        $media = $this->getMedia($media);

        if ($media) {
            return $media->getHeight();
        }

        return null;
    }

    /**
     * @param mixed $media
     *
     * @return MediaInterface|null|bool
     */
    private function getMedia($media)
    {
        if (!$media instanceof MediaInterface && strlen($media) > 0) {
            $media = $this->mediaManager->findOneBy(array(
                'id' => $media,
            ));
        }

        if (!$media instanceof MediaInterface) {
            return false;
        }

        if ($media->getProviderStatus() !== MediaInterface::STATUS_OK) {
            return false;
        }

        return $media;
    }
}
