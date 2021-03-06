<?php
namespace Spipu\CoreBundle\Tests;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\HttpFoundation\Type\FormTypeHttpFoundationExtension;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as Twig_Environment;

class SymfonyMock extends TestCase
{
    /**
     * @var string[]
     */
    protected static $consoleOutputMessages = [];

    /**
     * @var array
     */
    protected static $containerBuilderParameters = [];

    /**
     * @param TestCase $testCase
     * @param array $services
     * @param array $parameters
     * @return MockObject|ContainerInterface
     */
    public static function getContainer(TestCase $testCase, array $services = [], array $parameters = [])
    {
        $container = $testCase->createMock(ContainerInterface::class);

        $map = [];
        foreach ($services as $key => $value) {
            $map[] = [$key, 1, $value];
        }
        $container->method('get')->willReturnMap($map);

        $map = [];
        foreach (array_keys($services) as $key) {
            $map[] = [$key, true];
        }
        $container->method('has')->willReturnMap($map);

        $map = [];
        foreach ($parameters as $key => $value) {
            $map[] = [$key, $value];
        }
        $container->method('getParameter')->willReturnMap($map);

        $map = [];
        foreach (array_keys($parameters) as $key) {
            $map[] = [$key, true];
        }
        $container->method('hasParameter')->willReturnMap($map);

        /** @var ContainerInterface $container */
        return $container;
    }

    /**
     * @param TestCase $testCase
     * @param array $extensions
     * @return MockObject|ContainerBuilder
     */
    public static function getContainerBuilder(TestCase $testCase, array $extensions = [])
    {
        self::$containerBuilderParameters = [];

        $containerBuilder = $testCase->createMock(ContainerBuilder::class);

        $containerBuilder
            ->method('getParameterBag')
            ->willReturn(self::getParameterBag($testCase));

        $containerBuilder
            ->method('setParameter')
            ->willReturnCallback(
                function ($name, $value) {
                    SymfonyMock::$containerBuilderParameters[$name] = $value;
                }
            );

        $containerBuilder
            ->method('hasParameter')
            ->willReturnCallback(
                function ($name) {
                    return array_key_exists($name, SymfonyMock::$containerBuilderParameters);
                }
            );

        $containerBuilder
            ->method('getParameter')
            ->willReturnCallback(
                function ($name) {
                    return SymfonyMock::$containerBuilderParameters[$name];
                }
            );

        $containerBuilder
            ->method('prependExtensionConfig')
            ->willReturnCallback(
                function ($name, array $config) {
                    $name = 'prependConfig::'.$name;
                    SymfonyMock::$containerBuilderParameters[$name][] = $config;
                }
            );

        $containerBuilder
            ->method('getExtensionConfig')
            ->willReturnCallback(
                function ($name) {
                    $name = 'prependConfig::'.$name;
                    return SymfonyMock::$containerBuilderParameters[$name];
                }
            );

        $containerBuilder->method('getReflectionClass')->willReturnCallback(
            function (string $class, bool $throw = true) {
                return new \ReflectionClass($class);
            }
        );

        $containerBuilder->method('setAlias')->willReturnCallback(
            function ($alias, string $id) {
                return new Alias($id);
            }
        );

        $containerBuilder->method('getExtensions')->willReturn($extensions);

        /** @var ContainerBuilder $containerBuilder */
        return $containerBuilder;
    }

    /**
     * @param TestCase $testCase
     * @return MockObject|ParameterBagInterface
     * @throws \ReflectionException
     */
    public static function getParameterBag(TestCase $testCase)
    {
        $parameterBag = $testCase->createMock(ParameterBagInterface::class);

        $parameterBag->method('resolveValue')->willReturnArgument(0);
        $parameterBag->method('unescapeValue')->willReturnArgument(0);

        /** @var ParameterBagInterface $parameterBag */
        return $parameterBag;
    }

