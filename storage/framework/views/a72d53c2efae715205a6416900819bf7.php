
<?php ($menu = $menu ?? collect()); ?>
<?php ($cur = rtrim(url()->current(), '/')); ?>
<?php ($hVariant = $hVariant ?? 'hv-01'); ?>

<div class="site-header <?php echo e($hVariant); ?> <?php echo e(($hSticky ?? true) ? 'is-sticky' : 'no-sticky'); ?> <?php echo e(($hGlass ?? false) ? 'is-glass' : ''); ?> <?php echo e(($hTransparent ?? false) ? 'is-transparent' : ''); ?>">

    <?php if($hTopbar ?? true): ?>
    <div class="site-top">
        <div class="container">
            <div class="top-info">
                <?php $__currentLoopData = $topNotes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span><?php echo $icon($i === 0 ? 'shield' : 'building'); ?><?php echo e($note); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="top-right">
                <?php if(($hSocial ?? true) && collect($social)->filter()->isNotEmpty()): ?>
                    <div class="top-social">
                        <?php $__currentLoopData = $social; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $net => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(!empty($url)): ?><a href="<?php echo e($url); ?>" target="_blank" rel="noopener" aria-label="<?php echo e($net); ?>"><?php echo $icon($net); ?></a><?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php if($hLogin ?? true): ?><span class="top-div"></span><?php endif; ?>
                <?php endif; ?>
                <?php if($hLogin ?? true): ?>
                    <?php $__currentLoopData = $topLinks ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(($l['style'] ?? 'link') === 'btn'): ?>
                            <a class="top-pay" href="<?php echo e($l['url']); ?>" target="_blank" rel="noopener"><?php echo $icon($l['ic'] ?? 'card'); ?><span><?php echo e($l['label']); ?></span></a>
                        <?php else: ?>
                            <a class="top-link" href="<?php echo e($l['url']); ?>" target="_blank" rel="noopener"><?php echo $icon($l['ic'] ?? ''); ?><span><?php echo e($l['label']); ?></span></a>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <header class="site-head">
        <div class="container">
            <a href="<?php echo e(url('/')); ?>" class="brand" aria-label="<?php echo e($siteName); ?> — Home">
                <?php if(!empty($logo)): ?><img src="<?php echo e($logo); ?>" alt="<?php echo e($siteName); ?>"><?php endif; ?>
                <span class="brand-text"><?php echo e($siteName); ?><?php if(!empty($tagline)): ?><small><?php echo e(\Illuminate\Support\Str::limit($tagline, 40)); ?></small><?php endif; ?></span>
            </a>

            <div class="head-actions">
                <?php if(($hCta ?? true) && !empty($ctaLabel)): ?>
                    <a href="<?php echo e($ctaUrl); ?>" class="btn btn-enquire"><?php echo $icon('chat'); ?><?php echo e($ctaLabel); ?></a>
                <?php endif; ?>
                <button class="menu-toggle" aria-label="Open menu"><span></span><span></span><span></span></button>
            </div>

            <nav aria-label="Primary">
                <ul class="nav">
                    <?php $__empty_1 = true; $__currentLoopData = $menu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php ($u = rtrim($item->resolveUrl(), '/')); ?>
                        <?php ($kids = $item->children); ?>
                        <li class="<?php echo e($kids->count() ? 'has-children' : ''); ?>">
                            <a href="<?php echo e($item->resolveUrl()); ?>" class="<?php echo e($u === $cur ? 'is-active' : ''); ?>" <?php if($item->target): ?>target="<?php echo e($item->target); ?>"<?php endif; ?>><?php echo e($item->label); ?></a>
                            <?php if($kids->count()): ?>
                                <ul class="submenu <?php echo e($kids->count() >= 5 ? 'submenu--mega' : ''); ?>">
                                    <?php $__currentLoopData = $kids; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><a href="<?php echo e($child->resolveUrl()); ?>" <?php if($child->target): ?>target="<?php echo e($child->target); ?>"<?php endif; ?>><?php echo e($child->label); ?></a></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li><a href="<?php echo e(url('/')); ?>" class="is-active">Home</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
</div>


<div class="drawer-backdrop"></div>
<aside class="drawer" aria-label="Mobile menu">
    <div class="drawer-head">
        <span class="brand-text" style="font-size:1.15rem;color:var(--navy)"><?php echo e($siteName); ?></span>
        <button class="drawer-close" aria-label="Close menu">&times;</button>
    </div>
    <nav>
        <ul>
            <?php $__empty_1 = true; $__currentLoopData = $menu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <li class="<?php echo e($item->children->count() ? 'has-children' : ''); ?>">
                    <a href="<?php echo e($item->resolveUrl()); ?>"><?php echo e($item->label); ?></a>
                    <?php if($item->children->count()): ?>
                        <ul class="submenu" style="display:none;list-style:none;padding:0;margin:0">
                            <?php $__currentLoopData = $item->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><a href="<?php echo e($child->resolveUrl()); ?>"><?php echo e($child->label); ?></a></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <li><a href="<?php echo e(url('/')); ?>">Home</a></li>
            <?php endif; ?>
        </ul>
        <?php $__currentLoopData = $topLinks ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e($l['url']); ?>" target="_blank" rel="noopener" style="display:block;padding:.75rem .4rem;border-bottom:1px solid var(--line-soft);font-weight:600;color:var(--ink)"><?php echo e($l['label']); ?></a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php if(!empty($ctaLabel)): ?><a href="<?php echo e($ctaUrl); ?>" class="btn btn-enquire" style="margin-top:1.2rem;width:100%;justify-content:center"><?php echo e($ctaLabel); ?></a><?php endif; ?>
    </nav>
</aside>
<?php /**PATH F:\school-website\resources\views/themes/school/partials/header.blade.php ENDPATH**/ ?>