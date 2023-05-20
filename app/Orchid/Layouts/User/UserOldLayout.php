<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Layouts\Rows;

class UserOldLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            CheckBox::make('user.active')
                ->title('Управление доступом')
                ->placeholder('Активировать сервис демпинга')
                ->sendTrueOrFalse()

        ];
    }
}
