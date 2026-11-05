<?php
require_once dirname(__DIR__) . '/nav-data.php';
$columns = nectra_mega_menu_columns();
?>
<div class="nd-mega-menu" aria-label="Services mega menu">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($columns as $col): ?>
            <div class="col-lg-3 col-md-6">
                <div class="nd-mega-col-title">
                    <i class="fas <?php echo htmlspecialchars($col['icon']); ?>"></i>
                    <?php echo htmlspecialchars($col['title']); ?>
                </div>
                <div class="d-flex flex-column">
                    <?php foreach ($col['items'] as $item): ?>
                    <a href="<?php echo htmlspecialchars(nectra_nav_path($item['url'])); ?>" class="nd-mega-item">
                        <div class="nd-mega-item-icon"><i class="fas fa-angle-right"></i></div>
                        <div>
                            <div class="nd-mega-item-title"><?php echo htmlspecialchars($item['title']); ?></div>
                            <div class="nd-mega-item-desc"><?php echo htmlspecialchars($item['desc']); ?></div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4 pt-3 border-top">
            <a href="<?php echo htmlspecialchars(nectra_nav_path('/services')); ?>" class="btn btn-nectra-secondary btn-sm">
                Explore All Services <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</div>
