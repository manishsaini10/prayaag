
<?php ($seo = $seo ?? []); ?>
<title><?php echo e($seo['title'] ?? ($title ?? '')); ?></title>
<meta name="description" content="<?php echo e($seo['description'] ?? ''); ?>">
<?php if(!empty($seo['keywords'])): ?><meta name="keywords" content="<?php echo e($seo['keywords']); ?>"><?php endif; ?>
<meta name="robots" content="<?php echo e($seo['robots'] ?? 'index, follow'); ?>">
<link rel="canonical" href="<?php echo e($seo['canonical'] ?? url()->current()); ?>">


<meta property="og:type" content="<?php echo e($seo['og_type'] ?? 'website'); ?>">
<meta property="og:title" content="<?php echo e($seo['og_title'] ?? ($seo['title'] ?? '')); ?>">
<meta property="og:description" content="<?php echo e($seo['og_description'] ?? ($seo['description'] ?? '')); ?>">
<meta property="og:url" content="<?php echo e($seo['og_url'] ?? ($seo['canonical'] ?? url()->current())); ?>">
<?php if(!empty($seo['site_name'])): ?><meta property="og:site_name" content="<?php echo e($seo['site_name']); ?>"><?php endif; ?>
<meta property="og:locale" content="<?php echo e($seo['locale'] ?? 'en_IN'); ?>">
<?php if(!empty($seo['og_image'])): ?>
<meta property="og:image" content="<?php echo e($seo['og_image']); ?>">
<meta property="og:image:alt" content="<?php echo e($seo['og_title'] ?? ($seo['title'] ?? '')); ?>">
<?php endif; ?>


<meta name="twitter:card" content="<?php echo e($seo['twitter_card'] ?? 'summary'); ?>">
<meta name="twitter:title" content="<?php echo e($seo['twitter_title'] ?? ($seo['title'] ?? '')); ?>">
<meta name="twitter:description" content="<?php echo e($seo['twitter_description'] ?? ($seo['description'] ?? '')); ?>">
<?php if(!empty($seo['twitter_image'])): ?><meta name="twitter:image" content="<?php echo e($seo['twitter_image']); ?>"><?php endif; ?>
<?php if(!empty($seo['twitter_site'])): ?><meta name="twitter:site" content="<?php echo e($seo['twitter_site']); ?>"><?php endif; ?>
<?php /**PATH F:\school-website\resources\views/themes/school/partials/seo-head.blade.php ENDPATH**/ ?>