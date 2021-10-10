<?php

namespace Duck\ComposeGenerate\Console\Command;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Console\Cli;
use Magento\Framework\Filesystem;
use Magento\Framework\Validation\ValidationException;
use Magento\Setup\Console\Command\AbstractSetupCommand;
use Duck\ComposeGenerate\Model\VersionInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class Install extends AbstractSetupCommand
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var VersionInterface
     */
    protected $version;

    /**
     * InstallConstruct
     *
     * @param Filesystem $filesystem
     * @param VersionInterface $version
     */
    public function __construct(Filesystem $filesystem, VersionInterface $version)
    {
        $this->filesystem = $filesystem;
        $this->version = $version;
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('duck:compose:install');
        $this->setDescription('Generate docker compose');
        $this->setDefinition($this->getOptionsList());
        parent::configure();
    }

    /**
     * Create interaction mode installation
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws ValidationException
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');
        if (!$input->getOption('service-app')) {
            $question = new Question('<question>Service App:</question> ', '');
            $this->addNotEmptyValidator($question);

            $input->setOption('service-app',
                $questionHelper->ask($input, $output, $question)
            );
        }

        if (!$input->getOption('magento-version')) {
            $question = $this->gatherMagentoVersionWithSymfonyMenu($input, $output);
            if (empty($question)) {
                throw new ValidationException(__('The value cannot be empty'));
            }

            $input->setOption('magento-version', $question);
        }

        if (!$input->getOption('services')) {
            $question = $this->gatherServicesWithSymfonyMenu($input, $output);
            if (empty($question)) {
                throw new ValidationException(__('The value cannot be empty'));
            }

            $input->setOption('services', $question);
        }
    }

    /**
     * CLI command description
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->buildDockerCompose($input->getOptions());
        $this->buildDockerSync($input->getOptions());
        
        return Cli::RETURN_SUCCESS;
    }

    /**
     * Build the Docker compose file
     *
     * @param array $services
     * @return void
     */
    protected function buildDockerCompose(array $services): void
    {
        // build depends on
        $depends = array_filter($services['services'], function ($service) {
            return in_array($service, ['mariadb', 'redis', 'elasticsearch']);
        });
        $depends = array_map(function ($service) {
            return "            - {$service}";
        }, $depends);
        $depends = "depends_on:\n" . implode("\n", $depends);

        // build services
        $stubs = array_map(function ($service) {
            return file_get_contents(__DIR__ . "/../../stubs/{$service}.stub");
        }, $services['services']);
        $stubs = rtrim(implode('', $stubs));

        // build volumes
        $volumes = array_filter($services['services'], function ($service) {
            return in_array($service, ['mariadb']);
        });
        $volumes = array_map(function ($service) {
            return "    duck{$service}:\n        driver: local";
        }, $volumes);
        // set volume for docker-sync
        $volumes = implode("\n", $volumes) . "\n    {$services['service-app']}-sync:\n        external: true";
        if (!empty($volumes)) {
            $volumes = "volumes:\n" . $volumes;
        }

        $dockerCompose = file_get_contents(__DIR__ . '/../../stubs/docker-compose.stub');

        $dockerCompose = str_replace('{{depends}}', empty($depends) ? '' : '        ' . $depends, $dockerCompose);
        $dockerCompose = str_replace('{{services}}', $stubs, $dockerCompose);

        $magentoVersion = $this->version->version($services['magento-version']);
        $php = $magentoVersion->getPhp();
        $elasticsearch = $magentoVersion->getElasticsearch();
        $mariadb = $magentoVersion->getMariadb();
        $redis = $magentoVersion->getRedis();

        array_push($services['services'], 'php');
        foreach ($services['services'] as $service) {
            $dockerCompose = str_replace("{{{$service}_version}}", ${$service}, $dockerCompose);
        }
        $dockerCompose = str_replace("{{service}}", $services['service-app'], $dockerCompose);
        $dockerCompose = str_replace('{{volumes}}', $volumes, $dockerCompose);

        // Remove empty lines...
        $dockerCompose = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $dockerCompose);

        $root = $this->filesystem->getDirectoryRead(DirectoryList::ROOT)
            ->getAbsolutePath('/');
        file_put_contents($root . 'docker-compose.yml', $dockerCompose);
    }

    /**
     * Build docker sync file
     *
     * @param array $services
     */
    protected function buildDockerSync(array $services)
    {
        $stub = file_get_contents(__DIR__ . "/../../stubs/docker-sync.stub");
        $dockerSync = str_replace("{{service}}", $services['service-app'], $stub);

        $root = $this->filesystem->getDirectoryRead(DirectoryList::ROOT)
            ->getAbsolutePath('/');
        file_put_contents($root . 'docker-sync.yml', $dockerSync);
    }

    /**
     * Gather the desired duck magento version using a Symfony menu.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return string
     */
    protected function gatherMagentoVersionWithSymfonyMenu(
        InputInterface $input,
        OutputInterface $output
    ): string {
        $question = new ChoiceQuestion(
            'Which magento version would you like to install?',
            $this->version->getList(),
            0
        );
        $question->setMaxAttempts(null);
        return (new SymfonyStyle($input, $output))
            ->askQuestion($question);
    }

    /**
     * Gather the desired duck services using a Symfony menu.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array
     */
    protected function gatherServicesWithSymfonyMenu(
        InputInterface $input,
        OutputInterface $output
    ): array {
        $question = new ChoiceQuestion(
            'Which services would you like to install?',
            ['mariadb', 'redis', 'elasticsearch'],
            0
        );
        $question->setMaxAttempts(null)
            ->setMultiselect(true);
        return (new SymfonyStyle($input, $output))
            ->askQuestion($question);
    }

    /**
     * Get list of arguments for the command
     *
     * @param int $mode The mode of options.
     * @return InputOption[]
     */
    protected function getOptionsList(int $mode = InputOption::VALUE_REQUIRED): array
    {
        $required = ($mode === InputOption::VALUE_REQUIRED)
            ? '(Required) '
            : '';

        return [
            new InputOption(
                'service-app',
                null,
                $mode,
                $required . 'Service App'
            ),
            new InputOption(
                'magento-version',
                null,
                $mode,
                $required . 'Magento Version'
            ),
            new InputOption(
                'services',
                null,
                $mode,
                $required . 'Services'
            ),
        ];
    }

    /**
     * Validate not empty question
     *
     * @param Question $question
     */
    private function addNotEmptyValidator(Question $question)
    {
        $question->setValidator(function ($value) {
            if (trim($value) == '') {
                throw new ValidationException(__('The value cannot be empty'));
            }
            return $value;
        });
    }
}
