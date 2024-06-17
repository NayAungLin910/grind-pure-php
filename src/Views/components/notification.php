<?php if (isset($_SESSION['noti'])) : ?>
    <div class="noti <?php echo $_SESSION['noti']['class'] ?>">
        <i class="bi bi-x-square noti-close-icon" onclick="closeNoti()"></i>
        <?php echo $_SESSION['noti']['message'] ?>
    </div>
    <?php unset($_SESSION['noti']) ?>
<?php endif; ?>