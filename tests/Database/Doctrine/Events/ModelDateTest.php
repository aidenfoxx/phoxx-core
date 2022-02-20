<?php declare(strict_types=1);

namespace Phoxx\Core\Database\Doctrine\Events {
    function time()
    {
        return 12345;
    }
}

namespace Phoxx\Core\Tests\Database\Doctrine\Events {
    use Doctrine\ORM\Event\LifecycleEventArgs;

    use Phoxx\Core\Database\Doctrine\Events\ModelDate;
    use Phoxx\Core\Database\Model;
    
    use PHPUnit\Framework\TestCase;
    
    final class ModelDateTest extends TestCase
    {
      public function testShouldSetDateOnPrePersist()
      {
        $model = $this->getMockForAbstractClass(Model::class);
        $event = $this->createMock(LifecycleEventArgs::class);
        $event->expects($this->once())->method('getEntity')->willReturn($model);
    
        $modelDate = new ModelDate();
        $modelDate->prePersist($event);
    
        $this->assertSame(12345, $model->getDateCreated());
        $this->assertSame(12345, $model->getDateUpdated());
      }

      public function testShouldSetDateOnPreUpdate()
      {
        $model = $this->getMockForAbstractClass(Model::class);
        $event = $this->createMock(LifecycleEventArgs::class);
        $event->expects($this->once())->method('getEntity')->willReturn($model);

        $modelDate = new ModelDate();
        $modelDate->preUpdate($event);
    
        $this->assertSame(0, $model->getDateCreated());
        $this->assertSame(12345, $model->getDateUpdated());
      }
    }
}

