<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    
    <?php echo $__env->make('themes.school.partials.seo-head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $schema ?? ''; ?>


    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('site.css')); ?>?v=<?php echo e(@filemtime(public_path('site.css')) ?: '1'); ?>">
    <style>:root{ --primary: <?php echo e($primaryColor ?? '#0b2545'); ?>; }</style>

    
    <?php echo $themeHead ?? ''; ?>


    <?php echo $__env->yieldPushContent('head'); ?>
</head>
<body>
    <?php echo $header; ?>


    <main id="content"><?php echo $content; ?></main>

    <?php echo $footer; ?>


    <script src="<?php echo e(asset('site.js')); ?>?v=<?php echo e(@filemtime(public_path('site.js')) ?: '1'); ?>" defer></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH F:\school-website\resources\views/themes/school/layout.blade.php ENDPATH**/ ?>