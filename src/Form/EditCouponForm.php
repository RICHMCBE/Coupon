<?php

declare(strict_types=1);

namespace DOHWI\Coupon\Form;

use DOHWI\Coupon\Coupon;
use DOHWI\Coupon\CouponData;
use DOHWI\Coupon\Util\TimeStampUtil;
use muqsit\invmenu\InvMenu;
use pocketmine\form\Form;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;
use ryun42680\richdesign\Design;
use function count;
use function explode;
use function is_numeric;
use const PHP_INT_MAX;

final class EditCouponForm implements Form
{
    private CouponData $couponData;

    public function __construct(private readonly string $code)
    {
        $this->couponData = Coupon::getCoupon($this->code);
    }

    public function jsonSerialize(): array
    {
        return [
            "type" => "custom_form",
            "title" => "쿠폰 수정 ({$this->code})",
            "content" => [
                [
                    "type" => "input",
                    "text" => Design::FORM_TEXT."쿠폰의 선착순을 입력해주세요",
                    "default" => (string) $this->couponData->count
                ],
                [
                    "type" => "input",
                    "text" => Design::FORM_TEXT."쿠폰의 기간을 입력해주세요",
                    "placeholder" => "EX) 년-월-일-시-분-초",
                    "default" => TimeStampUtil::convertToString($this->couponData->time)
                ],
                [
                    "type" => "input",
                    "text" => Design::FORM_TEXT."쿠폰 사용 시 지급할 금액을 적어주세요",
                    "default" => (string) $this->couponData->money
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if($data === null) return;
        $count = $data[0];
        $money = $data[2];
        if(!is_numeric($count) || !is_numeric($money)) {
            $player->sendMessage(Design::$prefix_3."선착순 또는 금액은 숫자로만 적어주세요");
            return;
        }
        $count = $count === "-1" ? PHP_INT_MAX : (int) $count;
        $money = (int) $money;
        if($data[2] === "-1") {
            $time = PHP_INT_MAX;
        } else {
            $elements = explode("-", $data[2]);
            if(
                count($elements) < 6 ||
                !is_numeric($elements[0]) ||
                !is_numeric($elements[1]) ||
                !is_numeric($elements[2]) ||
                !is_numeric($elements[3]) ||
                !is_numeric($elements[4]) ||
                !is_numeric($elements[5])
            ) {
                $player->sendMessage(Design::$prefix_3."기간을 형식에 맞게 입력해주세요");
                return;
            }
            $time = TimeStampUtil::convertToInt($elements);
        }

        $inv = InvMenu::create(InvMenu::TYPE_CHEST);
        $inv->getInventory()->setContents($this->couponData->items);
        $inv->setInventoryCloseListener(function(Player $player, Inventory $inventory) use($count, $time, $money): void
        {
            Coupon::addCoupon($this->code, $count, $time, $money, $inventory->getContents());
            $player->sendMessage(Design::$prefix_3."{$this->code}(이)라는 쿠폰이 수정되었습니다");
        });
    }
}