# Parallel Curl requests

## What is this project?
The purpose of this project is to show how multiple curl requests can be run in parallel to reduce latency of a request

## Installation

Deploy this project as a regular code igniter project.
```
server {
    listen   80;
    server_name localhost;

    root    /path/to/your/local/direcotry/
    index  index.php;
    error_log log/error.log;

    # set expiration of assets to MAX for caching
    location ~* .(ico|css|js|gif|jpe?g|png)(?[0-9]+)?$ {
           expires max;
           log_not_found off;
    }

    # main codeigniter rewrite rule
    location / {
        try_files $uri $uri/ /index.php;
    }
        
        # php parsing 
        location ~ .php$ {
            root           /path/to/your/local/direcotry/;
            try_files $uri =404;
            fastcgi_pass   unix:/tmp/php5-fpm.sock;
            fastcgi_index  index.php;
            fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
            include        fastcgi_params;
            fastcgi_buffer_size 128k;
            fastcgi_buffers 256 4k;
            fastcgi_busy_buffers_size 256k;
            fastcgi_temp_file_write_size 256k;
        }

}
```

## Usage

Visit ```localhost:<port number>```
and visit
```localhost:<port number>/welcome/index_async```

Execution time will be displayed at the bottom of each page. You will see that the execution time for the index_async is approximately half of index.

## Reasoning

The reason for the speed up using index async is because both curl get requests are being performed in parallel rather than sequentially.
