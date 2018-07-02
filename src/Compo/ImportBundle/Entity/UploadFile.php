<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\ImportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UploadFile.
 *
 * @ORM\Table("import_file")
 * @ORM\Entity(repositoryClass="Compo\ImportBundle\Repository\DefaultRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class UploadFile
{
    use \Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\TimestampableEntityTrait;

    /**
     * STATUS_LOAD
     */
    public const STATUS_LOAD = 1;

    /**
     * STATUS_SUCCESS
     */
    public const STATUS_SUCCESS = 2;

    /**
     * STATUS_ERROR
     */
    public const STATUS_ERROR = 3;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $command;

    /**
     * @var string
     *
     * @ORM\Column(name="file", type="string")
     */
    private $file;

    /**
     * @var string
     *
     * @ORM\Column(name="encode", type="string")
     */
    private $encode;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default": true})
     */
    private $dryRun = true;

    /**
     * @var string
     *
     * @ORM\Column(name="loader_class", type="integer")
     */
    private $loaderClass;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set file.
     *
     * @param string $file
     *
     * @return UploadFile
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file.
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function prePersistUpdate()
    {
        if (!$this->status) {
            $this->status = self::STATUS_LOAD;
        }
    }

    /**
     * @param $encode
     *
     * @return UploadFile
     */
    public function setEncode($encode)
    {
        $this->encode = $encode;

        return $this;
    }

    /**
     * @return string
     */
    public function getEncode()
    {
        return $this->encode;
    }

    /**
     * @param $message
     *
     * @return UploadFile
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param $status
     *
     * @return UploadFile
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param $loaderClass
     *
     * @return $this
     */
    public function setLoaderClass($loaderClass)
    {
        $this->loaderClass = $loaderClass;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLoaderClass()
    {
        return $this->loaderClass;
    }

    /**
     * @param $uploadDir
     */
    public function move($uploadDir)
    {
        $file = $this->getFile();
        /** @noinspection PhpUndefinedMethodInspection */
        $fileName = md5(uniqid('', true) . time()) . '.' . $file->guessExtension();
        /* @noinspection PhpUndefinedMethodInspection */
        $file->move($uploadDir, $fileName);
        $this->setFile($uploadDir . '/' . $fileName);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function isDryRun()
    {
        return $this->dryRun;
    }

    /**
     * @param bool $dryRun
     */
    public function setDryRun($dryRun)
    {
        $this->dryRun = $dryRun;
    }

    /**
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param string $command
     */
    public function setCommand($command)
    {
        $this->command = $command;
    }
}
