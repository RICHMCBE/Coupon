<?php

declare(strict_types=1);

namespace DOHWI\Coupon;

use muqsit\invmenu\InvMenu;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\player\Player;
use RoMo\MoneyCore\wallet\WalletFactory;
use ryun42680\richdesign\Design;
use function time;

final class CouponData
{
    /**
     * @param int $money
     * @param int $count
     * @param int $time
     * @param Item[] $items
     * @param string[] $players
     */
    public function __construct
    (
        public int $money,
        public int $count,
        public int $time,
        public array $items,
        public array $players = []
    )
    {
    }

    public function useCoupon(Player $player): void
    {
        if(isset($this->players[$player->getName()])) {
            $player->sendMessage(Design::$prefix_3."이미 사용한 쿠폰입니다");
            return;
        }
        if($this->time < time()) {
            $player->sendMessage(Design::$prefix_3."해당 쿠폰은 이미 기한이 지났습니다");
            return;
        }
        if($this->count < 0) {
            $player->sendMessage(Design::$prefix_3."해당 쿠폰은 선착순이 마감되었습니다");
            return;
        }

        $inv = InvMenu::create(InvMenu::TYPE_CHEST);
        $inv->getInventory()->setContents($this->items);
        $inv->setInventoryCloseListener(function(Player $player): void
        {
            $playerName = $player->getName();
            $this->players[$playerName] = true;
            $this->count--;
            WalletFactory::getInstance()->getWallet($playerName)->addCoin($this->money);
        });
        $inv->send($player, "쿠폰 보상");
    }
}