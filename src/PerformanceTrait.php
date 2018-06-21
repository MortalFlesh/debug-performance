<?php declare(strict_types=1);

namespace MF\Debug\Performance;

use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

trait PerformanceTrait
{
    /** @var Stopwatch|null */
    private $stopWatch;

    private function watch(string $name, callable $callback, string $format = 'raw', bool $return = false): string
    {
        $this->startWatch($watch = $name . '__' . time());
        $callback();

        return $this->stopWatch($watch, $format, $return);
    }

    private function startWatch(string $name): void
    {
        if (!$this->stopWatch) {
            $this->stopWatch = new Stopwatch(true);
        }

        $this->stopWatch->start($name);
    }

    private function stopWatch(string $name, string $format = 'raw', bool $return = false): string
    {
        $event = $this->stopWatch->stop($name);
        $result = $this->formatWatchEvent($name, $event, $format);

        if (!$return) {
            echo $result;
        }

        return $result;
    }

    private function formatWatchEvent(string $name, StopwatchEvent $event, string $format): string
    {
        [$name] = explode('__', $name, 2);
        [$memory, $duration] = [$event->getMemory() / 1024 / 1024, $event->getDuration()];

        return $format === 'raw'
            ? sprintf("\n%s:\t%s MB,\t%s ms", $name, $memory, $duration)
            : sprintf("\n<fg=cyan;options=bold>%s</>: <fg=magenta>%s</> MB, <fg=magenta>%s</> s", $name, $memory,
                $duration);
    }
}
