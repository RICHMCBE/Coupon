<?php

declare(strict_types=1);

namespace DOHWI\Coupon;

use DOHWI\Coupon\Command\CouponCommand;
use DOHWI\Coupon\Command\CouponManageCommand;
use DOHWI\Coupon\Util\ItemUtil;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

final class Coupon extends PluginBase
{
    private static Config $config;

    /** @var CouponData[] $data */
    private static array $data = [];

    protected function onEnable(): void
    {
        $this->getServer()->getCommandMap()->registerAll($this->getName(), [
            new CouponCommand(),
            new CouponManageCommand()
        ]);
        self::$config = new Config($this->getDataFolder()."data.yml", Config::YAML);
        foreach(self::$config->getAll() as $code => $data) {
            self::$data[$code] = new CouponData(
                $data["MONEY"],
                $data["COUNT"],
                $data["TIME"],
                ItemUtil::convertToItems($data["ITEMS"]),
                $data["PLAYERS"]
            );
        }
    }

    protected function onDisable(): void
    {
        $data = [];
        foreach(self::$data as $code => $couponData) {
            $data[$code] = [
                "MONEY" => $couponData->money,
                "COUNT" => $couponData->count,
                "TIME" => $couponData->count,
                "ITEMS" => ItemUtil::convertToString($couponData->items),
                "PLAYERS" => $couponData->players
            ];
        }
        self::$config->setAll($data);
        self::$config->save();
    }

    public static function getCoupon(string $code): CouponData|null
    {
        return self::$data[$code] ?? null;
    }

    /** @return CouponData[] */
    public static function getAllCoupon(): array
    {
        return self::$data;
    }

    public static function addCoupon(string $code, int $count, int $time, int $money, array $items): void
    {
        self::$data[$code] = new CouponData(
            $money,
            $count,
            $time,
            $items
        );
    }

    public static function removeCoupon(string $code): void
    {
        unset(self::$data[$code]);
    }
}