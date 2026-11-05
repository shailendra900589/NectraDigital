<?php
$steps = [
    [
        'icon' => 'fa-magnifying-glass-chart',
        'title' => 'Discover',
        'desc' => 'Goals, users, requirements, and technical feasibility.',
    ],
    [
        'icon' => 'fa-sitemap',
        'title' => 'Plan',
        'desc' => 'Architecture, scope, stack, and delivery roadmap.',
    ],
    [
        'icon' => 'fa-pen-ruler',
        'title' => 'Design',
        'desc' => 'Information architecture, wireframes, and UI/UX.',
    ],
    [
        'icon' => 'fa-code',
        'title' => 'Develop',
        'desc' => 'Frontend, backend, APIs, and integrations.',
    ],
    [
        'icon' => 'fa-clipboard-check',
        'title' => 'Test',
        'desc' => 'Functional, responsive, performance, and security QA.',
    ],
    [
        'icon' => 'fa-rocket',
        'title' => 'Launch',
        'desc' => 'Production deploy, monitoring, and handover.',
    ],
    [
        'icon' => 'fa-chart-line',
        'title' => 'Scale',
        'desc' => 'Optimization, maintenance, and future releases.',
    ],
];
?>
<div class="nd-process-timeline nd-process-timeline--7" data-nd-stagger role="list" aria-label="How we work">
    <?php foreach ($steps as $i => $s): ?>
    <div class="nd-process-step nd-process-step--pro nd-reveal" role="listitem">
        <?php if ($i < count($steps) - 1): ?>
        <span class="nd-process-connector" aria-hidden="true"></span>
        <?php endif; ?>
        <div class="nd-process-step-icon" aria-hidden="true">
            <i class="fas <?php echo htmlspecialchars($s['icon']); ?>"></i>
        </div>
        <h3 class="nd-process-step-title"><?php echo htmlspecialchars($s['title']); ?></h3>
        <p class="nd-process-step-desc"><?php echo htmlspecialchars($s['desc']); ?></p>
    </div>
    <?php endforeach; ?>
</div>
