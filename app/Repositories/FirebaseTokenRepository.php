<?php

namespace App\Repositories;

use App\Models\FirebaseToken;

class FirebaseTokenRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(FirebaseToken::class);
    }

}
