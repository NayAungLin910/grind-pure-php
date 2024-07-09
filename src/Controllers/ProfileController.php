<?php

namespace Src\Controllers;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Src\Controller;
use Src\Models\Course;
use Src\Models\Tag;
use Src\Models\User;
use Src\Router;
use Src\Services\AuthService;
use Src\Services\FormService;
use Src\Validators\FormValidator;
use Src\Validators\User\UserValidator;

class ProfileController extends Controller
{
    public function __construct(private $router = new Router())
    {
    }

    /**
     * Shows profile page of the currently authenticated user
     */
    public function showProfile(): void
    {
        $title = isset($_GET['title']) ? $_GET['title'] : "";
        $pageSize = isset($_GET['page-size']) && $_GET['page-size'] > 0 ? $_GET['page-size'] : 10;
        $page =  isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
        $sortByOldest = isset($_GET['oldest']) ? $_GET['oldest'] : null;
        $completed = isset($_GET['completed']) ? $_GET['completed'] : null;
        $tagSelected = isset($_GET['tags']) ? $_GET['tags'] : null;

        require "../config/bootstrap.php";

        $paginationDql = $entityManager->createQueryBuilder()
            ->select('c')
            ->from(Course::class, 'c')
            ->innerJoin('c.users', 'us')
            ->leftJoin('c.tags', 't')
            ->leftJoin('c.enrollments', 'e')
            ->leftJoin('e.user', 'u')
            ->andWhere('e.user_id = :eu_id')->setParameter('eu_id', $_SESSION['auth']['id'])
            ->andwhere('us.id = :u_id')->setParameter('u_id', $_SESSION['auth']['id']);  

        if ($title !== "") $paginationDql->andWhere('c.title LIKE :title')->setParameter('title', "%$title%");

        if ($completed) $paginationDql->andWhere("e.status = 'completed'");

        if ($sortByOldest) {
            $paginationDql = $paginationDql->orderBy('c.id', 'ASC');
        } else {
            $paginationDql = $paginationDql->orderBy('c.id', 'DESC');
        }

        if ($tagSelected) {
            $paginationDql->andWhere('t.id IN (:tags)')->setParameter('tags', ['tags' => $tagSelected]);
        }

        $paginationDql->andWhere('c.deleted = false');
        $paginationDql->setFirstResult(($page - 1) * $pageSize);
        $paginationDql->setMaxResults($pageSize);

        $tags = $entityManager->createQueryBuilder()
            ->select('t')
            ->from(Tag::class, 't')
            ->andWhere('t.deleted = false')
            ->getQuery()
            ->getResult();

        $paginator = new Paginator($paginationDql);

        $courses = [];

        foreach ($paginator as $course) {
            $courses[] = $course;
        }

        $totalItems = count($paginator); // all the rows of the table filtered from paginationQuery
        $totalPages = ceil($totalItems / $pageSize);

        if (is_array($tagSelected)) $tagSelected = array_map('intval', $tagSelected);

        $formValidator = new FormValidator();
        $formValidator->flashOldRequestData(compact('title', 'created_by_me', 'sortByOldest', 'tagSelected', 'completed'));

        $this->render("auth/profile", compact('courses', 'pageSize', 'page', 'totalItems', 'totalPages', 'tags'));
    }

    /**
     * Handle post request to save profile
     */
    public function postProfile(): void
    {
        $userValidator = new UserValidator();

        $userValidator->checkRequestFields(['name', 'email']);

        $name = $_POST['name'];
        $email = $_POST['email'];
        $profile = $_FILES['profile'];

        $userValidator->nameValidate($name, 'name');
        $userValidator->emailValidate($email, 'email', $_SESSION['auth']['id']);
        if ($profile['name'] !== '') $userValidator->profileImageValidate($profile, 'profile');

        $userValidator->flashOldRequestData([
            'name' => $name,
            'email' => $email
        ]);
        $userValidator->flashErrors();

        require "../config/bootstrap.php";

        $user = $entityManager->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.id = :u_id')->setParameter('u_id', $_SESSION['auth']['id'])
            ->getQuery()
            ->getOneOrNullResult();

        if (!$user) { // if no user found, logout
            $authService = new AuthService();
            $authService->logout();
            $this->router->redirectTo('show-login');
        }

        $profileImage = $user->getProfileImage();

        if ($profile['name'] !== '') { // if new image is uploaded
            $formService = new FormService();
            $formService->deleteFile($profileImage);
            $profileImage = $formService->uploadFiles($profile, '/images', 'image');
        }

        $oldEmail = $user->getEmail();

        $user->setName($name);
        $user->setEmail($email);
        $user->setProfileImage($profileImage);

        $entityManager->flush();

        // reset session
        $_SESSION['auth'] = [
            "id" => $user->getId(),
            "name" => $user->getName(),
            "email" => $user->getEmail(),
            "role" => $user->getRole(),
            "profile_image" => $user->getProfileImage()
        ];

        // delete old login tokens and cookies
        $authService = new AuthService();
        $authService->deleteTokenEmail($oldEmail);
        $authService->clearCookies();

        // set cookie and token for new email
        $authService->setCookieAndToken($email);

        $this->router->notificationSessionFlash('noti-success', 'Profile saved successfully!');
        $this->router->redirectUsingRouteName('profile');
    }

    /**
     * Change Password
     */
    public function postPasswordChange(): void
    {
        $userValidator = new UserValidator();

        $userValidator->checkRequestFields(['confirm-password', 'new-password', 'current-password']);

        $confirmPassword = $_POST['confirm-password'];
        $newPassword = $_POST['new-password'];
        $currentPassword = $_POST['current-password'];

        if ($newPassword !== $confirmPassword) {
            $userValidator->addError('confirm-password', 'Confirm password must be the same with the new password!');
            $userValidator->flashErrors();
        }

        $userValidator->checkStringExists($confirmPassword, 'confirm-password');
        $userValidator->checkStringExists($currentPassword, 'current-password');
        $userValidator->passwordValidate($newPassword, 'new-password');
        $userValidator->flashErrors();

        require "../config/bootstrap.php";

        $user = $entityManager->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.id = :u_id')->setParameter('u_id', $_SESSION['auth']['id'])
            ->getQuery()
            ->getOneOrNullResult();

        if (!$user) { // if no user found, logout
            $authService = new AuthService();
            $authService->logout();
            $this->router->redirectTo('show-login');
        }

        $result = password_verify($currentPassword, $user->getPassword());

        if (!$result) {
            $userValidator->addError('current-password', 'Wrong current password!');
            $userValidator->flashErrors();
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $user->setPassword($hashedPassword);
        $entityManager->flush();

        $this->router->notificationSessionFlash('noti-success', 'Password changed successfully!');
        $this->router->redirectUsingRouteName('profile');
    }
}
