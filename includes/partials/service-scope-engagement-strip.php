<?php
/** Compact strip under deliverables — fills visual weight without empty columns. */
$engagement = [
    ['icon' => 'fa-calendar-check', 'title' => 'Clear timelines', 'desc' => 'Milestone-based plan with weekly check-ins from kickoff to launch.'],
    ['icon' => 'fa-chart-line', 'title' => 'Transparent reporting', 'desc' => 'Progress dashboards and executive summaries you can share with stakeholders.'],
    ['icon' => 'fa-handshake', 'title' => 'No lock-in', 'desc' => 'Flexible engagement — scale up, pause, or extend as your roadmap evolves.'],
];
?>
<div class="row g-3 svc-engagement-strip" data-nd-stagger>
    <?php foreach ($engagement as $item): ?>
    <div class="col-md-4">
        <div class="svc-engagement-card p-4 h-100">
            <i class="fas <?php echo $item['icon']; ?> text-primary mb-2"></i>
            <h3 class="h6 fw-bold text-dark mb-1"><?php echo htmlspecialchars($item['title']); ?></h3>
            <p class="text-muted small mb-0"><?php echo htmlspecialchars($item['desc']); ?></p>
        </div>
    </div>
    <?php endforeach; ?>
</div>
