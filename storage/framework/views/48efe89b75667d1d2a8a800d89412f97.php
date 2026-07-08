<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f4f5f7; display: flex; min-height: 100vh; align-items: center; justify-content: center; margin: 0; }
        .card { background: #fff; padding: 2rem; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.1); width: 340px; box-sizing: border-box; }
        h1 { font-size: 1.25rem; margin: 0 0 1rem; }
        label { display: block; font-size: .85rem; margin: .75rem 0 .25rem; color: #444; }
        input { width: 100%; padding: .55rem .6rem; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        button { margin-top: 1.25rem; width: 100%; padding: .6rem; background: #1d4ed8; color: #fff; border: 0; border-radius: 6px; font-size: 1rem; cursor: pointer; }
        .err { background: #fee2e2; color: #991b1b; padding: .5rem .6rem; border-radius: 6px; font-size: .85rem; margin-bottom: .5rem; }
        .row { display: flex; align-items: center; gap: .4rem; margin-top: .5rem; font-size: .85rem; color: #444; }
        .row input { width: auto; }
    </style>
</head>
<body>
    <form class="card" method="POST" action="/login">
        <?php echo csrf_field(); ?>
        <h1>Sign in</h1>
        <?php if($errors->any()): ?>
            <div class="err"><?php echo e($errors->first()); ?></div>
        <?php endif; ?>
        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="<?php echo e(old('email')); ?>" required autofocus>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" required>
        <div class="row">
            <input type="checkbox" name="remember" id="remember">
            <label for="remember" style="margin:0">Remember me</label>
        </div>
        <button type="submit">Sign in</button>
    </form>
</body>
</html>
<?php /**PATH F:\school-website\resources\views/auth/login.blade.php ENDPATH**/ ?>