<?php

namespace App\Controller;

use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use App\Services\MailerService;
use App\Services\RequestService;
use App\Services\ResponseService;
use App\Services\TokenService;
use App\Services\UserService;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PasswordController extends HelloworldController
{
    // ------------------------------ >

    public function __construct(
        ResponseService $responseService,
        RequestService $requestService,
        ValidatorInterface $validator,
        NormalizerInterface $normalizer,
        private UserService $userService,
        private TokenService $tokenService,
        private MailerService $mailerService,
        private TokenRepository $tokenRepository,
        private UserRepository $userRepository,
    ) {
        parent::__construct($responseService, $requestService, $validator, $normalizer);
    }

    // ------------------------------ >

    /**
     * @Route("/passowrd/{token}/renew", name="renew_password", methods={ "POST" })
     *
     * @throws Exception
     * @throws ExceptionInterface
     */
    public function newPasswordAction(Request $request, string $token): Response
    {
        //TODO move it to AbstractController
        $content = $request->getContent();
        $parameters = json_decode($content, true);

        $errors = $this->validate($parameters, [
            'password' => [new Type(['type' => 'string']), new NotBlank()],
        ]);

        if (!empty($errors)) {
            return $errors;
        }

        $validToken = $this->tokenRepository->findOneByValue($token);

        if (null === $validToken) {
            throw new Exception('Invalid token');
        }

        $user = $this->userService->getUserByToken($validToken);

        $this->userService->updatePassword($user, $parameters['password']);
        $this->tokenService->deleteToken($validToken);

        return $this->buildSuccessResponse(Response::HTTP_OK, ['message' => 'Votre mot de passe a bien ??t?? modifi??. Vous pouvez vous connecter de nouveau avec ce nouveau mot de passe.']);
    }

    /**
     * @Route("/password/reset", name="reset_password", methods={ "POST" })
     *
     * @throws Exception
     * @throws ExceptionInterface
     */
    public function sendResetPasswordEmail(Request $request): Response
    {
        //TODO move it to AbstractController
        $content = $request->getContent();
        $parameters = json_decode($content, true);

        $errors = $this->validate($parameters, [
            'email' => [new Type(['type' => 'string']), new NotBlank()],
        ]);

        if (!empty($errors)) {
            return $errors;
        }

        $email = $parameters['email'];
        $user = $this->userRepository->findOneByEmail($email);

        if (null === $user) {
            return $this->buildSuccessResponse(Response::HTTP_OK, ['message' => 'Un email ?? ??t?? envoy?? ?? l\'adresse email indiqu?? si elle existe']);
        }

        $this->mailerService->forgetPasswordEmail($email, $user);

        return $this->buildSuccessResponse(Response::HTTP_OK, ['message' => 'Un email ?? ??t?? envoy?? ?? l\'adresse email indiqu?? si elle existe']);
    }
}
