<?php if ($page > 0 && count($pagiResource) > 0) : ?>
    <div class="pagi-nav">

        <a href="?page=<?= $page - 1 ?>" class="btn square btn-small <?php if ($page <= 1) echo "disabled" ?>"><i class="bi bi-chevron-left"></i> </a>

        <a href="?page=<?= $page + 1 ?>" class="btn square btn-small <?php if ($page >= $totalPages) echo "disabled" ?>"><i class="bi bi-chevron-right"></i></a>

        <div>Page <?= $page ?> of <?= $totalPages ?></div>
    </div>
<?php endif; ?>