<?php

declare(strict_types=1);

namespace DOHWI\Coupon\Util;

use pocketmine\item\Item;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\TreeRoot;

final class ItemUtil
{
    /** @param Item[] $items */
    public static function convertToString(array $items): string
    {
        $serializer = new LittleEndianNbtSerializer();
        $tag = new ListTag();
        foreach($items as $item) {
            $tag->push($item->nbtSerialize());
        }
        return $serializer->write(new TreeRoot($tag));
    }

    /** @return Item[] */
    public static function convertToItems(string $stringItem): array
    {
        $deserializer = new LittleEndianNbtSerializer();
        /** @var ListTag $tag */
        $tag = $deserializer->read($stringItem)->getTag();
        $contents = [];
        foreach($tag as $item) {
            $contents[] = Item::nbtDeserialize($item);
        }
        return $contents;
    }
}