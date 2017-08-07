<?php
/**
 * Created by PhpStorm.
 * User: Alaa
 * Date: 06-Aug-17
 * Time: 1:16 PM
 */

namespace App\Transformers;


class UserTransformer extends Transformer
{
    public function transform($user)
    {
        return
        [
            'id' => (integer) $user['id'],
            'name' => $user['name'],
            'username' => $user['username'],
            'email' => $user['email']
        ];
    }
}