<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerMgxjy9S\App_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerMgxjy9S/App_KernelDevDebugContainer.php') {
    touch(__DIR__.'/ContainerMgxjy9S.legacy');

    return;
}

if (!\class_exists(App_KernelDevDebugContainer::class, false)) {
    \class_alias(\ContainerMgxjy9S\App_KernelDevDebugContainer::class, App_KernelDevDebugContainer::class, false);
}

return new \ContainerMgxjy9S\App_KernelDevDebugContainer([
    'container.build_hash' => 'Mgxjy9S',
    'container.build_id' => '19281f48',
    'container.build_time' => 1717772242,
    'container.runtime_mode' => \in_array(\PHP_SAPI, ['cli', 'phpdbg', 'embed'], true) ? 'web=0' : 'web=1',
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerMgxjy9S');
