# Task manager



---

### Require

 - docker and docker-compose
---

Here use sail, you can 

### Set Up

Create `.env` file from the example
```
cp .env.example .env
```

Install packages
```
./run_after_clone.sh 
```

Up an application 
```
./vendor/bin/sail up
```

However, instead of typing `vendor/bin/sail` repeatedly, you can create a Shell alias:
```
alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'
```
Add this to a shell configuration file in your home directory, such as ~/.zshrc or ~/.bashrc, and then restart your shell.


Generate an application key
```
sail artisan key:generate
```

Generate an application key
```
sail artisan migrate
```

Generate an application key
```
sail artisan db:seed
```

Set secret key
```
sail artisan jwt:secret
```
---

### Routes

##### `routes/api.php`


Documentation: http://localhost/api/docs



