<?php

declare(strict_types=1);

namespace DOHWI\Coupon\Command;

use DOHWI\Coupon\Coupon;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use ryun42680\richdesign\Design;
use function array_shift;

final class  CouponCommand extends Command
{
    public function __construct()
    {
        parent::__construct("쿠폰", "쿠폰 관련 명령어입니다");
        $this->setPermission(DefaultPermissions::ROOT_USER);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if(!$sender instanceof Player) return;
        $code = array_shift($args);
        if($code === null) {
            $sender->sendMessage(Design::$prefix_3."/쿠폰 <코드>");
            return;
        }
        $couponData = Coupon::getCoupon($code);
        if($couponData === null) {
            $sender->sendMessage(Design::$prefix_3."그런 쿠폰은 존재하지 않습니다");
            return;
        }
        $couponData->useCoupon($sender);
    }
}