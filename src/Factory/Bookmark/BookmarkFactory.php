<?php

declare(strict_types = 1);

namespace App\Factory\Bookmark;

use App\Entity\Auction\Item;
use App\Entity\Bookmark;
use App\Entity\User;

class BookmarkFactory
{
    public function create(null|User $user, null|string $title = null, null|string $description = null): Bookmark
    {
        $bookmark = new Bookmark();
        $bookmark->setTitle($title);
        $bookmark->setDescription($description);
        $bookmark->setOwner($user);

        return $bookmark;
    }

    public function createItemBookmark(
        null|User $user,
        Item $item,
        null|string $title = null,
        null|string $description = null
    ): Bookmark {
        $bookmark = $this->create($user, $title, $description);
        $bookmark->setItem($item);

        return $bookmark;
    }
}
