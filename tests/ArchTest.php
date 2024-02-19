<?php

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('ensures `.env` variables are not referenced outside of config files')
    ->expect('env')
    ->toBeUsedInNothing();
