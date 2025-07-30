</div>
<footer>
    <div class="footer-content">
        <p>&copy; <?php echo date('Y'); ?> Modern Trends Shoe Shop. All rights reserved.</p>
        <div class="footer-links">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
        </div>
    </div>
</footer>

<!-- Modal Structure -->
<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <div class="modal-body"></div>
    </div>
</div>

<script>
// Modal functionality
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modal');
    const closeModal = document.querySelector('.close-modal');

    closeModal.addEventListener('click', () => {
        modal.style.display = 'none';
    });
});