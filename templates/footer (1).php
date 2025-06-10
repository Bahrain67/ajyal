    </main>
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-section">
                    <h3>أجيال كاش</h3>
                    <p>منصة أجيال كاش الرائدة التي تساعدك على تحقيق أرباح مستدامة.</p>
                </div>
                <div class="footer-section">
                    <h3>روابط سريعة</h3>
                    <a href="index.php?action=home">الرئيسية</a>
                    <?php if (!isset($_SESSION['user_id'])) { ?>
                        <a href="index.php?action=register">تسجيل</a>
                        <a href="index.php?action=login">تسجيل الدخول</a>
                    <?php } else { ?>
                        <a href="index.php?action=profile">الملف الشخصي</a>
                        <a href="index.php?action=deposit">إيداع</a>
                        <a href="index.php?action=bonds">شراء السندات</a>
                        <a href="index.php?action=logout">تسجيل الخروج</a>
                    <?php } ?>
                </div>
                <div class="footer-section">
                    <h3>تواصلوا معنا</h3>
                    <a href="mailto:support@ajyalcash.com"><i class="fas fa-envelope"></i> support@ajyalcash.com</a>
                </div>
            </div>
            <p style="text-align: center; opacity: 0.8;"> أجيال كاش© جميع الحقوق محفوظة <?php echo date('Y'); ?></p>
        </div>
    </footer>
    <script>
        // Mobile Menu Toggle
        const mobileMenu = document.querySelector('.mobile-menu');
        const navLinks = document.querySelector('.nav-links');
        mobileMenu.addEventListener('click', () => {
            navLinks.style.display = navLinks.style.display === 'flex' ? 'none' : 'flex';
            navLinks.style.flexDirection = 'column';
            navLinks.style.position = 'absolute';
            navLinks.style.top = '70px';
            navLinks.style.right = '0';
            navLinks.style.background = 'rgba(255, 255, 255, 0.95)';
            navLinks.style.padding = '1rem';
            navLinks.style.width = '100%';
            navLinks.style.boxShadow = '0 5px 20px rgba(0,0,0,0.1)';
        });

        // Tabs Functionality
        function showTab(tabId) {
            const tabs = document.querySelectorAll('.tab-content');
            const buttons = document.querySelectorAll('.tab-button');
            tabs.forEach(tab => tab.classList.remove('active'));
            buttons.forEach(button => button.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            const activeButton = Array.from(buttons).find(button => button.getAttribute('onclick') === `showTab('${tabId}')`);
            if (activeButton) activeButton.classList.add('active');
        }

        // Ensure tabs work on page load
        document.addEventListener('DOMContentLoaded', () => {
            const defaultTab = document.querySelector('.tab-content');
            if (defaultTab) {
                showTab(defaultTab.id);
            }
        });
    </script>
</body>
</html>