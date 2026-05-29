<!-- Footer -->
<footer class="tt-footer">
	<div class="container">
		<div class="row g-4">
			<!-- Brand -->
			<div class="col-lg-4">
				<h4 class="mb-3">Golden <span class="brand-accent" style="color:var(--tt-accent)">Pangolin</span></h4>
				<p class="mb-3">
					Your trusted local travel agency to Zimbabwe's most incredible destinations.
					We create authentic experiences that connect you with our beautiful homeland.
				</p>
				<div class="social-links">
					<a href="https://www.facebook.com/share/1Xpkia2ZNN/" target="_blank" rel="noopener" class="social-icon" aria-label="Facebook">
						<i class="fab fa-facebook-f"></i>
					</a>
					<a href="https://www.instagram.com/goldenpangolin2026?utm_source=qr&igsh=MXBxMnlzY2x1Z2tvaA==" target="_blank" rel="noopener" class="social-icon" aria-label="Instagram">
						<i class="fab fa-instagram"></i>
					</a>
					<a href="https://www.tiktok.com/@golden.pangolin?_r=1&_t=ZS-96d1rIKYQ80" target="_blank" rel="noopener" class="social-icon" aria-label="TikTok">
						<i class="fab fa-tiktok"></i>
					</a>
				</div>
			</div>

			<!-- Quick Links -->
			<div class="col-lg-2 col-md-6">
				<h5>Quick Links</h5>
				<a href="{{ url('/') }}" class="tt-footer-link">Home</a>
				<a href="{{ route('packages') }}" class="tt-footer-link">Destinations</a>
				<a href="{{ route('blog') }}" class="tt-footer-link">Blog</a>
				<a href="{{ route('about') }}" class="tt-footer-link">About</a>
				<a href="{{ route('contact') }}" class="tt-footer-link">Contact</a>
			</div>

			<!-- Services -->
			<div class="col-lg-3 col-md-6">
				<h5>Our Services</h5>
				<a href="#" class="tt-footer-link">Safari Packages</a>
				<a href="#" class="tt-footer-link">Zambezi Activities</a>
				<a href="#" class="tt-footer-link">Cultural Experiences</a>
				<a href="#" class="tt-footer-link">Adventure Packages</a>
				<a href="#" class="tt-footer-link">Group Packages</a>
			</div>

			<!-- Contact Info -->
			<div class="col-lg-3 col-md-6">
				<h5>Contact Info</h5>
				
				<div class="contact-row">
					<i class="fas fa-phone"></i>
					<a href="tel:+263777417507" style="color:inherit; text-decoration:none;">+263 777 417 507</a>
				</div>
				<div class="contact-row">
					<i class="fas fa-phone"></i>
					<a href="tel:+263783073063" style="color:inherit; text-decoration:none;">+263 783 073 063</a>
				</div>
				<div class="contact-row">
					<i class="fas fa-envelope"></i>
					<a href="mailto:info@goldenpangolin.com" style="color:inherit; text-decoration:none;">info@goldenpangolin.com</a>
				</div>
			</div>
		</div>

		<!-- Bottom bar -->
		<div class="tt-footer-bottom">
			<div class="row align-items-center">
				<div class="col-md-6">
					<p>&copy; {{ date('Y') }} Golden Pangolin. All rights reserved.</p>
				</div>
				<div class="col-md-6 text-md-end">
					<a href="#" class="me-3">Privacy Policy</a>
					<a href="#">Terms of Service</a>
				</div>
			</div>
		</div>
	</div>
</footer>
