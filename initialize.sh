#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to check command existence
check_command() {
    if ! command -v $1 &> /dev/null; then
        echo -e "${RED}Error: $1 is not installed${NC}"
        return 1
    fi
    return 0
}

# Function to create directory
create_dir() {
    if [ ! -d "$1" ]; then
        mkdir -p "$1"
        echo -e "${GREEN}Created directory: $1${NC}"
    fi
}

# Function to set permissions
set_permissions() {
    chmod -R $2 "$1"
    echo -e "${GREEN}Set permissions $2 for: $1${NC}"
}

echo -e "${BLUE}=== HomeCare Website Initialization ===${NC}\n"

# Check required commands
echo -e "${YELLOW}Checking requirements...${NC}"
check_command php || exit 1
check_command chmod || exit 1

# Check if in correct directory
if [ ! -f "init.php" ]; then
    echo -e "${RED}Error: init.php not found. Make sure you're in the homecare directory.${NC}"
    exit 1
fi

# Check web server
echo -e "\n${YELLOW}Checking web server...${NC}"
if check_command apache2 || check_command httpd; then
    echo -e "${GREEN}Apache web server found${NC}"
else
    echo -e "${YELLOW}Warning: Apache web server not found. Please install and configure manually.${NC}"
fi

# Create required directories
echo -e "\n${YELLOW}Creating directories...${NC}"
create_dir "logs"
create_dir "uploads"
create_dir "uploads/documents"
create_dir "uploads/images"
create_dir "uploads/temp"

# Set permissions
echo -e "\n${YELLOW}Setting permissions...${NC}"
set_permissions "logs" 755
set_permissions "uploads" 755
set_permissions ".htaccess" 644
set_permissions "includes/config.php" 644

# Create required files
touch "logs/error.log"
touch "logs/access.log"
chmod 644 logs/*.log

# Run PHP initialization
echo -e "\n${YELLOW}Running PHP initialization...${NC}"
php init.php

# Check exit status
if [ $? -eq 0 ]; then
    echo -e "\n${GREEN}=== Initialization completed successfully! ===${NC}"
    
    # Get server URL
    SERVER_URL="http://localhost"
    if [ -n "$XAMPP_ROOT" ]; then
        SERVER_URL="$SERVER_URL/homecare"
    fi
    
    echo -e "\n${YELLOW}Next steps:${NC}"
    echo "1. Update includes/config.php with your settings"
    echo "2. Configure your web server (if not using XAMPP)"
    echo "3. Visit $SERVER_URL in your browser"
    echo -e "4. Check ${BLUE}logs/error.log${NC} for any issues"
    
    echo -e "\n${YELLOW}Testing website accessibility...${NC}"
    if curl -s --head "$SERVER_URL" | grep "200 OK" > /dev/null; then
        echo -e "${GREEN}Website is accessible!${NC}"
    else
        echo -e "${RED}Warning: Website not accessible. Please check your web server configuration.${NC}"
    fi
    
    echo -e "\n${BLUE}For detailed instructions, see README-init.txt${NC}"
else
    echo -e "\n${RED}Initialization failed. Please check the errors above.${NC}"
    echo -e "Review logs/error.log for details."
fi
