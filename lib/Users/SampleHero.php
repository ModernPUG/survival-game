<?php

declare(strict_types=1);

namespace Users;

class SampleHero extends SampleUser
{
    public function getName(): string
    {
        return '영웅용사';
    }
}
