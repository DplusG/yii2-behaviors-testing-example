<?php

declare(strict_types=1);

namespace app\vats_api\events;

use app\common\vats\models\User;
use yii\base\Event;

class EventBeforeCrmSecretMethod extends Event
{
    public User $user;
}