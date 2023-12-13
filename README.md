#  Requirements: 
* php ^8.1
* composer
## OR
* Docker
* Docker-compose
# Installation
```bash
make install
```
## Docker installation
```bash
make docker-up-local
make docker-exec-php
make install
```

# Testing
You can run tests using next command 
## Without docker installation
```bash
make test
```

## With docker installation you should enter container:
```bash
make docker-exec-php
```
And run tests
```bash
make test
```

# Using script manual
To use script with your own manual file you should run this command
```bash
cat test.txt | php src/analyze.php -u 97 -t 30.1
```