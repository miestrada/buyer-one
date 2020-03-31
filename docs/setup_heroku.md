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