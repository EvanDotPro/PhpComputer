<?php
namespace Edp\PhpComputer\Util;

trait SubjectTrait
{
    protected $observers = [];

    public function attach(ObserverInterface $observer)
    {
        $this->observers[] = $observer;
        return $this;
    }

    public function detach(ObserverInterface $observer)
    {
        foreach ($this->observers as $i => $attachedObserver) {
            if ($observer === $attachedObserver) {
                unset($this->observers[$i]);

                return true;
            }
        }

        return false;
    }

    protected function notify()
    {
        foreach ($this->observers as $observer) {
            $observer->update();
        }
    }
}
