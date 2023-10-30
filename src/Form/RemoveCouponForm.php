<?php

declare(strict_types=1);

namespace DOHWI\Coupon\Form;

use DOHWI\Coupon\Coupon;
use pocketmine\form\Form;
use pocketmine\player\Player;
use ryun42680\richdesign\Design;
use function str_replace;

final class RemoveCouponForm implements Form
{
    private array $coupons = [];
    private array $buttons = [];

    public function __construct()
    {
        foreach(Coupon::getAllCoupon() as $code => $data) {
            $this->coupons[] = $code;
            $this->buttons[] = ["text" => str_replace("content", "$code", Design::FORM_BUTTON)];
        }
    }

    public function jsonSerialize(): array
    {
        return [
            "type" => "form",
            "title" => "쿠폰 제거",
            "content" => "제거할 쿠폰을 선택해주세요",
            "buttons" => $this->buttons
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if($data === null) return;
        $code = $this->coupons[$data];
        Coupon::removeCoupon($code);
        $player->sendMessage(Design::$prefix_3."{$code}(이)라는 쿠폰이 제거되었습니다");
    }
}