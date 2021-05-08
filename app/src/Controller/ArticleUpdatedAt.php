<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Article;

class ArticleUpdatedAt
{
    public function __invoke(Article $data): Article
    {
        $data->setUpdatedAt(new \DateTimeImmutable('tomorrow'));

        return $data;
    }
}
