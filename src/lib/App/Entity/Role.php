<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\Arrays;
use Plumbok\Annotation\Getter;
use Plumbok\Annotation\Setter;

/**
 * @ORM\Entity ()
 * @ORM\Table (name="security_roles")
 * @method int getId()
 * @method string getName()
 * @method void setName(string $name)
 * @method array getFlags()
 * @method \App\Entity\Role getParent()
 * @method void setParent(\App\Entity\Role $parent)
 */
class Role {

    /**
     * @var int
     * @Getter()
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @Getter()
     * @Setter()
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var array
     * @Getter()
     * @ORM\Column(type="array", length=496)
     */
    private $flags = array();

    /**
     * @var Role
     * @Getter()
     * @Setter()
     * @ORM\OneToOne(targetEntity="Role")
     */
    private $parent;

    public function allow ($flag) {
        if (!$this->isAllowed($flag)) {
            array_push($this->flags, $flag);
        }
    }

    public function deny ($flag) {
        $this->flags = array_diff($this->flags, [$flag]);
    }

    public function isAllowed ($flag, $includeParent = TRUE) {
        $allowed = in_array($flag, $this->flags) || in_array("*", $this->flags);

        if (!$allowed && $includeParent && $this->parent != NULL) {
            $allowed = $this->parent->isAllowed($flag, TRUE);
        }

        return $allowed;
    }
}