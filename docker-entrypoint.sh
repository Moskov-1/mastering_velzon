#!/bin/sh
set -e

# 1. Cache the configuration and routes for better performance in production
# This reads the environment variables passed by ECS/Docker
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 2. Run migrations (Optional: usually better handled in CI/CD or as a separate task, 
# but for simple setups it can be done here. Be careful with auto-scaling!)
# php artisan migrate --force

# 3. Execute the container's main command (e.g., apache2-foreground)
echo "Starting application..."
exec "$@"
