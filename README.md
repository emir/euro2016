Euro2016
=================

UEFA EURO 2016 results for hackers.

[http://api.football-data.org/index](http://api.football-data.org/index)

Data Source: [http://api.football-data.org/v1/soccerseasons/424](http://api.football-data.org/v1/soccerseasons/424)

![app.php](http://i.imgur.com/Zk9Jk33.png)

### Installing

After cloning the repository:

```
composer install
```

**Important: You should set date.timezone on your php.ini**


Usage
------------

`php app.php fixtures`

Default argument is today. Also **current**, **finished** and **all** are supported arguments.
With argument **all** you can specified team you want to see schedule/results. For example:

`php app.php fixtures all -t Turkey`

License
-------------

[MIT License](http://emir.mit-license.org/)
