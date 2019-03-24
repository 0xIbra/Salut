<?php
/**
 * Created by PhpStorm.
 * User: ibragim.abubakarov
 * Date: 24/03/2019
 * Time: 13:41
 */

namespace App\Security\Voter;


use App\Entity\User;

class Voters
{
    const VIEW = 'VIEW';
    const EDIT = 'EDIT';

    public static function isAdmin(User $user)
    {
        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        return false;
    }

}