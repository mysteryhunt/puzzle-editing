# Cron doesn't inherit env vars the container was started with so we save
# our environment to .env
env > /var/www/html/.env

# Start cron and tail all the cron logs
touch /var/log/cron.log
touch /var/log/cron-email.out
touch /var/log/cron-reminder.out
cron && tail -F /var/log/cron*