    /**
     * @param TestCase $testCase
     * @param array $getValues
     * @return MockObject|RequestStack
     */
    public static function getRequestStack(TestCase $testCase, $getValues = [])
    {
        $request = new Request();
        $request->initialize($getValues);

        $requestStack = $testCase->createMock(RequestStack::class);
        $requestStack->expects($testCase->any())->method('getCurrentRequest')->willReturn($request);

        /** @var RequestStack $requestStack */
        return $requestStack;
    }

    /**
     * @param TestCase $testCase
     * @return Session
     */
    public static function getSession(TestCase $testCase)
    {
        return new Session(new MockArraySessionStorage());
    }

    /**
     * @param TestCase $testCase
     * @return MockObject|RouterInterface
     */
    public static function getRouter(TestCase $testCase)
    {
        $router = $testCase->createMock(RouterInterface::class);
        $router
            ->expects($testCase->any())
            ->method('generate')
            ->willReturnCallback(
                function ($name, $parameters = []) {
                    $url = '/' . $name . '/';
                    if ($parameters && $query = http_build_query($parameters, '', '&', PHP_QUERY_RFC3986)) {
                        $url .= '?'.strtr($query, ['%2F' => '/']);
                    }
                    return $url;
                }
            );

        /** @var RouterInterface $router */
        return $router;
    }

    /**
     * @param TestCase $testCase
     * @return MockObject|TranslatorInterface
     */
    public static function getTranslator(TestCase $testCase)
    {
        $translator = $testCase->createMock(TranslatorInterface::class);
        $translator
            ->expects($testCase->any())
            ->method('trans')
            ->will($testCase->returnArgument(0));

        /** @var TranslatorInterface $translator */
        return $translator;
    }

    /**
     * @param TestCase $testCase
     * @return MockObject|EventDispatcherInterface
     */
    public static function getEventDispatcher(TestCase $testCase)
    {
        $eventDispatcher = $testCase->createMock(EventDispatcherInterface::class);

        /** @var EventDispatcherInterface $eventDispatcher */
        return $eventDispatcher;
    }

    /**
     * @param TestCase $testCase
     * @param array $values
     * @return MockObject|AuthorizationCheckerInterface
     */
    public static function getAuthorizationChecker(TestCase $testCase, array $values = [])
    {
        if ($values === []) {
            $values = [
                ['IS_AUTHENTICATED_REMEMBERED', null, true],
                ['ROLE_GOOD', null, true],
            ];
        }
        $authorizationChecker = $testCase->createMock(AuthorizationCheckerInterface::class);
        $authorizationChecker
            ->expects($testCase->any())
            ->method('isGranted')
            ->will($testCase->returnValueMap($values));

        /** @var AuthorizationCheckerInterface $authorizationChecker */
        return $authorizationChecker;
    }

    /**
     * @param TestCase $testCase
     * @return ClassMetadata|MockObject
     */
    public static function getEntityMetaData(TestCase $testCase)
    {
        $metaData = $testCase->createMock(ClassMetadata::class);

        /** @var ClassMetadata $metaData */
        return $metaData;
    }

    /**
     * @param TestCase $testCase
     * @return EntityManagerInterface|MockObject
     */
    public static function getEntityManager(TestCase $testCase)
    {
        $entityManager = $testCase->createMock(EntityManagerInterface::class);

        $entityManager
            ->method('getClassMetadata')
            ->willReturn(self::getEntityMetaData($testCase));

        $queryBuilder = self::getDoctrineQueryBuilder($testCase);
        $queryBuilder
            ->method('getEntityManager')
            ->willReturn($entityManager);

        $entityManager
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        /** @var EntityManagerInterface $entityManager */
        return $entityManager;
    }

    /**
     * @param TestCase $testCase
     * @return MockObject|ManagerRegistry
     */
    public static function getEntityRegistry(TestCase $testCase)
    {
        $registry = $testCase->createMock(ManagerRegistry::class);

        $registry
            ->method('getManagerForClass')
            ->willReturn(SymfonyMock::getEntityManager($testCase));

        /** @var ManagerRegistry $registry */
        return $registry;
    }

