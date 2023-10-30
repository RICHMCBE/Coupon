<?php

declare(strict_types=1);

namespace DOHWI\Coupon\Form;

use pocketmine\form\Form;
use pocketmine\player\Player;
use ryun42680\richdesign\Design;
use function str_replace;

final class CouponManageForm implements Form
{
    public function jsonSerialize(): array
    {
        return [
            "type" => "form",
            "title" => "쿠폰관리",
            "content" => Design::FORM_TEXT."하실 작업을 선택해주세요",
            "buttons" => [
                ["text" => str_replace("content", "생성", Design::FORM_BUTTON)],
                ["text" => str_replace("content", "수정", Design::FORM_BUTTON)],
                ["text" => str_replace("content", "제거", Design::FORM_BUTTON)]
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if($data === null) return;
        $player->sendForm(match($data) {
            0 => new CreateCouponForm(),
            1 => new SelectEditCouponForm(),
            2 => new RemoveCouponForm()
        });
    }
}