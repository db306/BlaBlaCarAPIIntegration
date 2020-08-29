# ReadMe

Hi there, thanks for reviewing this exercise.

This has been a great exercise of not over-engineering things when they can be easy. I have however typed most things and made sure that all objects could only have valid states.

### Running tests
In order to test you can run this command in terminal:

```
make test
```

### Installing dependencies

```
make install
```

### Running locally

For the simplicity of the exercise, I used Symfony internal server and a local PHP 7.4.4 with Xdebug

If you do not have the Symfony CLI Prompt tool you can install it like this

```bash
curl -sS https://get.symfony.com/cli/installer | bash
```

Once install you can run the following command

```
make run
```

And the application will then be available on http://127.0.0.1:8000/