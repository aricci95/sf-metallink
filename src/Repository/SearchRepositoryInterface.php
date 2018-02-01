<?php

namespace App\Repository;

use App\Entity\User;

interface SearchRepositoryInterface
{
    public function search(User $user, array $params = [], array $blacklist = [], $page = 1, $pageSize = 50);

    public function searchCount(User $user, array $params = [], array $blacklist = []);
}
