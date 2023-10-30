<?php

declare(strict_types=1);

namespace DOHWI\Coupon\Form;

use DOHWI\Coupon\Coupon;
use pocketmine\form\Form;
use pocketmine\player\Player;
use ryun42680\richdesign\Design;
use function str_replace;

final class SelectEditCouponForm implements Form
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
            "title" => "쿠폰 수정",
            "content" => "수정할 쿠폰을 선택해주세요",
            "buttons" => $this->buttons
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if($data === null) return;
        $player->sendForm(new EditCouponForm($this->coupons[$data]));
    }
}