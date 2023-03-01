<?php
/**
 * Created by PhpStorm.
 * User: giorgiopagnoni
 * Date: 17/01/18
 * Time: 10:22
 */

namespace App\Entity;

use App\Validator\Constraints\ComplexPassword;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity()
 * @UniqueEntity(fields={"email"}, message="user.exists")
 */
class User implements AdvancedUserInterface, \Serializable

{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $username;

 

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @Assert\NotBlank(groups={"Registration"})
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank(groups={"Registration", "PasswordReset"})
     * @Assert\Length(min="8")
     * @ComplexPassword()
     */

    private $password;

  


    /**
     * @ORM\Column(type="string",length=64 )
     */
    private $role;


    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $token;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $activatedAt;

    
    protected $captchaCode;
















    
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }



    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $nome
     */
    public function setName($name): void
    {
        $this->name = $name;
    }




/**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }



    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }


    //houni
    /**
     * @param string $username
     */
    public function setUsername ($username): void
    {
        $this->username= $username;
    }




    
 /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole($role): void
    {
        $this->role = $role;
    }





    /**
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive($isActive): void
    {
        $this->isActive = $isActive;
    }



    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token): void
    {
        $this->token = $token;
    }

    /**
     * @param mixed $activatedAt
     */
    public function setActivatedAt($activatedAt): void
    {
        $this->activatedAt = $activatedAt;
    }
//lll

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }







    public function getCaptchaCode()
    {
        return $this->captchaCode;
    }

    public function setCaptchaCode($captchaCode)
    {
        $this->captchaCode = $captchaCode;
    }









    // not used

    /**
     * @return string
     */
   /* public function getUsername()
    {
        return $this->getEmail();
    }
*/
    /**
     * @return string
     */
    public function getUsername()
     {
         return $this->username;
     }





     public function getSalt()
     {
         return null;
     }

     public function eraseCredentials()
     {
     }

     public function getRoles()
     {
         return array('ROLE_USER');
     }

     public function isAccountNonExpired()
     {
         return true;
     }

     public function isAccountNonLocked()
     {
         return true;
     }

     public function isCredentialsNonExpired()
     {
         return true;
     }



     /**
      * @return bool
      */
    public function isEnabled()
    {
        return $this->isActive;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->name,
            $this->email,
            $this->password,
            
            $this->role,
            $this->isActive,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            
            $this->name,
            $this->password,
            
            $this->role,
            $this->isActive,
           
            ) = unserialize($serialized);
    }
}