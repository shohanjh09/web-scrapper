# Company Search Web Application (Scrapping)

A web application for searching, scrapping, and managing company information.

## Table of Contents
- [Functional Descriptions](#functional-descriptions)
- [Technologies Used](#technologies-used)
- [Setup Guide](#setup-guide)
- [Contributing](#contributing)
- [License](#license)

## Functional Descriptions

- **Company Search:** Allows users to search for company information using the company's registration code. Firstly, it tries to find in the database; otherwise, it scrapes data from the following URL: https://rekvizitai.vz.lt
- **Company Display:** Displays detailed information about a company, including name, registration code, VAT, address, and more.
- **Company Creation:** Users can create new company records by entering the required details.
- **Company Editing:** Users can edit existing company records to update their information.
- **Company Deletion:** Allows users to delete company records after confirming the action.
- **Company Turnover:** Displays turnover information for companies, including financial details for specific years.

## Technologies Used

- Symfony: A PHP web application framework.
- Bootstrap: A CSS framework for responsive and user-friendly designs.
- Font Awesome: A library for adding icons to your application.
- KnpPaginatorBundle: A bundle for adding pagination to lists of data.
- jQuery: A JavaScript library for interactivity and AJAX requests.
- Dockerized stack consisting of Nginx, PHP 8.2, MySQL 8, RabbitMQ, and Redis

## Setup Guide

To get the project up and running on your local machine, follow these steps:

1. Clone the repo and `cd` into it:
```bash
git clone https://github.com/shohanjh09/web-scrapper.git
cd web-scrapper
```
2. Rename or copy `.env.test` file to `.env`
   
3. Build and start the Docker containers:
```bash
docker-compose up --build -d
```

4. Access the application container:

```bash
docker-compose exec app sh
```

5. Install the required dependencies inside the container:

```bash
composer install
```

6. Create the database schema inside the container:

```bash
php bin/console doctrine:migrations:migrate
```

7. Visit `http://localhost` or `https://localhost` in your browser

8. Visit `http://localhost:8080` (username: root, password: 123) in your browser to access phpMyAdmin

9. Visit `http://localhost:15672` (username: guest, password: guest) in your browser to access RabbitMQ

## Contributing

Contributions are welcome! If you find any bugs or want to enhance the functionality, feel free to submit pull requests or issues.

## License
This project is licensed under the MIT License.
