<?php
/**
 * KosCare - Layout Footer
 */
defined('KOSCARE_APP') or define('KOSCARE_APP', true);
?>
<footer class="footer-custom py-4 mt-auto">
    <div class="container text-center text-md-start">
        <div class="row align-items-center gy-3">
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-2 justify-content-center justify-content-md-start">
                    <span class="brand-icon-sm">
                        <i class="bi bi-house-gear-fill"></i>
                    </span>
                    <span class="fw-bold text-charcoal">KosCare</span>
                    <span class="text-taupe fs-8">| Sistem Pelaporan Kerusakan Fasilitas Kos</span>
                </div>
                <p class="text-taupe fs-8 mb-0 mt-1">
                    Hunian Nyaman, Perbaikan Terpantau. Transparansi dan ketepatan penanganan fasilitas hunian bersama.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <small class="text-taupe">
                    &copy; <?php echo date('Y'); ?> KosCare. Seluruh hak cipta dilindungi.
                </small>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5.3 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom App JS -->
<script src="<?php echo BASE_URL; ?>/assets/js/app.js"></script>
</body>
</html>
