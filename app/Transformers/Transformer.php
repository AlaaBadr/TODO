<?php
/**
 * Created by PhpStorm.
 * User: Alaa
 * Date: 03-Aug-17
 * Time: 10:35 AM
 */

namespace App\Transformers;


abstract class Transformer
{
    public function transformCollection(array $items)
    {
        return array_map([$this, 'transform'], $items);
    }

    public abstract function transform($item);
}