<?php
// includes/footer.php
?>
<style>
/* Footer Styles */
.site-footer {
    background-color: #0f1a1fff;
    color: #ecf0f1;
    padding: 3rem 0 0;
    margin-top: auto; /* pushes footer to bottom in flex layout */
    font-size: 0.95rem;
}
.footer-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}
.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}
.footer-brand {
    margin-bottom: 1.5rem;
}
.footer-logo {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #fff;
    font-size: 1.5rem;
    font-weight: 700;
    text-decoration: none;
    margin-bottom: 0.5rem;
}
.footer-logo i { color: #e74c3c; }
.footer-tagline { color: #bdc3c7; font-size: 0.9rem; }
.footer-section { margin-bottom: 1.5rem; }
.footer-heading {
    color: #fff;
    font-size: 1.1rem;
    margin-bottom: 1rem;
    position: relative;
    padding-bottom: 0.5rem;
}
.footer-heading::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 40px;
    height: 2px;
    background-color: #e74c3c;
}
.footer-list { list-style: none; padding: 0; margin: 0; }
.footer-list li { margin-bottom: 0.5rem; }
.footer-list a {
    color: #bdc3c7;
    text-decoration: none;
    transition: color 0.3s;
}
.footer-list a:hover { color: #e74c3c; }
.social-links {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}
.social-links a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background-color: #34495e;
    color: #ecf0f1;
    transition: all 0.3s;
}
.social-links a:hover {
    background-color: #e74c3c;
    transform: translateY(-3px);
}
.contact-email {
    color: #bdc3c7;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.footer-bottom {
    border-top: 1px solid #34495e;
    padding: 1.5rem 0;
    text-align: center;
    color: #bdc3c7;
    font-size: 0.85rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.version { color: #7f8c8d; font-size: 0.8rem; }
#back-to-top {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: #e74c3c;
    color: white;
    border: none;
    cursor: pointer;
    display: none;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    transition: all 0.3s;
    z-index: 99;
}
#back-to-top:hover {
    background-color: #c0392b;
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}
@media (max-width: 768px) {
    .footer-content { grid-template-columns: 1fr; }
    .footer-bottom {
        flex-direction: column;
        gap: 0.5rem;
    }
    #back-to-top {
        width: 40px;
        height: 40px;
        bottom: 1rem;
        right: 1rem;
    }
}
</style>

<footer class="site-footer">
    <div class="footer-container">
        <div class="footer-content">
            <div class="footer-brand">
                <a href="index.php" class="footer-logo">
                    <i class="fas fa-heartbeat"></i>
                    <span>MedCare</span>
                </a>
                <p>Advanced healthcare management system designed to streamline medical practice operations and improve patient care.</p>
            </div>
            

            

           

            <div class="footer-section">
                <h3 class="footer-heading">Connect</h3>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
                <p class="contact-email">
                    <ul>
                        <li><i class="fas fa-map-marker-alt"></i> 123 Medical Drive, Colombo 1</li>
                        <li><i class="fas fa-phone"></i> (011) 456-7890</li>
                        <li><i class="fas fa-envelope"></i> info@medicare.com</li>
                    </ul>
                </p>
            </div>
        </div>

       <div class="footer-bottom">
            <p>&copy; <?php echo date("Y"); ?> Online Medical Record Management System. All rights reserved.</p> 
            
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button id="back-to-top" aria-label="Back to top">
    <i class="fas fa-arrow-up"></i>
</button>

<script>
    const backToTopButton = document.getElementById('back-to-top');
    window.addEventListener('scroll', () => {
        backToTopButton.style.display = window.pageYOffset > 300 ? 'block' : 'none';
    });
    backToTopButton.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
</script>

<?php
if (isset($conn)) { $conn->close(); }
?>
