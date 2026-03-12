<?php
namespace Core;

abstract class Controller
{
    protected function view($name, $data = [])
    {
        // Extract data to make it available in the view
        extract($data);

        $viewFile = ROOT_PATH . "/views/" . $name . ".php";

        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View $name not found.");
        }
    }

    protected function redirect($url)
    {
        header("Location: " . APP_URL . "/" . $url);
        exit();
    }

    protected function json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}
?>