###################
What is this project?
###################
The purpose of this project is to show how multiple curl requests can be run in parallel to reduce latency of a request


########
Installation
########
Deploy this project as a regular code igniter project.
```
server {
        server_name domain.tld;
 
        root /var/www/codeignitor;
        index index.html index.php;
 
        # set expiration of assets to MAX for caching
        location ~* \.(ico|css|js|gif|jpe?g|png)(\?[0-9]+)?$ {
                expires max;
                log_not_found off;
        }
 
        location / {
                # Check if a file or directory index file exists, else route it to index.php.
                try_files $uri $uri/ /index.php;
        }
 
        location ~* \.php$ {
                fastcgi_pass 127.0.0.1:9000;
                include fastcgi.conf;
        }
}
```

#######
Usage
#######
Visit localhost:<port number>
and visit
localhost:<port number>/welcome/index_async

Execution time will be displayed at the bottom and you will see that the execution time for the index_async is approximately half of index.


######
Reasoning
######

The reason for the speed up using index async is because both curl get requests are being performed in parallel rather than sequentially.
