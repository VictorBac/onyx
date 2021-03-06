<?php

namespace Payutc\OnyxBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

use Payutc\OnyxBundle\Exception\UnvalidatedEmailException;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository implements UserProviderInterface
{
    /**
     * Find a user instance from a username in the EntityRepository.
     *
     * @param string $username
     * @return User
     */
	public function loadUserByUsername($username)
    {
        $user = null;

        $qb = $this->_em->createQueryBuilder();

        $qb->select('u')
            ->from('PayutcOnyxBundle:User', 'u')
            ->where('u.email = :email')
            ->setParameter('email', $username)
        ;

        try {
            $user = $qb->getQuery()->getSingleResult();
        }
        catch (NoResultException $e) {
            throw new UsernameNotFoundException(sprintf('Ce nom d\'utilisateur est inconnu : "%s".', $username), 0, $e);
        }

        return $user;
    }

    /**
     * Refresh the given user instance from EntityRepository.
     *
     * @param UserInterface $user
     * @return User
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user));
        }

        return $this->find($user->getId());
    }
    
    /**
     * Define if a given classname is a subclass of this entity or not.
     *
     * @param string $class
     * @return boolean
     */
    public function supportsClass($class)
    {
        return ($class === $this->getEntityName() || is_subclass_of($class, $this->getEntityName()));
    }

    /**
     * Find one entity by id that have is_email_validated property set up to false.
     *
     * @return User
     */
    public function findOneWithUnvalidatedEmail($id)
    {
        $user = null;

        $qb = $this->_em->createQueryBuilder();

        $qb->select('u')
            ->from('PayutcOnyxBundle:User', 'u')
            ->where('u.id = :id')
            ->andWhere('u.isEmailValidated = :isEmailValidated')
            ->setParameter('id', $id)
            ->setParameter('isEmailValidated', false)
        ;

        try {
            $user = $qb->getQuery()->getSingleResult();
        }
        catch (NoResultException $e) {}

        return $user;
    }
}