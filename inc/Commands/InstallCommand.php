<?php

namespace RocketLauncherFrontTakeOff\Commands;

use Ahc\Cli\IO\Interactor;
use RocketLauncherBuilder\Commands\Command;
use RocketLauncherBuilder\ObjectValues\InvalidValue;
use RocketLauncherFrontTakeOff\ObjectValues\FrontVersion;
use RocketLauncherFrontTakeOff\Services\FrontEndInstallationManage;
use RocketLauncherFrontTakeOff\Services\ProjectManager;

class InstallCommand extends Command
{

    /**
     * @var FrontEndInstallationManage
     */
    protected $front_end_installation_manage;

    /**
     * @var ProjectManager
     */
    protected $project_manager;

    /**
     * Instantiate the class.
     *
     * @param FrontEndInstallationManage $front_end_installation_manage
     * @param ProjectManager $project_manager
     */
    public function __construct(FrontEndInstallationManage $front_end_installation_manage, ProjectManager $project_manager)
    {
        parent::__construct('front:install', 'Install a frontend');

        $this->front_end_installation_manage = $front_end_installation_manage;
        $this->project_manager = $project_manager;

        $this
            ->argument('[type]', 'Type from the frontend to install')
            // Usage examples:
            ->usage(
            // append details or explanation of given example with ` ## ` so they will be uniformly aligned when shown
                '<bold>  $0 front:install</end> <comment>react </end> ## Install a frontend<eol/>'
            );
    }

    public function interact(Interactor $io)
    {
        // Collect missing opts/args
        if (!$this->type) {

            $types = [
                FrontVersion::REACT => 'React',
                FrontVersion::VANILLA => 'Vanilla',
                FrontVersion::VUE => 'Vue',
            ];

            $this->set('type', $io->choice('Select a frontend', $types, 'vanilla'));
        }
    }

    public function execute($type) {
        $io = $this->app()->io();

        try {
            $type = new FrontVersion($type);
        } catch (InvalidValue $value) {
            $io->error('The type of frontend is not valid');
            return;
        }

        $this->front_end_installation_manage->create_template_folder();
        $this->front_end_installation_manage->move_front_assets($type);
        $this->project_manager->cleanup();
    }
}
