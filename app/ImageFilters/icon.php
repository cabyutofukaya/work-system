<?php

namespace App\ImageFilters;

use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;

class icon implements FilterInterface
{
    public function applyFilter(Image $image): Image
    {
        return $image
            // EXIF情報を元に回転
            ->orientate()
            // リサイズ
            ->widen(120, function ($constraint) {
                // 拡大は行わない
                $constraint->upsize();
            });
    }
}
