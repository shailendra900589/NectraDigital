<?php
/**
 * Admin Shared Head Partial
 */
$adminTitle = $adminTitle ?? 'Nectra Control Center';
$adminCtx = function_exists('admin_layout_context') ? admin_layout_context() : ['assets' => '../assets/'];
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($adminTitle); ?> — Nectra Digital</title>
    
    <!-- Modern Typography: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Master Admin CSS Tokens -->
    <link rel="stylesheet" href="<?php echo htmlspecialchars($adminCtx['assets']); ?>css/growth-admin.css?v=6">
</head>
<body class="ge-admin">