    /**
     * @param TestCase $testCase
     * @return MockObject|Twig_Environment
     */
    public static function getTwig(TestCase $testCase)
    {
        $twig = $testCase->createMock(Twig_Environment::class);

        /** @var Twig_Environment $twig */
        return $twig;
    }

    /**
     * @param TestCase $testCase
     * @return MockObject|CacheItemPoolInterface
     */
    public static function getCachePool(TestCase $testCase)
    {
        $cache = new ArrayAdapter(0, false);

        /** @var CacheItemPoolInterface $cache */
        return $cache;
    }

    /**
     * @param TestCase $testCase
     * @param MockObject $file
     * @param string $fileName
     * @param string $guessExtension
     * @param string $mimeType
     * @return MockObject
     */
    protected static function prepareFile(
        TestCase $testCase,
        MockObject $file,
        string $fileName = '/tmp/image.jpg',
        string $guessExtension = 'jpeg',
        string $mimeType = 'image/jpeg'
    ) {
        $tempFile = new File($fileName, false);

        $file->method('getPath')->willReturn($tempFile->getPath());
        $file->method('getFilename')->willReturn($tempFile->getFilename());
        $file->method('getExtension')->willReturn($tempFile->getExtension());
        $file->method('getBasename')->willReturn($tempFile->getBasename());
        $file->method('getPathname')->willReturn($tempFile->getPathname());
        $file->method('getBasename')->willReturn($tempFile->getBasename());
        $file->method('getRealPath')->willReturn($tempFile->getRealPath());
        $file->method('getPathInfo')->willReturn($tempFile->getPathInfo());
        $file->method('getType')->willReturn('file');
        $file->method('getSize')->willReturn(42);
        $file->method('isDir')->willReturn(false);
        $file->method('isFile')->willReturn(true);
        $file->method('isLink')->willReturn(false);
        $file->method('isExecutable')->willReturn(false);
        $file->method('isReadable')->willReturn(true);
        $file->method('isWritable')->willReturn(true);

        $file->method('guessExtension')->willReturn($guessExtension);
        $file->method('getMimeType')->willReturn($mimeType);

        $file->method('move')->willReturnCallback(
            function ($directory, $name = null) use ($file, $testCase, $fileName, $guessExtension, $mimeType) {
                $newName = $fileName;
                if ($name !== null) {
                    $newName = $name;
                }
                $target = rtrim($directory, '/\\') . DIRECTORY_SEPARATOR . $newName;
                return SymfonyMock::getFile($testCase, $target, $guessExtension, $mimeType);
            }
        );

        return $file;
    }

    /**
     * @param TestCase $testCase
     * @param string $fileName
     * @param string $guessExtension
     * @param string $mimeType
     * @return MockObject|File
     */
    public static function getFile(
        TestCase $testCase,
        string $fileName = '/tmp/image.jpg',
        string $guessExtension = 'jpeg',
        string $mimeType = 'image/jpeg'
    ) {
        $file = $testCase->createMock(File::class);

        self::prepareFile($testCase, $file, $fileName, $guessExtension, $mimeType);

        /** @var File $file */
        return $file;
    }

    /**
     * @param TestCase $testCase
     * @param string $path
     * @param string $originalFileName
     * @param string $guessExtension
     * @param string $mimeType
     * @return MockObject|UploadedFile
     */
    public static function getUploadedFile(
        TestCase $testCase,
        string $path = '/tmp/uploaded_image.jpg',
        string $originalFileName = 'image.jpg',
        string $guessExtension = 'jpeg',
        string $mimeType = 'image/jpeg'
    ) {
        $file = $testCase->createMock(UploadedFile::class);

        self::prepareFile($testCase, $file, $path, $guessExtension, $mimeType);

        $file->method('getClientOriginalName')->willReturn($originalFileName);
        $file->method('getClientOriginalExtension')->willReturn(pathinfo($originalFileName, PATHINFO_EXTENSION));
        $file->method('getClientMimeType')->willReturn($mimeType);
        $file->method('guessClientExtension')->willReturn(null);
        $file->method('isValid')->willReturn(true);

        /** @var UploadedFile $file */
        return $file;
    }

