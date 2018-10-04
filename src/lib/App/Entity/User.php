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
 * @method bool isEnabled()
 * @method void setEnabled(bool $enabled)
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
        $this->enabled = TRUE;
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
     * @ORM\Column(unique=true, type="string", length=496)
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
     * @var bool
     * @ORM\Column(type="boolean")
     * @Getter()
     * @Setter()
     */
    private $enabled;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     * @Getter()
     * @Setter()
     */
    private $otp_token;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true)
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
        $this->password = $this->hashPassword($password);
    }

    public function passwordsMatch ($password) {
        return $this->hashPassword($password) == $this->getPassword();
    }

    private function hashPassword ($password) {
        return hash("sha256", $password . $this->username . $this->created->format("d.m.Y"));
    }

    public function isAllowed ($flag) {
        foreach ($this->roles as $role) {
            if ($role->isAllowed($flag)) return TRUE;
        }
        return FALSE;
    }
}