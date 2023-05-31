# User API

This is a simple API for a web application that allows client applications to interact with a user database. The API provides endpoints for creating, reading, updating, and deleting users.

The API is designed to be secure and utilizes an authentication mechanism, such as access tokens, to protect the endpoints.

## Technologies Used

- PHP
- Swagger (OpenAPI)

## API Endpoints

### Create a User

- Method: `POST`
- URL: `/users`
- Description: Create a new user
- Request Body: User data
- Response:
- `201 Created`: User created successfully
- `400 Bad Request`: Invalid request data

### Get All Users

- Method: `GET`
- URL: `/users`
- Description: Get all users
- Response:
- `200 OK`: Successful response with an array of user objects
- `404 Not Found`: No users found

### Get User by ID

- Method: `GET`
- URL: `/users/{id}`
- Description: Get a user by ID
- Parameters:
- `id` (integer): User ID
- Response:
- `200 OK`: Successful response with the user object
- `404 Not Found`: User not found

### Update User by ID

- Method: `PUT`
- URL: `/users/{id}`
- Description: Update a user by ID
- Parameters:
- `id` (integer): User ID
- Request Body: User data
- Response:
- `200 OK`: User updated successfully
- `404 Not Found`: User not found

### Delete User by ID

- Method: `DELETE`
- URL: `/users/{id}`
- Description: Delete a user by ID
- Parameters:
- `id` (integer): User ID
- Response:
- `200 OK`: User deleted successfully
- `404 Not Found`: User not found

### User Authentication

- Method: `POST`
- URL: `/login`
- Description: User login
- Request Body: User login credentials (email and password)
- Response:
- `200 OK`: Successful login with an access token
- `401 Unauthorized`: Invalid credentials

## Security

The API endpoints are secured using an authentication mechanism. Client applications should include an access token in the request headers to authenticate and access the protected endpoints.

## Contributing

Contributions to this project are welcome. If you find any issues or have suggestions for improvements, please open a new issue or submit a pull request.

## License

This project is licensed under the [MIT License](LICENSE).