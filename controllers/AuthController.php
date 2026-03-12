<?php
namespace Controllers;

use Core\Controller;
use Models\User;

class AuthController extends Controller
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Basic validation
            $email = $_POST['email'];
            if ($this->userModel->findByEmail($email)) {
                return $this->view('auth/register', ['error' => 'Email already exists']);
            }

            // Create User
            $userData = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'role' => $_POST['role'] ?? 'buyer'
            ];

            if ($this->userModel->create($userData)) {
                $this->redirect('auth/login');
            } else {
                return $this->view('auth/register', ['error' => 'Registration failed']);
            }
        } else {
            $this->view('auth/register');
        }
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $user = $this->userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['name'];

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    $this->redirect('admin/dashboard');
                } elseif ($user['role'] === 'farmer') {
                    $this->redirect('farmer/dashboard');
                } else {
                    $this->redirect('shop');
                }
            } else {
                $this->view('auth/login', ['error' => 'Invalid email or password']);
            }
        } else {
            $this->view('auth/login');
        }
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('auth/login');
    }
}
?>