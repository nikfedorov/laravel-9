## Installation

### Clone

Clone the repository:

```
git clone git@github.com:nikfedorov/laravel-9.git
```

### Build

Make sure you have docker installed and running.

To pull project images and setup containers:

```shell
make up
```

Make sure to keep Laravel Horizon running. If in some case if exited or needs restarting please run:

```shell
make horizon
```

### Test

To run the testsuite use:

```
make test
```

### Usage

To use the application please go to [Test Route](http://localhost/test). It will give you a curl command that you should run on your terminal.

Example:

```shell
curl -XPOST -H "Content-type: application/json" -d '
    {
        "emails": [
            {
                "email": "lmetz@collier.net",
                "subject": "Fully-configurable national opensystem",
                "body": "Autem rerum voluptate harum."
            }
        ]
    }' 'http://localhost/api/1/send?api_token=E3iXwtNSmE'
```

After you run this command you can check out [Mailpit](http://localhost:8025) to see the sent emails.

You can also check out [Laravel Horizon](http://localhost/horizon) to see the jobs that are being processed.

The list of sent emails can be found on [list route](http://localhost/api/list).

### Shut down

To shut down containers run:

```
make down
```
