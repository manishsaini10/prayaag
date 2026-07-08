
<?php ($links = ($footerMenu ?? collect())->isNotEmpty() ? $footerMenu : ($menu ?? collect())); ?>
<footer class="site-foot <?php echo e($fVariant ?? 'fv-01'); ?>">
    <div class="container">
        <div class="foot-grid">
            
            <div class="foot-col">
                <div class="brand-text" style="font-family:var(--font-head);font-weight:700;font-size:1.5rem;margin-bottom:.8rem"><?php echo e($siteName); ?></div>
                <p style="font-size:var(--fs-sm);max-width:34ch"><?php echo e($about ?: $tagline); ?></p>
                <div class="foot-social">
                    <?php $__currentLoopData = $social; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $net => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(!empty($url)): ?><a href="<?php echo e($url); ?>" target="_blank" rel="noopener" aria-label="<?php echo e($net); ?>"><?php echo $icon($net); ?></a><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            
            <div class="foot-col">
                <h4>Quick Links</h4>
                <ul>
                    <?php $__empty_1 = true; $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li><a href="<?php echo e($item->resolveUrl()); ?>"><?php echo e($item->label); ?></a></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li><a href="<?php echo e(url('/')); ?>">Home</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            
            <div class="foot-col">
                <h4>Get in Touch</h4>
                <div class="foot-contact">
                    <?php if(!empty($address)): ?><span><?php echo $icon('pin'); ?><span><?php echo e($address); ?></span></span><?php endif; ?>
                    <?php if(!empty($phone)): ?><span><?php echo $icon('phone'); ?><a href="tel:<?php echo e(preg_replace('/[^+0-9]/', '', $phone)); ?>"><?php echo e($phone); ?></a></span><?php endif; ?>
                    <?php if(!empty($email)): ?><span><?php echo $icon('mail'); ?><a href="mailto:<?php echo e($email); ?>"><?php echo e($email); ?></a></span><?php endif; ?>
                </div>
            </div>

            
            <div class="foot-col">
                <h4>Stay Updated</h4>
                <form class="foot-news" method="POST" action="<?php echo e(url('/subscribe')); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="text" name="website" tabindex="-1" autocomplete="off" style="display:none" aria-hidden="true">
                    <input type="email" name="email" placeholder="Your email address" required>
                    <button type="submit" class="btn btn-gold" style="width:100%;justify-content:center">Subscribe</button>
                </form>
                <?php if(!empty($mapEmbed)): ?>
                    <div class="foot-map"><?php echo $mapEmbed; ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="foot-bottom">
        <div class="container" style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:.6rem;width:100%">
            <span>&copy; <?php echo e(date('Y')); ?> <?php echo e($siteName); ?>. All rights reserved.</span>
            <span>Managed via the school CMS.</span>
        </div>
    </div>
</footer>
<?php /**PATH F:\school-website\resources\views/themes/school/partials/footer.blade.php ENDPATH**/ ?>