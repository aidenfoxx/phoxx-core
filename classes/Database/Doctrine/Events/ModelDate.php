<?php

namespace Phoxx\Core\Database\Doctrine\Events;

use Doctrine\ORM\Event\LifecycleEventArgs;

use Phoxx\Core\Database\Model;

class ModelDate
{
  public function prePersist(LifecycleEventArgs $eventArgs): void
  {
    if (($model = $eventArgs->getEntity()) instanceof Model) {
      $model->setDateUpdated(time());
      $model->setDateCreated(time());
    }
  }

  public function preUpdate(LifecycleEventArgs $eventArgs): void
  {
    if (($model = $eventArgs->getEntity()) instanceof Model) {
      $model->setDateUpdated(time());
    }
  }
}
