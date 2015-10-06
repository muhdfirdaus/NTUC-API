README
======

> Strongly recommend you to checkout the [**More**](#More) section before you start.
And anytime you feel lost, you can always refer to that section for more information or directly contact me via
[issues](https://github.com/muhdfirdaus/NTUC-API/issues) or email.

Pre-request
---------
### Turn on PHP CURL Extension ###
Because our test framework depends on PHP CURL extension.
Go to the PHP installation Folder, looking for `php.ini` file(the configuration file of PHP)
The file may locate elsewhere, in this case run command `php --ini` in your CLI(command line interface) to find out.
Go to the extension section of the file. make sure there is no `;` before line `extension=php_curl.dll`(or `extension=php_curl.so` in *nix OS)

How To
-----
* Launch CLI and move to our project's root folder which is `NTUC-API`
* Run `php -S localhost:80 route.php` to enable PHP build-in development server(If you encounter any Port conflict problem,
 please make sure you disabled apache or any other local server which occupied port 80)
* (Optional) Run `vender\bin\codecept build` once,to build your helpers and load new test module if needed
(If you have changed those functions or modified any yaml file)
* Run `vender\bin\codecept run --html` to run test cases
* Open the `<project folder>/tests/_output/report.html` in browser for test result

More
---------
### Intro
The `test` folder is used for automate testing.
There are 2 test collections at this moment.
Each collection have several test cases, included every expected or unexpected condition.
If everything pass, then means the functionalities are working properly, otherwise the service should
 not be rolling out for production.

### Framework
The test are composed using Codeception, please refer to http://codeception.com/docs/ for more information

### Test Type
We are using Functional Test at this moment, because it will run faster than acceptance test,
 and our project is not capable to do unit-test.
