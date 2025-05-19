<?php
namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    // ✅ Enregistrer manuellement le WebProfilerBundle pour dev et test
    public function registerBundles(): iterable
    {
        $contents = require $this->getProjectDir().'/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }
}
