git pull
rvm use ruby-2.2.3
export RAILS_ENV=development
bundle install --deployment --without production test
bundle exec rake assets:precompile db:migrate RAILS_ENV=development
passenger-config restart-app $(pwd)
#sudo /etc/init.d/apache2 restart