    /**
     * @param TestCase $testCase
     * @param array $arguments
     * @param array $options
     * @return MockObject|InputInterface
     */
    public static function getConsoleInput(TestCase $testCase, array $arguments = [], array $options = [])
    {
        $input = $testCase->createMock(InputInterface::class);

        $map = [];
        foreach ($arguments as $key => $value) {
            $map[] = [$key, $value];
        }
        $input->method('getArgument')->willReturnMap($map);

        $map = [];
        foreach ($options as $key => $value) {
            $map[] = [$key, $value];
        }
        $input->method('getOption')->willReturnMap($map);

        /** @var InputInterface $input */
        return $input;
    }

    /**
     * @param TestCase $testCase
     * @return MockObject|OutputInterface
     */
    public static function getConsoleOutput(TestCase $testCase)
    {
        self::$consoleOutputMessages = [];

        $output = $testCase->createMock(OutputInterface::class);

        $output
            ->method('getFormatter')
            ->willReturn($testCase->createMock(OutputFormatterInterface::class));

        $output
            ->method('write')
            ->willReturnCallback(
                function ($messages, $newline = false, $options = 0) {
                    if (!is_array($messages)) {
                        $messages = [(string) $messages];
                    }
                    foreach ($messages as $key => $value) {
                        $messages[$key] = trim(strip_tags($value));
                    }

                    self::$consoleOutputMessages = array_merge(self::$consoleOutputMessages, array_values($messages));
                }
            );

        $output
            ->method('writeln')
            ->willReturnCallback(
                function ($messages, $options = 0) use ($output) {
                    $output->write($messages, true, $options);
                }
            );

        /** @var OutputInterface $output */
        return $output;
    }

    /**
     * @param TestCase $testCase
     * @return MockObject|SymfonyStyle
     */
    public static function getConsoleSymfonyStyle(TestCase $testCase)
    {
        self::$consoleOutputMessages = [];

        $symfonyStyle = $testCase->createMock(SymfonyStyle::class);

        $symfonyStyle
            ->method('writeln')
            ->willReturnCallback(
                function ($messages, $type = 1) {
                    if (!is_array($messages)) {
                        $messages = [(string) $messages];
                    }
                    foreach ($messages as $key => $value) {
                        $messages[$key] = trim(strip_tags($value));
                    }

                    self::$consoleOutputMessages = array_merge(self::$consoleOutputMessages, array_values($messages));
                }
            );

        $symfonyStyle
            ->method('text')
            ->willReturnCallback(
                function ($messages) use ($symfonyStyle) {
                    $symfonyStyle->writeln($messages);
                }
            );

        /** @var SymfonyStyle $symfonyStyle */
        return $symfonyStyle;
    }

    /**
     * @return string[]
     */
    public static function getConsoleOutputResult()
    {
        return self::$consoleOutputMessages;
    }

    /**
     * @param TestCase $testCase
     * @param string $tokenValue
     * @return FormFactoryInterface
     */
    public static function getFormFactory(TestCase $testCase , string $tokenValue = 'mock_token_value')
    {
        $tokenManager = $testCase->createMock(CsrfTokenManagerInterface::class);
        $tokenManager->method('getToken')->willReturn($tokenValue);
        $tokenManager->method('isTokenValid')->willReturn(true);

        /** @var CsrfTokenManagerInterface $tokenManager */
        $extensions = [
            new CsrfExtension($tokenManager),
        ];

        $typeExtensions = [
            new FormTypeHttpFoundationExtension(),
        ];

        return Forms::createFormFactoryBuilder()
            ->addExtensions($extensions)
            ->addTypeExtensions($typeExtensions)
            ->addTypes([])
            ->addTypeGuessers([])
            ->getFormFactory();
    }

