<?php

declare(strict_types=1);

namespace App\Behaviors;

use App\Models\User;
use App\Events\EventBeforeCrmSecretMethod;
use yii\base\Behavior;
use yii\base\Exception;

class CheckCrmUserBehavior extends Behavior
{
    public User $user;

    public function events(): array
    {
        return [
            EventBeforeCrmSecretMethod::class => 'beforeCrmSecretMethod',
        ];
    }

    /**
     * @param EventBeforeCrmSecretMethod $event
     * @return void
     * @throws Exception
     */
    public function beforeCrmSecretMethod(EventBeforeCrmSecretMethod $event): void
    {
        if (!$event->user->isCrmUser()) {
            throw new Exception('Вам запрещено данное действие');
        }
    }
}