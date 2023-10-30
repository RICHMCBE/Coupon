<?php

declare(strict_types=1);

namespace DOHWI\Coupon\Form;

use DOHWI\Coupon\Coupon;
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

final class CreateCouponForm implements Form
{
    public function jsonSerialize(): array
    {
        return [
            "type" => "custom_form",
            "title" => "쿠폰생성",
            "content" => [
                [
                    "type" => "input",
                    "text" => Design::FORM_TEXT."쿠폰의 코드를 입력해주세요*"
                ],
                [
                    "type" => "input",
                    "text" => Design::FORM_TEXT."쿠폰의 선착순을 입력해주세요",
                    "default" => "-1"
                ],
                [
                    "type" => "input",
                    "text" => Design::FORM_TEXT."쿠폰의 기간을 입력해주세요",
                    "default" => "-1",
                    "placeholder" => "EX) 년-월-일-시-분-초"
                ],
                [
                    "type" => "input",
                    "text" => Design::FORM_TEXT."쿠폰 사용 시 지급할 금액을 적어주세요",
                    "default" => "0"
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if($data === null) return;
        $code = $data[0];
        if(!$code) {
            $player->sendMessage(Design::$prefix_3."쿠폰의 코드를 똑바로 적어주세요");
            return;
        }
        if(Coupon::getCoupon($code) !== null) {
            $player->sendMessage(Design::$prefix_3."{$code}(이)라는 쿠폰은 이미 존재합니다");
            return;
        }
        $count = $data[1];
        $money = $data[3];
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
        $inv->setInventoryCloseListener(function(Player $player, Inventory $inventory) use($code, $count, $time, $money): void
        {
            Coupon::addCoupon($code, $count, $time, $money, $inventory->getContents());
            $player->sendMessage(Design::$prefix_3."{$code}(이)라는 쿠폰이 생성되었습니다");
        });
        $inv->send($player, "{$code}쿠폰 아이템 설정");
    }
}