    /**
     * @param TestCase $testCase
     * @return MockObject|TokenStorageInterface
     */
    public static function getTokenStorage(TestCase $testCase)
    {
        $token = $testCase->createMock(TokenInterface::class);

        $tokenStorage = $testCase->createMock(TokenStorageInterface::class);

        $tokenStorage
            ->method('getToken')
            ->willReturn($token);

        /** @var TokenStorageInterface $tokenStorage */
        return $tokenStorage;
    }

    /**
     * @param TestCase $testCase
     * @return MockObject|UserPasswordEncoderInterface
     */
    public static function getPasswordEncoder(TestCase $testCase)
    {
        $encoder = $testCase->createMock(PasswordEncoderInterface::class);

        $encoder
            ->method('encodePassword')
            ->willReturnCallback(
                function ($raw, $salt) {
                    return 'encoded_' . $raw;
                }
            );

        $encoder
            ->method('isPasswordValid')
            ->willReturnCallback(
                function ($encoded, $raw, $salt) use ($encoder) {
                    return $encoded === $encoder->encodePassword($raw, $salt);
                }
            );

        /** @var UserPasswordEncoderInterface $encoder */
        return $encoder;
    }

    /**
     * @param TestCase $testCase
     * @return MockObject|EncoderFactoryInterface
     */
    public static function getEncoderFactory(TestCase $testCase)
    {
        $encoderFactory = $testCase->createMock(EncoderFactoryInterface::class);
        $encoderFactory
            ->method('getEncoder')
            ->willReturn(self::getPasswordEncoder($testCase));

        /** @var EncoderFactoryInterface $encoderFactory */
        return $encoderFactory;
    }

    /**
     * @param TestCase $testCase
     * @param UserInterface $user
     * @return MockObject|UserProviderInterface
     */
    public static function getUserProvider(TestCase $testCase, UserInterface $user)
    {
        $userProvider = $testCase->createMock(UserProviderInterface::class);
        $userProvider
            ->expects($testCase->once())
            ->method('loadUserByUsername')
            ->with($user->getUsername())
            ->willReturn($user);

        /** @var UserProviderInterface $userProvider */
        return $userProvider;
    }

    /**
     * @param TestCase $testCase
     * @return MockObject|UserPasswordEncoderInterface
     */
    public static function getUserPasswordEncoder(TestCase $testCase)
    {
        $encoder = $testCase->createMock(UserPasswordEncoderInterface::class);

        $encoder
            ->method('encodePassword')
            ->willReturnCallback(
                function (UserInterface $user, $plainPassword) {
                    return 'encoded_' . $plainPassword;
                }
            );

        $encoder
            ->method('isPasswordValid')
            ->willReturnCallback(
                function (UserInterface $user, $raw) use ($encoder) {
                    return $user->getPassword() === $encoder->encodePassword($user, $raw);
                }
            );

        /** @var UserPasswordEncoderInterface $encoder */
        return $encoder;
    }

    /**
     * @param TestCase $testCase
     * @return QueryBuilder|MockObject
     */
    public static function getDoctrineQueryBuilder(TestCase $testCase)
    {
        $builder = $testCase->createMock(QueryBuilder::class);

        $methods = [
            'select',
            'add',
            'addSelect',
            'update',
            'delete',
            'from',
            'where',
            'andWhere',
            'orWhere',
            'groupBy',
            'addGroupBy',
            'join',
            'innerJoin',
            'leftJoin',
            'set',
            'indexBy',
            'distinct',
            'setParameter',
            'setParameters',
            'setFirstResult',
            'setMaxResults',
            'setCacheable',
            'setCacheRegion',
            'setLifetime',
            'setCacheMode',
        ];

        foreach ($methods as $method) {
            $builder
                ->method($method)
                ->willReturn($builder);
        }

        $builder
            ->method('getQuery')
            ->willReturn(self::getDoctrineQuery($testCase));

        /** @var QueryBuilder $builder */
        return $builder;
    }

    /**
     * @param TestCase $testCase
     * @return Query|MockObject
     */
    public static function getDoctrineQuery(TestCase $testCase)
    {
        $query = $testCase->createMock(AbstractQuery::class);

        /** @var Query $query*/
        return $query;
    }
}
