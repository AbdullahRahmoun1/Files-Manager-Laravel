<?php

namespace App\Repositories;

use App\Models\FirebaseToken;
use Wever\Laradot\App\Repositories\DotRepository;

class FirebaseTokenRepository extends DotRepository
{
    public function __construct()
    {
        parent::__construct(app(FirebaseToken::class));
    }

}
