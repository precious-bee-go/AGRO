<?php
namespace Controllers;

use Core\Controller;
use Models\Notification;

class NotificationController extends Controller
{
    private $notificationModel;

    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
        }
        $this->notificationModel = new Notification();
    }

    public function index()
    {
        $notifications = $this->notificationModel->getUnread($_SESSION['user_id']);
        $this->view('notifications/index', [
            'title' => 'Notifications',
            'notifications' => $notifications
        ]);
    }

    public function markRead()
    {
        $this->notificationModel->markAllAsRead($_SESSION['user_id']);
        $this->redirect('notifications');
    }
}
?>