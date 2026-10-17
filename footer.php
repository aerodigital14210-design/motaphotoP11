<?php
/**
 * Footer du thème Nathalie Mota.
 *
 * @package Nathalie_Mota
 */
?>

<footer class="site-footer">
    <nav class="footer-navigation" aria-label="Navigation du pied de page">
        <ul class="footer-menu">
            <li>
                <a href="<?php echo esc_url(home_url('/mentions-legales/')); ?>">
                    Mentions légales
                </a>
            </li>

            <li>
                <a href="<?php echo esc_url(home_url('/vie-privee/')); ?>">
                    Vie privée
                </a>
            </li>

            <li class="footer-copyright">
                Tous droits réservés
            </li>
        </ul>
    </nav>
</footer>

<?php wp_footer(); ?>
</body>
</html>