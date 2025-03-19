HomeCare Website - Initialization Instructions
============================================

There are two ways to initialize the HomeCare website:

1. Using the Shell Script (Recommended)
-------------------------------------
a) Open terminal/command prompt
b) Navigate to the homecare directory:
   cd path/to/homecare

c) Make the script executable (Unix/Linux/Mac):
   chmod +x initialize.sh

d) Run the script:
   ./initialize.sh
   
   For Windows users:
   bash initialize.sh
   
   Without bash:
   sh initialize.sh

2. Using PHP Directly
-------------------
a) Open terminal/command prompt
b) Navigate to the homecare directory:
   cd path/to/homecare

c) Run the PHP script:
   php init.php

What the Initialization Does
--------------------------
1. Creates required directories
2. Sets proper file permissions
3. Initializes log files
4. Secures sensitive directories
5. Verifies critical files
6. Tests write permissions

Troubleshooting
--------------
1. Permission Denied
   - Try running with sudo (Unix/Linux):
     sudo ./initialize.sh
   - Check folder ownership
   - Verify PHP has write permissions

2. PHP Not Found
   - Ensure PHP is installed
   - Add PHP to system PATH
   - Use full PHP path:
     /usr/bin/php init.php

3. Script Not Found
   - Verify you're in the correct directory
   - Check file permissions
   - Try using full path to script

4. Failed Operations
   - Check error messages in terminal
   - Verify PHP has required extensions
   - Check disk space
   - Review logs after initialization

After Initialization
------------------
1. Update includes/config.php with your settings
2. Configure your web server (Apache/Nginx)
3. Test all forms and features
4. Check log files for any errors
5. Update sitemap.xml with your domain
6. Configure email settings

Security Notes
-------------
1. Keep config.php secure
2. Update default passwords
3. Set proper file permissions
4. Enable HTTPS in production
5. Regularly check log files

For additional help, refer to README.md or contact support.
