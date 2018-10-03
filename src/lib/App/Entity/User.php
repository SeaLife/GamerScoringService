<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Plumbok\Annotation\Getter;
use Plumbok\Annotation\Setter;

/**
 * @ORM\Entity ()
 * @ORM\Table (name="security_users")
 * @method int getId()
 * @method string getUsername()
 * @method void setUsername(string $username)
 * @method string getPassword()
 * @method string getOtpToken()
 * @method void setOtpToken(string $otp_token)
 * @method string getEmail()
 * @method void setEmail(string $email)
 * @method \DateTime getCreated()
 * @method void setCreated(\DateTime $created)
 * @method \App\Entity\Role[] getRoles()
 * @method void setRoles(\App\Entity\Role[] $roles)
 */
class User {

    /**
     * Constructs User, set default values for inserting.
     */
    public function __construct () {
        $this->created = new \DateTime();
    }

    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Getter()
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(unique=true, type="string")
     * @Getter()
     * @Setter()
     */
    private $username;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Getter()
     */
    private $password;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     * @Getter()
     * @Setter()
     */
    private $otp_token;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Getter()
     * @Setter()
     */
    private $email;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Getter()
     * @Setter()
     */
    private $created;

    /**
     * @var \App\Entity\Role[]
     * @ORM\ManyToMany(targetEntity="Role")
     * @Getter()
     * @Setter()
     */
    private $roles = array();

    public function setPassword ($password) {
        $this->password = hash("sha256", $password);
    }
}