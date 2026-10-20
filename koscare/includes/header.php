<?php
/**
 * KosCare - Layout Header
 */
defined('KOSCARE_APP') or define('KOSCARE_APP', true);
require_once __DIR__ . '/theme.php';
$appName = getAppName();
$appTagline = getAppTagline();
$pageTitle = $pageTitle ?? ($appName . ' — ' . $appTagline);
$activeNav = $activeNav ?? '';
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle); ?></title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom Theme CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    
    <!-- Dynamic Theme Styling From Database -->
    <?php renderThemeStyles(); ?>
</head>
<body class="bg-soft-lavender text-charcoal">
