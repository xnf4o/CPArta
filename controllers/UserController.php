<?php

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="User API"
 * )
 */
class UserController
{

    private $connection;
    private $authMiddleware;
    private $userModel;

    public function __construct($connection, $authMiddleware)
    {
        $this->connection = $connection;
        $this->authMiddleware = $authMiddleware;
        $this->userModel = new User($this->connection);
    }

    /**
     * @OA\Post(
     *     path="/users",
     *     summary="Create a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CreateUserRequest")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="User created successfully"
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Invalid request data"
     *     )
     * )
     */
    public function createUser($data, $request)
    {
        $this->authMiddleware->authenticate();

        // Validate the request data
        $validationResult = $this->validateCreateRequest($request);
        if (!$validationResult['success']) {
            // Return validation error response
            echo json_encode(['error' => $validationResult['message']]);
            return true;
        }

        // Retrieve the request data
        $name = $request['name'];
        $email = $request['email'];
        $password = password_hash($request['password'], PASSWORD_DEFAULT);

        // Create a new user
        $this->userModel->createUser($name, $email, $password);

        http_response_code(201);
        echo json_encode(['message' => 'User created successfully']);
    }

    private function validateCreateRequest($request): array
    {
        if (!isset($request['name']) || !isset($request['email']) || !isset($request['password'])) {
            return ['success' => false, 'message' => 'Email, name and password are required.'];
        }

        return ['success' => true];
    }

    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     summary="Get a user by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successful response",
     *         @OA\Schema(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="User not found"
     *     )
     * )
     */
    public function getUser($request)
    {
        $this->authMiddleware->authenticate();

        // Get the user
        $user = $this->userModel->getUser($request['id']);

        if ($user) {
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'User not found']);
        }
    }

    /**
     * @OA\Get(
     *     path="/users",
     *     summary="Get all users",
     *     @OA\Response(
     *         response="200",
     *         description="Successful response",
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="No users found"
     *     )
     * )
     */
    public function getUsers()
    {
        $this->authMiddleware->authenticate();

        // Get all users
        $users = $this->userModel->getUsers();

        if ($users) {
            echo json_encode($users);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'No users found']);
        }
    }

    /**
     * @OA\Post(
     *     path="/users/{id}",
     *     summary="Update a user",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateUserRequest")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="User updated successfully"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="User not found"
     *     )
     * )
     */
    public function updateUser($data, $request)
    {
        $this->authMiddleware->authenticate();

        // Retrieve the request data
        $name = $request['name'];
        $email = $request['email'];

        // Update the user
        $this->userModel->updateUser($data['id'], $name, $email);

        echo json_encode(['message' => 'User updated successfully']);
    }

    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     summary="Delete a user",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="User deleted successfully"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="User not found"
     *     )
     * )
     */
    public function deleteUser($request)
    {
        $this->authMiddleware->authenticate();

        // Delete the user
        $this->userModel->deleteUser($request['id']);

        echo json_encode(['message' => 'User deleted successfully']);
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     summary="User login",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Invalid credentials"
     *     )
     * )
     */
    public function login($data, $request)
    {
        // Validate the request data
        $validationResult = $this->validateLoginRequest($request);
        if (!$validationResult['success']) {
            // Return validation error response
            echo json_encode(['error' => $validationResult['message']]);
            return true;
        }

        // Retrieve the login credentials from the request (e.g., email and password)
        $email = $request['email'];
        $password = $request['password'];

        $user = $this->userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            // Generate a token for the authenticated user
            $token = $this->authMiddleware->generateToken();

            // Return the token as a response
            echo json_encode(['token' => $token]);
        } else {
            // Authentication failed
            echo json_encode(['error' => 'Invalid credentials']);
        }
        return true;
    }

    private function validateLoginRequest($request): array
    {
        if (!isset($request['email']) || !isset($request['password'])) {
            return ['success' => false, 'message' => 'Email and password are required.'];
        }

        return ['success' => true];
    }
}
