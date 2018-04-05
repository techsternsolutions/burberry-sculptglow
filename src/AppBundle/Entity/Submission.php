<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SubmissionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Submission
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="share.form.error.name.blank")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="share.form.error.city.blank")
     */
    protected $location;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"default" = 0})
     */
    protected $likes = 0;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="share.form.error.picture.blank")
     */
    protected $path;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $approved = false;


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     * @return $this
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isApproved()
    {
        return $this->approved;
    }

    /**
     * @param boolean $approved
     * @return $this
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;
        return $this;
    }



    /**
     * @return int
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * @param int $likes
     * @return $this
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;
        return $this;
    }

    /**
     * Absolute path to file
     *
     * @return string
     */
    public function getAbsolutePath()
    {
        return $this->getUploadRootDir() . '/' . $this->path;
    }

    /**
     * Web path to the file
     *
     * @return string
     */
    public function getWebPath()
    {
        return $this->getUploadDir();
    }

    /**
     * Where on the file system file lives
     *
     * @return string
     */
    protected function getUploadRootDir()
    {
        return realpath(__DIR__.'/../../../../web/'.$this->getUploadDir());
    }

    /**
     * Name of the uploads directory
     *
     * @return string
     */
    protected function getUploadDir()
    {
        return 'uploads';
    }

    /**
     * Remove the entity file
     *
     * @ORM\PostRemove()
     */
    public function postRemove()
    {
        if (file_exists($path = $this->getAbsolutePath())) {
            unlink($path);
        }
    }
}