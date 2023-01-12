<h1 align="center"> Stock Quotes API </h1>

<p align="center">
  <img src="https://user-images.githubusercontent.com/50057372/211948933-41afdfe5-6ec1-4167-9573-b2fdcb33fff9.png" width="1000" alt="" />
</p>

<hr>

<p align="center">
    <img src="https://img.shields.io/badge/docker_build-ok-green" />
    <img src="https://img.shields.io/badge/php-8-blueviolet" />
    <img src="https://img.shields.io/badge/composer-2.4.1-yellowgreen" />
    <img src="https://img.shields.io/badge/auth-JWT-blue" />
    <img src="https://img.shields.io/badge/documentation-Open%2FAPI-green" />
    <img src="https://img.shields.io/badge/DBMS-MySql-orange" />
    <img src="https://img.shields.io/badge/mailer-Synfony%2FMailer-9cf" />
    <img src="https://img.shields.io/badge/ORM-Doctrine-important" />
</p>

This project it's a simple Slim Framework v4 REST API, that the user can use to track the value of stocks in the stock market. Below you can find some examples of how to install and use it.

## Summary
* [What Will You Found](#What_Will_You_Found)
* [Install](#Install)
* [Basic Usage](#Basic_Usage)
* [Author](#Author)

<br>

<h2 id="What_Will_You_Found">What will you found here:</h2>

### :hammer: Project Features (Overview)

- `Feature 1`: Create a new User, storing the email and information to log in later;
- `Feature 2`: Allows users to login;
- `Feature 3`: Request stock quote by symbol, save research and send a e-mail to user with stock quote information;
- `Feature 4`: Retrieve the history of queries made to the API service by that user.

<br>

<h2 id="Install">Install instructions:</h2>
For this tutorial, I'll consider that you already know and have Docker installed.

1. First of all, copy the `.env.sample` file into `.env` and modify its contents to match your current settings.
    - Make sure you set the ENV variable to `dev` and fill all the options for JWT configs, and Symfony Mailer.
	- Please note that, as we are using Docker for our database service, the Database credentials comes pre configured, so as long as you don't make any changes to `docker-compose.yml`, you won't need to change anything here.

2. After that, we can start the project, by running:
```bash
docker-compose up
```

3. This project has all the tables managed by `doctrine migrations`, so you don't need to worry about it. Cause all the database settings will be done automatically. For this we will need two commands:
	- First one, to install all the project dependencies:
		```bash
		docker-compose exec app composer install
		```
	- Second one, to run the migrations:
		```bash
		docker-compose exec app composer migrate
		```

4. And finally, you should be able to check the project running on http://localhost:8000

<br>

<h2 id="Basic_Usage">Basic Usage:</h2>

This API provides 4 routes, like mencioned before on [WWYF](#What_Will_You_Found) section. For each feature, we have a route which I will comment on briefly below:
    
> **Note**
> that, if you want something more practical and detailed, this API is documented using Open/API and you can find the `openapi.yaml` file, in the **documentation** folder at the project source.
    
<br>

#### `Feature 1 - Create user - (POST: /users)`: 

Request body should be like:
> **Note**
> As the API will need to send emails to the user, it is **highly** recommended to use a real email in this registration
``` json
{
    "name": "John Doe",
    "email": "johndoe@gmail.com",
    "password": "12345"
}
```

Response body [ STATUS 200 ]:
``` json
{
	"id": 1
}
```
The response will also come with a JWT Token in the `Authorization` header, which should be passed in the same header for `/stock` and `/history` requests.

<br>

`Feature 2 - User login - (POST: /login)`:

Request body should be like:
``` json
{
    "email": "johndoe@gmail.com",
    "password": "12345"
}
```

Response body [ STATUS 200 ]:
``` json
{
	"id": 1,
	"name": "John Doe"
}
```
As same as in the `POST: /users`, the response will come with a JWT Token in the `Authorization` header, which should be passed in the same header for `/stock` and `/history` requests.

<br>

`Feature 3 - Get stock quote - (GET: /stock?q=<quote_symbol>)`: 

In this case, you need to make sure that you are passing a valid quote symbol, you can find more symbols in [this list](https://stooq.com/t/?i=518).

> **Warning**
> Remember that here you have to pass a valid JWT token through the Authorization header. Otherwise, you will get a 401 Unauthorized message.

Response body (symbol=aapl.us) [ STATUS 200 ]:
``` json
{
	"name": "APPLE",
	"symbol": "AAPL.US",
	"open": 155.47,
	"high": 157.82,
	"low": 154.75,
	"close": 157.37
}
```
This route also sends an email to the registered user email with the same information returned on the response body.

<p align="center">
    <img src="https://user-images.githubusercontent.com/50057372/211948375-06b95b74-232b-4f49-b995-737ed4400a57.png">
</p>

<br>

`Feature 4 - Get user history - (GET: /history)`:

Here user can retrieve the history of queries made to the API. For that route, no parameter is needed.

> **Warning**
> Remember that here you have to pass a valid JWT token through the Authorization header. Otherwise, you will get a 401 Unauthorized message.

Response body [ STATUS 200 ]:
``` json
[
	{
		"date": "2022-09-09 18:08:20",
		"name": "APPLE",
		"symbol": "AAPL.US",
		"open": "155.470",
		"high": "157.820",
		"low": "154.750",
		"close": "157.370"
	},
	{
		"date": "2022-09-09 18:03:37",
		"name": "APPLE",
		"symbol": "AAPL.US",
		"open": "154.640",
		"high": "156.360",
		"low": "152.680",
		"close": "154.450"
	},
	{
		"date": "2022-09-09 17:58:38",
		"name": "APPLE",
		"symbol": "AAPL.US",
		"open": "154.640",
		"high": "156.360",
		"low": "152.680",
		"close": "154.450"
	}
    ...
]
```
<br>

<h2 id="Author">Author:</h2>

And that's it for today! If you have any doubts you can find me on [LinkedIn](https://www.linkedin.com/in/castroogbs/).

[<img src="https://avatars.githubusercontent.com/u/50057372?v=4" width=115><br><sub>Gabriel Castro</sub>](https://github.com/castroogbs)
