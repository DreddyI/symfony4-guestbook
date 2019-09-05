<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Post;
use League\Csv\CannotInsertRecord;
use League\Csv\Writer;

final class PostExporter
{

    /**
     * @param array[] Post
     * @return string|null
     * @throws CannotInsertRecord
     */
    public function export(array $posts): ?string
    {
        $posts = array_map(function (Post $post) {
            return [$post->getAuthor(), $post->getBody(), $post->getCreatedAt()->format('d.m.Y H:i:s')];
        }, $posts);

        $header = ['author', 'body', 'created date'];

        $writer = Writer::createFromString();
        $writer->insertOne($header);
        $writer->insertAll($posts);

        $content = $writer->getContent();

        return $content;
    }
}