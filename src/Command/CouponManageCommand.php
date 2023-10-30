<?php

declare(strict_types=1);

namespace DOHWI\Coupon\Command;

use DOHWI\Coupon\Form\CouponManageForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;

final class CouponManageCommand extends Command
{
    public function __construct()
    {
        parent::__construct("쿠폰관리", "쿠폰을 관리하는 명령어입니다");
        $this->setPermission(DefaultPermissions::ROOT_OPERATOR);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if(!$this->testPermission($sender) || !$sender instanceof Player) return;
        $sender->sendForm(new CouponManageForm());
    }
}