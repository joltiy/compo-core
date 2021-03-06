<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Translation\Finder;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class OrderingTranslationFilesFinder implements TranslationFilesFinderInterface
{
    /**
     * @var TranslationFilesFinderInterface
     */
    private $translationFilesFinder;

    /**
     * @param TranslationFilesFinderInterface $translationFilesFinder
     */
    public function __construct(TranslationFilesFinderInterface $translationFilesFinder)
    {
        $this->translationFilesFinder = $translationFilesFinder;
    }

    public function findTranslationFiles($path)
    {
        $files = $this->translationFilesFinder->findTranslationFiles($path);

        /*
         * PHP 5.* bug, fixed in PHP 7: https://bugs.php.net/bug.php?id=50688
         * "usort(): Array was modified by the user comparison function"
         */
        @usort($files, function ($firstFile, $secondFile) use ($path) {
            $firstFile = str_replace($path, '', $firstFile);
            $secondFile = str_replace($path, '', $secondFile);

            return mb_strpos($secondFile, 'translations') - mb_strpos($firstFile, 'translations');
        });

        return $files;
    }
}
