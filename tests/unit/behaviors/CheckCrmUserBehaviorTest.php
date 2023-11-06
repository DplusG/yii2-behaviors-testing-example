<?php namespace behaviors;

use App\Models\User;
use App\Behaviors\CheckCrmUserBehavior;
use App\Events\EventBeforeCrmSecretMethod;
use Codeception\Test\Unit;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\log\Logger;

class CheckCrmUserBehaviorTest extends Unit
{
    /**
     * @return void
     * @throws InvalidConfigException
     */
    protected function _before(): void
    {
        Yii::setLogger(Yii::createObject(Logger::class));
        Yii::$app->log->setLogger(Yii::getLogger());
    }

    protected function _after()
    {
        Yii::getLogger()->flush();
    }

    public function getTestController(int $user): Component
    {
        return new class(['user' => new User(['empId' => $user])]) extends Component {
            public User $user;

            public function behaviors(): array
            {
                return [
                    'checkCrmUser' => CheckCrmUserBehavior::class
                ];
            }

            public function onlyForCrmUsers(): string
            {
                $this->trigger(EventBeforeCrmSecretMethod::class, new EventBeforeCrmSecretMethod(['user' => $this->user]));
                return 'All is good';
            }

            public function forAll(): string
            {
                return 'All is good';
            }
        };
    }

    public function testItDisallowsSecret(): void
    {
        $this->expectException(Exception::class);
        $controller = $this->getTestController(222);
        $controller->onlyForCrmUsers();
    }

    public function testItAllowsOther(): void
    {
        $controller = $this->getTestController(222);
        $this->assertEquals('All is good', $controller->forAll());
    }

    public function testItAllowsSecretForCrm(): void
    {
        $controller = $this->getTestController(-2);
        $this->assertEquals('All is good', $controller->onlyForCrmUsers());
    }

    public function testItAllowsOtherForCrm(): void
    {
        $controller = $this->getTestController(-2);
        $this->assertEquals('All is good', $controller->forAll());
    }
}