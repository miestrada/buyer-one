# Setup Heroku

## Install heroku client
````
Ubuntu WSL
curl https://cli-assets.heroku.com/install.sh | sh 
heroku login
````

````
Ubuntu
sudo snap install --classic heroku
heroku login
````

## Setup new app
````
heroku login
heroku create buyer-one --region=eu
heroku config:set APP_ENV=prod
````

# Create /Procfile
````
web: $(composer config bin-dir)/heroku-php-apache2 public/
````

# Push
````
git push heroku heroku:master
````

# Add webpack
````
heroku buildpacks:add --index 2 heroku/nodejs
Edit package.json copy scripts/build and rename it to heroku-postbuild
````