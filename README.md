# HomeCare Website

A professional healthcare service provider website built with PHP, JavaScript, and modern web technologies.

## Features

- Responsive design for all devices
- Service booking system
- Contact form with email notifications
- SEO-friendly structure
- Secure form handling
- Accessibility compliant
- Performance optimized

## Directory Structure

```
homecare/
├── css/
│   └── styles.css
├── js/
│   ├── script.js
│   └── booking.js
├── images/
├── includes/
│   ├── config.php
│   ├── header.php
│   └── footer.php
├── logs/
├── uploads/
├── index.php
├── about.php
├── services.php
├── service-details.php
├── pricing.php
├── contact.php
├── booking.php
├── process_contact.php
├── process_booking.php
├── 404.php
├── robots.txt
├── sitemap.xml
└── .htaccess
```

## Installation

1. Clone the repository to your web server:
   ```bash
   git clone https://github.com/yourusername/homecare.git
   ```

2. Configure your web server (Apache) to point to the project directory

3. Update configuration files:
   - Update domain in `includes/config.php`
   - Set reCAPTCHA keys
   - Configure email settings
   - Update social media links

4. Set proper permissions:
   ```bash
   chmod 755 homecare
   chmod 644 homecare/.htaccess
   chmod -R 755 homecare/images
   chmod -R 755 homecare/uploads
   chmod -R 755 homecare/logs
   ```

## Development Setup

1. Enable debug mode in `includes/config.php`:
   ```php
   define('DEBUG_MODE', true);
   ```

2. Install development dependencies (if any):
   ```bash
   npm install
   ```

3. Configure local environment variables

## Production Deployment

1. Update configuration:
   - Set `DEBUG_MODE` to false
   - Update domain name in sitemap.xml
   - Configure proper email addresses
   - Set secure database credentials
   - Enable HTTPS redirects in .htaccess

2. Security checklist:
   - Enable HTTPS
   - Set secure file permissions
   - Configure error logging
   - Enable HSTS
   - Update robots.txt

3. Performance optimization:
   - Enable caching in .htaccess
   - Compress static assets
   - Optimize images
   - Minify CSS/JS files

## Maintenance

### Regular Tasks
- Check error logs regularly
- Monitor form submissions
- Update content as needed
- Verify all forms are working
- Test booking system
- Check email notifications

### Security Updates
- Keep PHP version updated
- Monitor file permissions
- Check for unauthorized access
- Review error logs for security issues
- Update SSL certificates

### Backup Process
1. Backup all files:
   ```bash
   tar -czf homecare_backup_$(date +%Y%m%d).tar.gz homecare/
   ```

2. Store backups securely off-site

## Support

For technical support or questions, contact:
- Email: support@homecare.com
- Phone: (555) 123-4567

## License

[Your License Type] - See LICENSE file for details

## Credits

- Design: [Your Company/Name]
- Development: [Your Company/Name]
- Icons: Font Awesome
- Photos: [Source Attribution]

## Version History

- 1.0.0 (2025-03-19)
  - Initial release
  - Basic features implemented
  - Responsive design completed

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## Known Issues

- Document any known issues here
- Include workarounds if available
- Link to issue tracker if public

---

© 2025 HomeCare. All Rights Reserved.
