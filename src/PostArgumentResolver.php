<?php
declare(strict_types=1);


namespace App;


use App\Request\CreatePostRequest;
use App\Request\PostRequest;
use Generator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PostArgumentResolver implements ArgumentValueResolverInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ObjectNormalizer
     */
    private $normalizer;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param ValidatorInterface $validator
     * @param ObjectNormalizer $normalizer
     * @param FlashBagInterface $flashBag
     * @param RouterInterface $router
     */
    public function __construct(
        ValidatorInterface $validator,
        ObjectNormalizer $normalizer,
        FlashBagInterface $flashBag,
        RouterInterface $router
    )
    {
        $this->validator = $validator;
        $this->normalizer = $normalizer;
        $this->flashBag = $flashBag;
        $this->router = $router;
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return bool
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return $argument->getType() === CreatePostRequest::class;
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return Generator
     * @throws ExceptionInterface
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        if(null ===$request->request->all()){
            return;
        }

        $obj = $this->normalizer->denormalize(
            $request->request->all(),
            CreatePostRequest::class
        );

        $errors = $this->validator->validate($obj);

        if (count($errors) > 0) {
            $this->renderFlashMessages($errors);
            // this should prob remove but for now it's ok
            throw new BadRequestHttpException((string) $errors);
        }

        yield $obj;
    }

    /**
     * @param ConstraintViolationListInterface $errors
     */
    private function renderFlashMessages(ConstraintViolationListInterface $errors): void
    {
        /** @var ConstraintViolationInterface[] $error */
        foreach ($errors as $error) {
            $errorsMessage = sprintf(
                '%s %s',
                $error->getPropertyPath(),
                $error->getMessage()
            );

            $this->flashBag->add('error', $errorsMessage);
        }
    }
}